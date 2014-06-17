<?php
// Debug?
if (!defined('DEBUG')) {
    define('DEBUG', true);
}

// Turn error reporting on/off
ini_set('display_errors', DEBUG);
ini_set('auto_detect_line_endings', true);
ini_set('allow_url_fopen', true);
error_reporting(E_ALL ^ E_DEPRECATED);

// Define absolute Paths
define('INC_ROOT', __DIR__);
define('PROJ_ROOT', realpath(INC_ROOT . '/..'));
define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT']);

define('PROJ_ROOT_URL', str_replace(DOC_ROOT, '', PROJ_ROOT));

// set Error log
ini_set('log_errors', true);
ini_set("error_log", PROJ_ROOT . '/var/log/php-error.log');

date_default_timezone_set('Europe/Zurich');
setlocale(LC_TIME, 'de_DE');
gc_enable();


//ini_set('memory_limit', '1024M');

// includes
require_once 'autoloader.php';