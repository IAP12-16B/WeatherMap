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

$savePath = PROJ_ROOT . '/maps';

$mapTypes = array(
    'Wetter' => $forecaster->saveWeatherMaps($savePath, true),
    'Temperatur' => $forecaster->saveTemperatureMaps($savePath, true),
    'Wind' => $forecaster->saveWindMaps($savePath, true),
    'Pollen' => $forecaster->savePollenMaps($savePath, true)
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
<div class="outer-wrapper">
    <header class="main-header">
        <nav class="main-nav">
            <ul>
                <?php foreach ($mapTypes as $name => $maps): ?>
                    <li data-map-type="<?php echo strtolower($name); ?>">
                        <?php echo $name; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </header>

    <div class="inner-wrapper">
        <ul class="maps">
            <li data-map-type="weather">
                <?php
                $maps = $forecaster->savePollenMaps(PROJ_ROOT . '/maps', true);
                ?>
                <article class="map-area">
                </article>
                <aside class="control">

                </aside>
            </li>
        </ul>
    </div>
</div>


<!-- Scripts -->
<script src="<?php echo PROJ_ROOT_URL; ?>/js/mootools-core-1.5.0.js"></script>
<script src="<?php echo PROJ_ROOT_URL; ?>/js/mootools-more-1.5.0.js"></script>
<script src="<?php echo PROJ_ROOT_URL; ?>/js/forecaster.js"></script>
</body>
</html>