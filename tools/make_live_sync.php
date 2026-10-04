<?php
/**
 * Writes live-content-sync.sql from this computer's database, so the live site shows exactly what was built
 * and checked here. Import the file on the live server (phpMyAdmin → Import) after pushing the code.
 *
 * Usage: C:\xampp\php\php.exe tools/make_live_sync.php
 *
 * - Content tables are replaced in full (page text, team, products, stats, reports, projects, stories).
 * - Site settings are updated key by key. Passwords, payment keys, order alerts and maintenance mode are never
 *   included, and empty local values are skipped so they cannot blank a value already set on the live site.
 * - Orders, customers, messages, subscribers and admin accounts are never touched.
 * - Every image or file a row points to must be in git (so it exists on the server); the script stops if not.
 */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../includes/db.php';
$root = dirname(__DIR__);

$tables = ['products', 'team_members', 'stats', 'programs', 'projects', 'blog_categories', 'blog_posts',
           'testimonials', 'impact_stories', 'reports', 'content_items'];
$neverSettings = '/^(smtp_|pesapal_|admin_alert_|maintenance_)/';

// Files in git: anything a row points to must be among them
$tracked = array_flip(array_map('trim', explode("\n", (string) shell_exec('git -C ' . escapeshellarg($root) . ' ls-files assets uploads'))));
$missing = [];

$q = fn($v) => $v === null ? 'NULL' : $pdo->quote((string) $v);
$out = "-- BetterLife International: live content sync, generated " . date('Y-m-d H:i') . " by tools/make_live_sync.php\n"
     . "-- Replaces the site's content tables and updates site settings. Never touches orders, messages, subscribers,\n"
     . "-- admin accounts, passwords or payment keys.\n\n"
     . "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS = 0;\nSET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n";

foreach ($tables as $t) {
    $create = $pdo->query("SHOW CREATE TABLE `$t`")->fetch(PDO::FETCH_NUM)[1];
    $rows = $pdo->query("SELECT * FROM `$t`")->fetchAll(PDO::FETCH_ASSOC);
    $out .= "-- $t (" . count($rows) . " rows)\nDROP TABLE IF EXISTS `$t`;\n" . $create . ";\n";
    foreach (array_chunk($rows, 25) as $chunk) {
        $cols = '`' . implode('`, `', array_keys($chunk[0])) . '`';
        $vals = array_map(fn($r) => '(' . implode(', ', array_map($q, $r)) . ')', $chunk);
        $out .= "INSERT INTO `$t` ($cols) VALUES\n" . implode(",\n", $vals) . ";\n";
    }
    $out .= "\n";
    foreach ($rows as $r) foreach ($r as $v) {
        if (is_string($v) && preg_match_all('~(?:assets|uploads)/[A-Za-z0-9_./ -]+\.(?:jpe?g|png|webp|gif|svg|pdf|mp4)~i', $v, $m)) {
            foreach ($m[0] as $p) if (!isset($tracked[$p])) $missing["$t: $p"] = true;
        }
    }
}

$out .= "-- Site settings (content only)\n";
foreach ($pdo->query("SELECT setting_key, setting_value FROM settings ORDER BY setting_key") as $s) {
    if (preg_match($neverSettings, $s['setting_key']) || trim((string) $s['setting_value']) === '') continue;
    $out .= "INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES (" . $q($s['setting_key']) . ", " . $q($s['setting_value']) . ") ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);\n";
    if (preg_match('~^(?:assets|uploads)/\S+\.(?:jpe?g|png|webp|gif|svg|ico)$~i', $s['setting_value']) && !isset($tracked[$s['setting_value']])) $missing['settings: ' . $s['setting_value']] = true;
}
$out .= "\nSET FOREIGN_KEY_CHECKS = 1;\n";

if ($missing) {
    fwrite(STDERR, "These files are referenced but not in git, so they would be missing on the live site:\n  " . implode("\n  ", array_keys($missing)) . "\nNothing written.\n");
    exit(1);
}
file_put_contents($root . '/live-content-sync.sql', $out);
echo 'live-content-sync.sql written: ' . number_format(strlen($out)) . " bytes, " . count($tables) . " tables plus settings\n";
