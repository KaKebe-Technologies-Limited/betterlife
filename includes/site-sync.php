<?php
/**
 * Moving the site's content from one database to another: this computer → the live site.
 *
 * tools/make_live_sync.php writes live-content-sync.sql with site_sync_dump(); it travels to the server with
 * the code, and Admin → Site Update loads it with site_sync_apply(). The same format is used for the backup
 * taken just before an update, so a backup can be put back the same way.
 *
 * Only the content tables below and the site settings can change. Orders, messages, subscribers and admin
 * accounts are not in the list, and passwords, payment keys, order alerts and maintenance mode are never
 * written, whatever a file contains.
 */

const SITE_SYNC_TABLES = [
    'products'        => 'Farm shop products',
    'team_members'    => 'Team and board',
    'stats'           => 'Impact numbers',
    'programs'        => 'Programmes',
    'projects'        => 'Projects',
    'blog_categories' => 'Blog categories',
    'blog_posts'      => 'Blog posts',
    'testimonials'    => 'Testimonials',
    'impact_stories'  => 'Impact stories',
    'reports'         => 'Reports',
    'content_items'   => 'Page text',
];
const SITE_SYNC_PROTECTED = '/^(smtp_|pesapal_|admin_alert_|maintenance_|content_sync_)/';
const SITE_SYNC_FILE = __DIR__ . '/../live-content-sync.sql';
const SITE_SYNC_BACKUPS = __DIR__ . '/../database/backups';

/** Writes tables and settings as SQL in the update-file format. */
function site_sync_dump(PDO $pdo, array $tables, string $title, bool $withEmptySettings, array $clear = []): string
{
    $q = fn($v) => $v === null ? 'NULL' : $pdo->quote((string) $v);
    $out = "-- BetterLife International: $title, generated " . date('Y-m-d H:i') . "\n"
         . "-- Replaces the site's content tables and updates site settings. Never touches orders, messages, subscribers,\n"
         . "-- admin accounts, passwords or payment keys. Load it from Admin > Site Update.\n\n"
         . "SET NAMES utf8mb4;\nSET FOREIGN_KEY_CHECKS = 0;\nSET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n";

    foreach ($tables as $t) {
        $create = $pdo->query("SHOW CREATE TABLE `$t`")->fetch(PDO::FETCH_NUM)[1];
        // MariaDB writes current_timestamp(); every MySQL and MariaDB version reads CURRENT_TIMESTAMP
        $create = str_ireplace('current_timestamp()', 'CURRENT_TIMESTAMP', $create);
        $rows = $pdo->query("SELECT * FROM `$t`")->fetchAll(PDO::FETCH_ASSOC);
        $out .= "-- $t (" . count($rows) . " rows)\nDROP TABLE IF EXISTS `$t`;\n" . $create . ";\n";
        foreach (array_chunk($rows, 25) as $chunk) {
            $cols = '`' . implode('`, `', array_keys($chunk[0])) . '`';
            $vals = array_map(fn($r) => '(' . implode(', ', array_map($q, $r)) . ')', $chunk);
            $out .= "INSERT INTO `$t` ($cols) VALUES\n" . implode(",\n", $vals) . ";\n";
        }
        $out .= "\n";
    }

    $out .= "-- Site settings (content only)\n";
    foreach ($pdo->query("SELECT setting_key, setting_value FROM settings ORDER BY setting_key") as $s) {
        if (preg_match(SITE_SYNC_PROTECTED, $s['setting_key']) || !preg_match('/^[a-z0-9_]+$/', $s['setting_key'])) continue;
        if (!$withEmptySettings && trim((string) $s['setting_value']) === '') continue;
        $out .= "INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES (" . $q($s['setting_key']) . ", " . $q($s['setting_value'] ?? '') . ") ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);\n";
    }
    foreach ($clear as $k) {
        $out .= "UPDATE `settings` SET `setting_value` = '' WHERE `setting_key` = " . $q($k) . ";\n";
    }
    return $out . "\nSET FOREIGN_KEY_CHECKS = 1;\n";
}

/** "2026-10-09 19:14" from a file's first line, or null. */
function site_sync_stamp(string $path): ?string
{
    $fh = @fopen($path, 'r');
    if (!$fh) return null;
    $line = (string) fgets($fh, 400);
    fclose($fh);
    return preg_match('/generated (\d{4}-\d{2}-\d{2} \d{2}:\d{2})/', $line, $m) ? $m[1] : null;
}

/** True when the update file is newer than what this site last loaded. */
function site_sync_pending(PDO $pdo): bool
{
    $stamp = site_sync_stamp(SITE_SYNC_FILE);
    return $stamp !== null && $stamp !== setting($pdo, 'content_sync_applied', '');
}

/** Splits SQL into statements, leaving semicolons inside quoted text and comments alone. */
function site_sync_statements(string $sql): array
{
    $out = [];
    $start = 0;
    $n = strlen($sql);
    $buf = '';
    for ($i = 0; $i < $n; $i++) {
        $c = $sql[$i];
        if ($c === "'" || $c === '"' || $c === '`') {
            for ($j = $i + 1; $j < $n; $j++) {
                if ($sql[$j] === '\\' && $c !== '`') { $j++; continue; }
                if ($sql[$j] === $c) {
                    if ($j + 1 < $n && $sql[$j + 1] === $c) { $j++; continue; }   // a doubled quote
                    break;
                }
            }
            if ($j >= $n) throw new RuntimeException('The file ends inside quoted text, so it is incomplete.');
            $i = $j;
            continue;
        }
        $isLineComment = $c === '#' || ($c === '-' && ($sql[$i + 1] ?? '') === '-' && in_array($sql[$i + 2] ?? "\n", [' ', "\t", "\r", "\n"], true));
        if ($isLineComment || ($c === '/' && ($sql[$i + 1] ?? '') === '*')) {
            $buf .= substr($sql, $start, $i - $start) . ' ';
            $end = $isLineComment ? strpos($sql, "\n", $i) : strpos($sql, '*/', $i + 2);
            if ($end === false) $end = $n;
            $i = $isLineComment ? $end : $end + 1;
            $start = $i + 1;
            continue;
        }
        if ($c === ';') {
            $buf .= substr($sql, $start, $i - $start);
            if (trim($buf) !== '') $out[] = trim($buf);
            $buf = '';
            $start = $i + 1;
        }
    }
    $buf .= substr($sql, $start);
    if (trim($buf) !== '') $out[] = trim($buf);
    return $out;
}

/**
 * Reads an update file into a plan. Every statement must be one the format allows, for a content table or
 * a site setting; anything else stops the whole file. Protected settings are left out.
 */
function site_sync_plan(string $sql): array
{
    $plan = ['tables' => [], 'settings' => [], 'clear' => [], 'skipped' => [], 'expected' => []];
    preg_match_all('/^-- (\w+) \((\d+) rows?\)\r?$/m', $sql, $m, PREG_SET_ORDER);
    foreach ($m as $x) $plan['expected'][$x[1]] = (int) $x[2];

    $str = "'(?:[^'\\\\]++|\\\\.)*+'";
    $cur = null;
    foreach (site_sync_statements($sql) as $st) {
        if (preg_match("/^SET (NAMES utf8mb4|FOREIGN_KEY_CHECKS\\s*=\\s*[01]|SQL_MODE\\s*=\\s*'[A-Z_,]*')$/i", $st)) continue;

        if (preg_match('/^DROP TABLE IF EXISTS `(\w+)`$/', $st, $x) && isset(SITE_SYNC_TABLES[$x[1]])) {
            $cur = $x[1];
            $plan['tables'][$cur] = ['create' => null, 'inserts' => []];
            continue;
        }
        if (preg_match('/^CREATE TABLE `(\w+)` \(/', $st, $x) && $x[1] === $cur && $plan['tables'][$cur]['create'] === null) {
            $plan['tables'][$cur]['create'] = $st;
            continue;
        }
        if (preg_match('/^INSERT INTO `(\w+)` \(/', $st, $x) && $x[1] === $cur && $plan['tables'][$cur]['create'] !== null) {
            $plan['tables'][$cur]['inserts'][] = $st;
            continue;
        }
        if (preg_match("/^INSERT INTO `settings` \\(`setting_key`, `setting_value`\\) VALUES \\('([a-z0-9_]+)', (?:NULL|$str)\\) ON DUPLICATE KEY UPDATE `setting_value` = VALUES\\(`setting_value`\\)$/s", $st, $x)) {
            if (preg_match(SITE_SYNC_PROTECTED, $x[1])) $plan['skipped'][] = $x[1];
            else $plan['settings'][$x[1]] = $st;
            continue;
        }
        if (preg_match("/^UPDATE `settings` SET `setting_value` = '' WHERE `setting_key` = '([a-z0-9_]+)'$/", $st, $x)) {
            if (preg_match(SITE_SYNC_PROTECTED, $x[1])) $plan['skipped'][] = $x[1];
            else $plan['clear'][] = $x[1];
            continue;
        }
        throw new RuntimeException('The file has a step Site Update does not allow, so nothing was loaded: ' . mb_strimwidth(preg_replace('/\s+/', ' ', $st), 0, 90, '…'));
    }
    foreach ($plan['tables'] as $t => $p) {
        if ($p['create'] === null) throw new RuntimeException("The file removes the $t table without recreating it, so nothing was loaded.");
    }
    if (!$plan['tables'] && !$plan['settings']) throw new RuntimeException('The file has no content in it.');
    return $plan;
}

/** A table's design with its name, foreign keys and next id left out, for comparing two tables. */
function site_sync_shape(PDO $pdo, string $table): ?string
{
    try {
        $c = $pdo->query("SHOW CREATE TABLE `$table`")->fetch(PDO::FETCH_NUM)[1];
    } catch (PDOException $e) {
        return null;   // not there
    }
    $c = preg_replace('/^CREATE TABLE `\w+`/', 'CREATE TABLE `t`', $c);
    $c = preg_replace('/,\s*CONSTRAINT `[^`]+` FOREIGN KEY [^\n]*/', '', $c);
    return preg_replace('/ AUTO_INCREMENT=\d+/', '', $c);
}

function site_sync_count(PDO $pdo, string $table): ?int
{
    try {
        return (int) $pdo->query("SELECT COUNT(*) FROM `$table`")->fetchColumn();
    } catch (PDOException $e) {
        return null;
    }
}

/** Saves the current content tables and settings to database/backups/ and returns the file name. */
function site_sync_backup(PDO $pdo): string
{
    $dir = SITE_SYNC_BACKUPS;
    if (!is_dir($dir) && !@mkdir($dir, 0755, true)) throw new RuntimeException('Could not create the backups folder (database/backups), so nothing was changed.');
    if (!is_file("$dir/.htaccess")) @file_put_contents("$dir/.htaccess", "Require all denied\n");
    $have = array_values(array_filter(array_keys(SITE_SYNC_TABLES), fn($t) => site_sync_count($pdo, $t) !== null));
    $name = 'site-backup-' . date('Y-m-d-His') . '.sql';
    if (@file_put_contents("$dir/$name", site_sync_dump($pdo, $have, 'backup taken before a Site Update', true), LOCK_EX) === false) {
        throw new RuntimeException('Could not save a backup in database/backups, so nothing was changed.');
    }
    $all = site_sync_backups();
    foreach (array_slice($all, 10) as $old) @unlink("$dir/$old");   // keep the ten most recent
    return $name;
}

/** Backup file names, newest first. */
function site_sync_backups(): array
{
    $files = array_map('basename', glob(SITE_SYNC_BACKUPS . '/site-backup-*.sql') ?: []);
    rsort($files);
    return $files;
}

function site_sync_backup_path(string $name): ?string
{
    $path = SITE_SYNC_BACKUPS . '/' . $name;
    return preg_match('/^site-backup-\d{4}-\d{2}-\d{2}-\d{6}\.sql$/', $name) && is_file($path) ? $path : null;
}

/**
 * Loads an update file. Every table is first loaded into a side copy, so a bad file changes nothing; the
 * live tables are then switched over together and the settings written in the same transaction. A backup
 * is taken first, and if the switch fails part way the backup is put straight back.
 *
 * Returns ['ok' => bool, 'error' => ?string, 'backup' => ?string, 'tables' => [t => [before, after, rebuilt]],
 *          'settings' => int, 'cleared' => [...], 'restored' => bool]
 */
function site_sync_apply(PDO $pdo, string $sql, bool $takeBackup = true): array
{
    $r = ['ok' => false, 'error' => null, 'backup' => null, 'tables' => [], 'settings' => 0, 'cleared' => [], 'restored' => false];
    @set_time_limit(300);
    ignore_user_abort(true);

    try {
        $plan = site_sync_plan($sql);
        if ($takeBackup) $r['backup'] = site_sync_backup($pdo);
    } catch (Throwable $e) {
        $r['error'] = $e->getMessage();
        return $r;
    }

    $mode = $pdo->query('SELECT @@SESSION.sql_mode')->fetchColumn();
    $pdo->exec('SET NAMES utf8mb4');
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');   // also stops deletes here from emptying order lines' product links
    $pdo->exec("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO'");
    $side = fn($t) => "{$t}__sync";
    $dropSides = function () use ($pdo, $plan, $side) {
        foreach (array_keys($plan['tables']) as $t) {
            try { $pdo->exec('DROP TABLE IF EXISTS `' . $side($t) . '`'); } catch (Throwable $e) {}
        }
    };
    $finish = function () use ($pdo, $mode, $dropSides) {
        $dropSides();
        $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        $pdo->prepare('SET SQL_MODE = ?')->execute([$mode]);
    };

    // 1. Load everything into side copies. Foreign keys stay off the copies: their names must be unique.
    try {
        foreach ($plan['tables'] as $t => $p) {
            $pdo->exec('DROP TABLE IF EXISTS `' . $side($t) . '`');
            $create = preg_replace('/^CREATE TABLE `\w+`/', 'CREATE TABLE `' . $side($t) . '`', $p['create'], 1);
            $pdo->exec(preg_replace('/,\s*CONSTRAINT `[^`]+` FOREIGN KEY [^\n]*/', '', $create));
            foreach ($p['inserts'] as $ins) {
                $pdo->exec(preg_replace('/^INSERT INTO `\w+`/', 'INSERT INTO `' . $side($t) . '`', $ins, 1));
            }
            $n = site_sync_count($pdo, $side($t));
            if (isset($plan['expected'][$t]) && $plan['expected'][$t] !== $n) {
                throw new RuntimeException(SITE_SYNC_TABLES[$t] . ": the file lists {$plan['expected'][$t]} rows but $n loaded.");
            }
            $r['tables'][$t] = [site_sync_count($pdo, $t), $n, false];
        }
    } catch (Throwable $e) {
        $finish();
        $r['error'] = 'The update could not be read into the database, so nothing was changed. ' . $e->getMessage();
        $r['tables'] = [];
        return $r;
    }

    // 2. Switch over. A table whose design has changed (or is new here) is rebuilt; the rest are refilled
    //    in one transaction together with the settings.
    $changed = false;
    try {
        foreach (array_keys($plan['tables']) as $t) {
            if (site_sync_shape($pdo, $t) === site_sync_shape($pdo, $side($t))) continue;
            $changed = true;
            $r['tables'][$t][2] = true;
            $pdo->exec("DROP TABLE IF EXISTS `$t`");
            $pdo->exec($plan['tables'][$t]['create']);
            $pdo->exec("INSERT INTO `$t` SELECT * FROM `" . $side($t) . '`');
        }
        $pdo->beginTransaction();
        foreach (array_keys($plan['tables']) as $t) {
            if ($r['tables'][$t][2]) continue;
            $pdo->exec("DELETE FROM `$t`");
            $pdo->exec("INSERT INTO `$t` SELECT * FROM `" . $side($t) . '`');
        }
        foreach ($plan['settings'] as $st) $pdo->exec($st);
        $clear = $pdo->prepare("UPDATE settings SET setting_value = '' WHERE setting_key = ?");
        foreach ($plan['clear'] as $k) {
            $clear->execute([$k]);
            if ($clear->rowCount()) $r['cleared'][] = $k;
        }
        $pdo->commit();
        $r['settings'] = count($plan['settings']);
        $r['ok'] = true;
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        $r['error'] = 'The update stopped part way: ' . $e->getMessage();
        if ($changed && $r['backup'] && ($path = site_sync_backup_path($r['backup']))) {
            $finish();
            $back = site_sync_apply($pdo, (string) file_get_contents($path), false);
            $r['restored'] = $back['ok'];
            $r['error'] .= $back['ok'] ? ' The content as it was before has been put back.' : ' Putting the backup back also failed: ' . $back['error'];
            return $r;
        }
        $r['error'] .= ' Nothing was changed.';
    }
    $finish();
    return $r;
}
