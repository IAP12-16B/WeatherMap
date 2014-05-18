<?php
// Debug?
if (!defined('DEBUG')) {
    define('DEBUG', true);
}

// Turn error reporting on/off
ini_set('display_errors', DEBUG);
error_reporting(E_ALL ^ E_DEPRECATED);

// Define absolute Paths
define('INC_ROOT', __DIR__);
define('PROJ_ROOT', realpath(INC_ROOT . '/..'));
define('DOC_ROOT', $_SERVER['DOCUMENT_ROOT']);

define('PROJ_ROOT_URL', str_replace(DOC_ROOT, '', PROJ_ROOT));

// set Error log
ini_set("log_errors", true);
ini_set("error_log", PROJ_ROOT . "/var/log/php-error.log");


//set Zend_Locale
if (class_exists('Zend_Registry', false)) {
    Zend_Registry::set('Zend_Locale', new Zend_Locale('de_CH')); // is that required in newer versions?
}


// includes
require_once 'autoloader.php';