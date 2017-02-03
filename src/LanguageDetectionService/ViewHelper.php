<?php
namespace Sta\LanguageDetectionService;

use TheIconic\Tracking\GoogleAnalytics\Analytics;

class ViewHelper
{
    protected $requestUri;
    protected $baseUrl;
    protected $basePath;

    public function getRequestUri()
    {
        if ($this->requestUri === null) {
            $this->requestUri = $this->detectRequestUri();
        }

        return $this->requestUri;
    }

    /**
     * Get the base path.
     *
     * @return string
     */
    public function getBasePath($relativeFile = '')
    {
        if ($this->basePath === null) {
            $this->basePath = $this->detectBasePath();
        }

        if (null !== $relativeFile) {
            $relativeFile = '/' . ltrim($relativeFile, '/');
        }

        return $this->basePath . $relativeFile;
    }

    public function getHost()
    {
        // URI host & port
        $host = null;
        $port = null;

        if (!$host && $this->server('SERVER_NAME')) {
            $host = $this->server('SERVER_NAME');
            if ($this->server('SERVER_PORT')) {
                $port = (int)$this->server('SERVER_PORT');
            }
            // Check for missinterpreted IPv6-Address
            // Reported at least for Safari on Windows
            if ($this->server('SERVER_ADDR') && preg_match('/^\[[0-9a-fA-F\:]+\]$/', $host)) {
                $host = '[' . $this->server('SERVER_ADDR') . ']';
                if ($port . ']' == substr($host, strrpos($host, ':') + 1)) {
                    // The last digit of the IPv6-Address has been taken as port
                    // Unset the port so the default port can be used
                    $port = null;
                }
            }
        }

        return $host . ($port != 80 && $port != 443 ? ':' . $port : '');
    }

    /**
     * Get the base URL.
     *
     * @return string
     */
    public function getBaseUrl()
    {
        if ($this->baseUrl === null) {
            $this->baseUrl = $this->detectBaseUrl();
        }

        return $this->baseUrl;
    }

    public function apiProblem($title, $status, $detail)
    {
        return [
            'title' => $title,
            'status' => $status,
            'detail' => $detail,
        ];
    }

    public function echoJson($response, $status = 200)
    {
        header('Content-Type: application/json; charset=utf8');
        http_response_code($status);
        echo json_encode($response);
    }

    public function trackRequestOnAnalytics($config)
    {
        try {
            $analytics = self::getAnalytics($config);
            $analytics->sendPageview();
        } catch (\Exception $e) {
        }
    }

    public function trackTimeOnAnalytics(array $config, $milliseconds)
    {
        try {
            $analytics = self::getAnalytics($config);
            $analytics->setUserTimingCategory('Detection');
            $analytics->setUserTimingLabel('Detection Lang');
            $analytics->setUserTimingVariableName('detection');
            $analytics->setUserTimingTime($milliseconds);
            $analytics->sendTiming();
        } catch (\Exception $e) {
        }
    }

    protected function detectBaseUrl()
    {
        $filename       = $this->server('SCRIPT_FILENAME', '');
        $scriptName     = $this->server('SCRIPT_NAME');
        $phpSelf        = $this->server('PHP_SELF');
        $origScriptName = $this->server('ORIG_SCRIPT_NAME');

        if ($scriptName !== null && basename($scriptName) === $filename) {
            $baseUrl = $scriptName;
        } elseif ($phpSelf !== null && basename($phpSelf) === $filename) {
            $baseUrl = $phpSelf;
        } elseif ($origScriptName !== null && basename($origScriptName) === $filename) {
            // 1and1 shared hosting compatibility.
            $baseUrl = $origScriptName;
        } else {
            // Backtrack up the SCRIPT_FILENAME to find the portion
            // matching PHP_SELF.

            $baseUrl  = '/';
            $basename = basename($filename);
            if ($basename) {
                $path    = ($phpSelf ? trim($phpSelf, '/') : '');
                $basePos = strpos($path, $basename) ?: 0;
                $baseUrl .= substr($path, 0, $basePos) . $basename;
            }
        }

        // If the baseUrl is empty, then simply return it.
        if (empty($baseUrl)) {
            return '';
        }

        // Does the base URL have anything in common with the request URI?
        $requestUri = $this->getRequestUri();

        // Full base URL matches.
        if (0 === strpos($requestUri, $baseUrl)) {
            return $baseUrl;
        }

        // Directory portion of base path matches.
        $baseDir = str_replace('\\', '/', dirname($baseUrl));
        if (0 === strpos($requestUri, $baseDir)) {
            return $baseDir;
        }

        $truncatedRequestUri = $requestUri;

        if (false !== ($pos = strpos($requestUri, '?'))) {
            $truncatedRequestUri = substr($requestUri, 0, $pos);
        }

        $basename = basename($baseUrl);

        // No match whatsoever
        if (empty($basename) || false === strpos($truncatedRequestUri, $basename)) {
            return '';
        }

        // If using mod_rewrite or ISAPI_Rewrite strip the script filename
        // out of the base path. $pos !== 0 makes sure it is not matching a
        // value from PATH_INFO or QUERY_STRING.
        if (strlen($requestUri) >= strlen($baseUrl)
            && (false !== ($pos = strpos($requestUri, $baseUrl)) && $pos !== 0)
        ) {
            $baseUrl = substr($requestUri, 0, $pos + strlen($baseUrl));
        }

        return $baseUrl;
    }

    protected function detectRequestUri()
    {
        $requestUri = null;

        // Check this first so IIS will catch.
        $httpXRewriteUrl = $this->server('HTTP_X_REWRITE_URL');
        if ($httpXRewriteUrl !== null) {
            $requestUri = $httpXRewriteUrl;
        }

        // Check for IIS 7.0 or later with ISAPI_Rewrite
        $httpXOriginalUrl = $this->server('HTTP_X_ORIGINAL_URL');
        if ($httpXOriginalUrl !== null) {
            $requestUri = $httpXOriginalUrl;
        }

        // IIS7 with URL Rewrite: make sure we get the unencoded url
        // (double slash problem).
        $iisUrlRewritten = $this->server('IIS_WasUrlRewritten');
        $unencodedUrl    = $this->server('UNENCODED_URL', '');
        if ('1' == $iisUrlRewritten && '' !== $unencodedUrl) {
            return $unencodedUrl;
        }

        // HTTP proxy requests setup request URI with scheme and host [and port]
        // + the URL path, only use URL path.
        if (!$httpXRewriteUrl) {
            $requestUri = $this->server('REQUEST_URI');
        }

        if ($requestUri !== null) {
            return preg_replace('#^[^/:]+://[^/]+#', '', $requestUri);
        }

        // IIS 5.0, PHP as CGI.
        $origPathInfo = $this->server('ORIG_PATH_INFO');
        if ($origPathInfo !== null) {
            $queryString = $this->server('QUERY_STRING', '');
            if ($queryString !== '') {
                $origPathInfo .= '?' . $queryString;
            }

            return $origPathInfo;
        }

        return '/';
    }

    public function detectBasePath()
    {
        $filename = basename($this->server('SCRIPT_FILENAME', ''));
        $baseUrl  = $this->getBaseUrl();

        // Empty base url detected
        if ($baseUrl === '') {
            return '';
        }

        // basename() matches the script filename; return the directory
        if (basename($baseUrl) === $filename) {
            return str_replace('\\', '/', dirname($baseUrl));
        }

        // Base path is identical to base URL
        return $baseUrl;
    }

    /**
     * @param $config
     *
     * @return \TheIconic\Tracking\GoogleAnalytics\Analytics
     */
    private static function getAnalytics(array $config)
    {
        $clientId = 'anonym';
        $remoteIp = null;
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $remoteIp = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $remoteIp = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED'])) {
            $remoteIp = $_SERVER['HTTP_X_FORWARDED'];
        } else if (isset($_SERVER['HTTP_FORWARDED_FOR'])) {
            $remoteIp = $_SERVER['HTTP_FORWARDED_FOR'];
        } else if (isset($_SERVER['HTTP_FORWARDED'])) {
            $remoteIp = $_SERVER['HTTP_FORWARDED'];
        } else if (isset($_SERVER['REMOTE_ADDR'])) {
            $remoteIp = $_SERVER['REMOTE_ADDR'];
        }

        $analytics = new Analytics(true);

        $analytics->setProtocolVersion('1')
                  ->setTrackingId($config['analytics-id'])
                  ->setClientId(md5($clientId))
                  ->setDocumentPath(php_sapi_name() == 'cli' ? 'terminal-cli' : $_SERVER['REQUEST_URI']);

        if ($remoteIp) {
            $analytics->setIpOverride($remoteIp);
        }

        $analytics->setAsyncRequest(true);

        return $analytics;
    }

    public function server($name, $default = null)
    {
        return isset($_SERVER[$name]) ? $_SERVER[$name] : $default;
    }

    public function header($name, $default = null)
    {
        return $this->server('HTTP_' . $name, $default);
    }

}
