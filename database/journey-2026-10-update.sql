-- About page journey (October 2026): 2024 now leads with the communal farms
-- powered by clean energy; 2026 leads with setting up the 20-acre farm.
-- Run once against the live database:
--   mysql -u <user> -p --default-character-set=utf8mb4 <dbname> < database/journey-2026-10-update.sql

UPDATE content_items
  SET body = 'We started our communal farms, powered by clean energy. Our water access and livelihoods programmes continued to grow.'
  WHERE page = 'about' AND section_key = 'journey' AND title = '2024';

UPDATE content_items
  SET body = 'We began setting up our 20-acre communal farm, with clean energy supporting its operations. The Women’s Action Circle approach began expanding through Farm Radio International’s Green Leaf Platforms Uganda, and work continued on Soilla, Agribusiness Connekt, the BetterLife Climate Academy and regional youth innovation.'
  WHERE page = 'about' AND section_key = 'journey' AND title = '2026';
