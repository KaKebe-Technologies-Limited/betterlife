
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
DROP TABLE IF EXISTS `products`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `slug` varchar(170) NOT NULL,
  `category` varchar(80) NOT NULL,
  `price` decimal(10,2) DEFAULT NULL,
  `unit` varchar(50) DEFAULT NULL,
  `short_desc` varchar(255) DEFAULT NULL,
  `description` longtext DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `products` WRITE;
/*!40000 ALTER TABLE `products` DISABLE KEYS */;
INSERT INTO `products` VALUES (1,'Pure Wild Honey','pure-wild-honey','Honey',25000.00,'500ml jar','Raw, unfiltered honey harvested by community beekeepers in Rukungiri.','Our Pure Wild Honey is harvested by community beekeepers trained through BetterLife\'s green-skills program. It is raw, unfiltered, and never heat-treated — so it keeps its natural enzymes, pollen, and rich amber flavour. Every jar sold supports beekeeping livelihoods for youth and women in refugee-hosting districts.','assets/img/product-honey-real.jpg',1,1,1,'2026-08-22 00:55:00','2026-08-25 09:11:10'),(2,'Golden Comb Honey','golden-comb-honey','Honey',32000.00,'400g jar','Honeycomb harvested whole for the purest, most natural honey experience.','For the purist, our Golden Comb Honey is harvested whole from the hive and bottled exactly as nature made it. Chew the comb or let it melt over warm bread — either way, you are tasting honey the way it was meant to be enjoyed, straight from our apiaries around the BetterLife Farm.','assets/img/product-honey-real.jpg',0,1,2,'2026-08-22 00:55:00','2026-08-25 09:11:10'),(3,'Traditional Ghee','traditional-ghee','Ghee',30000.00,'500ml jar','Slow-simmered clarified butter made from grass-fed cows on our model farm.','Made using a traditional slow-simmer method, our Ghee is produced from the milk of grass-fed cows raised on our Rukungiri model farm. Rich, nutty, and free of additives, it is a staple for cooking, baking, and traditional recipes — and a direct product of the dairy-farming skills we teach local farmers.','assets/img/product-ghee-real.jpg',1,1,3,'2026-08-22 00:55:00','2026-08-25 09:11:10'),(4,'Cultured Butter Ghee','cultured-butter-ghee','Ghee',35000.00,'350ml jar','A deeper, cultured flavour profile made from fermented cream.','Our Cultured Butter Ghee starts with fermented cream for a deeper, tangier flavour before it is slow-clarified into golden ghee. It is a small-batch product made by our farm cooperative, prized by chefs for its aroma and long shelf life without refrigeration.','assets/img/product-ghee-real.jpg',0,1,4,'2026-08-22 00:55:00','2026-08-25 09:11:10'),(5,'Natural Set Yoghurt','natural-set-yoghurt','Yoghurt',8000.00,'500ml tub','Thick, creamy, naturally cultured yoghurt with no added preservatives.','Our Natural Set Yoghurt is made fresh from farm milk and live cultures — no preservatives, no shortcuts. Thick, tangy, and creamy, it is produced daily in small batches by our dairy cooperative and cooled for delivery to keep it as fresh as the farm it came from.','assets/img/product-yogurt.jpg',1,1,5,'2026-08-22 00:55:00','2026-08-22 00:55:00'),(6,'Fruit-Infused Yoghurt','fruit-infused-yoghurt','Yoghurt',9000.00,'500ml tub','Our natural yoghurt swirled with locally grown seasonal fruit.','We blend our Natural Set Yoghurt with seasonal fruit grown by partner smallholder farmers — passion fruit, mango, or berries depending on the season. It is a naturally sweetened treat that supports two livelihoods in every tub: dairy and fruit farming.','assets/img/product-yogurt-2.jpg',0,1,6,'2026-08-22 00:55:00','2026-08-22 00:55:00'),(7,'BetterLife Organic Boost','betterlife-organic-boost','Farm Inputs',45000.00,'20L jerry can','Organic liquid fertilizer that improves soil fertility and boosts crop yield naturally.','BetterLife Organic Boost is an eco-friendly, organic liquid fertilizer produced at BetterLife Agro Tourism Farm. Safe for plants, people and the planet, it improves soil fertility and boosts crop yield naturally — the same climate-smart, sustainable approach we teach farmers across our programs, now bottled for your own garden or farm.','assets/img/product-organic-boost.jpg',1,1,7,'2026-08-25 09:11:10','2026-08-25 09:11:10');
/*!40000 ALTER TABLE `products` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `team_members`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `team_members` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `role` varchar(150) NOT NULL,
  `category` enum('leadership','staff','board','volunteer') DEFAULT 'staff',
  `bio` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `email` varchar(150) DEFAULT NULL,
  `facebook` varchar(255) DEFAULT NULL,
  `twitter` varchar(255) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `team_members` WRITE;
/*!40000 ALTER TABLE `team_members` DISABLE KEYS */;
INSERT INTO `team_members` VALUES (1,'Ayebare Denise','Executive Director & Founder','leadership','Denise Ayebare is a Ugandan lawyer, climate justice advocate and youth leader who founded BetterLife International at nineteen. She currently serves as Secretary for External Affairs of Uganda\'s National Youth Council and has represented young people in regional and international climate-policy processes. Denise previously served as Prime Minister of Uganda\'s Fifth National Youth Parliament and has contributed to youth leadership, climate resilience and locally led development across Africa. Recognised in the Forbes Africa 30 Under 30 Class of 2026, she leads BetterLife\'s work with women, refugees, young people and vulnerable communities, connecting sustainable livelihoods with policy influence and long-term institutional change.',NULL,NULL,NULL,NULL,NULL,1,1,'2026-08-22 00:55:00'),(2,'Ahabwe Samuel','Head of Finance','leadership','Ahabwe Samuel serves as Head of Finance at BetterLife International, supporting the organisation\'s financial planning, accountability and responsible use of resources. He oversees budgeting, expenditure tracking, financial documentation and reporting across BetterLife\'s programmes and country operations. Working closely with the executive and programme teams, Samuel helps ensure that organisational resources are aligned with approved activities and deliver value to the communities BetterLife serves. His role is central to strengthening internal controls, supporting donor compliance and improving the financial systems required for sustainable growth. Samuel brings a careful, accountability-focused approach to BetterLife\'s mission and its continued expansion across Africa.',NULL,NULL,NULL,NULL,NULL,2,1,'2026-08-22 00:55:00'),(3,'Douglas Drake Onen','Chief Operations Officer','leadership','Douglas Drake Onen is a legal professional and governance advocate serving as Chief Operations Officer at BetterLife International. Educated at Uganda Christian University, he has gained experience in constitutional governance and legal research, with a particular interest in the rule of law. At BetterLife, Douglas coordinates organisational operations and helps translate institutional strategy into effective programme delivery. He works across country teams to strengthen internal systems, staff coordination, compliance and implementation. His legal background supports BetterLife\'s commitment to accountable governance, responsible partnerships and community-centred development. Douglas brings structured leadership to an organisation operating across different national contexts and serving communities facing displacement, poverty and climate vulnerability.',NULL,NULL,NULL,NULL,NULL,3,1,'2026-08-22 00:55:00'),(4,'Kaitesi Shallon','Administrator','staff','Kaitesi Shallon is an environmental advocate and administrator with experience in youth-led climate action and programme coordination. Before joining BetterLife International, she publicly identified her work across environmentalism, debate, Rotaract and programme coordination with Cherish Aid Foundation. At BetterLife, Shallon supports the organisation\'s daily administration, internal coordination, documentation and communication across teams. She helps ensure that meetings, records, schedules and operational processes are organised effectively, enabling programme staff to concentrate on community delivery. Her combination of administrative ability and commitment to climate justice strengthens BetterLife\'s youth-led character and its work to build sustainable, inclusive opportunities for vulnerable communities across Africa.',NULL,NULL,NULL,NULL,NULL,4,1,'2026-08-22 00:55:00'),(5,'Kembabazi Charity','Monitoring and Evaluation Specialist','staff','Kembabazi Charity is a monitoring and evaluation professional who supports BetterLife International to understand, document and strengthen the results of its work. Her public professional footprint identifies experience in monitoring and evaluation, complementing her responsibility for tracking programme implementation, participant outcomes and organisational learning. Charity works with programme teams to develop indicators, collect and review data, document progress and identify areas requiring improvement. Her role helps BetterLife move beyond reporting activities to understanding whether interventions are producing meaningful changes in household income, food security, resilience and opportunity. Through evidence and continuous learning, she supports accountable decision-making and stronger programme design across BetterLife\'s operations.',NULL,NULL,NULL,NULL,NULL,5,1,'2026-08-22 00:55:00'),(6,'Comboni Daniel Mutabaazi','Country Manager, Tanzania','staff','Comboni Daniel Mutabaazi serves as BetterLife International\'s Country Manager in Tanzania, leading the organisation\'s national programmes and partnerships. He coordinates community engagement, programme implementation and relationships with local authorities, organisations and other stakeholders. His work helps ensure that BetterLife\'s interventions respond to the realities of the communities in which they are delivered, particularly in sustainable agriculture, climate resilience, youth livelihoods and support for vulnerable households. Daniel also connects the Tanzania team with BetterLife\'s wider regional leadership, strengthening reporting, learning and coordination across countries. His role reflects BetterLife\'s commitment to locally grounded leadership and solutions shaped by the people closest to the challenges being addressed.',NULL,NULL,NULL,NULL,NULL,6,1,'2026-08-22 00:55:00'),(7,'Alec Becky','Country Manager, South Sudan','staff','Alec Becky serves as BetterLife International\'s Country Manager in South Sudan, coordinating programmes that support young people, displaced families and communities affected by economic and climate pressures. Her public footprint also reflects active participation in competitive debate, including international adjudication and engagement with South Sudan\'s debate community. This background brings strong communication, analysis and youth-engagement skills to her work. At BetterLife, Becky oversees local implementation, team coordination, stakeholder engagement and programme reporting. She helps ensure that activities are rooted in community priorities while remaining connected to BetterLife\'s regional mission of strengthening livelihoods, resilience, social cohesion and meaningful opportunities for young people across Africa.',NULL,NULL,NULL,NULL,NULL,7,1,'2026-08-22 00:55:00'),(8,'Victoria Kinobe','Country Manager, DR Congo','staff','Victoria Kinobe Nakatudde is an engineer with experience in water resources, catchment-management planning, environmental and social impact assessment, and stakeholder engagement. Her professional work has included water-resources planning and regulation, alongside participation in regional and international climate discussions. As BetterLife International\'s Country Manager for the Democratic Republic of Congo, Victoria brings technical knowledge of water, environmental management and community engagement to the organisation\'s programmes. She supports locally appropriate interventions addressing climate vulnerability, livelihoods and sustainable resource use. Her experience helps connect technical environmental planning with the everyday needs of communities, strengthening BetterLife\'s ability to design practical, evidence-informed and environmentally responsible programmes.',NULL,NULL,NULL,NULL,NULL,8,1,'2026-08-22 00:55:00'),(9,'Agaba Ian','Programs Coordinator','staff','Agaba Ian is a Ugandan lawyer, climate justice advocate, writer and Pan-African youth leader serving as Programmes Coordinator at BetterLife International. His public work includes climate advocacy, youth engagement, public speaking and discussions on climate-related displacement and its effects on children. Ian has also participated in university debate, developing skills in research, policy analysis and persuasive communication. At BetterLife, he supports programme design, implementation, coordination and reporting across the organisation\'s thematic and country teams. He helps connect community experiences with wider conversations about climate justice, livelihoods and youth participation, ensuring that BetterLife\'s programmes remain responsive, practical and grounded in the needs of vulnerable communities.',NULL,NULL,NULL,NULL,NULL,9,1,'2026-08-22 00:55:00'),(10,'Desmond Dorvlo','Country Manager, Ghana','staff','Desmond Dorvlo is a Ghanaian communications professional, researcher, writer and debater serving as BetterLife International\'s Country Manager in Ghana. He studied at Kwame Nkrumah University of Science and Technology, where he has worked as a teaching and research assistant in English. His research interests include African literature, political communication, governance, gendered language and public discourse. Desmond has also worked in communications and external relations and produced writing on Ghanaian culture and travel. At BetterLife, he brings together research, storytelling and stakeholder engagement to coordinate programmes, partnerships and community relationships, strengthening the organisation\'s presence and locally led work in Ghana.',NULL,NULL,NULL,NULL,NULL,10,1,'2026-08-22 00:55:00'),(11,'Nkajja Janice','Head of Communications & Advocacy','staff','Nkajja Janice is a Ugandan communicator, public speaker and youth advocate serving as Head of Communications and Advocacy at BetterLife International. She was recognised as a co-winner in the speech category of Uganda\'s 2021 National Speech and Debate Championship and has subsequently contributed to debate and youth-communication initiatives. Janice has also served in public-relations work with Signals from the Grassroots. At BetterLife, she leads storytelling, media engagement, advocacy communication and the documentation of community experiences. She helps ensure that the voices of women, refugees, young people and smallholder farmers are represented accurately and with dignity, connecting their realities to partners, policymakers and wider public audiences.',NULL,NULL,NULL,NULL,NULL,11,1,'2026-08-22 00:55:00'),(12,'Ainembabazi Desire','Outreach Coordinator','staff','Ainembabazi Desire serves as Outreach Coordinator at BetterLife International, helping the organisation build meaningful relationships with communities, schools, young people and institutional partners. Desire supports mobilisation, participant engagement, outreach activities and communication between BetterLife\'s programme teams and the people they serve. The role is particularly important in ensuring that opportunities reach intended beneficiaries and that community feedback informs programme planning and implementation. Working across BetterLife\'s areas of climate education, sustainable livelihoods and youth empowerment, Desire helps turn organisational plans into accessible community action. This work strengthens local participation, builds trust and ensures that BetterLife\'s programmes remain visible, inclusive and responsive to community priorities.',NULL,NULL,NULL,NULL,NULL,12,1,'2026-08-22 00:55:00'),(19,'Sedrick Otolo','Board Member','board','Bio coming soon — this profile will be updated shortly.',NULL,NULL,NULL,NULL,NULL,19,1,'2026-08-22 00:55:00'),(20,'Felicia Davis','Board Member','board','With over two decades of experience, Felicia Davis has been at the forefront of integrating sustainability into educational institutions. In 2016, she co-founded the HBCU Green Fund, a nonprofit dedicated to financing sustainable building projects at Historically Black Colleges and Universities.','assets/img/board-felicia-davis.jpg',NULL,NULL,NULL,NULL,20,1,'2026-08-22 00:55:00'),(21,'Mohamed Nasheed','Board Member','board','In 2008, Nasheed became the first democratically elected president of the Maldives. His administration focused on democratic reforms, human rights and environmental sustainability, and he gained international acclaim for organizing the world\'s first underwater cabinet meeting on climate change.','assets/img/board-nasheed.jpg',NULL,NULL,NULL,NULL,21,1,'2026-08-22 00:55:00'),(22,'Pamela Musimenta','Board Member','board','Pamela Musimenta is a Ugandan water resources engineer, environmentalist and climate justice advocate working with the Ministry of Water and Environment. Her professional interests span water resources planning and regulation, hydraulic engineering, environmental compliance and access to clean, safe water. A UNFCCC advocate and beVisioneers Fellow, Pamela leads community-based work addressing deforestation, land degradation, soil erosion and flood risk through tree nurseries and soil and water conservation. She brings to BetterLife International\'s Board a combination of government experience, technical expertise and grassroots climate leadership, strengthening the organisation\'s work in renewable energy, sustainable agriculture, water security and community resilience across Africa.',NULL,NULL,NULL,NULL,NULL,22,1,'2026-08-22 00:55:00'),(23,'Bob Natifu','Board Member','board','As Commissioner for Climate Change in Uganda\'s Ministry of Water and Environment, Bob Natifu coordinates national climate response strategies and serves as a UN CC:Learn Ambassador.','assets/img/board-bob-natifu.jpg',NULL,NULL,NULL,NULL,23,1,'2026-08-22 00:55:00');
/*!40000 ALTER TABLE `team_members` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `stats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `stats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(150) NOT NULL,
  `value` varchar(50) NOT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `stats` WRITE;
/*!40000 ALTER TABLE `stats` DISABLE KEYS */;
INSERT INTO `stats` VALUES (1,'People reached in 2025','112,430',NULL,1,1),(2,'Farmers supported','18,900',NULL,2,1),(3,'Refugees and host-community members reached','41,200',NULL,3,1),(4,'Students engaged in climate education','4,580+',NULL,4,1),(5,'Green Libraries and Eco Labs','20+',NULL,5,1),(6,'Household biogas systems','48+',NULL,6,1),(7,'Community boreholes','65',NULL,7,1),(8,'Tree seedlings raised','50,000+',NULL,8,1);
/*!40000 ALTER TABLE `stats` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `programs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `programs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `slug` varchar(170) NOT NULL,
  `tagline` varchar(255) DEFAULT NULL,
  `summary` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `programs` WRITE;
/*!40000 ALTER TABLE `programs` DISABLE KEYS */;
INSERT INTO `programs` VALUES (1,'Climate-Resilient Agriculture and Food Security','climate-resilient-agriculture','','We help farmers and families grow food in the face of unreliable rain, poor soils and limited land. The work includes demonstration gardens, composting, mulching, drought-tolerant crops, agroforestry, greenhouse farming, irrigation, poultry, aquaculture and beekeeping.','','assets/img/betterlifeint-source/programs/program-photo-2.jpg','leaf',1,1,'2026-08-27 12:27:15','2026-08-30 05:06:04'),(2,'Green Skills, Livelihoods and Markets','green-skills-livelihoods','','We train people in skills that respond to local opportunities, then help them move beyond training through savings groups, enterprise support, finance and market connections. Our work spans agriculture, carpentry, tailoring, barbering, poultry, weaving and solar technology.','','assets/img/project-smiles.jpg','users',2,1,'2026-08-27 12:27:15','2026-08-27 12:27:15'),(3,'Climate Education and Youth Leadership','climate-education-youth-leadership','','Through Green Libraries, Eco Labs, school clubs, youth centres, debates, Climate Academies and innovation challenges, children and young people gain the knowledge, tools and confidence to take part in the decisions shaping their future.','','assets/img/betterlifeint-source/programs/program-photo-4.jpg','file-text',3,1,'2026-08-27 12:27:15','2026-08-30 05:06:04'),(4,'Clean Energy, Water and Restoration','clean-energy-water-restoration','','We work with communities on tree nurseries, agroforestry, biogas, briquettes, waste recovery and water access. Each solution is designed to ease pressure on both households and the environment.','','assets/img/betterlifeint-source/programs/program-photo-1.jpg','leaf',4,1,'2026-08-27 12:27:15','2026-08-30 05:06:04'),(5,'Digital Innovation for Farmers','digital-innovation','','Soilla and Agribusiness Connekt bring soil advice, climate information, services, finance and markets closer to farmers. The technology is paired with face-to-face support so that information becomes something people can use.','','assets/img/project-soilla-app.jpg','trending-up',5,1,'2026-08-27 12:27:15','2026-08-27 12:27:15');
/*!40000 ALTER TABLE `programs` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `projects`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `projects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `category` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `projects` WRITE;
/*!40000 ALTER TABLE `projects` DISABLE KEYS */;
INSERT INTO `projects` VALUES (1,'Climate Education and Youth Empowerment','climate-education-youth-empowerment','Climate Education','Engaging students in debates, public speaking and Green Libraries that build climate literacy and leadership skills across partner schools.','assets/img/project-climate-education.jpg',1,1,'2026-08-22 00:55:00'),(2,'BetterLife Spring Project','betterlife-spring-project','Renewable Energy & Agriculture','Sustainable Powered Resilient Irrigation for Next Generation Farming (SPRING) — giving South Sudanese farmers access to renewable-energy-powered irrigation so they can grow food despite unpredictable weather.','assets/img/project-spring.jpg',2,1,'2026-08-22 00:55:00'),(3,'SMILES','smiles','Empowering Refugees & Host Communities','A programme supporting social cohesion and livelihoods between refugee and host communities through shared training, resources and community dialogue.','assets/img/project-smiles.jpg',3,1,'2026-08-22 00:55:00'),(4,'BetterLife Renewable Pathways','betterlife-renewable-pathways','Plastic Pollution in Uganda','Turning plastic waste into fuel and other reusable materials, reducing pollution while creating green income opportunities for youth.','assets/img/project-renewable-pathways.jpg',4,1,'2026-08-22 00:55:00'),(5,'BetterLife Agro-Tourism Farm','betterlife-agro-tourism-farm','Agriculture in Uganda','Our Rukungiri model farm training centre for organic dairy and crop farming — and the source of the honey, ghee and yoghurt sold under the BetterLife Farm brand.','assets/img/project-agro-tourism.jpg',5,1,'2026-08-22 00:55:00'),(6,'Empowering Refugee Women and IDPs Through Smart and Sustainable Agriculture','empowering-refugee-women-idps-agriculture','Driving Local Solutions for a Global Future','Equipping refugee and internally displaced women with climate-smart agricultural skills and small-scale farming techniques to build food security and income.','assets/img/project-women-idps.jpg',6,1,'2026-08-22 00:55:00'),(7,'Soilla App','soilla-app','AI & IoT Technology','A web and mobile platform that leverages AI and IoT technologies to help smallholder farmers monitor soil health and make better-informed farming decisions.','assets/img/project-soilla-app.jpg',7,1,'2026-08-22 00:55:00');
/*!40000 ALTER TABLE `projects` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `blog_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `blog_categories` WRITE;
/*!40000 ALTER TABLE `blog_categories` DISABLE KEYS */;
INSERT INTO `blog_categories` VALUES (1,'Climate and Food','climate-and-food'),(2,'Work and Enterprise','work-and-enterprise'),(3,'Partnerships and News','partnerships-and-news'),(4,'From the Field','from-the-field'),(5,'Young People and Ideas','young-people-and-ideas');
/*!40000 ALTER TABLE `blog_categories` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `blog_posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `blog_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `slug` varchar(220) NOT NULL,
  `category_id` int(11) DEFAULT NULL,
  `excerpt` varchar(500) DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `featured_image` varchar(255) DEFAULT NULL,
  `author` varchar(100) DEFAULT 'Admin',
  `status` enum('draft','published') DEFAULT 'draft',
  `views` int(11) DEFAULT 0,
  `published_at` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `blog_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `blog_posts` WRITE;
/*!40000 ALTER TABLE `blog_posts` DISABLE KEYS */;
INSERT INTO `blog_posts` VALUES (1,'Climate Justice: Beyond Financial Transactions to Protect the Vulnerable','climate-justice-beyond-financial-transactions',1,'Climate justice is not merely about financial transactions to protect the environment. It embodies the fight for fairness for the communities least responsible for the crisis, yet most affected by it.','<p>Climate justice is not merely about financial transactions to protect the environment. It embodies the fight for fairness for the communities least responsible for the climate crisis, yet most affected by its consequences.</p><p>Across Uganda, South Sudan, Tanzania, Ghana and the DR Congo, the families BetterLife International works with are on the frontline of a crisis they did little to cause — unpredictable rains, shrinking grazing land, and displacement driven by environmental stress. True climate justice means channelling resources, skills and decision-making power directly to these communities, not just discussing figures in international finance rooms.</p><p>That is why our approach pairs climate education with tangible livelihoods: green skills training, climate-smart agriculture, and renewable energy access such as biogas and boreholes. When a family can irrigate a field with solar power or cook with biogas instead of charcoal, climate justice becomes something they can hold in their hands, not just a policy conversation.</p><p>We continue to advocate for financing that reaches the last mile — the refugee-hosting districts and rural communities that are too often left out of climate finance altogether.</p>','assets/img/blog-climate-justice.jpg','Admin','published',4,'2024-10-26 09:00:00','2026-08-22 00:55:00','2026-08-30 15:06:37'),(2,'Climate Change: Amplifying Extreme Weather and Displacement','climate-change-extreme-weather-displacement',1,'Climate change significantly heightens the risks of extreme weather events — storms, floods, wildfires — and the displacement that follows them.','<p>Climate change significantly heightens the risks of extreme weather events — such as storms, floods, wildfires, and droughts — and the human displacement that so often follows in their wake.</p><p>In the regions where BetterLife International works, we see this firsthand: families who already fled conflict now facing floods that destroy their new farmland, or droughts that fail the very crops meant to rebuild their food security. Displacement driven by climate stress is compounding an already fragile humanitarian situation across Sub-Saharan Africa.</p><p>Our response combines immediate resilience-building — climate-smart agriculture, water access through boreholes, and renewable energy — with long-term climate education through our Green Libraries and Eco Clubs, reaching thousands of students with the knowledge to adapt and lead.</p><p>Addressing climate-driven displacement requires both humanitarian urgency and sustained investment in local adaptation. It is a challenge we are meeting one community, one borehole, one biogas plant at a time.</p>','assets/img/blog-extreme-weather.jpg','Admin','published',1,'2024-10-14 09:00:00','2026-08-22 00:55:00','2026-08-30 15:06:43'),(3,'Congratulations to Dr. Okello Sharon Nagenjwa on Her PhD and Outstanding Community Impact!','dr-okello-sharon-nagenjwa-phd',5,'We are happy to celebrate the remarkable journey of Dr. Okello Sharon Nagenjwa, who was recently awarded her PhD after years of dedicated research and community impact.','<p>We are happy to celebrate the remarkable journey of Dr. Okello Sharon Nagenjwa, who was recently awarded her PhD after years of dedicated research and community impact work across Uganda\'s agricultural and refugee-hosting communities.</p><p>Dr. Nagenjwa\'s research has directly informed several of BetterLife International\'s programs in sustainable agriculture and climate-smart farming, helping bridge the gap between academic research and practical, farmer-led solutions.</p><p>Her achievement reflects the spirit of BetterLife International\'s mission: that those closest to a challenge are often best placed to solve it. We congratulate Dr. Nagenjwa and look forward to continued collaboration as we grow our agricultural and livelihoods programs, including the BetterLife Farm.</p>','assets/img/blog-sharon-phd.png','Admin','published',0,'2024-09-29 09:00:00','2026-08-22 00:55:00','2026-08-27 12:27:15'),(4,'The Importance of Civic Education in Uganda: An Evaluative Analysis','importance-of-civic-education-in-uganda',5,'Civic education plays a crucial role in the development of any democratic society, yet in Uganda it remains under-prioritized in many communities.','<p>Civic education plays a crucial role in the development of any democratic society, yet in Uganda it remains under-prioritized in many communities, particularly among youth and displaced populations.</p><p>Through our Climate Education and Awareness programme, BetterLife International has seen how civic literacy and environmental literacy reinforce one another — young people who understand their rights and responsibilities are also more likely to take an active role in protecting their environment and holding leaders accountable for it.</p><p>We believe civic education should be woven into every level of schooling, not treated as an afterthought. It is one of the quiet foundations of the resilient, self-reliant communities we work to build.</p>','assets/img/blog-civic-education.jpg','Admin','published',0,'2024-09-20 09:00:00','2026-08-22 00:55:00','2026-08-27 12:27:15'),(5,'The Naked Truth of Corruption: A Critical Analysis of Uganda\'s Socioeconomic State and Youth-Led Activism','naked-truth-of-corruption-uganda',5,'Uganda is currently grappling with a critical socio-political issue: rampant corruption, which has become deeply embedded and is fuelling youth-led activism.','<p>Uganda is currently grappling with a critical socio-political issue: rampant corruption, which has become deeply embedded in public institutions and continues to undermine development outcomes for ordinary citizens.</p><p>At BetterLife International, we see the downstream effects of this every day — resources that should reach vulnerable communities are too often diverted, delayed, or diminished. This is part of why we are youth-led: young people across our five countries of operation are refusing to accept corruption, inequality, or environmental destruction as the status quo.</p><p>Youth-led activism, paired with transparent, community-owned programs like our farm cooperatives and green-skills training, is one practical way to build systems that are accountable from the ground up.</p>','assets/img/blog-corruption.jpg','Admin','published',0,'2023-09-06 09:00:00','2026-08-22 00:55:00','2026-08-27 12:27:15');
/*!40000 ALTER TABLE `blog_posts` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `testimonials`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `testimonials` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quote` text NOT NULL,
  `author_name` varchar(150) NOT NULL,
  `author_role` varchar(150) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `testimonials` WRITE;
/*!40000 ALTER TABLE `testimonials` DISABLE KEYS */;
INSERT INTO `testimonials` VALUES (2,'The training I received in organic dairy farming changed everything for my family. Today I supply milk for the farm\'s own ghee and yoghurt.','A Farmer, Rukungiri Cooperative','BetterLife Farm Partner',NULL,2,1,'2026-08-22 00:55:00'),(3,'Beekeeping gave me an income for the first time since I fled home. Every jar of honey we sell carries our story forward.','A Beekeeper','Refugee-led Livelihoods Program',NULL,3,1,'2026-08-22 00:55:00');
/*!40000 ALTER TABLE `testimonials` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `impact_stories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `impact_stories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `caption` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `impact_stories` WRITE;
/*!40000 ALTER TABLE `impact_stories` DISABLE KEYS */;
INSERT INTO `impact_stories` VALUES (1,'Empowering Refugee Women','Skills training that builds real livelihoods','assets/img/impact-story-1.jpg',1,1),(2,'Empowering Refugee Communities','Resilience built together, family by family','assets/img/impact-story-2.jpg',2,1),(3,'Building Our Demo Farm','How the BetterLife Agro-Tourism Farm began','assets/img/impact-story-3.jpg',3,1);
/*!40000 ALTER TABLE `impact_stories` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `reports`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reports` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) NOT NULL,
  `year` varchar(10) DEFAULT NULL,
  `file_url` varchar(500) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `reports` WRITE;
/*!40000 ALTER TABLE `reports` DISABLE KEYS */;
INSERT INTO `reports` VALUES (1,'BetterLife International Annual Report','2023','https://assets.zyrosite.com/m5KvNaBjBKtjxV5x/betterlife-international-annual-report-20223-AoPvD3b9PLsZ7ryj.pdf',1,1,'2026-08-22 00:55:00'),(2,'BetterLife International Annual Report','2022','https://assets.zyrosite.com/m5KvNaBjBKtjxV5x/betterlife-international-annual-report-2022-YbNvbWgWWESzw1nz.pdf',2,1,'2026-08-22 00:55:00');
/*!40000 ALTER TABLE `reports` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;


-- Safe, surgical update: only the farm_text content key (does NOT touch
-- smtp_*, pesapal_*, admin_alert_* or any other settings row).
UPDATE `settings` SET `setting_value` = 'BetterLife Agro Tourism Farm is a clean-energy-powered social enterprise where our humanitarian support, climate-smart agriculture and market-access work come together. Solar energy powers the farm''s irrigation, water pumping and key production activities, reducing dependence on fossil fuels while demonstrating that agriculture can be both productive and environmentally responsible.\n\nThe farm supports refugees, women and vulnerable households to move from emergency assistance towards producing their own food and earning stable incomes. Refugees who are able to participate contribute up to two hours of their time at the farm while rebuilding their livelihoods. In return, they receive practical agricultural training, food support, free seedlings and other starter inputs to establish gardens of their own. This flexible, temporary arrangement allows participants to contribute with dignity while retaining time to care for their families, study, seek employment or develop other sources of income. As they become stable, they can transition into independent producers and suppliers to BetterLife.\n\nThe farm demonstrates solar-powered irrigation, greenhouse farming, beekeeping, livestock rearing and sustainable food production. Produce from the farm and farmers trained by BetterLife International is processed and marketed through BetterLife Agro Tourism Farm Ltd as products such as BetterLife Honey, Ghee and Vanilla Yoghurt—connecting training and production to packaging, customers and real household income.\n\nIt also serves as a practical learning and agro-tourism space where schools, farmers, partners and visitors can experience how clean energy, agriculture and local enterprise can work together to address hunger, unemployment and climate vulnerability.'
WHERE `setting_key` = 'farm_text';
