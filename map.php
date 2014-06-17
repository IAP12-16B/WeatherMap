<?php

require_once 'inc/global.inc.php';

use kije\Forecaster\CSV\ForecasterDescriptor;
use kije\Forecaster\Forecaster;
use kije\Forecaster\Themes\Theme;

ob_start(); // prevent error outputing

$theme = new Theme(PROJ_ROOT . '/var/themes/forecaster.ftheme');
$forecaster = new Forecaster(
    new ForecasterDescriptor(';'),
    //'http://home.gibm.ch/m307/wetter.php',
    PROJ_ROOT . '/test.csv',
    $theme
);

if ($err_contents = ob_get_contents()) {
    error_log($err_contents);
}

ob_end_clean();


if (array_key_exists('date', $_GET) && array_key_exists('type', $_GET)) {
    header('Content-Type: image/jpg');
    header("Cache-Control: max-age=2592000");
    if ($image = $forecaster->getMap($_GET['date'], $_GET['type'])) {
        echo $image->render()->render()->getJPEG();
    } else {
        http_response_code(500);
    }
    exit();
} else {
    http_response_code(400);
    exit();
}

// if this script somehow gets to this, send a 418 response code :D
// Only supported by the HTCPCP (Hyper Text Coffee Pot Control Protocol)
http_response_code(418); // I’m a teapot
