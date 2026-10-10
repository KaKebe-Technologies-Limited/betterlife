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
    // BetterLife's own policies (Annexes I to III of its policy document), word for word, after a way to report
    $safeguarding = [
        ['Raise a concern', "If you are worried about the safety of a child or anyone connected with our work, or you suspect fraud, corruption or other misconduct, please tell us. You can:\n\n- write to ethics@betterlifeint.org\n- write to {email} or call {phone}\n- use {concern}\n- write to us at {postal}\n\nUnder the policies below, you may report anonymously, identities are protected, and no one who reports a concern will face retaliation.\n\nIf someone is in immediate danger, call the police on 999 or 112. In Uganda, 116 is the free national child helpline.", ''],
        ['Child Safeguarding Policy', "## 1. Purpose\nTo ensure all children who come into contact with BetterLife International Organization (BIO) are protected from harm, abuse, exploitation, or neglect, and to promote their safety, dignity, and rights.\n\n## 2. Scope\nApplies to all BIO staff, volunteers, consultants, partners, donors, and affiliates in contact with children, directly or indirectly.\n\n## 3. Principles\n- **Zero tolerance** for abuse, exploitation, or neglect.\n- **Best interest of the child** is paramount.\n- **Non-discrimination** irrespective of gender, disability, or background.\n- **Participation** of children in safe and meaningful ways.\n\n## 4. Definitions\n- **Child**: Anyone under 18 years.\n- **Child Abuse**: Physical, emotional, sexual harm, neglect, or exploitation.\n\n## 5. Expected Conduct\na. Avoid being alone with a child in private settings.\nb. Do not touch children inappropriately.\nc. No exchange of money or gifts for favours.\nd. Use appropriate language and behaviour.\ne. Take immediate action if a child is at risk.\n\n## 6. Reporting\n- All concerns must be reported within 24 hours to the Safeguarding Focal Point.\n- Confidentiality must be maintained, except for child safety.\n- Investigations will follow established safeguarding protocols.\n\n## 7. Implementation\n- All personnel sign a child safeguarding declaration.\n- Annual training and refresher courses are mandatory.\n- Partner organizations must adhere to this policy.", 'Annex I'],
        ['Anti-Fraud and Corruption Policy', "## 1. Purpose\nTo safeguard BetterLife International’s resources and reputation by preventing, detecting, and responding to fraud and corruption in all forms.\n\n## 2. Definitions\na) **Fraud**: Deliberate deception for personal or organizational gain.\nb) **Corruption**: Abuse of entrusted power for private gain.\nc) **Bribery**: Offering, giving, receiving, or soliciting anything of value to influence a decision.\n\n## 3. Prohibited Actions\nI. Falsifying records or invoices.\nII. Misuse of donor funds.\nIII. Offering or accepting bribes or kickbacks.\nIV. Colluding with vendors for inflated pricing.\nV. Misrepresentation of credentials.\n\n## 4. Roles & Responsibilities\nI. **All staff** must report suspicions.\nII. **Finance & Compliance Unit** oversees audits and investigations.\nIII. **Managers** must foster transparency and internal control.\n\n## 5. Prevention Measures\na) Separation of duties in finance and procurement.\nb) Regular internal and external audits.\nc) Whistleblower protections (see Annex III).\nd) Anti-fraud clauses in all contracts.\n\n## 6. Reporting & Investigation\n- Report anonymously or to ethics@betterlifeint.org.\n- Investigations are confidential and fair.\n- Proven fraud leads to termination and legal action.", 'Annex II'],
        ['Whistleblower Protection Policy', "## 1. Purpose\nTo encourage reporting of misconduct without fear of retaliation and to protect the integrity of the organization.\n\n## 2. Scope\nCovers all reports relating to:\n\nI. Fraud\nII. Misconduct\nIII. Abuse\nIV. Violations of laws or policies\n\n## 3. Principles\na) **Confidentiality**: Identities are protected.\nb) **Non-retaliation**: Any retaliation will result in disciplinary action.\nc) **Fair process**: Accused individuals are given a chance to respond.\n\n## 4. Reporting Mechanisms\n- Internal: Supervisor or HR\n- Ethics hotline (confidential)\n- Email: ethics@betterlifeint.org\n\n## 5. Handling Reports\na. Acknowledge within 7 days.\nb. Investigations conclude within 30 working days.\nc. Actions taken include warnings, dismissal, or referral to law enforcement.\n\n## 6. Protection Measures\n- No demotion, discrimination, or harassment of whistleblowers.\n- Support offered through HR and legal aid (if applicable).", 'Annex III'],
    ];
    return ['privacy' => $privacy, 'safeguarding' => $safeguarding][$page] ?? [];
}

/**
 * The page's sections from Page Content, or the starting text: [[heading, text, label], ...] and the date
 * last changed. The label is the small line above a heading (e.g. "Annex I").
 */
function policy_sections(PDO $pdo, string $page): array
{
    $rows = content_items($pdo, $page, 'sections');
    if (!$rows) return [array_map(fn($s) => [$s[0], $s[1], $s[2] ?? ''], policy_defaults($page)), null];
    $updated = max(array_map(fn($r) => (string) $r['updated_at'], $rows));
    return [array_map(fn($r) => [$r['title'], (string) $r['body'], (string) ($r['subtitle'] ?? '')], $rows), $updated];
}

/** One line of text as HTML: escaped, **bold** kept, email addresses linked, contact details filled in. */
function policy_inline(string $line, array $fill): string
{
    $html = h($line);
    $html = preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $html);
    $html = preg_replace('/(?<![\w.@-])([A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[a-z]{2,})(?![\w@-])/', '<a href="mailto:$1">$1</a>', $html);
    return strtr($html, $fill);
}

/**
 * A section's text as HTML. A blank line starts a new block; "## " makes a sub-heading; lines starting "- "
 * make a list; lines starting "a. ", "a) " or "I. " make a lettered or numbered list; **bold** is kept.
 */
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
        while ($lines && str_starts_with($lines[0], '## ')) {
            $out .= '<h3>' . policy_inline(substr(array_shift($lines), 3), $fill) . '</h3>';
        }
        if (!$lines) continue;
        $all = fn($re) => count(preg_grep($re, $lines)) === count($lines);
        if ($all('/^- /')) {
            $out .= '<ul>' . implode('', array_map(fn($l) => '<li>' . policy_inline(substr($l, 2), $fill) . '</li>', $lines)) . '</ul>';
        } elseif ($all('/^(?:[a-z]|[IVX]+)[.)] /')) {
            $type = preg_match('/^[IVX]+[.)] /', $lines[0]) ? 'I' : 'a';
            $out .= '<ol type="' . $type . '">' . implode('', array_map(fn($l) => '<li>' . policy_inline(preg_replace('/^(?:[a-z]|[IVX]+)[.)] /', '', $l), $fill) . '</li>', $lines)) . '</ol>';
        } else {
            $out .= '<p>' . policy_inline(implode(' ', $lines), $fill) . '</p>';
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
        <?php foreach ($sections as [$h, $text, $label]): ?>
          <section id="<?= h($slug($h)) ?>"><?php if ($label !== ''): ?><p class="pl-label"><?= h($label) ?></p><?php endif; ?><h2><?= h($h) ?></h2><?= policy_html($pdo, $text) ?></section>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
</main>
<?php
}
