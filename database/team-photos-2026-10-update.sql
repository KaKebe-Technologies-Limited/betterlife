-- Leadership photos (October 2026). The image files ship with the site in assets/img/team/.
-- Run once against the live database:
--   mysql -u <user> -p --default-character-set=utf8mb4 <dbname> < database/team-photos-2026-10-update.sql
-- With all three set, the About page shows the leadership faces automatically.

UPDATE team_members SET photo = 'assets/img/team/denise-ayebare.jpg'
  WHERE name IN ('Ayebare Denise', 'Denise Ayebare') AND category = 'leadership';

UPDATE team_members SET photo = 'assets/img/team/desire-ainembabazi.jpg'
  WHERE name IN ('Desire Ainembabazi', 'Ainembabazi Desire') AND category = 'leadership';

UPDATE team_members SET photo = 'assets/img/team/ahabwe-samuel.jpg'
  WHERE name = 'Ahabwe Samuel' AND category = 'leadership';
