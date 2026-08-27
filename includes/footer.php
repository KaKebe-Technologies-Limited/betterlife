<?php
$logo = setting($pdo, 'logo', 'assets/img/logo.png');

$footerPrograms = $pdo->query("SELECT title, slug FROM programs WHERE status = 1 ORDER BY sort_order LIMIT 5")->fetchAll();
?>
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <div class="footer-brand">
          <img src="<?= asset_url($logo) ?>" alt="<?= h(setting($pdo, 'site_name')) ?>">
          <span><?= h(setting($pdo, 'site_name')) ?></span>
        </div>
        <p style="font-size:14px;"><?= h(setting($pdo, 'footer_about')) ?></p>
        <div class="footer-social">
          <?php if ($fb = setting($pdo, 'facebook')): ?><a href="<?= h($fb) ?>" target="_blank" rel="noopener" aria-label="Facebook"><?= icon('facebook', 16) ?></a><?php endif; ?>
          <?php if ($tw = setting($pdo, 'twitter')): ?><a href="<?= h($tw) ?>" target="_blank" rel="noopener" aria-label="Twitter / X"><?= icon('x-twitter', 16) ?></a><?php endif; ?>
          <?php if ($ig = setting($pdo, 'instagram')): ?><a href="<?= h($ig) ?>" target="_blank" rel="noopener" aria-label="Instagram"><?= icon('instagram', 16) ?></a><?php endif; ?>
          <?php if ($yt = setting($pdo, 'youtube')): ?><a href="<?= h($yt) ?>" target="_blank" rel="noopener" aria-label="YouTube"><?= icon('youtube', 16) ?></a><?php endif; ?>
        </div>
      </div>

      <div>
        <h4>Quick Links</h4>
        <ul class="footer-links">
          <li><a href="<?= SITE_URL ?>/about.php">About Us</a></li>
          <li><a href="<?= SITE_URL ?>/programs.php">Our Work</a></li>
          <li><a href="<?= SITE_URL ?>/impact-reports.php">Impact &amp; Reports</a></li>
          <li><a href="<?= SITE_URL ?>/farm.php">BetterLife Farm</a></li>
          <li><a href="<?= SITE_URL ?>/team.php">Our Team</a></li>
          <li><a href="<?= SITE_URL ?>/blog.php">Stories</a></li>
          <li><a href="<?= SITE_URL ?>/contact.php">Contact</a></li>
        </ul>
      </div>

      <div>
        <h4>Get In Touch</h4>
        <ul class="footer-links">
          <li class="ico-text"><?= icon('map-pin', 15) ?> <?= h(setting($pdo, 'address')) ?></li>
          <li class="ico-text"><?= icon('mail', 15) ?> <a href="mailto:<?= h(setting($pdo, 'email')) ?>"><?= h(setting($pdo, 'email')) ?></a></li>
          <li class="ico-text"><?= icon('phone', 15) ?> <a href="tel:<?= h(setting($pdo, 'phone')) ?>"><?= h(setting($pdo, 'phone')) ?></a></li>
        </ul>
      </div>

      <div>
        <h4>Our Programs</h4>
        <ul class="footer-links">
          <?php foreach ($footerPrograms as $p): ?>
            <li><a href="<?= SITE_URL ?>/programs.php#<?= h($p['slug']) ?>"><?= h($p['title']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div>
        <h4>Stay Connected</h4>
        <p style="font-size:14px;">Get program updates, farm news and stories from the communities we work with.</p>
        <form class="footer-newsletter" action="<?= SITE_URL ?>/newsletter-submit.php" method="post">
          <input type="email" name="email" placeholder="Your email address" required>
          <button type="submit" aria-label="Subscribe"><?= icon('arrow-right', 16) ?></button>
        </form>
      </div>
    </div>

    <div class="footer-bottom">
      <span>&copy; <?= date('Y') ?> <?= h(setting($pdo, 'site_name')) ?>. All rights reserved.</span>
      <span>Built with purpose in Uganda &middot; <a href="<?= ADMIN_URL ?>/login.php">Admin</a></span>
    </div>
  </div>
</footer>

<a href="#top" class="back-to-top" aria-label="Back to top"><?= icon('arrow-right', 18) ?></a>
<script src="<?= SITE_URL ?>/assets/js/main.js?v=<?= @filemtime(__DIR__ . '/../assets/js/main.js') ?: time() ?>"></script>
</body>
</html>
