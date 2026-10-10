<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/donations.php';
require_once __DIR__ . '/includes/programmes.php';
$pageTitle = 'Donate';
$pageDescription = 'Give to BetterLife International by mobile money or card. Your gift supports our work with women, young people, refugees, displaced families and farming communities.';
$activePage = 'donate';

$canPay = donations_can_pay($pdo);
$presets = donation_presets();
$error = null;
$old = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $old = array_intersect_key($_POST, array_flip(['amount', 'amount_other', 'name', 'email', 'phone', 'message']));
    $choice = (string) ($_POST['amount'] ?? '');
    $amount = (int) preg_replace('/[^0-9]/', '', $choice === 'other' ? (string) ($_POST['amount_other'] ?? '') : $choice);
    $name = trim((string) ($_POST['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $message = mb_substr(trim((string) ($_POST['message'] ?? '')), 0, 1000);

    if (!csrf_verify()) {
        $error = 'Your session expired. Please try again.';
    } elseif (trim((string) ($_POST['website'] ?? '')) !== '') {
        $error = 'Something went wrong. Please try again.';   // the hidden field only automated forms fill in
    } elseif (!$canPay) {
        $error = 'Online giving is not open yet. Please write to us and we will tell you how to give.';
    } elseif ($amount < DONATION_MIN || $amount > DONATION_MAX) {
        $error = 'Please choose an amount of at least ' . format_price(DONATION_MIN) . '.';
    } elseif ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please give your name and a valid email address, so we can send your receipt.';
    } else {
        try {
            donations_ensure_table($pdo);
            $ref = donation_ref();
            $pdo->prepare('INSERT INTO donations (donation_ref, donor_name, donor_email, donor_phone, amount, currency, message) VALUES (?, ?, ?, ?, ?, ?, ?)')
                ->execute([$ref, mb_substr($name, 0, 150), mb_substr($email, 0, 150), mb_substr($phone, 0, 50), $amount, 'UGX', $message]);
            $gift = donation_find($pdo, $ref);
            redirect(donation_initiate_payment($pdo, $gift));
        } catch (Throwable $e) {
            error_log('Donation could not start: ' . $e->getMessage());
            if (!empty($ref)) $pdo->prepare("UPDATE donations SET status = 'cancelled' WHERE donation_ref = ? AND status = 'pending'")->execute([$ref]);
            $error = 'We could not open the payment page just now. Please try again in a moment, or write to us.';
        }
    }
}

$picked = (string) ($old['amount'] ?? '50000');
$usdRate = (float) setting($pdo, 'usd_rate', '0');
$regNo = setting($pdo, 'ngo_reg_no');
$areas = pp_areas();

$pageStyles  = ['assets/css/about.css', 'assets/css/shop.css', 'assets/css/donate.css'];
$pageScripts = ['assets/js/about.js', 'assets/js/donate.js'];
$pageHead = '<script>document.documentElement.classList.add("ab-js")</script>';
require __DIR__ . '/includes/header.php';
?>

<main class="ab sh dn" id="top">
  <section class="dn-hero" aria-labelledby="dnTitle">
    <div class="container dn-hero-grid">
      <div class="dn-hero-copy">
        <nav class="sh-crumb is-dark" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><span aria-current="page">Donate</span></nav>
        <p class="ab-eyebrow">Give</p>
        <h1 id="dnTitle">Support our work</h1>
        <p class="dn-lead">Your gift supports BetterLife’s work with women, young people, refugees, displaced families and farming communities across five African countries: food people can grow, skills they can earn from, cleaner energy and stronger routes to market.</p>
        <ul class="dn-trust">
          <?php if ($regNo): ?><li><?= icon('check', 16) ?> A registered NGO in Uganda, Reg. No. <?= h($regNo) ?></li><?php endif; ?>
          <?php if ($canPay): ?>
            <li><?= icon('check', 16) ?> Pay by mobile money or card, through Pesapal</li>
            <li><?= icon('check', 16) ?> A receipt by email for every gift</li>
          <?php endif; ?>
        </ul>
        <p class="dn-links">
          <a href="<?= SITE_URL ?>/impact-reports.php" class="ab-link">Read our annual reports <?= icon('arrow-right', 15) ?></a>
          <a href="<?= SITE_URL ?>/team.php#board" class="ab-link">Meet our board <?= icon('arrow-right', 15) ?></a>
        </p>
      </div>

      <div class="dn-card">
        <?php if ($canPay): ?>
          <form method="post" class="dn-form" data-donate data-usd-rate="<?= $usdRate > 0 ? h((string) $usdRate) : '' ?>" novalidate>
            <?= csrf_field() ?>
            <h2>Choose your gift</h2>
            <?php if ($error): ?><p class="sh-flash is-error" role="alert"><?= h($error) ?></p><?php endif; ?>
            <fieldset class="dn-amounts">
              <legend class="sr-only">Amount in Uganda shillings</legend>
              <?php foreach ($presets as $p): ?>
                <label class="dn-amount"><input type="radio" name="amount" value="<?= $p ?>" <?= $picked === (string) $p ? 'checked' : '' ?>><span><small>UGX</small> <?= number_format($p) ?></span></label>
              <?php endforeach; ?>
              <label class="dn-amount is-other"><input type="radio" name="amount" value="other" <?= $picked === 'other' ? 'checked' : '' ?>><span>Other amount</span></label>
            </fieldset>
            <div class="sh-field dn-other" data-other <?= $picked === 'other' ? '' : 'hidden' ?>>
              <label for="dnOther">Your amount in Uganda shillings</label>
              <input id="dnOther" type="text" name="amount_other" inputmode="numeric" autocomplete="off" placeholder="e.g. 75,000" value="<?= h($old['amount_other'] ?? '') ?>">
            </div>
            <p class="dn-usd" data-usd aria-live="polite"></p>

            <div class="sh-fields">
              <div class="sh-field"><label for="dnName">Your name <span aria-hidden="true">*</span></label><input id="dnName" type="text" name="name" required autocomplete="name" value="<?= h($old['name'] ?? '') ?>"></div>
              <div class="sh-field"><label for="dnEmail">Email address <span aria-hidden="true">*</span></label><input id="dnEmail" type="email" name="email" required autocomplete="email" value="<?= h($old['email'] ?? '') ?>"><small>For your receipt</small></div>
              <div class="sh-field is-full"><label for="dnPhone">Phone number <small>(optional, for mobile money)</small></label><input id="dnPhone" type="tel" name="phone" autocomplete="tel" value="<?= h($old['phone'] ?? '') ?>" placeholder="e.g. 0700 000 000"></div>
              <div class="sh-field is-full"><label for="dnMessage">A message for our team <small>(optional)</small></label><textarea id="dnMessage" name="message" rows="3" maxlength="1000"><?= h($old['message'] ?? '') ?></textarea></div>
            </div>
            <p class="dn-hp" aria-hidden="true"><label>Leave this empty <input type="text" name="website" tabindex="-1" autocomplete="off"></label></p>

            <button type="submit" class="sh-btn is-wide" data-give><?= icon('heart', 16) ?> <span>Give now</span></button>
            <p class="sh-form-note"><?= icon('check', 14) ?> You will pay on Pesapal’s secure page, by mobile money or card. Payment is in Uganda shillings.</p>
          </form>
        <?php else: ?>
          <div class="dn-soon">
            <h2>Give to BetterLife</h2>
            <p>Online giving is being set up. To give now, write to us and we will reply with how.</p>
            <a href="<?= SITE_URL ?>/contact.php?subject=<?= urlencode('Partnership enquiry') ?>" class="sh-btn is-wide"><?= icon('mail', 16) ?> Write to us about giving</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <section class="dn-areas" aria-labelledby="dnAreasTitle">
    <div class="container">
      <p class="ab-eyebrow">Where gifts go</p>
      <h2 id="dnAreasTitle">Five areas of work</h2>
      <ul class="dn-area-list">
        <?php foreach ($areas as $slug => $a): [$lead] = ab_split($a['card']); ?>
          <li class="dn-area">
            <a href="<?= h(pp_area_url($slug)) ?>">
              <span class="dn-area-media"><?= ab_img($a['image'][0], '', '', true, 'style="object-position: ' . h($a['image'][2] ?? '50% 50%') . '"', '(max-width: 720px) 100vw, 240px') ?></span>
              <span class="dn-area-body"><strong><?= h($a['short']) ?></strong><span><?= h($lead) ?></span></span>
            </a>
          </li>
        <?php endforeach; ?>
      </ul>
    </div>
  </section>

  <section class="dn-ways" aria-labelledby="dnWaysTitle">
    <div class="container">
      <h2 id="dnWaysTitle">Other ways to help</h2>
      <ul class="dn-way-list">
        <li><a href="<?= SITE_URL ?>/contact.php?subject=<?= urlencode('Partnership enquiry') ?>"><?= icon('heart', 20) ?><strong>Partner with us</strong><span>Funding, expertise or a joint project</span></a></li>
        <li><a href="<?= SITE_URL ?>/contact.php?subject=<?= urlencode('Volunteer enquiry') ?>"><?= icon('users', 20) ?><strong>Volunteer</strong><span>Give your time or your skills</span></a></li>
        <li><a href="<?= SITE_URL ?>/products.php"><?= icon('shopping-bag', 20) ?><strong>Shop the farm</strong><span>Honey, ghee, yoghurt and more</span></a></li>
        <li><a href="<?= SITE_URL ?>/contact.php?subject=<?= urlencode('General enquiry') ?>"><?= icon('mail', 20) ?><strong>Larger or regular gifts</strong><span>Write to us and we will talk it through</span></a></li>
      </ul>
    </div>
  </section>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
