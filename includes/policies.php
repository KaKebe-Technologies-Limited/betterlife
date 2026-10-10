<?php
/**
 * The Privacy and Safeguarding pages. Their text lives in Admin > Page Content (pages "privacy" and
 * "safeguarding", one row per section: heading and text). The sections below are the starting text,
 * used until those rows exist. In the text, a blank line starts a new paragraph, lines starting "- "
 * make a list, and {email}, {phone} and {postal} are filled in from Site Settings ({concern} links to the contact form).
 */
require_once __DIR__ . '/functions.php';

function policy_defaults(string $page): array
{
    $privacy = [
        ['Who we are', "BetterLife International Organization is a registered non-governmental organisation in Uganda. This notice explains what personal information this website collects, why, and what we do with it. You can reach us about it at {email}, by phone on {phone}, or by post at {postal}."],
        ['What we collect, and why', "We only collect what you give us in a form, and we use it for the reason you gave it:\n\n- Contact form: your name, email address, phone number (optional) and message, so we can reply.\n- Newsletter: your email address, so we can send you updates.\n- Farm shop orders: your name, email address, phone number, delivery location and any notes, so we can deliver your order and send your receipt.\n- Donations: your name, email address, phone number (optional), the amount and any message, so we can record your gift and send your receipt."],
        ['Payments', "Card and mobile money payments are made on the secure page of Pesapal, our payment provider. Your card or mobile money details go to Pesapal, not to us. Pesapal tells us whether a payment went through."],
        ['Who else sees it', "We do not sell or rent your information. It is seen by the BetterLife team who need it, and it passes through the services that run this website: our web host, our email service (which sends our replies and receipts) and Pesapal for payments. Pages on this site load their typefaces from Google Fonts."],
        ['Cookies and similar', "This website uses one essential cookie, which keeps your basket and protects our forms while you browse. It does not use advertising cookies. If you pause a film on our pages, your browser remembers that choice on your own device. If we count visits to this website, we use Cloudflare Web Analytics, which does not use cookies or identify individual visitors."],
        ['How long we keep it', "We keep your information only for as long as we need it for the reason you gave it, and for as long as the law requires us to keep records, such as records of payments."],
        ['Your choices', "You can ask to see the information we hold about you, to correct it, or to have it deleted, by writing to {email}. You can leave our newsletter at any time, through the link in any newsletter we send or by writing to us. These rights are set out in Uganda's Data Protection and Privacy Act, 2019."],
        ['Changes', "If we change how we use personal information, we will update this page."],
    ];
    $safeguarding = [
        ['Our commitment', "BetterLife works with women, children and young people, refugees, displaced families and farming communities. Everyone who takes part in our work has the right to be safe from harm, abuse and exploitation, including sexual exploitation and abuse. Keeping people safe comes before any other part of our work."],
        ['Who this covers', "This commitment applies to everyone who works with or represents BetterLife: our staff, board members, volunteers, partners and visitors to our programmes."],
        ['How we work', "- We treat the people we work with with respect, and never ask for anything in return for help or support.\n- We ask for consent before we photograph people or share their stories, and we do not share details that could put anyone at risk.\n- We take extra care with children and young people, in schools and community activities."],
        ['Raise a concern', "If you are worried about the safety or treatment of anyone connected with our work, or about the behaviour of anyone who works with us, please tell us. You can:\n\n- write to {email}\n- call {phone}\n- use {concern}\n- write to us at {postal}\n\nIf someone is in immediate danger, call the police on 999 or 112. In Uganda, 116 is the free national child helpline."],
        ['What happens next', "Every concern is taken seriously. We handle it in confidence, sharing it only with the people who need to know in order to keep someone safe, and we will not treat anyone unfairly for raising a concern in good faith."],
    ];
    return ['privacy' => $privacy, 'safeguarding' => $safeguarding][$page] ?? [];
}

/** The page's sections from Page Content, or the starting text: [[heading, text], ...] and the date last changed. */
function policy_sections(PDO $pdo, string $page): array
{
    $rows = content_items($pdo, $page, 'sections');
    if (!$rows) return [policy_defaults($page), null];
    $updated = max(array_map(fn($r) => (string) $r['updated_at'], $rows));
    return [array_map(fn($r) => [$r['title'], (string) $r['body']], $rows), $updated];
}

/** A section's text as HTML: paragraphs, "- " lists, and the contact details filled in. */
function policy_html(PDO $pdo, string $text): string
{
    $email = setting($pdo, 'email');
    $phone = setting($pdo, 'phone');
    $fill = [
        '{email}'  => $email ? '<a href="mailto:' . h($email) . '">' . h($email) . '</a>' : 'our email address',
        '{phone}'  => $phone ? '<a href="tel:' . h(preg_replace('/[^0-9+]/', '', $phone)) . '">' . h($phone) . '</a>' : 'our phone number',
        '{postal}' => h(setting($pdo, 'postal_address') ?: setting($pdo, 'address')),
        '{concern}' => '<a href="' . SITE_URL . '/contact.php?subject=' . rawurlencode('Safeguarding concern') . '">our contact form</a>, which opens with Report a safeguarding concern chosen',
    ];
    $out = '';
    foreach (preg_split('/\R\s*\R/', trim(str_replace("\r", '', $text))) as $block) {
        $lines = array_values(array_filter(array_map('trim', explode("\n", $block)), 'strlen'));
        if (!$lines) continue;
        if (count(array_filter($lines, fn($l) => str_starts_with($l, '- '))) === count($lines)) {
            $out .= '<ul>' . implode('', array_map(fn($l) => '<li>' . strtr(h(substr($l, 2)), $fill) . '</li>', $lines)) . '</ul>';
        } else {
            $out .= '<p>' . strtr(h(implode(' ', $lines)), $fill) . '</p>';
        }
    }
    return $out;
}

/** Renders a policy page: a title band, then the sections with a short contents list beside them. */
function policy_page(PDO $pdo, string $page, string $title, string $lead): void
{
    [$sections, $updated] = policy_sections($pdo, $page);
    $slug = fn($s) => trim(preg_replace('/[^a-z0-9]+/', '-', strtolower($s)), '-');
    ?>
<main class="ab pl" id="top">
  <section class="pl-head" aria-labelledby="plTitle">
    <div class="container">
      <nav class="sh-crumb" aria-label="Breadcrumb"><a href="<?= SITE_URL ?>/index.php">Home</a><span aria-hidden="true">/</span><span aria-current="page"><?= h($title) ?></span></nav>
      <h1 id="plTitle"><?= h($title) ?></h1>
      <p class="pl-lead"><?= h($lead) ?></p>
      <?php if ($updated): ?><p class="pl-date">Last updated <?= h(date('j F Y', strtotime($updated))) ?></p><?php endif; ?>
    </div>
  </section>
  <section class="pl-body">
    <div class="container pl-grid">
      <nav class="pl-toc" aria-label="On this page">
        <p>On this page</p>
        <ol><?php foreach ($sections as [$h]): ?><li><a href="#<?= h($slug($h)) ?>"><?= h($h) ?></a></li><?php endforeach; ?></ol>
      </nav>
      <div class="pl-text">
        <?php foreach ($sections as [$h, $text]): ?>
          <section id="<?= h($slug($h)) ?>"><h2><?= h($h) ?></h2><?= policy_html($pdo, $text) ?></section>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>
<?php
}
