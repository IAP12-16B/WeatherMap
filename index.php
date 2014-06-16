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

    <link rel="stylesheet" href="<?php echo PROJ_ROOT_URL; ?>/css/normalize.css">
    <link rel="stylesheet" href="<?php echo PROJ_ROOT_URL; ?>/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo PROJ_ROOT_URL; ?>/css/forecaster.css">

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
            <?php foreach ($mapTypes as $name => $maps): ?>
                <?php $first = current($maps); ?>
                <?php $mapType = strtolower($name); ?>
                <li data-map-type="<?php echo $mapType; ?>">
                    <article class="map-area">
                        <img src="<?php echo $first; ?>" alt="<?php echo $name; ?>" class="map">
                    </article>
                    <aside class="control">
                        <?php $num = 0; ?>
                        <datalist id="<?php echo $mapType; ?>_datalist">
                            <?php foreach ($maps as $date => $map): ?>
                                <option
                                    value="<?php echo $map; ?>"
                                    data-text="<?php echo date('l', $date); ?>"
                                    data-index="<?php echo $num; ?>"
                                    ></option>
                                <?php $num++; ?>
                            <?php endforeach; ?>
                        </datalist>
                        <input type="range" class="date-slider" min="0" max="<?php echo $num - 1; ?>" step="1" value="0"
                               list="<?php echo $mapType; ?>_datalist">
                    </aside>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>


<!-- Scripts -->
<script src="<?php echo PROJ_ROOT_URL; ?>/js/mootools-core-1.5.0.js"></script>
<script src="<?php echo PROJ_ROOT_URL; ?>/js/mootools-more-1.5.0.js"></script>
<script src="<?php echo PROJ_ROOT_URL; ?>/js/forecaster.js"></script>
</body>
</html>