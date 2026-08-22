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

function paginate(int $total, int $page, int $perPage): array
{
    $pages = max(1, (int) ceil($total / $perPage));
    $page = max(1, min($page, $pages));
    return ['page' => $page, 'pages' => $pages, 'offset' => ($page - 1) * $perPage];
}
