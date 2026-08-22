<?php
/**
 * Shared, print-friendly invoice/receipt markup — used on the customer
 * order-confirmation page and in the admin order detail view.
 */
function render_invoice_html(PDO $pdo, array $order, array $items): string
{
    $logo = asset_url(setting($pdo, 'logo', 'assets/img/logo.png'));
    $siteName = h(setting($pdo, 'site_name', 'BetterLife International'));
    $address = h(setting($pdo, 'address'));
    $email = h(setting($pdo, 'email'));
    $phone = h(setting($pdo, 'phone'));
    $isPaid = $order['status'] === 'paid';

    $rows = '';
    foreach ($items as $it) {
        $rows .= '<tr>
            <td>' . h($it['product_name']) . '</td>
            <td style="text-align:center;">' . (int) $it['quantity'] . '</td>
            <td style="text-align:right;">' . format_price((float) $it['unit_price']) . '</td>
            <td style="text-align:right;">' . format_price((float) $it['line_total']) . '</td>
        </tr>';
    }

    $statusBadge = match ($order['status']) {
        'paid' => '<span class="badge-invoice paid">Paid</span>',
        'failed' => '<span class="badge-invoice failed">Payment Failed</span>',
        'cancelled' => '<span class="badge-invoice failed">Cancelled</span>',
        default => '<span class="badge-invoice pending">Awaiting Payment</span>',
    };

    ob_start();
    ?>
    <div class="invoice-box">
      <div class="invoice-head">
        <div class="invoice-brand">
          <img src="<?= asset_url($logo) ?>" alt="<?= $siteName ?>">
          <div><strong><?= $siteName ?></strong><span><?= $address ?></span></div>
        </div>
        <div class="invoice-meta">
          <h3><?= $isPaid ? 'Receipt' : 'Order Invoice' ?></h3>
          <span>Ref: <strong><?= h($order['order_ref']) ?></strong></span><br>
          <span><?= format_date($order['created_at'], 'F j, Y') ?></span><br>
          <?= $statusBadge ?>
        </div>
      </div>

      <div class="invoice-parties">
        <div>
          <span class="label">Billed To</span>
          <p><strong><?= h($order['customer_name']) ?></strong><br>
          <?= h($order['customer_email']) ?><br>
          <?= h($order['customer_phone']) ?></p>
        </div>
        <div>
          <span class="label">Deliver To</span>
          <p><?= h($order['delivery_location']) ?></p>
          <?php if (!empty($order['notes'])): ?><span class="label">Notes</span><p><?= nl2br(h($order['notes'])) ?></p><?php endif; ?>
        </div>
      </div>

      <table class="invoice-table">
        <thead><tr><th>Item</th><th style="text-align:center;">Qty</th><th style="text-align:right;">Unit Price</th><th style="text-align:right;">Amount</th></tr></thead>
        <tbody><?= $rows ?></tbody>
      </table>

      <div class="invoice-totals">
        <div><span>Subtotal</span><strong><?= format_price((float) $order['subtotal']) ?></strong></div>
        <div class="grand"><span>Total <?= $isPaid ? 'Paid' : 'Due' ?></span><strong><?= format_price((float) $order['total_amount']) ?></strong></div>
      </div>

      <div class="invoice-footer">
        <p>Thank you for supporting BetterLife International.</p>
        <p><?= $email ?> &middot; <?= $phone ?></p>
      </div>
    </div>
    <?php
    return ob_get_clean();
}
