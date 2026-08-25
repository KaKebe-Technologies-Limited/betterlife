<?php
/**
 * Global configuration. Auto-detects whether the app is running on the
 * local XAMPP dev machine or on the live server, and loads the right
 * database credentials for each — the same codebase works in both places
 * with zero manual edits when you deploy.
 */

$httpHost = $_SERVER['HTTP_HOST'] ?? '';
$isLocal = PHP_SAPI === 'cli'
    || $httpHost === 'localhost'
    || str_starts_with($httpHost, 'localhost:')
    || str_starts_with($httpHost, '127.0.0.1')
    || str_starts_with($httpHost, '::1');

define('IS_LOCAL_ENV', $isLocal);

if ($isLocal) {
    // Local XAMPP defaults (standard XAMPP install: root / no password).
    define('DB_HOST', 'localhost');
    define('DB_NAME', 'betterlife');
    define('DB_USER', 'root');
    define('DB_PASS', '');
} else {
    // Live server credentials are kept OUT of git entirely. Create
    // config/config.local.php on the live server itself (copy
    // config.local.php.example and fill in the real values) — see that
    // file for the exact format.
    $liveConfig = __DIR__ . '/config.local.php';
    if (file_exists($liveConfig)) {
        require $liveConfig;
    } else {
        http_response_code(500);
        die('Live database configuration is missing. Create config/config.local.php on the server (copy config/config.local.php.example and fill in your real DB credentials).');
    }
}

// Compute the site's root URL (e.g. /betterlife, or '' at a domain root)
// regardless of how deep the current script is (root pages vs admin/
// pages) or which server it's running on, so links always work.
$rootFs   = str_replace('\\', '/', realpath(dirname(__DIR__)));
$docRoot  = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
$siteUrl  = substr($rootFs, strlen($docRoot));
define('SITE_URL', $siteUrl === false ? '' : $siteUrl);

define('ADMIN_URL', SITE_URL . '/admin');
define('UPLOADS_URL', SITE_URL . '/uploads');

date_default_timezone_set('Africa/Kampala');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

error_reporting(E_ALL);
if (IS_LOCAL_ENV) {
    ini_set('display_errors', '1');
} else {
    // Never leak errors (which can include DB details) to live visitors.
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}
