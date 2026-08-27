-- ---------------------------------------------------------------------------
-- BetterLife content refresh (from "Edits for website" brief, 2026).
-- Safe to re-run: settings use upsert; stats/programs/blog_categories are
-- rebuilt from scratch. Run on the live DB after deploying the new templates:
--   mysql -u <user> -p --default-character-set=utf8mb4 <db> < database/content-2026-update.sql
-- ---------------------------------------------------------------------------

-- ---- Site settings (editable copy that already lived in the settings table) --
INSERT INTO settings (setting_key, setting_value) VALUES
('hero_kicker',   'Founded in Uganda. Working across five African countries.'),
('hero_title',    'Led by people who understand what it means to begin again.'),
('hero_subtitle', 'BetterLife works with women, young people, refugees, displaced families and farming communities to turn climate pressure into practical action: food people can grow, skills they can earn from, cleaner energy and stronger routes to market.'),
('mission_text',  'To work with communities to build the food systems, livelihoods, knowledge and local institutions they need to live with greater security and dignity in a changing climate.'),
('vision_text',   'Communities with the power, resources and opportunity to shape their own future.'),
('about_who_title', 'Who We Are'),
('about_who_text', 'Denise Ayebare founded BetterLife International in Uganda in 2021. She was nineteen, and the organisation began with USD 200 and a small group of young people who were tired of watching communities receive short-term help while the conditions keeping them vulnerable remained unchanged.\n\nThey had seen farmers lose a season and start again with nothing. They had seen women carry the weight of food, water and household survival without access to finance or decision-making. They had seen young people complete training and still have no tools, customers or way into work. They had also seen how much knowledge already existed inside communities, often overlooked by programmes designed from far away.\n\nBetterLife was created to work differently.\n\nWhat began as a small youth-led initiative in Uganda now works across Uganda, South Sudan, Tanzania, Ghana and the Democratic Republic of Congo. The organisation brings together climate-resilient agriculture, livelihoods, clean energy, water, restoration, education, technology and market access.\n\nThe work has grown, but the starting point has remained the same: people closest to a problem must have a real hand in defining it, designing the response and deciding what success looks like.'),
('farm_title',    'BetterLife Agro Tourism Farm'),
('farm_text',     'BetterLife Agro Tourism Farm is where our work in agriculture, clean energy and livelihoods meets production and sales.\n\nThe farm demonstrates solar-powered irrigation, greenhouse farming, dairy production, beekeeping and livestock rearing. It also creates a route for farmers trained by BetterLife International to supply produce for processing and sale through BetterLife Agro Tourism Farm Ltd.\n\nOur products include BetterLife Honey, Ghee and Vanilla Yoghurt. Each one is part of a wider value chain connecting knowledge, production and household income.'),
('footer_about',  'BetterLife International works with communities across five African countries to build food security, livelihoods and practical climate resilience.'),
('phone',         '+256 770 933 286'),
('email',         'info@betterlifeint.org'),
('address',       'Rukungiri, Uganda')
ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value);

-- ---- Impact reach figures (Impact & Reports "Our Reach" grid) ---------------
DELETE FROM stats;
ALTER TABLE stats AUTO_INCREMENT = 1;
INSERT INTO stats (label, value, sort_order) VALUES
('People reached in 2025', '112,430', 1),
('Farmers supported', '18,900', 2),
('Refugees and host-community members reached', '41,200', 3),
('Students engaged in climate education', '4,580+', 4),
('Green Libraries and Eco Labs', '20+', 5),
('Household biogas systems', '48+', 6),
('Community boreholes', '65', 7),
('Tree seedlings raised', '50,000+', 8);

-- ---- Programme areas (Home "What We Do" grid + footer + Our Work anchors) ---
DELETE FROM programs;
ALTER TABLE programs AUTO_INCREMENT = 1;
INSERT INTO programs (title, slug, tagline, summary, content, image, icon, sort_order) VALUES
('Climate-Resilient Agriculture and Food Security', 'climate-resilient-agriculture', '', 'We help farmers and families grow food in the face of unreliable rain, poor soils and limited land. The work includes demonstration gardens, composting, mulching, drought-tolerant crops, agroforestry, greenhouse farming, irrigation, poultry, aquaculture and beekeeping.', '', 'assets/img/farm-field-1.jpg', 'leaf', 1),
('Green Skills, Livelihoods and Markets', 'green-skills-livelihoods', '', 'We train people in skills that respond to local opportunities, then help them move beyond training through savings groups, enterprise support, finance and market connections. Our work spans agriculture, carpentry, tailoring, barbering, poultry, weaving and solar technology.', '', 'assets/img/project-smiles.jpg', 'users', 2),
('Climate Education and Youth Leadership', 'climate-education-youth-leadership', '', 'Through Green Libraries, Eco Labs, school clubs, youth centres, debates, Climate Academies and innovation challenges, children and young people gain the knowledge, tools and confidence to take part in the decisions shaping their future.', '', 'assets/img/project-climate-education.jpg', 'file-text', 3),
('Clean Energy, Water and Restoration', 'clean-energy-water-restoration', '', 'We work with communities on tree nurseries, agroforestry, biogas, briquettes, waste recovery and water access. Each solution is designed to ease pressure on both households and the environment.', '', 'assets/img/program-trees.jpg', 'leaf', 4),
('Digital Innovation for Farmers', 'digital-innovation', '', 'Soilla and Agribusiness Connekt bring soil advice, climate information, services, finance and markets closer to farmers. The technology is paired with face-to-face support so that information becomes something people can use.', '', 'assets/img/project-soilla-app.jpg', 'trending-up', 5);

-- ---- Stories taxonomy (Stories page: five sections, no "Uncategorized") -----
UPDATE blog_categories SET name = 'Climate and Food',        slug = 'climate-and-food'        WHERE slug = 'climate-change';
UPDATE blog_categories SET name = 'Work and Enterprise',     slug = 'work-and-enterprise'     WHERE slug = 'agriculture';
UPDATE blog_categories SET name = 'Partnerships and News',   slug = 'partnerships-and-news'   WHERE slug = 'politics';
UPDATE blog_categories SET name = 'From the Field',          slug = 'from-the-field'          WHERE slug = 'uncategorized';
INSERT INTO blog_categories (name, slug)
SELECT 'Young People and Ideas', 'young-people-and-ideas'
WHERE NOT EXISTS (SELECT 1 FROM blog_categories WHERE slug = 'young-people-and-ideas');

UPDATE blog_posts SET category_id = (SELECT id FROM blog_categories WHERE slug = 'climate-and-food')
  WHERE slug = 'climate-change-amplifying-extreme-weather-and-displacement'
     OR title LIKE 'Climate Change: Amplifying%';
UPDATE blog_posts SET category_id = (SELECT id FROM blog_categories WHERE slug = 'young-people-and-ideas')
  WHERE title LIKE 'Congratulations to Dr.%'
     OR title LIKE 'The Importance of Civic Education%'
     OR title LIKE 'The Naked Truth of Corruption%';
