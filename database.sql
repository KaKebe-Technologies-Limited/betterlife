-- BetterLife International — database schema + seed data
-- Import via phpMyAdmin or: mysql -u root < database.sql

CREATE DATABASE IF NOT EXISTS betterlife CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE betterlife;

-- ----------------------------------------------------------------------
-- Admins
-- ----------------------------------------------------------------------
CREATE TABLE admins (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  username VARCHAR(50) NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL,
  password VARCHAR(255) NOT NULL,
  avatar VARCHAR(255) DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Default admin: username "admin", password "BetterLife2026!" (change after first login)
INSERT INTO admins (name, username, email, password) VALUES
('Site Administrator', 'admin', 'ot.sedrick@gmail.com', '$2y$10$02rq2f5iDzOc5REMVG1f1OVN8xuEtFV8429n6PX7x/L3kKnC.kfEi');

-- ----------------------------------------------------------------------
-- Settings (key/value site content store)
-- ----------------------------------------------------------------------
CREATE TABLE settings (
  setting_key VARCHAR(100) PRIMARY KEY,
  setting_value LONGTEXT
) ENGINE=InnoDB;

INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'BetterLife International'),
('tagline', 'Sustainable Livelihoods. Green Skills. Lasting Hope.'),
('logo', 'assets/img/logo.png'),
('hero_title', 'Growing a Better Life, Rooted in Community'),
('hero_subtitle', 'We work with children, youth, refugees and displaced communities across Uganda, South Sudan, Tanzania, Ghana and the DRC to build sustainable livelihoods, green skills and lasting hope — from climate-smart farms to the honey, ghee and yoghurt they produce.'),
('hero_image', 'assets/img/hero-real-1.jpg'),
('hero_image_1', 'assets/img/hero-real-1.jpg'),
('hero_image_2', 'assets/img/farm-field-1.jpg'),
('hero_image_3', 'assets/img/product-honey.jpg'),
('hero_image_4', 'assets/img/about-real-1.jpg'),
('hero_image_5', 'assets/img/farm-field-2.jpg'),
('hero_image_6', 'assets/img/program-trees.jpg'),
('hero_image_7', 'assets/img/product-ghee.jpg'),
('hero_image_8', 'assets/img/product-yogurt.jpg'),
('hero_image_9', 'assets/img/betterlifeint-source/programs/program-photo-1.jpg'),
('hero_image_10', 'assets/img/betterlifeint-source/programs/program-photo-3.jpg'),
('hero_image_11', 'assets/img/betterlifeint-source/projects/project-agro-tourism-alt.jpeg'),
('hero_image_12', 'assets/img/betterlifeint-source/impact-reports/impact-photo-1.jpeg'),
('founded_year', '2021'),
('about_who_title', 'Who We Are'),
('about_who_text', 'BetterLife International was founded in 2021 by a group of young leaders and refugees who shared a vision: a world where every person, regardless of their background, can live with dignity, opportunity and hope. Born out of lived experiences of displacement, marginalization and resilience, our organization is youth-led and refugee-inspired, driven by the belief that those who have faced adversity have the greatest insights and solutions for creating lasting change.\n\nWe work hand-in-hand with children, youth, refugees, and internally displaced persons across Uganda, South Sudan, Tanzania, Ghana, and the Democratic Republic of Congo, designing programs that empower communities, build livelihoods, and strengthen resilience. Our interventions focus on sustainable agriculture, green skills, climate education, renewable energy, community health, and social cohesion, ensuring that those most vulnerable have the tools to thrive.\n\nAt BetterLife International, our approach is rooted in the voices of the communities we serve. Every program is shaped by local needs, co-created with young people, and strengthened by the resilience, courage, and creativity of those who refuse to give up despite life''s challenges. We believe that hope is not given, it is built — and that together, communities can transform adversity into opportunity.'),
('mission_text', 'To create a better life for everyone by promoting sustainable practices, empowering young people, supporting vulnerable groups, and fostering peace and equality in communities.'),
('vision_text', 'We envision a world where every person, regardless of their background, has the opportunity to live a fulfilling and sustainable life.'),
('about_image', 'assets/img/about-real-1.jpg'),
('farm_title', 'BetterLife Farm — Rukungiri, Uganda'),
('farm_text', 'Our BetterLife Agro-Tourism Farm in Rukungiri is a working model of sustainable agriculture — a learning centre where farmers gain hands-on skills in organic dairy and crop farming. What began as a training ground for climate-smart agriculture has grown into a small but proud farm-to-market enterprise. Every jar of honey, every batch of ghee, and every cup of yoghurt sold funds our community programs and proves that organic, ethically produced food can also be a livelihood.'),
('farm_image', 'assets/img/farm-field-1.jpg'),
('address', 'Rukungiri, Uganda'),
('phone', '+256 700 000 000'),
('email', 'info@betterlifeint.org'),
('shop_email', 'farm@betterlifeint.org'),
('facebook', 'https://facebook.com/betterlifeintl'),
('twitter', 'https://twitter.com/betterlifeintl'),
('instagram', 'https://instagram.com/betterlifeintl'),
('linkedin', 'https://linkedin.com/company/betterlifeintl'),
('youtube', 'https://youtube.com/@betterlifeintl'),
('footer_about', 'BetterLife International is a youth-led, refugee-inspired organization creating sustainable livelihoods, green skills and lasting hope across Sub-Saharan Africa.'),
('map_embed', ''),
('board_quote', 'BetterLife International is changing lives at every level, empowering youth, building resilient communities, and inspiring hope where it''s needed most.'),
('board_quote_author', 'Hillary Clinton, Board Director'),
('admin_alert_email', 'ot.sedrick@gmail.com'),
('smtp_host', 'smtp.gmail.com'),
('smtp_port', '587'),
('smtp_username', ''),
('smtp_app_password', ''),
('smtp_from_name', 'BetterLife International'),
('pesapal_sandbox', '0'),
('pesapal_consumer_key', ''),
('pesapal_consumer_secret', '');
-- NOTE: SMTP + Pesapal credentials are intentionally left blank here so
-- live secrets never enter version control. Set them once via
-- Admin -> Site Settings -> Payments & Email (they're stored in this same
-- `settings` table, just not committed to this seed file).

-- ----------------------------------------------------------------------
-- Impact stats
-- ----------------------------------------------------------------------
CREATE TABLE stats (
  id INT AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(150) NOT NULL,
  value VARCHAR(50) NOT NULL,
  icon VARCHAR(50) DEFAULT NULL,
  sort_order INT DEFAULT 0,
  status TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO stats (label, value, icon, sort_order) VALUES
('Students Engaged', '4,580+', 'graduation-cap', 1),
('Tonnes of Plastic Recycled', '20,000', 'recycle', 2),
('Trees Planted', '1M+', 'tree', 3),
('Refugee Camps Impacted', '5', 'tent', 4),
('Women & Girls Empowered', '280', 'heart', 5);

-- ----------------------------------------------------------------------
-- Programs
-- ----------------------------------------------------------------------
CREATE TABLE programs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  slug VARCHAR(170) NOT NULL UNIQUE,
  tagline VARCHAR(255),
  summary TEXT,
  content LONGTEXT,
  image VARCHAR(255),
  icon VARCHAR(50),
  sort_order INT DEFAULT 0,
  status TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO programs (title, slug, tagline, summary, content, image, icon, sort_order) VALUES
('Sustainable Agriculture — BetterLife Agro-Tourism', 'sustainable-agriculture', 'Bringing innovation to the community', 'Our model farm in Rukungiri trains farmers in organic dairy and crop farming, and produces the honey, ghee and yoghurt sold under the BetterLife Farm brand.', 'BetterLife Agro-Tourism Farm is leading a revolution in Ugandan agriculture by promoting sustainable practices and ecological conservation. We empower farmers with modern and sustainable agricultural techniques, focusing on dairy and crop farming, and organic methods that benefit both people and the planet. Our model farm in Rukungiri serves as a learning centre where farmers gain practical skills and knowledge to cultivate organic crops.\n\nAdditionally, we are spearheading the introduction of organic produce to Ugandan markets, ensuring consumers have access to healthier and environmentally friendly food options. With a commitment to sustainability and innovation, we are paving the way for a greener and healthier future.', 'assets/img/farm-field-1.jpg', 'leaf', 1),
('Climate Education & Awareness', 'climate-education-awareness', 'Saving mother earth', 'Debates, public speaking and Green Libraries that put climate literacy directly into the hands of students.', 'Our Climate Education and Awareness Programme aims to educate and empower students to take action against climate change. Through engaging in debates and public speaking competitions held in schools, we foster dialogue and critical thinking about environmental issues. Additionally, we support the establishment of Green Libraries in schools, providing educational materials to promote sustainability and environmental stewardship among students.', 'assets/img/hero-farm-1.jpg', 'book-open', 2),
('Nature-Based Solutions Programme', 'nature-based-solutions', 'Conserving the environment for future generations', 'Over 30 nursery beds across 24 districts, growing indigenous and fruit trees to restore ecosystems.', 'Our Nature-Based Solutions Programme is dedicated to promoting environmental conservation through tree-planting initiatives. With over 30 nursery beds spread across 24 districts in Uganda and spanning 3 regions nationwide, we actively grow indigenous and fruit trees. By planting trees, we aim to enhance biodiversity, mitigate climate change, and create sustainable ecosystems. Our efforts not only contribute to environmental conservation but also foster appreciation for nature among communities and individuals.', 'assets/img/program-trees.jpg', 'tree', 3),
('Empowering Women in Refugee & IDP Communities', 'empowering-women', 'We bring hope to the voiceless', 'Skill-building in briquette-making and small-scale farming that gives displaced women a sustainable income.', 'Our project focuses on empowering women in refugee and internally displaced communities through skill-building and sustainable livelihood initiatives. We provide training in briquette making, equipping women with the knowledge and resources to produce environmentally friendly fuel sources. Additionally, we offer guidance on small-scale farming techniques tailored to limited land resources, enabling women to cultivate their own food and generate income. Through innovative approaches and practical training, we empower women to sustain themselves and their families, fostering resilience and self-reliance in challenging environments.', 'assets/img/farm-field-3.jpg', 'heart-handshake', 4);

-- ----------------------------------------------------------------------
-- Products (BetterLife Farm produce)
-- ----------------------------------------------------------------------
CREATE TABLE products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  slug VARCHAR(170) NOT NULL UNIQUE,
  category VARCHAR(80) NOT NULL,
  price DECIMAL(10,2) DEFAULT NULL,
  unit VARCHAR(50) DEFAULT NULL,
  short_desc VARCHAR(255),
  description LONGTEXT,
  image VARCHAR(255),
  featured TINYINT(1) DEFAULT 0,
  status TINYINT(1) DEFAULT 1,
  sort_order INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO products (name, slug, category, price, unit, short_desc, description, image, featured, sort_order) VALUES
('Pure Wild Honey', 'pure-wild-honey', 'Honey', 25000.00, '500ml jar', 'Raw, unfiltered honey harvested by community beekeepers in Rukungiri.', 'Our Pure Wild Honey is harvested by community beekeepers trained through BetterLife''s green-skills program. It is raw, unfiltered, and never heat-treated — so it keeps its natural enzymes, pollen, and rich amber flavour. Every jar sold supports beekeeping livelihoods for youth and women in refugee-hosting districts.', 'assets/img/product-honey.jpg', 1, 1),
('Golden Comb Honey', 'golden-comb-honey', 'Honey', 32000.00, '400g jar', 'Honeycomb harvested whole for the purest, most natural honey experience.', 'For the purist, our Golden Comb Honey is harvested whole from the hive and bottled exactly as nature made it. Chew the comb or let it melt over warm bread — either way, you are tasting honey the way it was meant to be enjoyed, straight from our apiaries around the BetterLife Farm.', 'assets/img/product-honey-2.jpg', 0, 2),
('Traditional Ghee', 'traditional-ghee', 'Ghee', 30000.00, '500ml jar', 'Slow-simmered clarified butter made from grass-fed cows on our model farm.', 'Made using a traditional slow-simmer method, our Ghee is produced from the milk of grass-fed cows raised on our Rukungiri model farm. Rich, nutty, and free of additives, it is a staple for cooking, baking, and traditional recipes — and a direct product of the dairy-farming skills we teach local farmers.', 'assets/img/product-ghee.jpg', 1, 3),
('Cultured Butter Ghee', 'cultured-butter-ghee', 'Ghee', 35000.00, '350ml jar', 'A deeper, cultured flavour profile made from fermented cream.', 'Our Cultured Butter Ghee starts with fermented cream for a deeper, tangier flavour before it is slow-clarified into golden ghee. It is a small-batch product made by our farm cooperative, prized by chefs for its aroma and long shelf life without refrigeration.', 'assets/img/product-ghee-2.jpg', 0, 4),
('Natural Set Yoghurt', 'natural-set-yoghurt', 'Yoghurt', 8000.00, '500ml tub', 'Thick, creamy, naturally cultured yoghurt with no added preservatives.', 'Our Natural Set Yoghurt is made fresh from farm milk and live cultures — no preservatives, no shortcuts. Thick, tangy, and creamy, it is produced daily in small batches by our dairy cooperative and cooled for delivery to keep it as fresh as the farm it came from.', 'assets/img/product-yogurt.jpg', 1, 5),
('Fruit-Infused Yoghurt', 'fruit-infused-yoghurt', 'Yoghurt', 9000.00, '500ml tub', 'Our natural yoghurt swirled with locally grown seasonal fruit.', 'We blend our Natural Set Yoghurt with seasonal fruit grown by partner smallholder farmers — passion fruit, mango, or berries depending on the season. It is a naturally sweetened treat that supports two livelihoods in every tub: dairy and fruit farming.', 'assets/img/product-yogurt-2.jpg', 0, 6);

-- ----------------------------------------------------------------------
-- Team members (leadership / staff / board / volunteer)
-- ----------------------------------------------------------------------
CREATE TABLE team_members (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  role VARCHAR(150) NOT NULL,
  category ENUM('leadership','staff','board','volunteer') DEFAULT 'staff',
  bio TEXT,
  photo VARCHAR(255),
  email VARCHAR(150),
  facebook VARCHAR(255),
  twitter VARCHAR(255),
  linkedin VARCHAR(255),
  sort_order INT DEFAULT 0,
  status TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO team_members (name, role, category, bio, photo, sort_order) VALUES
('Ayebare Denise', 'Executive Director & Founder', 'leadership', 'Founding leader of BetterLife International, driving the organization''s youth-led vision across five countries.', NULL, 1),
('Ahabwe Samuel', 'Head of Finance', 'leadership', 'Oversees financial strategy and accountability across all BetterLife country programs.', NULL, 2),
('Douglas Drake Onen', 'Chief Operations Officer', 'leadership', 'Leads day-to-day operations, ensuring programs are delivered efficiently across all regions.', NULL, 3),
('Kaitesi Shallon', 'Administrator', 'staff', 'Manages administration and coordination across the BetterLife team.', NULL, 4),
('Kembabazi Charity', 'Monitoring and Evaluation Specialist', 'staff', 'Tracks program impact and ensures data-driven decision making.', NULL, 5),
('Comboni Daniel Mutabaazi', 'Country Manager, Tanzania', 'staff', 'Leads BetterLife programs and partnerships in Tanzania.', NULL, 6),
('Alec Becky', 'Country Manager, South Sudan', 'staff', 'Leads BetterLife programs and partnerships in South Sudan.', NULL, 7),
('Victoria Kinobe', 'Country Manager, DR Congo', 'staff', 'Leads BetterLife programs and partnerships in the Democratic Republic of Congo.', NULL, 8),
('Agaba Ian', 'Programs Coordinator', 'staff', 'Coordinates cross-country program design and delivery.', NULL, 9),
('Desmond Dorvlo', 'Country Manager, Ghana', 'staff', 'Leads BetterLife programs and partnerships in Ghana.', NULL, 10),
('Nkajja Janice', 'Head of Communications & Advocacy', 'staff', 'Leads storytelling, media and advocacy for BetterLife International.', NULL, 11),
('Ainembabazi Desire', 'Outreach Coordinator', 'staff', 'Coordinates community outreach and partner engagement.', NULL, 12),
('Apili Cynthia Esther', 'Volunteer', 'volunteer', 'Dedicated volunteer supporting BetterLife community programs.', NULL, 13),
('Odong Vincent', 'Volunteer', 'volunteer', 'Dedicated volunteer supporting BetterLife community programs.', NULL, 14),
('Ashaba Donald', 'Volunteer', 'volunteer', 'Dedicated volunteer supporting BetterLife community programs.', NULL, 15),
('Ninsiima Alison', 'Volunteer', 'volunteer', 'Dedicated volunteer supporting BetterLife community programs.', NULL, 16),
('Duncan K', 'Volunteer', 'volunteer', 'Dedicated volunteer supporting BetterLife community programs.', NULL, 17),
('Akello Melisa', 'Volunteer', 'volunteer', 'Dedicated volunteer supporting BetterLife community programs.', NULL, 18),
('Hillary Clinton', 'Board Director', 'board', 'Hillary Rodham Clinton is an American politician, diplomat, lawyer, and author. She served as the First Lady of the United States from 1993 to 2001, championing healthcare reform and children''s initiatives. She represented New York as a U.S. Senator from 2001 to 2009 and served as Secretary of State from 2009 to 2013, promoting women''s rights and global diplomacy.', 'assets/img/board-hillary-clinton.jpg', 19),
('Felicia Davis', 'Board Member', 'board', 'With over two decades of experience, Felicia Davis has been at the forefront of integrating sustainability into educational institutions. In 2016, she co-founded the HBCU Green Fund, a nonprofit dedicated to financing sustainable building projects at Historically Black Colleges and Universities.', 'assets/img/board-felicia-davis.jpg', 20),
('Mohamed Nasheed', 'Board Member', 'board', 'In 2008, Nasheed became the first democratically elected president of the Maldives. His administration focused on democratic reforms, human rights and environmental sustainability, and he gained international acclaim for organizing the world''s first underwater cabinet meeting on climate change.', 'assets/img/board-nasheed.jpg', 21),
('Alyse Nelson', 'Board Member', 'board', 'Alyse Nelson co-founded Vital Voices Global Partnership in 1999 alongside former U.S. Secretary of State Hillary Rodham Clinton and former Secretary of State Madeleine Albright, promoting the advancement of women worldwide.', 'assets/img/board-alyse-nelson.jpg', 22),
('Bob Natifu', 'Board Member', 'board', 'As Commissioner for Climate Change in Uganda''s Ministry of Water and Environment, Bob Natifu coordinates national climate response strategies and serves as a UN CC:Learn Ambassador.', 'assets/img/board-bob-natifu.jpg', 23);

-- ----------------------------------------------------------------------
-- Testimonials
-- ----------------------------------------------------------------------
CREATE TABLE testimonials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  quote TEXT NOT NULL,
  author_name VARCHAR(150) NOT NULL,
  author_role VARCHAR(150),
  photo VARCHAR(255),
  sort_order INT DEFAULT 0,
  status TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO testimonials (quote, author_name, author_role, photo, sort_order) VALUES
('BetterLife International is changing lives at every level, empowering youth, building resilient communities, and inspiring hope where it''s needed most.', 'Hillary Clinton', 'Board Director', 'assets/img/board-hillary-clinton.jpg', 1),
('The training I received in organic dairy farming changed everything for my family. Today I supply milk for the farm''s own ghee and yoghurt.', 'A Farmer, Rukungiri Cooperative', 'BetterLife Farm Partner', NULL, 2),
('Beekeeping gave me an income for the first time since I fled home. Every jar of honey we sell carries our story forward.', 'A Beekeeper', 'Refugee-led Livelihoods Program', NULL, 3);

-- ----------------------------------------------------------------------
-- Blog
-- ----------------------------------------------------------------------
CREATE TABLE blog_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO blog_categories (name, slug) VALUES
('Climate Change', 'climate-change'),
('Agriculture', 'agriculture'),
('Politics', 'politics'),
('Uncategorized', 'uncategorized');

CREATE TABLE blog_posts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  category_id INT DEFAULT NULL,
  excerpt VARCHAR(500),
  content LONGTEXT,
  featured_image VARCHAR(255),
  author VARCHAR(100) DEFAULT 'Admin',
  status ENUM('draft','published') DEFAULT 'draft',
  views INT DEFAULT 0,
  published_at DATETIME DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES blog_categories(id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT INTO blog_posts (title, slug, category_id, excerpt, content, featured_image, author, status, published_at) VALUES
('Climate Justice: Beyond Financial Transactions to Protect the Vulnerable', 'climate-justice-beyond-financial-transactions', 1,
'Climate justice is not merely about financial transactions to protect the environment. It embodies the fight for fairness for the communities least responsible for the crisis, yet most affected by it.',
'<p>Climate justice is not merely about financial transactions to protect the environment. It embodies the fight for fairness for the communities least responsible for the climate crisis, yet most affected by its consequences.</p><p>Across Uganda, South Sudan, Tanzania, Ghana and the DR Congo, the families BetterLife International works with are on the frontline of a crisis they did little to cause — unpredictable rains, shrinking grazing land, and displacement driven by environmental stress. True climate justice means channelling resources, skills and decision-making power directly to these communities, not just discussing figures in international finance rooms.</p><p>That is why our approach pairs climate education with tangible livelihoods: green skills training, climate-smart agriculture, and renewable energy access such as biogas and boreholes. When a family can irrigate a field with solar power or cook with biogas instead of charcoal, climate justice becomes something they can hold in their hands, not just a policy conversation.</p><p>We continue to advocate for financing that reaches the last mile — the refugee-hosting districts and rural communities that are too often left out of climate finance altogether.</p>',
'assets/img/blog-climate-justice.jpg', 'Admin', 'published', '2024-10-26 09:00:00'),

('Climate Change: Amplifying Extreme Weather and Displacement', 'climate-change-extreme-weather-displacement', 4,
'Climate change significantly heightens the risks of extreme weather events — storms, floods, wildfires — and the displacement that follows them.',
'<p>Climate change significantly heightens the risks of extreme weather events — such as storms, floods, wildfires, and droughts — and the human displacement that so often follows in their wake.</p><p>In the regions where BetterLife International works, we see this firsthand: families who already fled conflict now facing floods that destroy their new farmland, or droughts that fail the very crops meant to rebuild their food security. Displacement driven by climate stress is compounding an already fragile humanitarian situation across Sub-Saharan Africa.</p><p>Our response combines immediate resilience-building — climate-smart agriculture, water access through boreholes, and renewable energy — with long-term climate education through our Green Libraries and Eco Clubs, reaching thousands of students with the knowledge to adapt and lead.</p><p>Addressing climate-driven displacement requires both humanitarian urgency and sustained investment in local adaptation. It is a challenge we are meeting one community, one borehole, one biogas plant at a time.</p>',
'assets/img/blog-extreme-weather.jpg', 'Admin', 'published', '2024-10-14 09:00:00'),

('Congratulations to Dr. Okello Sharon Nagenjwa on Her PhD and Outstanding Community Impact!', 'dr-okello-sharon-nagenjwa-phd', 2,
'We are happy to celebrate the remarkable journey of Dr. Okello Sharon Nagenjwa, who was recently awarded her PhD after years of dedicated research and community impact.',
'<p>We are happy to celebrate the remarkable journey of Dr. Okello Sharon Nagenjwa, who was recently awarded her PhD after years of dedicated research and community impact work across Uganda''s agricultural and refugee-hosting communities.</p><p>Dr. Nagenjwa''s research has directly informed several of BetterLife International''s programs in sustainable agriculture and climate-smart farming, helping bridge the gap between academic research and practical, farmer-led solutions.</p><p>Her achievement reflects the spirit of BetterLife International''s mission: that those closest to a challenge are often best placed to solve it. We congratulate Dr. Nagenjwa and look forward to continued collaboration as we grow our agricultural and livelihoods programs, including the BetterLife Farm.</p>',
'assets/img/blog-sharon-phd.png', 'Admin', 'published', '2024-09-29 09:00:00'),

('The Importance of Civic Education in Uganda: An Evaluative Analysis', 'importance-of-civic-education-in-uganda', 3,
'Civic education plays a crucial role in the development of any democratic society, yet in Uganda it remains under-prioritized in many communities.',
'<p>Civic education plays a crucial role in the development of any democratic society, yet in Uganda it remains under-prioritized in many communities, particularly among youth and displaced populations.</p><p>Through our Climate Education and Awareness programme, BetterLife International has seen how civic literacy and environmental literacy reinforce one another — young people who understand their rights and responsibilities are also more likely to take an active role in protecting their environment and holding leaders accountable for it.</p><p>We believe civic education should be woven into every level of schooling, not treated as an afterthought. It is one of the quiet foundations of the resilient, self-reliant communities we work to build.</p>',
'assets/img/blog-civic-education.jpg', 'Admin', 'published', '2024-09-20 09:00:00'),

('The Naked Truth of Corruption: A Critical Analysis of Uganda''s Socioeconomic State and Youth-Led Activism', 'naked-truth-of-corruption-uganda', 3,
'Uganda is currently grappling with a critical socio-political issue: rampant corruption, which has become deeply embedded and is fuelling youth-led activism.',
'<p>Uganda is currently grappling with a critical socio-political issue: rampant corruption, which has become deeply embedded in public institutions and continues to undermine development outcomes for ordinary citizens.</p><p>At BetterLife International, we see the downstream effects of this every day — resources that should reach vulnerable communities are too often diverted, delayed, or diminished. This is part of why we are youth-led: young people across our five countries of operation are refusing to accept corruption, inequality, or environmental destruction as the status quo.</p><p>Youth-led activism, paired with transparent, community-owned programs like our farm cooperatives and green-skills training, is one practical way to build systems that are accountable from the ground up.</p>',
'assets/img/blog-corruption.jpg', 'Admin', 'published', '2023-09-06 09:00:00');

-- ----------------------------------------------------------------------
-- Contact messages
-- ----------------------------------------------------------------------
CREATE TABLE contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  email VARCHAR(150) NOT NULL,
  phone VARCHAR(50),
  subject VARCHAR(200),
  message TEXT NOT NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------
-- Newsletter subscribers
-- ----------------------------------------------------------------------
CREATE TABLE newsletter_subscribers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(150) NOT NULL UNIQUE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ----------------------------------------------------------------------
-- Projects ("Our Work" > Projects)
-- ----------------------------------------------------------------------
CREATE TABLE projects (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  category VARCHAR(150),
  description TEXT,
  image VARCHAR(255),
  sort_order INT DEFAULT 0,
  status TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO projects (title, slug, category, description, image, sort_order) VALUES
('Climate Education and Youth Empowerment', 'climate-education-youth-empowerment', 'Climate Education', 'Engaging students in debates, public speaking and Green Libraries that build climate literacy and leadership skills across partner schools.', 'assets/img/project-climate-education.jpg', 1),
('BetterLife Spring Project', 'betterlife-spring-project', 'Renewable Energy & Agriculture', 'Sustainable Powered Resilient Irrigation for Next Generation Farming (SPRING) — giving South Sudanese farmers access to renewable-energy-powered irrigation so they can grow food despite unpredictable weather.', 'assets/img/project-spring.jpg', 2),
('SMILES', 'smiles', 'Empowering Refugees & Host Communities', 'A programme supporting social cohesion and livelihoods between refugee and host communities through shared training, resources and community dialogue.', 'assets/img/project-smiles.jpg', 3),
('BetterLife Renewable Pathways', 'betterlife-renewable-pathways', 'Plastic Pollution in Uganda', 'Turning plastic waste into fuel and other reusable materials, reducing pollution while creating green income opportunities for youth.', 'assets/img/project-renewable-pathways.jpg', 4),
('BetterLife Agro-Tourism Farm', 'betterlife-agro-tourism-farm', 'Agriculture in Uganda', 'Our Rukungiri model farm training centre for organic dairy and crop farming — and the source of the honey, ghee and yoghurt sold under the BetterLife Farm brand.', 'assets/img/project-agro-tourism.jpg', 5),
('Empowering Refugee Women and IDPs Through Smart and Sustainable Agriculture', 'empowering-refugee-women-idps-agriculture', 'Driving Local Solutions for a Global Future', 'Equipping refugee and internally displaced women with climate-smart agricultural skills and small-scale farming techniques to build food security and income.', 'assets/img/project-women-idps.jpg', 6),
('Soilla App', 'soilla-app', 'AI & IoT Technology', 'A web and mobile platform that leverages AI and IoT technologies to help smallholder farmers monitor soil health and make better-informed farming decisions.', 'assets/img/project-soilla-app.jpg', 7);

-- ----------------------------------------------------------------------
-- Impact stories ("Our Work" > Impact & Reports)
-- ----------------------------------------------------------------------
CREATE TABLE impact_stories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  caption VARCHAR(255),
  image VARCHAR(255),
  sort_order INT DEFAULT 0,
  status TINYINT(1) DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO impact_stories (title, caption, image, sort_order) VALUES
('Empowering Refugee Women', 'Skills training that builds real livelihoods', 'assets/img/impact-story-1.jpg', 1),
('Empowering Refugee Communities', 'Resilience built together, family by family', 'assets/img/impact-story-2.jpg', 2),
('Building Our Demo Farm', 'How the BetterLife Agro-Tourism Farm began', 'assets/img/impact-story-3.jpg', 3);

-- ----------------------------------------------------------------------
-- Annual / impact reports (downloadable PDFs)
-- ----------------------------------------------------------------------
CREATE TABLE reports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  year VARCHAR(10),
  file_url VARCHAR(500),
  sort_order INT DEFAULT 0,
  status TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

INSERT INTO reports (title, year, file_url, sort_order) VALUES
('BetterLife International Annual Report', '2023', 'https://assets.zyrosite.com/m5KvNaBjBKtjxV5x/betterlife-international-annual-report-20223-AoPvD3b9PLsZ7ryj.pdf', 1),
('BetterLife International Annual Report', '2022', 'https://assets.zyrosite.com/m5KvNaBjBKtjxV5x/betterlife-international-annual-report-2022-YbNvbWgWWESzw1nz.pdf', 2);

-- ----------------------------------------------------------------------
-- Orders & payments (BetterLife Farm checkout — Pesapal: card + mobile money)
-- ----------------------------------------------------------------------
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_ref VARCHAR(40) NOT NULL UNIQUE,
  customer_name VARCHAR(150) NOT NULL,
  customer_email VARCHAR(150) NOT NULL,
  customer_phone VARCHAR(50) NOT NULL,
  delivery_location VARCHAR(255) NOT NULL,
  notes TEXT,
  subtotal DECIMAL(12,2) NOT NULL DEFAULT 0,
  total_amount DECIMAL(12,2) NOT NULL DEFAULT 0,
  currency VARCHAR(10) NOT NULL DEFAULT 'UGX',
  status ENUM('pending','paid','failed','cancelled') NOT NULL DEFAULT 'pending',
  pesapal_tracking_id VARCHAR(100),
  pesapal_merchant_ref VARCHAR(100),
  paid_at DATETIME DEFAULT NULL,
  admin_notified TINYINT(1) DEFAULT 0,
  receipt_sent TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  product_id INT DEFAULT NULL,
  product_name VARCHAR(150) NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  line_total DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;
