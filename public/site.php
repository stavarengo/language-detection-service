<?php
/** @var \Sta\LanguageDetectionService\ViewHelper $helper */
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
                    <h1 class="text-muted">Free Online Tool Language Detector</h1>
                </nav>
            </div>

            <div class="alert alert-danger" role="alert">
                <h4 class="alert-heading">
                    This documentation page is still under construction.
                </h4>
                <p class="mb-0">Since: 03-feb-2017. Help to finish it is needed.</p>
            </div>

            <!-- Jumbotron -->
            <div class="jumbotron">
                <h1>It will always be free!</h1>
                <p class="lead">
                    My name is Rafael and I'm from Brazil. I recently needed a reliable tool to detect the language of a text.
                    Since I could not find any free tools, I decided to create my own and make it available to everyone free of charge.
                </p>
            </div>

            <!-- Example row of columns -->
            <div class="row">
                <div class="col-lg-4">
                    <h2>How it works?</h2>
                    <p>
                        Just make a request to <code><?php
                            echo $helper->server('REQUEST_SCHEME') . '://' .
                            $helper->getHost() . $helper->getBasePath('/detect?t=') . urlencode('Bonjour le monde')
                        ?></code>
                        and you will get a answer like above.
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
                <div class="col-lg-4">
                    <h2>Multiple languages in same text</h2>
                    <p>Donec id elit non mi porta gravida at eget metus. Fusce dapibus, tellus ac cursus commodo, tortor mauris condimentum nibh, ut fermentum massa justo sit amet risus. Etiam porta sem malesuada magna mollis euismod. Donec sed odio dui.</p>
                    <p><a class="btn btn-primary" href="#" role="button">View details &raquo;</a></p>
                </div>
                <div class="col-lg-4">
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

