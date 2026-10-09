<?php
/**
 * Writes live-content-sync.sql from this computer's database, so the live site shows exactly what was built
 * and checked here. Push the code, then on the live site open Admin > Site Update and press the button
 * (or, by hand, phpMyAdmin > Import this file).
 *
 * Usage: C:\xampp\php\php.exe tools/make_live_sync.php
 *
 * - Content tables are replaced in full (page text, team, products, stats, reports, projects, stories).
 * - Site settings are updated key by key. Passwords, payment keys, order alerts and maintenance mode are never
 *   included, and empty local values are skipped so they cannot blank a value already set on the live site,
 *   except the few in $clearOnLive, which are meant to be empty everywhere.
 * - Orders, customers, messages, subscribers and admin accounts are never touched.
 * - Every image or file a row points to must be in git (so it exists on the server); the script stops if not.
 */
if (PHP_SAPI !== 'cli') { http_response_code(404); exit; }
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/functions.php';
require __DIR__ . '/../includes/site-sync.php';
$root = dirname(__DIR__);
$tables = array_keys(SITE_SYNC_TABLES);

// Settings that are empty on purpose here (BetterLife has no Facebook page) and must be emptied on the live site
$clearOnLive = array_values(array_filter(['facebook'], fn($k) => trim(setting($pdo, $k, '')) === ''));

// Files in git: anything a row points to must be among them
$tracked = array_flip(array_map('trim', explode("\n", (string) shell_exec('git -C ' . escapeshellarg($root) . ' ls-files assets uploads'))));
$missing = [];
foreach ($tables as $t) {
    foreach ($pdo->query("SELECT * FROM `$t`")->fetchAll(PDO::FETCH_ASSOC) as $r) foreach ($r as $v) {
        if (is_string($v) && preg_match_all('~(?:assets|uploads)/[A-Za-z0-9_./ -]+\.(?:jpe?g|png|webp|gif|svg|pdf|mp4)~i', $v, $m)) {
            foreach ($m[0] as $p) if (!isset($tracked[$p])) $missing["$t: $p"] = true;
        }
    }
}
foreach ($pdo->query("SELECT setting_key, setting_value FROM settings") as $s) {
    if (preg_match(SITE_SYNC_PROTECTED, $s['setting_key'])) continue;
    if (preg_match('~^(?:assets|uploads)/\S+\.(?:jpe?g|png|webp|gif|svg|ico)$~i', (string) $s['setting_value']) && !isset($tracked[$s['setting_value']])) $missing['settings: ' . $s['setting_value']] = true;
}
if ($missing) {
    fwrite(STDERR, "These files are referenced but not in git, so they would be missing on the live site:\n  " . implode("\n  ", array_keys($missing)) . "\nNothing written.\n");
    exit(1);
}

$out = site_sync_dump($pdo, $tables, 'live content sync', false, $clearOnLive);
site_sync_plan($out);   // the file must pass the same checks Site Update makes
file_put_contents($root . '/live-content-sync.sql', $out);
echo 'live-content-sync.sql written: ' . number_format(strlen($out)) . ' bytes, ' . count($tables) . " tables plus settings\n";
