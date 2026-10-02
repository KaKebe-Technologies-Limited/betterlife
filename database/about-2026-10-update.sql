-- About page refresh (October 2026): new mission, vision and slogan, and the
-- working-principle titles used on the redesigned About page.
-- Run once against the live database:
--   mysql -u <user> -p --default-character-set=utf8mb4 <dbname> < database/about-2026-10-update.sql

UPDATE settings SET setting_value = 'We envision a world where every person, regardless of their background, has the opportunity to live a fulfilling and sustainable life.'
  WHERE setting_key = 'vision_text';

UPDATE settings SET setting_value = 'To create a better life for everyone by promoting sustainable practices, empowering young people, supporting vulnerable groups, and fostering peace and equality in communities.'
  WHERE setting_key = 'mission_text';

-- Slogan (shown beside the logo in the site header)
UPDATE settings SET setting_value = 'Building Hope'
  WHERE setting_key = 'tagline';

UPDATE content_items SET title = 'We Build with Existing Groups'
  WHERE page = 'about' AND section_key = 'how_we_work' AND title = 'We Build on What Already Exists';

UPDATE content_items SET title = 'We Follow What Changes'
  WHERE page = 'about' AND section_key = 'how_we_work' AND title = 'We Pay Attention to What Changes';
