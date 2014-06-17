<?php
use kije\Forecaster\CSV\ForecasterDescriptor;
use kije\Forecaster\Forecaster;
use kije\Forecaster\Themes\Theme;

require_once 'inc/global.inc.php';

// load the theme for the maps
$theme = new Theme(PROJ_ROOT . '/var/themes/forecaster.ftheme');

// initialize the "Forecaster"
$forecaster = new Forecaster(
    new ForecasterDescriptor(';'), // this is the CSVDescriptor
    //'http://home.gibm.ch/m307/wetter.php',
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

    <link rel="stylesheet" href="<?php echo PROJ_ROOT_URL; ?>/css/normalize.css">
    <link rel="stylesheet" href="<?php echo PROJ_ROOT_URL; ?>/fontawesome/css/font-awesome.min.css">
    <link rel="stylesheet" href="<?php echo PROJ_ROOT_URL; ?>/css/forecaster.css">

    <link href="<?php echo $theme->getFont('googleFontsURL'); ?>" rel="stylesheet">
    <style>
        /* Set font from theme */
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
                <?php // generate navigation ?>
                <?php foreach ($forecaster->getTypes() as $name => $type): ?>
                    <?php // data-map-type is later used by JS to show/hide the appropriate maps ?>
                    <li data-map-type="<?php echo $type; ?>">
                        <?php echo $name; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    </header>

    <div class="inner-wrapper">
        <ul class="maps">
            <?php // generate the "map-containers". ?>
            <?php foreach ($forecaster->getTypes() as $name => $type): ?>

                <?php
                // get the first map, so something is displayed, even when the user has not clicked on something yet.
                $firstDate = current($forecaster->getDates());
                ?>
                <li data-map-type="<?php echo $type; ?>">
                    <article class="map-area">
                        <img src="<?php printf('%s/map.php?date=%s&type=%s', PROJ_ROOT_URL, $firstDate, $type); ?>"
                             alt="<?php echo $name; ?>" class="map">
                    </article>

                    <form class="control">

                        <?php // this datalist is used to map the map index (0-6) to the necessary map data ?>
                        <datalist id="<?php echo $type; ?>_datalist" class="image-datalist">
                            <?php $num = 0; ?>
                            <?php foreach ($forecaster->getDates() as $date): ?>
                                <option
                                    value="<?php echo $num; ?>"
                                    data-text="<?php echo strftime(
                                        '%A',
                                        $date
                                    ); //strftime to get a localized string ?>"
                                    data-src="<?php printf(
                                        '%s/map.php?date=%s&type=%s',
                                        PROJ_ROOT_URL,
                                        $date,
                                        $type
                                    ); ?>"
                                    ><?php echo strftime('%A', $date); ?></option>
                                <?php $num++; ?>
                            <?php endforeach; ?>
                        </datalist>

                        <input
                            type="range"
                            name="<?php echo $type; ?>_slider"
                            class="date-slider"
                            min="0"
                            max="<?php echo $num - 1; ?>"
                            step="1" value="0"
                            list="<?php echo $type; ?>_datalist">
                    </form>
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