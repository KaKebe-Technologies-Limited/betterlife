<?php
/**
 * Global configuration. Edit DB credentials here if your MySQL setup differs
 * from the default XAMPP install (root / no password).
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'betterlife');
define('DB_USER', 'root');
define('DB_PASS', '');

// Compute the site's root URL (e.g. /betterlife) regardless of how deep the
// current script is (root pages vs admin/ pages), so links always work.
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
ini_set('display_errors', '1');
