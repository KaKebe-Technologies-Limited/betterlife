<?php
$logo = setting($pdo, 'logo', 'assets/img/logo.png');
$legalName = setting($pdo, 'legal_name') ?: setting($pdo, 'site_name');
$regNo = setting($pdo, 'ngo_reg_no');
$permitNo = setting($pdo, 'ngo_permit_no');
$footerEmail = setting($pdo, 'email');
$footerPhone = setting($pdo, 'phone');
$footerPlace = setting($pdo, 'postal_address') ?: setting($pdo, 'address');
$footerLinks = [
    'about.php' => 'About us', 'programs.php' => 'Our work', 'impact-reports.php' => 'Impact & reports', 'partners.php' => 'Partners',
    'team.php' => 'Our team', 'blog.php' => 'Stories', 'farm.php' => 'BetterLife Farm', 'donate.php' => 'Donate',
];
?>
<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-about">
        <a href="<?= SITE_URL ?>/index.php" class="footer-brand"><img src="<?= asset_url($logo) ?>" alt="<?= h(setting($pdo, 'site_name')) ?>, home" loading="lazy"></a>
        <p><?= h(setting($pdo, 'footer_about')) ?></p>
        <div class="footer-social">
          <?php foreach (social_links($pdo) as [$sIcon, $sNetwork, $sHandle, $sUrl]): ?><a href="<?= h($sUrl) ?>" target="_blank" rel="noopener" aria-label="BetterLife on <?= h($sNetwork) ?> (opens in a new tab)"><?= icon($sIcon, 16) ?></a><?php endforeach; ?>
        </div>
      </div>

      <nav class="footer-explore" aria-label="Footer">
        <h2 class="footer-title">Explore</h2>
        <ul class="footer-links">
          <?php foreach ($footerLinks as $href => $label): ?><li><a href="<?= SITE_URL ?>/<?= $href ?>"><?= h($label) ?></a></li><?php endforeach; ?>
        </ul>
      </nav>

      <div class="footer-contact">
        <h2 class="footer-title">Contact</h2>
        <ul class="footer-links">
          <?php if ($footerEmail): ?><li><a href="mailto:<?= h($footerEmail) ?>"><?= icon('mail', 15) ?> <?= h($footerEmail) ?></a></li><?php endif; ?>
          <?php if ($footerPhone): ?><li><a href="tel:<?= h(preg_replace('/[^0-9+]/', '', $footerPhone)) ?>"><?= icon('phone', 15) ?> <?= h($footerPhone) ?></a></li><?php endif; ?>
          <?php if ($footerPlace): ?><li class="footer-place"><?= icon('map-pin', 15) ?> <span><?= h($footerPlace) ?></span></li><?php endif; ?>
        </ul>
      </div>

      <div class="footer-news">
        <h2 class="footer-title">Newsletter</h2>
        <p>Programme updates, farm news and stories from the communities we work with.</p>
        <form class="footer-newsletter" action="<?= SITE_URL ?>/newsletter-submit.php" method="post">
          <label for="footerEmail" class="sr-only">Your email address</label>
          <input type="email" id="footerEmail" name="email" placeholder="Your email address" autocomplete="email" required>
          <button type="submit" aria-label="Subscribe"><?= icon('arrow-right', 16) ?></button>
        </form>
      </div>
    </div>

    <div class="footer-bottom">
      <span class="footer-small-links">&copy; <?= date('Y') ?> <?= h($legalName) ?> <a href="<?= SITE_URL ?>/privacy.php">Privacy</a> <a href="<?= SITE_URL ?>/safeguarding.php">Safeguarding</a></span>
      <?php if ($regNo): // Legal standing, as on the certificate and permit from the National Bureau for NGOs ?>
        <span class="footer-legal"><span><?= icon('check', 13) ?> Registered NGO in Uganda</span><span>Reg. No. <?= h($regNo) ?></span><?php if ($permitNo): ?><span>Permit No. <?= h($permitNo) ?></span><?php endif; ?></span>
      <?php endif; ?>
    </div>
  </div>
</footer>

<a href="#top" class="back-to-top" aria-label="Back to top"><?= icon('arrow-right', 18) ?></a>
<script src="<?= SITE_URL ?>/assets/js/main.js?v=<?= @filemtime(__DIR__ . '/../assets/js/main.js') ?: time() ?>"></script>
<?php foreach ($pageScripts ?? [] as $js): // optional per-page scripts (paths under the site root) ?>
<script src="<?= SITE_URL . '/' . $js ?>?v=<?= @filemtime(__DIR__ . '/../' . $js) ?: time() ?>"></script>
<?php endforeach; ?>
</body>
</html>
