<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Contact Us';
$activePage = 'contact';
$pageDescription = 'Talk to BetterLife International about supporting a programme, working with us, visiting the farm, buying our products or learning more about what we do.';
$prefillSubject = $_GET['subject'] ?? '';
$flash = flash_get();

$subjectOptions = [
    'Partnership enquiry',
    'Programme enquiry',
    'Media and speaking',
    'Volunteer enquiry',
    'Farm visit',
    'Product order',
    'General enquiry',
];

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span>Contact</div>
    <h1>Partnerships should start with a real conversation.</h1>
    <p style="max-width:600px;color:#e2f0e9;">Talk to Us.</p>
  </div>
</section>

<section>
  <div class="container">
    <div class="split" style="align-items:flex-start;">
      <div class="section-dark card fade-up" style="padding:44px;">
        <h3 style="color:#fff;">Talk to Us</h3>
        <p style="color:#cfe3d8;margin-bottom:30px;">Whether you want to support a programme, work with BetterLife, visit the farm, purchase our products or learn more about what we do, we would be glad to hear from you.</p>

        <div class="contact-info-item">
          <div class="icon"><?= icon('mail', 20) ?></div>
          <div>
            <h4>General Enquiries</h4>
            <p>Email: <a href="mailto:<?= h(setting($pdo, 'email')) ?>" style="color:#eaf3ee;"><?= h(setting($pdo, 'email')) ?></a><br>
            Telephone: <a href="tel:<?= h(preg_replace('/\s+/', '', setting($pdo, 'phone'))) ?>" style="color:#eaf3ee;"><?= h(setting($pdo, 'phone')) ?></a></p>
          </div>
        </div>
        <div class="contact-info-item">
          <div class="icon"><?= icon('map-pin', 20) ?></div>
          <div><h4>Uganda</h4><p>Rukungiri, Uganda<br>West Nile programme hub: Yumbe and Bidi Bidi</p></div>
        </div>
        <div class="contact-info-item">
          <div class="icon"><?= icon('map-pin', 20) ?></div>
          <div><h4>South Sudan</h4><p>Juba office<br>Yambio field operations</p></div>
        </div>
        <div class="contact-info-item">
          <div class="icon"><?= icon('map-pin', 20) ?></div>
          <div><h4>Tanzania</h4><p>Kayanga Town, Karagwe District</p></div>
        </div>

        <div class="footer-social" style="margin-top:20px;">
          <?php if ($fb = setting($pdo, 'facebook')): ?><a href="<?= h($fb) ?>" target="_blank" rel="noopener" aria-label="Facebook"><?= icon('facebook', 16) ?></a><?php endif; ?>
          <?php if ($tw = setting($pdo, 'twitter')): ?><a href="<?= h($tw) ?>" target="_blank" rel="noopener" aria-label="Twitter / X"><?= icon('x-twitter', 16) ?></a><?php endif; ?>
          <?php if ($ig = setting($pdo, 'instagram')): ?><a href="<?= h($ig) ?>" target="_blank" rel="noopener" aria-label="Instagram"><?= icon('instagram', 16) ?></a><?php endif; ?>
          <?php if ($li = setting($pdo, 'linkedin')): ?><a href="<?= h($li) ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><?= icon('linkedin', 16) ?></a><?php endif; ?>
        </div>
      </div>

      <div class="contact-card fade-up">
        <h3>Send Us a Message</h3>
        <?php if ($flash): ?>
          <div class="alert alert-<?= h($flash['type']) ?>"><?= h($flash['message']) ?></div>
        <?php endif; ?>
        <form method="post" action="<?= SITE_URL ?>/contact-submit.php">
          <?= csrf_field() ?>
          <div class="grid grid-2" style="gap:18px;">
            <div class="form-group"><label>Full Name *</label><input type="text" name="name" class="form-control" required></div>
            <div class="form-group"><label>Email Address *</label><input type="email" name="email" class="form-control" required></div>
          </div>
          <div class="grid grid-2" style="gap:18px;">
            <div class="form-group"><label>Phone Number</label><input type="text" name="phone" class="form-control"></div>
            <div class="form-group">
              <label>Subject</label>
              <select name="subject" class="form-control">
                <?php foreach ($subjectOptions as $opt): ?>
                  <option value="<?= h($opt) ?>" <?= strcasecmp($opt, $prefillSubject) === 0 ? 'selected' : '' ?>><?= h($opt) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>
          <div class="form-group"><label>Your Message *</label><textarea name="message" class="form-control" required></textarea></div>
          <button type="submit" class="btn btn-primary btn-block">Send Message</button>
        </form>
      </div>
    </div>
  </div>
</section>

<?php if ($map = setting($pdo, 'map_embed')): ?>
<section class="section-cream" style="padding-top:0;">
  <div class="container"><div class="fade-up" style="border-radius:var(--radius-lg);overflow:hidden;box-shadow:var(--shadow);"><?= $map ?></div></div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
