<?php
/**
 * The farm shop: pieces shared by the shop, product, basket and checkout pages.
 * Products come from Admin -> Products; the basket lives in the visitor's session.
 */
require_once __DIR__ . '/media.php';

/** What is in the basket: [items, number of items, total in UGX]. */
function shop_basket(): array
{
    $items = $_SESSION['cart'] ?? [];
    $count = (int) array_sum(array_column($items, 'qty'));
    $total = (float) array_sum(array_map(fn($i) => $i['price'] * $i['qty'], $items));
    return [$items, $count, $total];
}

/** How a purchase helps, in the farm's own terms (the same account as on the farm page). */
function shop_purpose_steps(): array
{
    return [
        ['users', 'Farmers grow', 'Refugee, displaced and host-community families farm with training and seedlings from BetterLife.'],
        ['basket', 'The farm buys their surplus', 'BetterLife Agro Tourism Farm buys what they grow beyond their own needs.'],
        ['sun', 'Value is added', 'Milk, honey and organic matter become yoghurt, ghee, honey and Organic Boost.'],
        ['heart', 'Your order keeps it going', 'Every sale helps keep that market open for the families who supply it.'],
    ];
}

/** One product as a card, with a quantity and an Add to basket button that works with or without JavaScript. */
function shop_product_card(PDO $pdo, array $p, string $sizes = '(max-width: 720px) 90vw, (max-width: 1100px) 45vw, 340px'): string
{
    $url = SITE_URL . '/product.php?slug=' . rawurlencode($p['slug']);
    $usd = format_usd($pdo, (float) $p['price']);
    ob_start(); ?>
    <article class="sh-card ab-reveal" data-category="<?= h($p['category']) ?>">
      <a class="sh-card-media" href="<?= h($url) ?>" tabindex="-1" aria-hidden="true"><?= ab_img($p['image'], '', '', true, '', $sizes) ?></a>
      <div class="sh-card-body">
        <span class="sh-card-cat"><?= h($p['category']) ?></span>
        <h3><a href="<?= h($url) ?>"><?= h($p['name']) ?></a></h3>
        <p class="sh-card-desc"><?= h($p['short_desc']) ?></p>
        <p class="sh-price"><strong><?= h(format_price((float) $p['price'])) ?></strong> <span>/ <?= h($p['unit']) ?></span><?php if ($usd): ?><small><?= h($usd) ?></small><?php endif; ?></p>
        <form class="sh-add" method="post" action="<?= SITE_URL ?>/cart-add.php">
          <input type="hidden" name="product_id" value="<?= (int) $p['id'] ?>">
          <?= shop_qty((int) $p['id'], 1) ?>
          <button type="submit" class="sh-add-btn"><?= icon('shopping-bag', 16) ?> <span>Add to basket</span></button>
        </form>
      </div>
    </article>
    <?php return ob_get_clean();
}

/** A quantity stepper: plain number box without JavaScript, minus and plus buttons with it. */
function shop_qty(int $id, int $value, string $name = 'qty'): string
{
    $fid = 'qty' . $id . '-' . substr(md5(uniqid('', true)), 0, 5);
    return '<div class="sh-qty" data-qty><label class="sr-only" for="' . $fid . '">Quantity</label>'
        . '<button type="button" data-step="-1" aria-label="One fewer" hidden>&minus;</button>'
        . '<input id="' . $fid . '" type="number" name="' . h($name) . '" value="' . $value . '" min="1" max="99" inputmode="numeric">'
        . '<button type="button" data-step="1" aria-label="One more" hidden>+</button></div>';
}

/** The basket bar along the foot of the shop pages; shown once something is in the basket. */
function shop_basket_bar(): string
{
    [, $count, $total] = shop_basket();
    ob_start(); ?>
    <div class="sh-bar" data-basket-bar<?= $count ? '' : ' hidden' ?>>
      <div class="sh-bar-inner">
        <span class="sh-bar-icon" aria-hidden="true"><?= icon('shopping-bag', 20) ?></span>
        <p class="sh-bar-text" role="status"><strong data-basket-count><?= $count ?> <?= $count === 1 ? 'item' : 'items' ?></strong> in your basket · <span data-basket-total><?= h(format_price($total)) ?></span></p>
        <a class="sh-bar-link" href="<?= SITE_URL ?>/cart.php">View basket</a>
        <a class="sh-bar-go" href="<?= SITE_URL ?>/checkout.php">Checkout <?= icon('arrow-right', 15) ?></a>
      </div>
    </div>
    <?php return ob_get_clean();
}
