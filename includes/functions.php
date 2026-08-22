<?php
require_once __DIR__ . '/db.php';

/* ---------------------------------------------------------------------
 * Settings (key/value store, loaded once per request)
 * ------------------------------------------------------------------- */
function settings_all(PDO $pdo): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach ($pdo->query('SELECT setting_key, setting_value FROM settings') as $row) {
            $cache[$row['setting_key']] = $row['setting_value'];
        }
    }
    return $cache;
}

function setting(PDO $pdo, string $key, string $default = ''): string
{
    $all = settings_all($pdo);
    return $all[$key] ?? $default;
}

/* ---------------------------------------------------------------------
 * General helpers
 * ------------------------------------------------------------------- */
function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function nl2p(?string $text): string
{
    $text = trim($text ?? '');
    if ($text === '') return '';
    $paragraphs = preg_split('/\n\s*\n/', $text);
    return implode('', array_map(fn($p) => '<p>' . nl2br(h(trim($p))) . '</p>', array_filter($paragraphs, fn($p) => trim($p) !== '')));
}

function slugify(string $text): string
{
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = trim($text, '-');
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text) ?: $text;
    $text = strtolower($text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    return $text !== '' ? $text : 'item';
}

function unique_slug(PDO $pdo, string $table, string $base, ?int $ignoreId = null): string
{
    $slug = slugify($base);
    $original = $slug;
    $i = 2;
    while (true) {
        $sql = "SELECT id FROM {$table} WHERE slug = ?" . ($ignoreId ? ' AND id != ?' : '');
        $stmt = $pdo->prepare($sql);
        $params = [$slug];
        if ($ignoreId) $params[] = $ignoreId;
        $stmt->execute($params);
        if (!$stmt->fetch()) return $slug;
        $slug = $original . '-' . $i;
        $i++;
    }
}

function excerpt(string $text, int $length = 160): string
{
    $text = trim(strip_tags($text));
    if (mb_strlen($text) <= $length) return $text;
    return mb_substr($text, 0, $length) . '…';
}

function time_ago(?string $datetime): string
{
    if (!$datetime) return '';
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'just now';
    $units = [31536000 => 'year', 2592000 => 'month', 604800 => 'week', 86400 => 'day', 3600 => 'hour', 60 => 'minute'];
    foreach ($units as $secs => $label) {
        $count = intdiv($diff, $secs);
        if ($count >= 1) return $count . ' ' . $label . ($count > 1 ? 's' : '') . ' ago';
    }
    return 'just now';
}

function format_date(?string $datetime, string $format = 'M j, Y'): string
{
    if (!$datetime) return '';
    return date($format, strtotime($datetime));
}

function format_price(?float $price): string
{
    if ($price === null) return '';
    return 'UGX ' . number_format($price, 0);
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

/* ---------------------------------------------------------------------
 * Flash messages (session based)
 * ------------------------------------------------------------------- */
function flash_set(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function flash_get(): ?array
{
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/* ---------------------------------------------------------------------
 * CSRF protection
 * ------------------------------------------------------------------- */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . h(csrf_token()) . '">';
}

function csrf_verify(): bool
{
    $token = $_POST['csrf_token'] ?? '';
    return !empty($token) && !empty($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/* ---------------------------------------------------------------------
 * Auth (admin)
 * ------------------------------------------------------------------- */
function is_logged_in(): bool
{
    return !empty($_SESSION['admin_id']);
}

function current_admin(): ?array
{
    return $_SESSION['admin'] ?? null;
}

/* ---------------------------------------------------------------------
 * Image upload helper. Returns relative path (e.g. uploads/products/x.jpg)
 * or null on no-file / failure. $errors is populated on failure.
 * ------------------------------------------------------------------- */
function handle_image_upload(array $file, string $subfolder, ?array &$errors = null): ?string
{
    $errors = [];
    if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return null; // no file chosen, not an error
    }
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Upload failed with error code ' . $file['error'];
        return null;
    }

    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!isset($allowed[$mime])) {
        $errors[] = 'Only JPG, PNG, WEBP or GIF images are allowed.';
        return null;
    }
    if ($file['size'] > 5 * 1024 * 1024) {
        $errors[] = 'Image must be smaller than 5MB.';
        return null;
    }

    $dir = __DIR__ . '/../uploads/' . $subfolder;
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $filename = date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $allowed[$mime];
    $destination = $dir . '/' . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        $errors[] = 'Could not save uploaded file.';
        return null;
    }

    return 'uploads/' . $subfolder . '/' . $filename;
}

function asset_url(?string $path): string
{
    if (!$path) return SITE_URL . '/assets/img/placeholder.jpg';
    if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) return $path;
    return SITE_URL . '/' . ltrim($path, '/');
}

/**
 * Public-facing base URL of the site (scheme + host, no path). Honors an
 * optional PESAPAL_NGROK_URL override defined in config/config.php so
 * local testing can receive Pesapal callbacks/IPN through an ngrok tunnel.
 */
function public_base_url(): string
{
    if (defined('PESAPAL_NGROK_URL') && PESAPAL_NGROK_URL !== '') {
        return rtrim(PESAPAL_NGROK_URL, '/');
    }
    $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    return $scheme . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
}

function full_asset_url(?string $path): string
{
    $url = asset_url($path);
    if (str_starts_with($url, 'http')) return $url;
    return public_base_url() . $url;
}

function paginate(int $total, int $page, int $perPage): array
{
    $pages = max(1, (int) ceil($total / $perPage));
    $page = max(1, min($page, $pages));
    return ['page' => $page, 'pages' => $pages, 'offset' => ($page - 1) * $perPage];
}

/* ---------------------------------------------------------------------
 * Inline SVG icon set (stroke-based, currentColor) — replaces emoji
 * throughout the site for a cleaner, more professional look.
 * ------------------------------------------------------------------- */
function icon(string $name, int $size = 20): string
{
    $paths = [
        'leaf'          => '<path d="M11 20A7 7 0 0 1 4 13c0-5 4-9 15-11 -2 11-6 15-11 15Z"/><path d="M4 13c3.5 3.5 7 4 11 3"/>',
        'globe'         => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a15 15 0 0 1 0 18 15 15 0 0 1 0-18Z"/>',
        'basket'        => '<path d="M4 9h16l-1.5 10.2a2 2 0 0 1-2 1.8H7.5a2 2 0 0 1-2-1.8L4 9Z"/><path d="M9 9 8 4M15 9l1-5M2 9h20M9 13v4M12 13v4M15 13v4"/>',
        'users'         => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>',
        'check'         => '<path d="M20 6 9 17l-5-5"/>',
        'mail'          => '<rect x="2" y="4" width="20" height="16" rx="2"/><path d="m2 7 10 6 10-6"/>',
        'phone'         => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.3 1.8.6 2.7a2 2 0 0 1-.4 2.1L8.1 9.7a16 16 0 0 0 6 6l1.2-1.2a2 2 0 0 1 2.1-.4c.9.3 1.8.5 2.7.6a2 2 0 0 1 1.9 2Z"/>',
        'map-pin'       => '<path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/>',
        'target'        => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="5"/><circle cx="12" cy="12" r="1.4"/>',
        'eye'           => '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3.2"/>',
        'user'          => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
        'calendar'      => '<rect x="3" y="4.5" width="18" height="16.5" rx="2"/><path d="M8 2.5v4M16 2.5v4M3 9.5h18"/>',
        'search'        => '<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3"/>',
        'arrow-right'   => '<path d="M5 12h14M13 6l6 6-6 6"/>',
        'chevron-down'  => '<path d="m6 9 6 6 6-6"/>',
        'menu'          => '<path d="M3 6h18M3 12h18M3 18h18"/>',
        'x'             => '<path d="M18 6 6 18M6 6l12 12"/>',
        'star'          => '<path d="M12 2.5 15 9l7 1-5 5 1.3 7-6.3-3.5L5.7 22 7 15 2 10l7-1 3-6.5Z"/>',
        'share'         => '<circle cx="18" cy="5" r="2.6"/><circle cx="6" cy="12" r="2.6"/><circle cx="18" cy="19" r="2.6"/><path d="m8.3 10.7 7.4-4.1M8.3 13.3l7.4 4.1"/>',
        'save'          => '<path d="M5 3h11l5 5v13a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1V4a1 1 0 0 1 1-1Z"/><path d="M8 3v6h8V3M8 21v-8h8v8"/>',
        'grid'          => '<rect x="3" y="3" width="8" height="8" rx="1.5"/><rect x="13" y="3" width="8" height="8" rx="1.5"/><rect x="3" y="13" width="8" height="8" rx="1.5"/><rect x="13" y="13" width="8" height="8" rx="1.5"/>',
        'box'           => '<path d="M21 8 12 3 3 8v8l9 5 9-5V8Z"/><path d="M3 8l9 5 9-5M12 13v8"/>',
        'message'       => '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v10Z"/>',
        'trending-up'   => '<path d="m3 17 6-6 4 4 8-8"/><path d="M15 7h6v6"/>',
        'newspaper'     => '<path d="M4 4h13a2 2 0 0 1 2 2v13a1 1 0 0 1-1.6.8L15 18H6a2 2 0 0 1-2-2V4Z"/><path d="M4 4v13a3 3 0 0 1-3 3M8 8h6M8 12h6M8 16h4"/>',
        'tag'           => '<path d="M12.6 2H4a2 2 0 0 0-2 2v8.6a2 2 0 0 0 .6 1.4l9 9a2 2 0 0 0 2.8 0l8-8a2 2 0 0 0 0-2.8l-9-9a2 2 0 0 0-1.4-.6Z"/><circle cx="7.5" cy="7.5" r="1.4"/>',
        'inbox'         => '<path d="M22 12h-6l-2 3h-4l-2-3H2"/><path d="M5.5 5h13l3.5 7v7a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-7l3.5-7Z"/>',
        'send'          => '<path d="m22 2-20 8 8 3.5L14 22l3-8 5-12Z"/>',
        'settings'      => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.2a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.2a1.7 1.7 0 0 0 1.5-1 1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.9.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.2a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.2a1.7 1.7 0 0 0-1.5 1Z"/>',
        'external-link' => '<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6M10 14 21 3"/>',
        'log-out'       => '<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5M21 12H9"/>',
        'shopping-bag'  => '<path d="M6 2 3 7v13a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V7l-3-5Z"/><path d="M3 7h18M16 11a4 4 0 0 1-8 0"/>',
        'clock'         => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.5 2"/>',
        'heart'         => '<path d="M12 21s-7-4.6-9.8-9A5.5 5.5 0 0 1 12 6a5.5 5.5 0 0 1 9.8 6c-2.8 4.4-9.8 9-9.8 9Z"/>',
        'award'         => '<circle cx="12" cy="8" r="6"/><path d="m9 13.5-1.5 7 4.5-2.5 4.5 2.5-1.5-7"/>',
        'file-text'     => '<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z"/><path d="M14 2v6h6M8 13h8M8 17h8M8 9h2"/>',
        'facebook'      => '<path d="M14 9V6.5A1.5 1.5 0 0 1 15.5 5H17V2h-2.5A4.5 4.5 0 0 0 10 6.5V9H7v3h3v10h4V12h2.6l.4-3H14Z"/>',
        'x-twitter'     => '<path d="m3 3 7.5 9.4L3.4 21H6l6-6.5 4.8 6.5H21l-8-9.9L20.2 3H17.6l-5.5 6L7.4 3H3Z"/>',
        'instagram'     => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.2" cy="6.8" r="1.1"/>',
        'linkedin'      => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M7.5 10v7M7.5 7v.01M11.5 17v-4a2.2 2.2 0 0 1 4.4 0v4M11.5 10v7"/>',
        'youtube'       => '<rect x="2" y="5" width="20" height="14" rx="4"/><path d="m10 9 6 3-6 3V9Z"/>',
        'whatsapp'      => '<path d="M20 12a8 8 0 1 1-14.8-4.2L4 20l4.4-1.2A8 8 0 0 1 20 12Z"/><path d="M9 10.5c.3 2.2 2.3 4.2 4.5 4.5"/>',
        'linkedin-alt'  => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M7.5 10v7M7.5 7v.01M11.5 17v-4a2.2 2.2 0 0 1 4.4 0v4M11.5 10v7"/>',
    ];
    $d = $paths[$name] ?? $paths['leaf'];
    return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $d . '</svg>';
}
