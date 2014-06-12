<?php
use kije\Forecaster\CSV\ForecasterDescriptor;
use kije\Forecaster\Forecaster;
use kije\Forecaster\Themes\Theme;

require_once 'inc/global.inc.php';

$forecaster = new Forecaster(
    new ForecasterDescriptor(';'),
    PROJ_ROOT.'/test.csv'
);

$theme = new Theme(PROJ_ROOT.'/var/themes/forecaster.ftheme');
?>
<!DOCTYPE html>
<html lang="de">
    <head>
        <meta charset="UTF-8" />
        <!--[if IE]>
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <![endif]-->

        <title>Wetterkarten &ndash; Forecaster &ndash; kije</title>

        <link href="<?php echo PROJ_ROOT_URL; ?>/css/normalize.css">
        <link href="<?php echo PROJ_ROOT_URL; ?>/fontawesome/css/font-awesome.min.css">
        <link href="<?php echo PROJ_ROOT_URL; ?>/css/forecaster.css">

        <link href="<?php echo $theme->get('font.googleFontsURL'); ?>" rel="stylesheet">
        <style>
            html,body {
                font-family: <?php echo $theme->get('font.name'); ?>, sans-serif;
            }
        </style>
    </head>
    <body>

        <!-- Scripts -->
        <script src="<?php echo PROJ_ROOT_URL; ?>/js/mootools-core-1.5.0.js"></script>
        <script src="<?php echo PROJ_ROOT_URL; ?>/js/mootools-more-1.5.0.js"></script>
        <script src="<?php echo PROJ_ROOT_URL; ?>/js/forecaster.js"></script>
    </body>
</html>