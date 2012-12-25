<?php
error_reporting(E_ALL | E_STRICT);
chdir(__DIR__ . '/../library');

/**
 * Simple autoloader
 */
spl_autoload_register(function($className){
    $className = ltrim($className, '\\');
    $fileName  = '';
    if ($lastNsPos = strripos($className, '\\')) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace('\\', DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    require $fileName;
});