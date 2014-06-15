<?php

// Autoloader
spl_autoload_register(
    function ($classname) {
        /** @inc $classname String */
        $classpath = str_replace('\\', DIRECTORY_SEPARATOR, $classname);
        if (file_exists((PROJ_ROOT . '/lib/local/' . $classpath . '.php'))) {
            include_once PROJ_ROOT . '/lib/local/' . $classpath . '.php';
        }
    }
);
