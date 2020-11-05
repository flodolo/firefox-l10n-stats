<?php

$root_folder = realpath(__DIR__ . '/../../');
require_once "{$root_folder}/vendor/autoload.php";

// Cache class
if (! defined('CACHE_ENABLED')) {
    // Allow disabling cache via config
    // TODO: reset to true
    define('CACHE_ENABLED', false);
}
define('CACHE_PATH', "{$root_folder}/cache/");
define('CACHE_TIME', 60 * 60 * 6); // 6 hours
