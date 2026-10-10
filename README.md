# BetterLife International — Website & Admin Dashboard

A full redesign of betterlifeint.org: a fresh, professional PHP + MySQL site with a
green & blue nature theme, real content pulled from the live BetterLife International
site, a new **BetterLife Farm** section (Honey, Ghee, Yoghurt), and a full admin
dashboard for managing everything without touching code.

## Stack

Plain PHP 8 + PDO/MySQL, no framework or build step — runs directly on XAMPP.
Quill.js (CDN) powers the rich-text blog editor in the admin dashboard.

## Local setup (XAMPP)

1. This folder already lives at `C:\xampp\htdocs\betterlife`, so with Apache + MySQL
   running in the XAMPP control panel the site is live at:
   **http://localhost/betterlife/**
2. The database (`betterlife`) has already been created and seeded from
   [`database.sql`](database.sql). If you ever need to reset it:
   ```bash
   "C:\xampp\mysql\bin\mysql.exe" -u root --default-character-set=utf8mb4 -e "DROP DATABASE IF EXISTS betterlife;"
   "C:\xampp\mysql\bin\mysql.exe" -u root --default-character-set=utf8mb4 < database.sql
   ```
   ⚠️ Always import with `--default-character-set=utf8mb4` — otherwise the MySQL
   client's default Windows codepage will corrupt accented characters and em dashes.
3. DB credentials live in [`config/config.php`](config/config.php) (defaults to the
   standard XAMPP `root` / no password — edit there if yours differs).

## Admin dashboard

URL: **http://localhost/betterlife/admin/login.php**

- Username: `admin`
- Password: `BetterLife2026!`

**Change this password immediately** via Site Settings → Change Admin Password.

From the dashboard you can manage, with zero code changes:

- **Programs** — the 4 program areas shown on the homepage & Our Programs page
- **Farm Products** — Honey / Ghee / Yoghurt catalogue (price, unit, photos, category)
- **Team & Board** — leadership, staff, board of directors, volunteers
- **Testimonials** — quotes shown on the homepage
- **Impact Stats** — the animated counters ("1M+ Trees Planted" etc.)
- **Projects** and **Impact & Reports** — under the "Our Work" nav dropdown; reports
  support either an uploaded PDF or a pasted URL
- **Blog Posts** — full rich-text editor, categories, draft/publish workflow, featured images
- **Blog Categories**
- **Orders** — every checkout order, with a printable invoice/receipt view, manual
  "mark as paid/cancelled", and a "resend receipt email" button
- **Messages** — inbox for the public Contact form
- **Subscribers** — newsletter sign-ups from the footer/blog sidebar
- **Site Settings** — hero text (incl. the 4 homepage hero photos), about/mission/vision
  copy, logo, contact info, social links, and **Payments & Email** (SMTP + Pesapal)

## Structure

```
/                     Public site (index.php, about.php, products.php, blog.php, ...)
/includes/            Shared PHP: db.php, functions.php, header.php, footer.php
/config/config.php    DB credentials + site URL detection
/admin/               Admin dashboard (auth-gated, one module per content type)
/assets/              Site-wide CSS/JS/images
/uploads/             Images uploaded via the admin dashboard (organised by type)
database.sql          Full schema + seed content
```

## Content notes

- Org story, mission/vision, programs, team, board and blog posts were sourced from
  the live betterlifeint.org site.
- Board member photos (Hillary Clinton, Mohamed Nasheed, etc.) are the same ones
  already published on betterlifeint.org's About page.
- Staff/volunteer members without a photo automatically get a clean initials avatar
  (via ui-avatars.com) until a real photo is uploaded in the admin.
- Farm product photography is placeholder stock imagery (Unsplash, free to use) —
  swap it for real product photos any time from Farm Products in the admin.
- Projects and Impact & Reports content/images/PDF report links were pulled from
  betterlifeint.org's own Projects and Impact & Reports pages.

## Checkout & payments (Pesapal)

Customers can add farm products to a cart, check out, and pay by **card or mobile
money** through [Pesapal](https://pesapal.com)'s hosted checkout — one integration
covers both payment methods. PHPMailer + Gmail SMTP sends the order confirmation,
an admin alert, and a paid receipt (all three degrade gracefully to just an error-log
entry if email isn't configured — checkout itself never breaks).

**Setup:** go to Admin → Site Settings → **Payments & Email** and fill in:
- SMTP host/port/username + a Google **App Password** (not your normal Gmail
  password — generate one at myaccount.google.com/apppasswords)
- Pesapal Consumer Key/Secret from pay.pesapal.com → Settings → API, and whether
  you're in sandbox (test) or live mode

⚠️ These credentials are stored in the `settings` table, **not** in `database.sql` —
the seed file ships with them blank on purpose so live secrets never end up in git
history. Re-importing `database.sql` will not wipe credentials you've already saved
through the admin UI unless you drop the database first.

**Testing locally:** Pesapal needs to reach your callback/IPN URLs over the public
internet, which `localhost` isn't. For local testing, tunnel with ngrok and set
`define('PESAPAL_NGROK_URL', 'https://xxxx.ngrok-free.app');` in
[`config/config.php`](config/config.php); once deployed to a real domain over HTTPS
this isn't needed (it auto-detects the host).

**Order flow:** `cart.php` → `checkout.php` (customer details) → Pesapal hosted
checkout → `order-callback.php` (verifies status on return) / `order-ipn.php`
(server-to-server webhook, the reliable source of truth) → `order-confirmation.php`
(neat branded invoice/receipt, printable to PDF via the browser).

## Updating the live site

Content (page text, projects, team, products, stories, reports, site settings) is prepared
in the local database. To publish it:

1. `C:\xampp\php\php.exe tools/make_live_sync.php` writes `live-content-sync.sql` from the
   local database. It stops if a row points to an image that is not in git.
2. Commit and push, so the code and that file reach the server.
3. On the live site: **Admin → Site Update** (marked *New*), tick the box, press
   **Update the site content**.

Site Update saves a backup of the live content first (in `database/backups/`, not reachable
from the web; the ten most recent are kept, and any can be downloaded or put back). It
loads the file into side tables before touching anything, so a bad file changes nothing.
It never touches orders, messages, subscribers, admin accounts, passwords, payment keys,
order alerts or maintenance mode. Importing `live-content-sync.sql` in phpMyAdmin does
the same, without the backup.

## Donations

`donate.php` takes gifts by mobile money or card through Pesapal, using the same keys as the
farm shop (Admin → Site Settings → Payments & Email). Without keys, the page asks people to
write instead. A gift is confirmed with Pesapal itself, on return (`donate-thanks.php`) and by
the IPN (`order-ipn.php` serves orders and gifts); the giver's receipt and the alert to
BetterLife go out once, when it is paid. Gifts are listed, totalled and downloadable under
Admin → Donations. The `donations` table creates itself on first use.

## Is email working?

Every email the site sends records whether it went out. If the last one failed, the admin
dashboard says so, and Admin → Site Settings → "Check that emails are going out" shows the
error and sends a test.

## Policies, statistics and phone films

- `privacy.php` and `safeguarding.php`: text in Admin → Page Content → Policies. Contact
  details are filled in from Site Settings ({email}, {phone}, {postal}, {concern}).
- Site Settings → Visitor Statistics & Google: paste a Cloudflare Web Analytics token
  (cookieless) and the Google Search Console verification tag; then submit `sitemap.php`.
- Films have a lighter `-360.mp4` copy beside each `-720.mp4`, chosen on phones, data saving
  and slow connections. To make one: `powershell -File tools/make_phone_video.ps1`, then
  `tools/mp4_faststart.php` (so playback starts while it downloads).

## Security notes

- Passwords are hashed with bcrypt (`password_hash`/`password_verify`).
- All DB queries use PDO prepared statements.
- Forms are CSRF-protected (session token checked on every POST).
- Uploaded files are validated by real MIME type (not extension) and capped at 5MB
  (15MB for report PDFs); `/uploads` blocks script execution via `.htaccess`.
- PHPMailer ships in `/vendor/phpmailer` (copied in directly since no Composer is
  available in this environment — no code changes needed if you later switch to a
  Composer-managed install).

## Known issue to fix

The SMTP app password currently saved is being rejected by Google ("Could not
authenticate" in the Apache error log) — likely revoked or needs regenerating.
Order emails fail silently (checkout still completes fine) until you generate a
fresh App Password at myaccount.google.com/apppasswords and save it under
Admin → Site Settings → Payments & Email.
