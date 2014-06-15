<?php
use kije\Forecaster\CSV\ForecasterDescriptor;
use kije\Forecaster\Forecaster;
use kije\Forecaster\Themes\Theme;

require_once 'inc/global.inc.php';

$theme = new Theme(PROJ_ROOT . '/var/themes/forecaster.ftheme');

$forecaster = new Forecaster(
    new ForecasterDescriptor(';'),
    PROJ_ROOT . '/test.csv',
    $theme
);

?>
<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8"/>
    <!--[if IE]>
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <![endif]-->

    <title>Wetterkarten &ndash; Forecaster &ndash; kije</title>

    <link href="<?php echo PROJ_ROOT_URL; ?>/css/normalize.css">
    <link href="<?php echo PROJ_ROOT_URL; ?>/fontawesome/css/font-awesome.min.css">
    <link href="<?php echo PROJ_ROOT_URL; ?>/css/forecaster.css">

    <link href="<?php echo $theme->getFont('googleFontsURL'); ?>" rel="stylesheet">
    <style>
        html, body {
            font-family: <?php echo $theme->getFont('name'); ?>, sans-serif;
        }
    </style>
</head>
<body>
<header class="main-header">
    <nav class="main-nav">
        <ul>
            <li data-map-type="weather">
                <a>Wetter</a>
            </li>
        </ul>
    </nav>
</header>
<img src="<?php
echo 'data:image/png;base64,' . base64_encode($forecaster->getTempMap()->getCanvas()->getPNG24());
?>" width="1000">

<!-- Scripts -->
<script src="<?php echo PROJ_ROOT_URL; ?>/js/mootools-core-1.5.0.js"></script>
<script src="<?php echo PROJ_ROOT_URL; ?>/js/mootools-more-1.5.0.js"></script>
<script src="<?php echo PROJ_ROOT_URL; ?>/js/forecaster.js"></script>
</body>
</html>