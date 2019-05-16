<?php
/** @var \Sta\LanguageDetectionService\ViewHelper $helper */

if (!isset($helper)) {
    // This request is access this site directly. Did not passed by index.php
    // Its like they type <http_address>/site.php in the browser address bar
    require_once __DIR__ . '/../vendor/autoload.php';
    $helper = new \Sta\LanguageDetectionService\ViewHelper();
    $helper->echoJson(
        $helper->apiProblem(
            'Not found',
            404,
            'This page does not exist.'
        ),
        404
    );
    return;
}

?>
<!doctype html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport"
            content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <link rel="stylesheet" href="<?php echo $helper->getBasePath('css/app.css') ?>">
        <link rel="stylesheet"
            href="<?php echo $helper->getBasePath('css/bootstrap-4.0.0-alpha.6-dist/css/bootstrap.min.css') ?>">
        <title>Free language detector online tool</title>
    </head>
    <body>

        <div class="container">
            <div class="masthead">
                <nav class="navbar navbar-light bg-faded rounded mb-3">
                    <h1 class="text-muted">Free Online Language Detector Tool</h1>
                </nav>
            </div>
            <!-- Jumbotron -->
            <div class="jumbotron">
                <h1>It will always be free!</h1>
            </div>

            <!-- Example row of columns -->
            <div class="row">
                <div class="col-lg-6">
                    <h2>How it works?</h2>
                    <p>
                        Just make a request to <code><?php
                            echo $helper->server('REQUEST_SCHEME') . '://' .
                            $helper->getHost() . $helper->getBasePath('/detect?t=') . urlencode('Bonjour le monde')
                        ?></code>
                        and you will get a answer like bellow.
                    </p>
                    <pre><?php print_r(
                        [
                            [
                                'lang' => 'fr',
                                'confidence' => 0.9,
                            ],
                        ]
                    ) ?></pre>
                    <p><a class="btn btn-primary" href="#" role="button">View details &raquo;</a></p>
                </div>
                <div class="col-lg-6">
                    <h2>How does it detect languages?</h2>
                    <p>It uses CLD2 PHP Extention, from here <a href="https://github.com/fntlnz/cld2-php-ext">https://github.com/fntlnz/cld2-php-ext</a>.</p>
                    <p><a class="btn btn-primary" href="#" role="button">View details &raquo;</a></p>
                </div>
            </div>

            <!-- Site footer -->
            <footer class="footer">
                <p>&copy; Company 2017</p>
            </footer>

        </div> <!-- /container -->


        <script type="application/javascript">
            (function() {
                if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
                    var msViewportStyle = document.createElement('style')
                    msViewportStyle.appendChild(
                        document.createTextNode(
                            '@-ms-viewport{width:auto!important}'
                        )
                    )
                    document.head.appendChild(msViewportStyle)
                }
            })()
        </script>
        <script src="<?php echo $helper->getBasePath('js/jquery-3.1.1.min.js') ?>"></script>
        <script src="<?php echo $helper->getBasePath('js/tether-1.3.3/dist/js/tether.min.js') ?>"></script>
        <script src="<?php echo $helper->getBasePath('css/bootstrap-4.0.0-alpha.6-dist/js/bootstrap.min.js') ?>"
            async></script>
    </body>
</html>

