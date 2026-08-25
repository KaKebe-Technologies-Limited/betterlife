<?php
require_once __DIR__ . '/includes/functions.php';
$pageTitle = 'Contact Us';
$activePage = 'contact';
$pageDescription = 'Get in touch with BetterLife International — questions about our programs, partnership enquiries, or orders from BetterLife Farm.';
$prefillSubject = $_GET['subject'] ?? '';
$flash = flash_get();

require __DIR__ . '/includes/header.php';
?>

<section class="page-header">
  <div class="container">
    <div class="crumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span>/</span>Contact</div>
    <h1>Let's Make Things Happen</h1>
    <p style="max-width:600px;color:#e2f0e9;">Have a question about our programs, want to partner with us, or want to order from BetterLife Farm? Reach out.</p>
  </div>
</section>

<section>
  <div class="container">
    <div class="split" style="align-items:flex-start;">
      <div class="section-dark card fade-up" style="padding:44px;">
        <h3 style="color:#fff;">Contact Information</h3>
        <p style="color:#cfe3d8;margin-bottom:30px;">Reach out through any of the channels below — our team typically responds within 1–2 business days.</p>
        <div class="contact-info-item">
          <div class="icon"><?= icon('map-pin', 20) ?></div>
          <div><h4>Our Address</h4><p><?= h(setting($pdo, 'address')) ?></p></div>
        </div>
        <div class="contact-info-item">
          <div class="icon"><?= icon('mail', 20) ?></div>
          <div><h4>Email Us</h4><p><?= h(setting($pdo, 'email')) ?><br><?= h(setting($pdo, 'shop_email')) ?> (farm orders)</p></div>
        </div>
        <div class="contact-info-item">
          <div class="icon"><?= icon('phone', 20) ?></div>
          <div><h4>Call Us</h4><p><?= h(setting($pdo, 'phone')) ?></p></div>
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
            <div class="form-group"><label>Subject</label><input type="text" name="subject" class="form-control" value="<?= h($prefillSubject) ?>"></div>
          </div>
          <div class="form-group"><label>Your Message *</label><textarea name="message" class="form-control" required></textarea></div>
          <button type="submit" class="btn btn-primary btn-block">Send Message →</button>
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
