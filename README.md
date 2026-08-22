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
- **Blog Posts** — full rich-text editor, categories, draft/publish workflow, featured images
- **Blog Categories**
- **Messages** — inbox for the public Contact form
- **Subscribers** — newsletter sign-ups from the footer/blog sidebar
- **Site Settings** — hero text, about/mission/vision copy, logo, contact info, social links

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
- "Enquire to Order" on the shop links to the Contact form rather than a live
  checkout — no payment processing is wired up.

## Security notes

- Passwords are hashed with bcrypt (`password_hash`/`password_verify`).
- All DB queries use PDO prepared statements.
- Forms are CSRF-protected (session token checked on every POST).
- Uploaded files are validated by real MIME type (not extension) and capped at 5MB;
  `/uploads` blocks script execution via `.htaccess`.
