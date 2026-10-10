<?php
require_once __DIR__ . '/includes/auth.php';
$activeNav = 'content';

/**
 * Registry of every admin-editable content section. Each entry describes
 * a repeatable list of cards/rows that a front-end page renders via
 * content_items($pdo, page, section). "fields" controls which columns
 * the add/edit form shows (and their labels) for that section.
 */
$SECTIONS = [
    'home:hero_gallery' => ['page' => 'home', 'section' => 'hero_gallery', 'group' => 'Homepage', 'label' => 'Hero Africa Photo Gallery', 'page_url' => 'index.php',
        'fields' => ['title' => 'Caption (used as photo alt text)', 'image' => 'Photo']],
    'about:how_we_work' => ['page' => 'about', 'section' => 'how_we_work', 'group' => 'About Page', 'label' => 'How We Work', 'page_url' => 'about.php',
        'fields' => ['title' => 'Heading', 'body' => 'Text']],
    'about:who_we_work_with' => ['page' => 'about', 'section' => 'who_we_work_with', 'group' => 'About Page', 'label' => 'Who We Work With', 'page_url' => 'about.php',
        'fields' => ['title' => 'Heading', 'body' => 'Text']],
    'about:who_we_work_with_gallery' => ['page' => 'about', 'section' => 'who_we_work_with_gallery', 'group' => 'About Page', 'label' => 'Who We Work With — Photo Strip', 'page_url' => 'about.php',
        'fields' => ['title' => 'Caption', 'image' => 'Photo', 'extra' => 'Alt Text (for accessibility)']],
    'about:where_we_work' => ['page' => 'about', 'section' => 'where_we_work', 'group' => 'About Page', 'label' => 'Where We Work', 'page_url' => 'about.php',
        'fields' => ['title' => 'Country', 'body' => 'Text']],
    'about:where_we_work_gallery' => ['page' => 'about', 'section' => 'where_we_work_gallery', 'group' => 'About Page', 'label' => 'Where We Work — Photo Strip', 'page_url' => 'about.php',
        'fields' => ['title' => 'Caption', 'image' => 'Photo', 'extra' => 'Alt Text (for accessibility)']],
    'about:journey' => ['page' => 'about', 'section' => 'journey', 'group' => 'About Page', 'label' => 'Our Journey (timeline)', 'page_url' => 'about.php',
        'fields' => ['title' => 'Year', 'body' => 'What happened']],
    'about:guides' => ['page' => 'about', 'section' => 'guides', 'group' => 'About Page', 'label' => 'What Guides Us (values)', 'page_url' => 'about.php',
        'fields' => ['title' => 'Value', 'body' => 'Text']],

    'programs:climate-resilient-agriculture' => ['page' => 'programs', 'section' => 'climate-resilient-agriculture', 'group' => 'Programme & Project Pages', 'label' => 'Climate-Resilient Agriculture', 'page_url' => 'program.php?slug=climate-resilient-agriculture',
        'fields' => ['title' => 'Title (project pages find this text by its title, so keep it unchanged)', 'subtitle' => 'Note / kicker (optional)', 'body' => 'Paragraphs (leave a blank line between paragraphs)', 'cta_label' => 'Button label (not used on the new pages)', 'cta_href' => 'Button link (not used on the new pages)']],
    'programs:green-skills-livelihoods' => ['page' => 'programs', 'section' => 'green-skills-livelihoods', 'group' => 'Programme & Project Pages', 'label' => 'RISE: Inclusive Skills & Enterprise', 'page_url' => 'program.php?slug=green-skills-livelihoods',
        'fields' => ['title' => 'Title (project pages find this text by its title, so keep it unchanged)', 'subtitle' => 'Note / kicker (optional)', 'body' => 'Paragraphs (leave a blank line between paragraphs)', 'cta_label' => 'Button label (not used on the new pages)', 'cta_href' => 'Button link (not used on the new pages)']],
    'programs:climate-education-youth-leadership' => ['page' => 'programs', 'section' => 'climate-education-youth-leadership', 'group' => 'Programme & Project Pages', 'label' => 'Climate Education & Youth Leadership', 'page_url' => 'program.php?slug=climate-education-youth-leadership',
        'fields' => ['title' => 'Title (project pages find this text by its title, so keep it unchanged)', 'subtitle' => 'Note / kicker (optional)', 'body' => 'Paragraphs (leave a blank line between paragraphs)', 'cta_label' => 'Button label (not used on the new pages)', 'cta_href' => 'Button link (not used on the new pages)']],
    'programs:clean-energy-water-restoration' => ['page' => 'programs', 'section' => 'clean-energy-water-restoration', 'group' => 'Programme & Project Pages', 'label' => 'Clean Energy, Water & Restoration', 'page_url' => 'program.php?slug=clean-energy-water-restoration',
        'fields' => ['title' => 'Title (project pages find this text by its title, so keep it unchanged)', 'subtitle' => 'Note / kicker (optional)', 'body' => 'Paragraphs (leave a blank line between paragraphs)', 'cta_label' => 'Button label (not used on the new pages)', 'cta_href' => 'Button link (not used on the new pages)']],
    'programs:digital-innovation' => ['page' => 'programs', 'section' => 'digital-innovation', 'group' => 'Programme & Project Pages', 'label' => 'Digital Innovation', 'page_url' => 'program.php?slug=digital-innovation',
        'fields' => ['title' => 'Title (project pages find this text by its title, so keep it unchanged)', 'subtitle' => 'Note / kicker (optional)', 'body' => 'Paragraphs (leave a blank line between paragraphs)', 'cta_label' => 'Button label (not used on the new pages)', 'cta_href' => 'Button link (not used on the new pages)']],
    'programs:our-projects' => ['page' => 'programs', 'section' => 'our-projects', 'group' => 'Programme & Project Pages', 'label' => 'Strengthening the Organisation', 'page_url' => 'partners.php#organisation',
        'fields' => ['title' => 'Title (project pages find this text by its title, so keep it unchanged)', 'subtitle' => 'Note / kicker (optional)', 'body' => 'Paragraphs (leave a blank line between paragraphs)', 'cta_label' => 'Button label (not used on the new pages)', 'cta_href' => 'Button link (not used on the new pages)']],

    'farm:on_the_farm' => ['page' => 'farm', 'section' => 'on_the_farm', 'group' => 'Farm Page', 'label' => 'On The Farm', 'page_url' => 'farm.php',
        'fields' => ['title' => 'Heading', 'body' => 'Text']],

    'products:farm_gallery' => ['page' => 'products', 'section' => 'farm_gallery', 'group' => 'Products Page', 'label' => 'Farm Gallery (photos)', 'page_url' => 'products.php',
        'fields' => ['title' => 'Caption', 'image' => 'Photo']],

    'privacy:sections' => ['page' => 'privacy', 'section' => 'sections', 'group' => 'Policies', 'label' => 'Privacy Notice', 'page_url' => 'privacy.php',
        'fields' => ['title' => 'Heading', 'body' => 'Text (a blank line starts a new paragraph; start lines with "- " for a list; {email}, {phone} and {postal} are filled in from Site Settings; {concern} links to the contact form)']],
    'safeguarding:sections' => ['page' => 'safeguarding', 'section' => 'sections', 'group' => 'Policies', 'label' => 'Safeguarding', 'page_url' => 'safeguarding.php',
        'fields' => ['subtitle' => 'Label above the heading (optional, e.g. Annex I)', 'title' => 'Heading', 'body' => 'Text (a blank line starts a new paragraph; "## " starts a sub-heading; start lines with "- " for a list, or "a. " / "I. " for a lettered or numbered list; **bold**; {email}, {phone} and {postal} are filled in from Site Settings; {concern} links to the contact form)']],
];

$sectionKeyParam = $_GET['s'] ?? array_key_first($SECTIONS);
if (!isset($SECTIONS[$sectionKeyParam])) $sectionKeyParam = array_key_first($SECTIONS);
$current = $SECTIONS[$sectionKeyParam];

$action = $_GET['action'] ?? 'list';
$id = (int) ($_GET['id'] ?? 0);

/* ---------------- Handle POST (create / update / delete) ---------------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify()) {
        flash_set('error', 'Session expired, please try again.');
        redirect(ADMIN_URL . '/content.php?s=' . urlencode($sectionKeyParam));
    }
    $postAction = $_POST['action'] ?? '';
    $postSectionKey = $_POST['section_key'] ?? $sectionKeyParam;
    if (!isset($SECTIONS[$postSectionKey])) { flash_set('error', 'Unknown section.'); redirect(ADMIN_URL . '/content.php'); }
    $sec = $SECTIONS[$postSectionKey];

    if ($postAction === 'delete') {
        $delId = (int) $_POST['id'];
        $img = $pdo->prepare("SELECT image FROM content_items WHERE id = ?");
        $img->execute([$delId]);
        if ($path = $img->fetchColumn()) {
            $full = __DIR__ . '/../' . $path;
            if (is_file($full)) @unlink($full);
        }
        $pdo->prepare("DELETE FROM content_items WHERE id = ?")->execute([$delId]);
        flash_set('success', 'Item deleted.');
        redirect(ADMIN_URL . '/content.php?s=' . urlencode($postSectionKey));
    }

    $title    = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $body     = trim($_POST['body'] ?? '');
    $ctaLabel = trim($_POST['cta_label'] ?? '');
    $ctaHref  = trim($_POST['cta_href'] ?? '');
    $extra    = trim($_POST['extra'] ?? '');
    $status   = isset($_POST['status']) ? 1 : 0;
    $sort     = (int) ($_POST['sort_order'] ?? 0);
    $editId   = (int) ($_POST['id'] ?? 0);

    if ($title === '') {
        flash_set('error', 'The first field is required.');
        redirect(ADMIN_URL . '/content.php?s=' . urlencode($postSectionKey) . '&action=' . ($editId ? 'edit&id=' . $editId : 'add'));
    }

    $errors = [];
    $image = handle_image_upload($_FILES['image'] ?? [], 'content', $errors);

    if ($editId) {
        if ($image) {
            $pdo->prepare("UPDATE content_items SET title=?, subtitle=?, body=?, image=?, cta_label=?, cta_href=?, extra=?, status=?, sort_order=? WHERE id=?")
                ->execute([$title, $subtitle, $body, $image, $ctaLabel ?: null, $ctaHref ?: null, $extra ?: null, $status, $sort, $editId]);
        } else {
            $pdo->prepare("UPDATE content_items SET title=?, subtitle=?, body=?, cta_label=?, cta_href=?, extra=?, status=?, sort_order=? WHERE id=?")
                ->execute([$title, $subtitle, $body, $ctaLabel ?: null, $ctaHref ?: null, $extra ?: null, $status, $sort, $editId]);
        }
        flash_set('success', 'Item updated successfully.');
    } else {
        $pdo->prepare("INSERT INTO content_items (page, section_key, title, subtitle, body, image, cta_label, cta_href, extra, sort_order, status) VALUES (?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([$sec['page'], $sec['section'], $title, $subtitle, $body, $image, $ctaLabel ?: null, $ctaHref ?: null, $extra ?: null, $sort, $status]);
        flash_set('success', 'Item added successfully.');
    }
    redirect(ADMIN_URL . '/content.php?s=' . urlencode($postSectionKey));
}

/* ---------------- Data for views ---------------- */
$editing = null;
if (in_array($action, ['add', 'edit'], true)) {
    if ($action === 'edit') {
        $stmt = $pdo->prepare("SELECT * FROM content_items WHERE id = ? AND page = ? AND section_key = ?");
        $stmt->execute([$id, $current['page'], $current['section']]);
        $editing = $stmt->fetch();
        if (!$editing) { flash_set('error', 'Item not found.'); redirect(ADMIN_URL . '/content.php?s=' . urlencode($sectionKeyParam)); }
    }
    $pageTitle = ($action === 'edit' ? 'Edit: ' : 'Add: ') . $current['label'];
    require __DIR__ . '/includes/header.php';
    ?>
    <div class="panel">
      <div class="panel-head"><h3><?= h($pageTitle) ?></h3><a href="<?= ADMIN_URL ?>/content.php?s=<?= urlencode($sectionKeyParam) ?>" class="btn btn-outline btn-sm">← Back to List</a></div>
      <div class="panel-body">
        <form method="post" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= h($editing['id'] ?? '') ?>">
          <input type="hidden" name="section_key" value="<?= h($sectionKeyParam) ?>">
          <div class="form-grid">
            <?php foreach ($current['fields'] as $field => $label): ?>
              <?php if ($field === 'body'): ?>
                <div class="form-group full"><label><?= h($label) ?></label><textarea name="body" class="form-control" style="min-height:160px;"><?= h($editing['body'] ?? '') ?></textarea></div>
              <?php elseif ($field === 'image'): ?>
                <div class="form-group">
                  <label><?= h($label) ?></label>
                  <?php if (!empty($editing['image'])): ?><img src="<?= asset_url($editing['image']) ?>" class="current-image"><br><?php endif; ?>
                  <input type="file" name="image" class="form-control" accept="image/*">
                </div>
              <?php elseif (in_array($field, ['title', 'subtitle', 'cta_label', 'cta_href', 'extra'], true)): ?>
                <div class="form-group<?= $field === 'subtitle' ? ' full' : '' ?>"><label><?= h($label) ?></label><input type="text" name="<?= $field ?>" class="form-control" value="<?= h($editing[$field] ?? '') ?>"></div>
              <?php endif; ?>
            <?php endforeach; ?>
            <div class="form-group">
              <label>Sort Order</label>
              <input type="number" name="sort_order" class="form-control" value="<?= h($editing['sort_order'] ?? (string) ((int) ($_GET['next_order'] ?? 0))) ?>">
              <p class="hint">Lower numbers show first.</p>
              <div class="checkbox-row" style="margin-top:14px;">
                <input type="checkbox" name="status" id="status" <?= (!isset($editing) || $editing['status']) ? 'checked' : '' ?>>
                <label for="status" style="margin:0;">Visible on website</label>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary ico-text"><?= icon('save', 16) ?> Save</button>
        </form>
      </div>
    </div>
    <?php
    require __DIR__ . '/includes/footer.php';
    exit;
}

/* ---------------- List view ---------------- */
$pageTitle = 'Page Content';
$items = content_items($pdo, $current['page'], $current['section']);
// content_items() only returns visible rows; the admin list must also show hidden ones.
$stmt = $pdo->prepare("SELECT * FROM content_items WHERE page = ? AND section_key = ? ORDER BY sort_order, id");
$stmt->execute([$current['page'], $current['section']]);
$items = $stmt->fetchAll();
$nextOrder = $items ? ((int) end($items)['sort_order'] + 10) : 0;

// Group the section picker by page for the dropdown.
$grouped = [];
foreach ($SECTIONS as $key => $s) $grouped[$s['group']][$key] = $s['label'];

require __DIR__ . '/includes/header.php';
?>
<div class="panel">
  <div class="panel-head" style="flex-wrap:wrap;gap:12px;">
    <h3>Page Content</h3>
    <form method="get" style="display:flex;gap:10px;align-items:center;">
      <select name="s" class="form-control" onchange="this.form.submit()" style="min-width:280px;">
        <?php foreach ($grouped as $groupLabel => $opts): ?>
          <optgroup label="<?= h($groupLabel) ?>">
            <?php foreach ($opts as $key => $label): ?>
              <option value="<?= h($key) ?>" <?= $key === $sectionKeyParam ? 'selected' : '' ?>><?= h($label) ?></option>
            <?php endforeach; ?>
          </optgroup>
        <?php endforeach; ?>
      </select>
    </form>
  </div>
  <div class="panel-body">
    <p class="help-text" style="margin-bottom:16px;">
      Shows on <a href="<?= SITE_URL ?>/<?= h($current['page_url']) ?>" target="_blank"><?= h($current['page_url']) ?></a> &middot;
      add, edit, reorder or hide cards below &mdash; changes go live immediately.
    </p>
    <a href="<?= ADMIN_URL ?>/content.php?s=<?= urlencode($sectionKeyParam) ?>&action=add&next_order=<?= $nextOrder ?>" class="btn btn-primary btn-sm" style="margin-bottom:16px;display:inline-block;">+ Add Item</a>
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th></th><th>Title</th><th>Preview</th><th>Order</th><th>Status</th><th></th></tr></thead>
        <tbody>
          <?php if (!$items): ?><tr class="empty-row"><td colspan="6">Nothing here yet. Add the first item.</td></tr><?php endif; ?>
          <?php foreach ($items as $it): ?>
            <tr>
              <td><?php if ($it['image']): ?><img src="<?= asset_url($it['image']) ?>" class="row-thumb"><?php endif; ?></td>
              <td><strong><?= h($it['title']) ?></strong><?php if ($it['subtitle']): ?><div class="muted" style="font-size:12px;"><?= h($it['subtitle']) ?></div><?php endif; ?></td>
              <td><?= h(excerpt((string) $it['body'], 70)) ?></td>
              <td><?= (int) $it['sort_order'] ?></td>
              <td><span class="badge <?= $it['status'] ? 'badge-green' : 'badge-gray' ?>"><?= $it['status'] ? 'Visible' : 'Hidden' ?></span></td>
              <td>
                <div class="row-actions">
                  <a href="<?= ADMIN_URL ?>/content.php?s=<?= urlencode($sectionKeyParam) ?>&action=edit&id=<?= $it['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                  <form method="post" style="display:inline;"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="section_key" value="<?= h($sectionKeyParam) ?>"><input type="hidden" name="id" value="<?= $it['id'] ?>"><button type="submit" class="btn btn-danger btn-sm" data-confirm="Delete this item?">Delete</button></form>
                </div>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
