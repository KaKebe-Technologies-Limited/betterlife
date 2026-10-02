-- Team update (October 2026): Douglas Drake Onen no longer listed;
-- Desire Ainembabazi becomes Regional Director (leadership).
-- Run once against the live database:
--   mysql -u <user> -p --default-character-set=utf8mb4 <dbname> < database/team-2026-10-update.sql

-- Hidden rather than deleted, so the record can be restored from Admin if needed
UPDATE team_members SET status = 0
  WHERE name = 'Douglas Drake Onen';

-- Short accurate bio until a full Regional Director bio is supplied
UPDATE team_members
  SET name = 'Desire Ainembabazi', role = 'Regional Director', category = 'leadership', sort_order = 2,
      bio = 'Desire Ainembabazi serves as Regional Director at BetterLife International.'
  WHERE name IN ('Ainembabazi Desire', 'Desire Ainembabazi');

UPDATE team_members SET sort_order = 3
  WHERE name = 'Ahabwe Samuel' AND category = 'leadership';
