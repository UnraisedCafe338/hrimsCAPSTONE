<?php
// Project bootstrap: central autoloader and base URL
// Adjust BASE_URL if the application is deployed under a different subfolder.
$projectRoot = realpath(__DIR__);
// Load Composer autoloader
if (file_exists($projectRoot . '/vendor/autoload.php')) {
    require_once $projectRoot . '/vendor/autoload.php';
}

if (!defined('BASE_URL')) {
    // Default base URL for this project when served from localhost (adjust if needed)
    define('BASE_URL', '/hrims');
}

if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', $projectRoot);
}

// Optional: set timezone if not set
if (!ini_get('date.timezone')) {
    date_default_timezone_set('UTC');
}

?>
