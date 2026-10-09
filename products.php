<?php
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/shop.php';
$pageTitle = 'Farm Shop';
$activePage = 'products';
$pageDescription = 'Honey, ghee, yoghurt and Organic Boost from BetterLife Agro Tourism Farm in Rukungiri. The farm buys surplus from community farmers, adds value and sells it, so every purchase helps keep that market open for refugee, displaced and host-community families.';

// Categories are links (they work without JavaScript); shop.js filters in place
$products = $pdo->query("SELECT * FROM products WHERE status = 1 ORDER BY sort_order, id")->fetchAll();
$categories = array_values(array_unique(array_column($products, 'category')));
$category = in_array($_GET['category'] ?? '', $categories, true) ? $_GET['category'] : '';
$byCategory = array_count_values(array_column($products, 'category'));

// The opening shelf: one of each kind
$shelf = [];
foreach ($products as $p) if (!isset($shelf[$p['category']])) $shelf[$p['category']] = $p;
$shelf = array_slice(array_values($shelf), 0, 4);

$flash = flash_get();
$phone = setting($pdo, 'phone');
$shopEmail = setting($pdo, 'shop_email') ?: setting($pdo, 'email');

$pageStyles  = ['assets/css/about.css', 'assets/css/shop.css'];
$pageScripts = ['assets/js/about.js', 'assets/js/shop.js'];
$pageHead = '<script>document.documentElement.classList.add("ab-js")</script>';

require __DIR__ . '/includes/header.php';
?>

<main class="ab sh" id="top">
  <?= ab_brush_defs() ?>

  <!-- Opening: what the shop is for, beside the products themselves -->
  <section class="sh-hero" aria-labelledby="shTitle">
    <div class="container sh-hero-grid">
      <div class="sh-hero-copy">
        <nav class="sh-crumb" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><a href="<?= SITE_URL ?>/farm.php">BetterLife Farm</a><span aria-hidden="true">/</span><span aria-current="page">Farm shop</span></nav>
        <p class="sh-kicker">The BetterLife Farm shop</p>
        <h1 id="shTitle">Good food, with a <?= ab_mark('purpose', 41) ?></h1>
        <p class="sh-lead">Honey, ghee, yoghurt and organic fertiliser from BetterLife Agro Tourism Farm in Rukungiri. The farm buys surplus from community farmers, adds value and sells it, so every purchase helps keep that market open for refugee, displaced and host-community families.</p>
        <ul class="sh-promises">
          <li><?= icon('phone', 16) ?> Pay by mobile money or card</li>
          <li><?= icon('map-pin', 16) ?> Delivery arranged by our team</li>
          <li><?= icon('leaf', 16) ?> Made at the farm in Rukungiri</li>
        </ul>
        <a class="sh-btn" href="#products">Shop the products <?= icon('arrow-right', 16) ?></a>
      </div>
      <div class="sh-shelf" aria-hidden="true">
        <span class="sh-sun"></span>
        <?php foreach ($shelf as $i => $p): ?>
          <span class="sh-jar sh-jar-<?= $i + 1 ?>"><?= ab_img($p['image'], '', '', $i > 1, '', '(max-width: 900px) 40vw, 240px') ?></span>
        <?php endforeach; ?>
        <span class="sh-plank"></span>
      </div>
    </div>
  </section>

  <!-- How a purchase helps -->
  <section class="sh-why" aria-labelledby="shWhyTitle">
    <div class="container">
      <div class="sh-head ab-reveal">
        <span class="ab-eyebrow">Why it matters</span>
        <h2 id="shWhyTitle">From a family’s field to your table</h2>
      </div>
      <ol class="sh-steps">
        <?php foreach (shop_purpose_steps() as $i => [$ico, $title, $text]): ?>
          <li class="ab-reveal"><span class="sh-step-icon" aria-hidden="true"><?= icon($ico, 22) ?></span><span class="sh-step-n" aria-hidden="true"><?= $i + 1 ?></span><strong><?= h($title) ?></strong><span><?= h($text) ?></span></li>
        <?php endforeach; ?>
      </ol>
      <p class="sh-why-link ab-reveal"><a href="<?= SITE_URL ?>/farm.php" class="ab-link">See how the farm works <?= icon('arrow-right', 15) ?></a></p>
    </div>
  </section>

  <!-- The products -->
  <section class="sh-products" id="products" aria-labelledby="shProductsTitle">
    <div class="container">
      <div class="sh-head sh-head-row ab-reveal">
        <div>
          <span class="ab-eyebrow">From the farm</span>
          <h2 id="shProductsTitle">Our products</h2>
        </div>
        <?php if (count($categories) > 1): ?>
          <nav class="sh-cats" aria-label="Product types">
            <a href="<?= SITE_URL ?>/products.php#products" data-cat=""<?= $category === '' ? ' aria-current="true"' : '' ?>>Everything <small><?= count($products) ?></small></a>
            <?php foreach ($categories as $c): ?>
              <a href="<?= SITE_URL ?>/products.php?category=<?= h(rawurlencode($c)) ?>#products" data-cat="<?= h($c) ?>"<?= $category === $c ? ' aria-current="true"' : '' ?>><?= h($c) ?> <small><?= (int) $byCategory[$c] ?></small></a>
            <?php endforeach; ?>
          </nav>
        <?php endif; ?>
      </div>

      <?php if ($flash): ?><p class="sh-flash is-<?= h($flash['type']) ?>" role="status"><?= h($flash['message']) ?></p><?php endif; ?>

      <?php if ($products): ?>
        <div class="sh-grid">
          <?php foreach ($products as $p): ?>
            <?php $card = shop_product_card($pdo, $p); echo $category !== '' && $p['category'] !== $category ? str_replace('<article class="sh-card ab-reveal"', '<article class="sh-card ab-reveal" hidden', $card) : $card; ?>
          <?php endforeach; ?>
        </div>
      <?php else: ?>
        <p class="sh-empty">New products from the farm are on their way. <a href="<?= SITE_URL ?>/contact.php?subject=<?= rawurlencode('Product order') ?>#write">Ask us what is available</a>.</p>
      <?php endif; ?>
    </div>
  </section>

  <!-- Good to know, then larger orders and visits -->
  <section class="sh-know" aria-labelledby="shKnowTitle">
    <div class="container sh-know-grid">
      <div class="sh-know-copy ab-reveal">
        <span class="ab-eyebrow">Good to know</span>
        <h2 id="shKnowTitle">Ordering from the farm</h2>
        <dl class="sh-facts">
          <div><dt><?= icon('phone', 18) ?> Paying</dt><dd>In Uganda shillings, by mobile money or card, on our secure payment page.</dd></div>
          <div><dt><?= icon('map-pin', 18) ?> Delivery</dt><dd>Our team contacts you after you order to arrange delivery.</dd></div>
          <div><dt><?= icon('message', 18) ?> Questions</dt><dd><?php if ($phone): ?>Call <a href="tel:<?= h(preg_replace('/\s+/', '', $phone)) ?>"><?= h($phone) ?></a><?php endif; ?><?php if ($phone && $shopEmail): ?> or email <?php elseif ($shopEmail): ?>Email <?php endif; ?><?php if ($shopEmail): ?><a href="mailto:<?= h($shopEmail) ?>"><?= h($shopEmail) ?></a><?php endif; ?>.</dd></div>
        </dl>
      </div>
      <div class="sh-bulk ab-reveal">
        <h3>Ordering for a shop, a school or an event?</h3>
        <p>Ask about larger quantities, stocking BetterLife products, or visiting the farm to see where they are made.</p>
        <div class="sh-bulk-actions">
          <a class="sh-btn" href="<?= SITE_URL ?>/contact.php?subject=<?= rawurlencode('Product order') ?>#write">Ask about a larger order <?= icon('arrow-right', 16) ?></a>
          <a class="sh-btn is-quiet" href="<?= SITE_URL ?>/contact.php?subject=<?= rawurlencode('Farm visit') ?>#write">Plan a farm visit</a>
        </div>
      </div>
    </div>
  </section>

  <?= shop_basket_bar() ?>
</main>

<?php require __DIR__ . '/includes/footer.php'; ?>
