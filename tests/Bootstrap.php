<?php
error_reporting(E_ALL | E_STRICT);
chdir(__DIR__ . '/../src/');

define('TEST_ASSETS_DIR', __DIR__ . '/assets');
define('TEST_CACHE_DIR', __DIR__ . '/cache');
define('TEST_PUBLIC_DIR', __DIR__ . '/public');

require '../vendor/autoload.php';
