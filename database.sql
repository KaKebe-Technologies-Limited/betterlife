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
('favicon', 'assets/img/favicon.png'),
('hero_kicker', 'Founded in Uganda. Working across five African countries.'),
('hero_title', 'Led by people who understand what it means to begin again.'),
('hero_subtitle', 'BetterLife works with women, young people, refugees, displaced families and farming communities to turn climate pressure into practical action: food people can grow, skills they can earn from, cleaner energy and stronger routes to market.'),
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
('about_who_text', 'Denise Ayebare founded BetterLife International in Uganda in 2021. She was nineteen, and the organisation began with USD 200 and a small group of young people who were tired of watching communities receive short-term help while the conditions keeping them vulnerable remained unchanged.\n\nThey had seen farmers lose a season and start again with nothing. They had seen women carry the weight of food, water and household survival without access to finance or decision-making. They had seen young people complete training and still have no tools, customers or way into work. They had also seen how much knowledge already existed inside communities, often overlooked by programmes designed from far away.\n\nBetterLife was created to work differently.\n\nWhat began as a small youth-led initiative in Uganda now works across Uganda, South Sudan, Tanzania, Ghana and the Democratic Republic of Congo. The organisation brings together climate-resilient agriculture, livelihoods, clean energy, water, restoration, education, technology and market access.\n\nThe work has grown, but the starting point has remained the same: people closest to a problem must have a real hand in defining it, designing the response and deciding what success looks like.'),
('mission_text', 'To work with communities to build the food systems, livelihoods, knowledge and local institutions they need to live with greater security and dignity in a changing climate.'),
('vision_text', 'Communities with the power, resources and opportunity to shape their own future.'),
('about_image', 'assets/img/about-real-1.jpg'),
('farm_title', 'BetterLife Agro Tourism Farm'),
('farm_tagline', 'From immediate support to lasting independence'),
('farm_text', 'BetterLife Agro Tourism Farm is where our work in agriculture, clean energy and livelihoods meets production and sales.\n\nThe farm demonstrates solar-powered irrigation, greenhouse farming, dairy production, beekeeping and livestock rearing. It also creates a route for farmers trained by BetterLife International to supply produce for processing and sale through BetterLife Agro Tourism Farm Ltd.\n\nOur products include BetterLife Honey, Ghee and Vanilla Yoghurt. Each one is part of a wider value chain connecting knowledge, production and household income.'),
('farm_image', 'assets/img/farm-field-1.jpg'),
('address', 'Rukungiri, Uganda'),
('phone', '+256 770 933 286'),
('email', 'info@betterlifeint.org'),
('shop_email', 'farm@betterlifeint.org'),
('facebook', 'https://facebook.com/betterlifeintl'),
('twitter', 'https://twitter.com/betterlifeintl'),
('instagram', 'https://instagram.com/betterlifeintl'),
('linkedin', 'https://linkedin.com/company/betterlifeintl'),
('youtube', 'https://youtube.com/@betterlifeintl'),
('footer_about', 'BetterLife International works with communities across five African countries to build food security, livelihoods and practical climate resilience.'),
('map_embed', ''),
('board_quote', 'BetterLife International is changing lives at every level, empowering youth, building resilient communities, and inspiring hope where it''s needed most.'),
('board_quote_author', 'BetterLife International Board of Directors'),
('admin_alert_email', 'ot.sedrick@gmail.com'),
('admin_alert_cc', ''),
('smtp_host', 'smtp.gmail.com'),
('smtp_port', '587'),
('smtp_username', ''),
('smtp_app_password', ''),
('smtp_from_name', 'BetterLife International'),
('pesapal_sandbox', '0'),
('pesapal_consumer_key', ''),
('pesapal_consumer_secret', ''),
('maintenance_mode', '0'),
('maintenance_message', 'We are carrying out a short update to improve the site. Please check back again shortly.');
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

INSERT INTO stats (label, value, sort_order) VALUES
('People reached in 2025', '112,430', 1),
('Farmers supported', '18,900', 2),
('Refugees and host-community members reached', '41,200', 3),
('Students engaged in climate education', '4,580+', 4),
('Green Libraries and Eco Labs', '20+', 5),
('Household biogas systems', '48+', 6),
('Community boreholes', '65', 7),
('Tree seedlings raised', '50,000+', 8);

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
('Climate-Resilient Agriculture and Food Security', 'climate-resilient-agriculture', '', 'We help farmers and families grow food in the face of unreliable rain, poor soils and limited land. The work includes demonstration gardens, composting, mulching, drought-tolerant crops, agroforestry, greenhouse farming, irrigation, poultry, aquaculture and beekeeping.', '', 'assets/img/betterlifeint-source/programs/program-photo-2.jpg', 'leaf', 1),
('Green Skills, Livelihoods and Markets', 'green-skills-livelihoods', '', 'We train people in skills that respond to local opportunities, then help them move beyond training through savings groups, enterprise support, finance and market connections. Our work spans agriculture, carpentry, tailoring, barbering, poultry, weaving and solar technology.', '', 'assets/img/project-smiles.jpg', 'users', 2),
('Climate Education and Youth Leadership', 'climate-education-youth-leadership', '', 'Through Green Libraries, Eco Labs, school clubs, youth centres, debates, Climate Academies and innovation challenges, children and young people gain the knowledge, tools and confidence to take part in the decisions shaping their future.', '', 'assets/img/betterlifeint-source/programs/program-photo-4.jpg', 'file-text', 3),
('Clean Energy, Water and Restoration', 'clean-energy-water-restoration', '', 'We work with communities on tree nurseries, agroforestry, biogas, briquettes, waste recovery and water access. Each solution is designed to ease pressure on both households and the environment.', '', 'assets/img/betterlifeint-source/programs/program-photo-1.jpg', 'leaf', 4),
('Digital Innovation for Farmers', 'digital-innovation', '', 'Soilla and Agribusiness Connekt bring soil advice, climate information, services, finance and markets closer to farmers. The technology is paired with face-to-face support so that information becomes something people can use.', '', 'assets/img/project-soilla-app.jpg', 'trending-up', 5);

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
('Pure Wild Honey', 'pure-wild-honey', 'Honey', 25000.00, '500ml jar', 'Raw, unfiltered honey harvested by community beekeepers in Rukungiri.', 'Our Pure Wild Honey is harvested by community beekeepers trained through BetterLife''s green-skills program. It is raw, unfiltered, and never heat-treated — so it keeps its natural enzymes, pollen, and rich amber flavour. Every jar sold supports beekeeping livelihoods for youth and women in refugee-hosting districts.', 'assets/img/product-honey-real.jpg', 1, 1),
('Golden Comb Honey', 'golden-comb-honey', 'Honey', 32000.00, '400g jar', 'Honeycomb harvested whole for the purest, most natural honey experience.', 'For the purist, our Golden Comb Honey is harvested whole from the hive and bottled exactly as nature made it. Chew the comb or let it melt over warm bread — either way, you are tasting honey the way it was meant to be enjoyed, straight from our apiaries around the BetterLife Farm.', 'assets/img/product-honey-real.jpg', 0, 2),
('Traditional Ghee', 'traditional-ghee', 'Ghee', 30000.00, '500ml jar', 'Slow-simmered clarified butter made from grass-fed cows on our model farm.', 'Made using a traditional slow-simmer method, our Ghee is produced from the milk of grass-fed cows raised on our Rukungiri model farm. Rich, nutty, and free of additives, it is a staple for cooking, baking, and traditional recipes — and a direct product of the dairy-farming skills we teach local farmers.', 'assets/img/product-ghee-real.jpg', 1, 3),
('Cultured Butter Ghee', 'cultured-butter-ghee', 'Ghee', 35000.00, '350ml jar', 'A deeper, cultured flavour profile made from fermented cream.', 'Our Cultured Butter Ghee starts with fermented cream for a deeper, tangier flavour before it is slow-clarified into golden ghee. It is a small-batch product made by our farm cooperative, prized by chefs for its aroma and long shelf life without refrigeration.', 'assets/img/product-ghee-real.jpg', 0, 4),
('Natural Set Yoghurt', 'natural-set-yoghurt', 'Yoghurt', 8000.00, '500ml tub', 'Thick, creamy, naturally cultured yoghurt with no added preservatives.', 'Our Natural Set Yoghurt is made fresh from farm milk and live cultures — no preservatives, no shortcuts. Thick, tangy, and creamy, it is produced daily in small batches by our dairy cooperative and cooled for delivery to keep it as fresh as the farm it came from.', 'assets/img/product-yogurt.jpg', 1, 5),
('Fruit-Infused Yoghurt', 'fruit-infused-yoghurt', 'Yoghurt', 9000.00, '500ml tub', 'Our natural yoghurt swirled with locally grown seasonal fruit.', 'We blend our Natural Set Yoghurt with seasonal fruit grown by partner smallholder farmers — passion fruit, mango, or berries depending on the season. It is a naturally sweetened treat that supports two livelihoods in every tub: dairy and fruit farming.', 'assets/img/product-yogurt-2.jpg', 0, 6),
('BetterLife Organic Boost', 'betterlife-organic-boost', 'Farm Inputs', 45000.00, '20L jerry can', 'Organic liquid fertilizer that improves soil fertility and boosts crop yield naturally.', 'BetterLife Organic Boost is an eco-friendly, organic liquid fertilizer produced at BetterLife Agro Tourism Farm. Safe for plants, people and the planet, it improves soil fertility and boosts crop yield naturally — the same climate-smart, sustainable approach we teach farmers across our programs, now bottled for your own garden or farm.', 'assets/img/product-organic-boost.jpg', 1, 7);

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
('Ayebare Denise', 'Executive Director & Founder', 'leadership', 'Denise Ayebare is a Ugandan lawyer, climate justice advocate and youth leader who founded BetterLife International at nineteen. She currently serves as Secretary for External Affairs of Uganda''s National Youth Council and has represented young people in regional and international climate-policy processes. Denise previously served as Prime Minister of Uganda''s Fifth National Youth Parliament and has contributed to youth leadership, climate resilience and locally led development across Africa. Recognised in the Forbes Africa 30 Under 30 Class of 2026, she leads BetterLife''s work with women, refugees, young people and vulnerable communities, connecting sustainable livelihoods with policy influence and long-term institutional change.', NULL, 1),
('Ahabwe Samuel', 'Head of Finance', 'leadership', 'Ahabwe Samuel serves as Head of Finance at BetterLife International, supporting the organisation''s financial planning, accountability and responsible use of resources. He oversees budgeting, expenditure tracking, financial documentation and reporting across BetterLife''s programmes and country operations. Working closely with the executive and programme teams, Samuel helps ensure that organisational resources are aligned with approved activities and deliver value to the communities BetterLife serves. His role is central to strengthening internal controls, supporting donor compliance and improving the financial systems required for sustainable growth.', NULL, 2),
('Douglas Drake Onen', 'Chief Operations Officer', 'leadership', 'Douglas Drake Onen is a legal professional and governance advocate serving as Chief Operations Officer at BetterLife International. Educated at Uganda Christian University, he has gained experience in constitutional governance and legal research, with a particular interest in the rule of law. At BetterLife, Douglas coordinates organisational operations and helps translate institutional strategy into effective programme delivery. He works across country teams to strengthen internal systems, staff coordination, compliance and implementation, bringing structured leadership to an organisation serving communities facing displacement, poverty and climate vulnerability.', NULL, 3),
('Kaitesi Shallon', 'Administrator', 'staff', 'Kaitesi Shallon is an environmental advocate and administrator with experience in youth-led climate action and programme coordination. Before joining BetterLife International, she worked across environmentalism, debate, Rotaract and programme coordination with Cherish Aid Foundation. At BetterLife, Shallon supports the organisation''s daily administration, internal coordination, documentation and communication across teams, helping ensure that meetings, records, schedules and operational processes are organised effectively so programme staff can concentrate on community delivery.', NULL, 4),
('Kembabazi Charity', 'Monitoring and Evaluation Specialist', 'staff', 'Kembabazi Charity is a monitoring and evaluation professional who supports BetterLife International to understand, document and strengthen the results of its work. She is responsible for tracking programme implementation, participant outcomes and organisational learning, working with programme teams to develop indicators, collect and review data, document progress and identify areas requiring improvement. Her role helps BetterLife move beyond reporting activities to understanding whether interventions are producing meaningful changes in household income, food security, resilience and opportunity.', NULL, 5),
('Comboni Daniel Mutabaazi', 'Country Manager, Tanzania', 'staff', 'Comboni Daniel Mutabaazi serves as BetterLife International''s Country Manager in Tanzania, leading the organisation''s national programmes and partnerships. He coordinates community engagement, programme implementation and relationships with local authorities, organisations and other stakeholders. His work helps ensure that BetterLife''s interventions respond to the realities of the communities in which they are delivered, particularly in sustainable agriculture, climate resilience, youth livelihoods and support for vulnerable households.', NULL, 6),
('Alec Becky', 'Country Manager, South Sudan', 'staff', 'Alec Becky serves as BetterLife International''s Country Manager in South Sudan, coordinating programmes that support young people, displaced families and communities affected by economic and climate pressures. Her background includes active participation in competitive debate, including international adjudication, bringing strong communication, analysis and youth-engagement skills to her work. At BetterLife, Becky oversees local implementation, team coordination, stakeholder engagement and programme reporting rooted in community priorities.', NULL, 7),
('Victoria Kinobe', 'Country Manager, DR Congo', 'staff', 'Victoria Kinobe Nakatudde is an engineer with experience in water resources, catchment-management planning, environmental and social impact assessment, and stakeholder engagement. As BetterLife International''s Country Manager for the Democratic Republic of Congo, Victoria brings technical knowledge of water, environmental management and community engagement to the organisation''s programmes, supporting locally appropriate interventions addressing climate vulnerability, livelihoods and sustainable resource use.', NULL, 8),
('Agaba Ian', 'Programs Coordinator', 'staff', 'Agaba Ian is a Ugandan lawyer, climate justice advocate, writer and Pan-African youth leader serving as Programmes Coordinator at BetterLife International. His public work includes climate advocacy, youth engagement, public speaking and discussions on climate-related displacement and its effects on children. At BetterLife, he supports programme design, implementation, coordination and reporting across the organisation''s thematic and country teams.', NULL, 9),
('Desmond Dorvlo', 'Country Manager, Ghana', 'staff', 'Desmond Dorvlo is a Ghanaian communications professional, researcher, writer and debater serving as BetterLife International''s Country Manager in Ghana. He studied at Kwame Nkrumah University of Science and Technology, where he worked as a teaching and research assistant in English. At BetterLife, he brings together research, storytelling and stakeholder engagement to coordinate programmes, partnerships and community relationships, strengthening the organisation''s locally led work in Ghana.', NULL, 10),
('Nkajja Janice', 'Head of Communications & Advocacy', 'staff', 'Nkajja Janice is a Ugandan communicator, public speaker and youth advocate serving as Head of Communications and Advocacy at BetterLife International. She was recognised as a co-winner in the speech category of Uganda''s 2021 National Speech and Debate Championship. At BetterLife, she leads storytelling, media engagement, advocacy communication and the documentation of community experiences, helping ensure the voices of women, refugees, young people and smallholder farmers are represented accurately and with dignity.', NULL, 11),
('Ainembabazi Desire', 'Outreach Coordinator', 'staff', 'Ainembabazi Desire serves as Outreach Coordinator at BetterLife International, helping the organisation build meaningful relationships with communities, schools, young people and institutional partners. Desire supports mobilisation, participant engagement, outreach activities and communication between BetterLife''s programme teams and the people they serve, ensuring that opportunities reach intended beneficiaries and that community feedback informs programme planning and implementation.', NULL, 12),
('Sedrick Otolo', 'Board Member', 'board', 'Bio coming soon — this profile will be updated shortly.', NULL, 19),
('Felicia Davis', 'Board Member', 'board', 'With over two decades of experience, Felicia Davis has been at the forefront of integrating sustainability into educational institutions. In 2016, she co-founded the HBCU Green Fund, a nonprofit dedicated to financing sustainable building projects at Historically Black Colleges and Universities.', 'assets/img/board-felicia-davis.jpg', 20),
('Mohamed Nasheed', 'Board Member', 'board', 'In 2008, Nasheed became the first democratically elected president of the Maldives. His administration focused on democratic reforms, human rights and environmental sustainability, and he gained international acclaim for organizing the world''s first underwater cabinet meeting on climate change.', 'assets/img/board-nasheed.jpg', 21),
('Pamela Musimenta', 'Board Member', 'board', 'Pamela Musimenta is a Ugandan water resources engineer, environmentalist and climate justice advocate working with the Ministry of Water and Environment. Her professional interests span water resources planning and regulation, hydraulic engineering, environmental compliance and access to clean, safe water. A UNFCCC advocate and beVisioneers Fellow, Pamela leads community-based work addressing deforestation, land degradation, soil erosion and flood risk through tree nurseries and soil and water conservation. She brings to BetterLife International''s Board a combination of government experience, technical expertise and grassroots climate leadership, strengthening the organisation''s work in renewable energy, sustainable agriculture, water security and community resilience across Africa.', NULL, 22),
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
('The training I received in organic dairy farming changed everything for my family. Today I supply milk for the farm''s own ghee and yoghurt.', 'A Farmer, Rukungiri Cooperative', 'BetterLife Farm Partner', NULL, 1),
('Beekeeping gave me an income for the first time since I fled home. Every jar of honey we sell carries our story forward.', 'A Beekeeper', 'Refugee-led Livelihoods Program', NULL, 2);

-- ----------------------------------------------------------------------
-- Blog
-- ----------------------------------------------------------------------
CREATE TABLE blog_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE
) ENGINE=InnoDB;

INSERT INTO blog_categories (name, slug) VALUES
('From the Field', 'from-the-field'),
('Climate and Food', 'climate-and-food'),
('Work and Enterprise', 'work-and-enterprise'),
('Young People and Ideas', 'young-people-and-ideas'),
('Partnerships and News', 'partnerships-and-news');

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
('Climate Justice: Beyond Financial Transactions to Protect the Vulnerable', 'climate-justice-beyond-financial-transactions', 2,
'Climate justice is not merely about financial transactions to protect the environment. It embodies the fight for fairness for the communities least responsible for the crisis, yet most affected by it.',
'<p>Climate justice is not merely about financial transactions to protect the environment. It embodies the fight for fairness for the communities least responsible for the climate crisis, yet most affected by its consequences.</p><p>Across Uganda, South Sudan, Tanzania, Ghana and the DR Congo, the families BetterLife International works with are on the frontline of a crisis they did little to cause — unpredictable rains, shrinking grazing land, and displacement driven by environmental stress. True climate justice means channelling resources, skills and decision-making power directly to these communities, not just discussing figures in international finance rooms.</p><p>That is why our approach pairs climate education with tangible livelihoods: green skills training, climate-smart agriculture, and renewable energy access such as biogas and boreholes. When a family can irrigate a field with solar power or cook with biogas instead of charcoal, climate justice becomes something they can hold in their hands, not just a policy conversation.</p><p>We continue to advocate for financing that reaches the last mile — the refugee-hosting districts and rural communities that are too often left out of climate finance altogether.</p>',
'assets/img/blog-climate-justice.jpg', 'Admin', 'published', '2024-10-26 09:00:00'),

('Climate Change: Amplifying Extreme Weather and Displacement', 'climate-change-extreme-weather-displacement', 2,
'Climate change significantly heightens the risks of extreme weather events — storms, floods, wildfires — and the displacement that follows them.',
'<p>Climate change significantly heightens the risks of extreme weather events — such as storms, floods, wildfires, and droughts — and the human displacement that so often follows in their wake.</p><p>In the regions where BetterLife International works, we see this firsthand: families who already fled conflict now facing floods that destroy their new farmland, or droughts that fail the very crops meant to rebuild their food security. Displacement driven by climate stress is compounding an already fragile humanitarian situation across Sub-Saharan Africa.</p><p>Our response combines immediate resilience-building — climate-smart agriculture, water access through boreholes, and renewable energy — with long-term climate education through our Green Libraries and Eco Clubs, reaching thousands of students with the knowledge to adapt and lead.</p><p>Addressing climate-driven displacement requires both humanitarian urgency and sustained investment in local adaptation. It is a challenge we are meeting one community, one borehole, one biogas plant at a time.</p>',
'assets/img/blog-extreme-weather.jpg', 'Admin', 'published', '2024-10-14 09:00:00'),

('Congratulations to Dr. Okello Sharon Nagenjwa on Her PhD and Outstanding Community Impact!', 'dr-okello-sharon-nagenjwa-phd', 4,
'We are happy to celebrate the remarkable journey of Dr. Okello Sharon Nagenjwa, who was recently awarded her PhD after years of dedicated research and community impact.',
'<p>We are happy to celebrate the remarkable journey of Dr. Okello Sharon Nagenjwa, who was recently awarded her PhD after years of dedicated research and community impact work across Uganda''s agricultural and refugee-hosting communities.</p><p>Dr. Nagenjwa''s research has directly informed several of BetterLife International''s programs in sustainable agriculture and climate-smart farming, helping bridge the gap between academic research and practical, farmer-led solutions.</p><p>Her achievement reflects the spirit of BetterLife International''s mission: that those closest to a challenge are often best placed to solve it. We congratulate Dr. Nagenjwa and look forward to continued collaboration as we grow our agricultural and livelihoods programs, including the BetterLife Farm.</p>',
'assets/img/blog-sharon-phd.png', 'Admin', 'published', '2024-09-29 09:00:00'),

('The Importance of Civic Education in Uganda: An Evaluative Analysis', 'importance-of-civic-education-in-uganda', 4,
'Civic education plays a crucial role in the development of any democratic society, yet in Uganda it remains under-prioritized in many communities.',
'<p>Civic education plays a crucial role in the development of any democratic society, yet in Uganda it remains under-prioritized in many communities, particularly among youth and displaced populations.</p><p>Through our Climate Education and Awareness programme, BetterLife International has seen how civic literacy and environmental literacy reinforce one another — young people who understand their rights and responsibilities are also more likely to take an active role in protecting their environment and holding leaders accountable for it.</p><p>We believe civic education should be woven into every level of schooling, not treated as an afterthought. It is one of the quiet foundations of the resilient, self-reliant communities we work to build.</p>',
'assets/img/blog-civic-education.jpg', 'Admin', 'published', '2024-09-20 09:00:00'),

('The Naked Truth of Corruption: A Critical Analysis of Uganda''s Socioeconomic State and Youth-Led Activism', 'naked-truth-of-corruption-uganda', 4,
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
