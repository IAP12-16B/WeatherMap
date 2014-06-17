<?php

require_once 'inc/global.inc.php';

use kije\Forecaster\CSV\ForecasterDescriptor;
use kije\Forecaster\Forecaster;
use kije\Forecaster\Themes\Theme;

ob_start(); // prevent error output

// load the theme for the maps
$theme = new Theme(PROJ_ROOT . '/var/themes/forecaster.ftheme');

// initialize the "Forecaster"
$forecaster = new Forecaster(
    new ForecasterDescriptor(';'), // this is the CSVDescriptor
    //'http://home.gibm.ch/m307/wetter.php',
    PROJ_ROOT . '/test.csv',
    $theme
);

// if there were errors, log them to error_log
if ($err_contents = ob_get_contents()) {
    error_log($err_contents);
}
// end output buffering
ob_end_clean();

// check, if the $_GET parameters are set ($_GET['date'] and $_GET['type'])
if (array_key_exists('date', $_GET) && array_key_exists('type', $_GET)) {

    // set headers, so images are displayed as images and not as cryptic-looking bunch of characters
    header('Content-Type: image/jpg');

    // also try to make the browser cache this image, so it doesn't get reloaded every time
    header("Cache-Control: max-age=2592000");

    // try to get requested map
    if ($image = $forecaster->getMap($_GET['date'], $_GET['type'])) {
        // if successful, output it directly...
        echo $image->render()->render()->getJPEG();
    } else {
        // else send 500 (Internal server error) status code
        http_response_code(500);
    }

    // we are done, so there is no need to continue.
    exit();
} else {
    // send 400 (Bad request) status code
    http_response_code(400);
    exit();
}

// if this script somehow gets to this, send a 418 response code :D
// Only supported by the HTCPCP (Hyper Text Coffee Pot Control Protocol)
http_response_code(418); // I’m a teapot
