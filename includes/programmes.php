<?php
/**
 * Programme areas and projects: structure, photographs and documented results.
 *
 * Three levels:
 *   Programme areas  explain the main fields of work        (program.php?slug=...)
 *   Projects         show the work in particular settings   (project.php?slug=...)
 *   Stories/reports  hold the detailed accounts             (linked from projects)
 *
 * The long project narratives stay in the database (Admin -> Page Content, page
 * "programs"), so staff can keep editing them. Each project names the block(s) it
 * draws its narrative from: [section key, title, first paragraph to show].
 *
 * Facts below come only from BetterLife's supplied material. Anything still to
 * confirm is marked 'confirm' so it can be checked before publication.
 */
require_once __DIR__ . '/media.php';

function pp_areas(): array
{
    return [
        'climate-resilient-agriculture' => [
            'short'  => 'Food Security and Climate-Resilient Agriculture',
            'formal' => 'Climate-Resilient Agriculture and Food Security',
            'icon'   => 'leaf',
            'card'   => 'When the rains become unreliable, the first loss may be a crop. We help farmers and families grow food more reliably through demonstration gardens and practical training, from composting and drought-tolerant crops to irrigation, poultry and beekeeping.',
            'image'  => ['assets/img/about/rukungiri-two-women.jpg', 'Two women smiling in a maize field in Rukungiri', '30% 40%'],
            'hero'   => ['assets/img/about/yumbe-greenhouse-aerial.jpg', 'Aerial view of a greenhouse and farm plots in Yumbe, Uganda', '50% 55%'],
            'lead'   => 'When the rains become unreliable, the first loss may be a crop. What follows can be a loss of income, fewer meals, unpaid school costs and debt carried into the next season.',
            'intro'  => [
                'BetterLife works with farmers, women, refugees and vulnerable households to make food production more reliable. We use demonstration gardens and practical training so that people can see a method working before they risk their own harvest on it.',
                'We also help participants think beyond the harvest. Savings groups, enterprise support, digital information and market connections make it more possible for farming to provide both food and income.',
            ],
            'who'    => 'Farmers, women, refugees, displaced families and vulnerable households.',
            'activities' => ['Demonstration gardens', 'Composting and mulching', 'Water conservation and irrigation', 'Drought-tolerant crops', 'Sack and box gardening', 'Agroforestry', 'Greenhouse farming', 'Poultry and aquaculture', 'Beekeeping', 'Savings groups and market connections'],
            'evidence' => [
                ['22% → 92%', 'Knowledge of climate-smart agriculture', 'Among the 72 women who completed structured training', 'womens-climate-resilience-yumbe'],
                ['35%', 'Average reduction in household spending on vegetables', 'Reported by participating households as home production improved', 'womens-climate-resilience-yumbe'],
            ],
            'gallery' => [
                ['assets/img/about/yumbe-planting-demo.jpg', 'A planting demonstration in Yumbe'],
                ['assets/img/about/yumbe-solar-irrigation.jpg', 'Solar-powered irrigation above a maize crop in Yumbe'],
                ['assets/img/about/yumbe-cabbages.jpg', 'Cabbages growing at a BetterLife-supported site in Yumbe'],
                ['assets/img/betterlifeint-source/programs/program-photo-9.jpg', 'A raised sack garden planted with seedlings'],
                ['assets/img/about/yumbe-beehives.jpg', 'Beehives at a BetterLife-supported site in Yumbe'],
                ['assets/img/about/rukungiri-farmer-maize.jpg', 'A farmer tending a maize crop in Rukungiri'],
            ],
            'invite' => 'Help more families grow food through a dry season: fund demonstration gardens, seedlings and irrigation, or share agronomy expertise.',
        ],
        'green-skills-livelihoods' => [
            'short'  => 'Livelihoods, Skills and Markets',
            'formal' => 'Green Skills, Livelihoods and Market Access',
            'icon'   => 'basket',
            'card'   => 'Learning a trade is one step. Finding tools, capital and customers is another. We pair practical skills, from tailoring and carpentry to poultry and solar technology, with savings groups, enterprise coaching, finance and routes to market.',
            'image'  => ['assets/img/about/yumbe-shopkeeper.jpg', 'A smiling woman at her market stall in Yumbe', '60% 35%'],
            'hero'   => ['assets/img/market-stall-vendor.webp', 'A woman standing at her market stall', '70% 40%'],
            'lead'   => 'Learning a trade is one step. Finding tools, capital and customers is another.',
            'intro'  => [
                'BetterLife combines practical skills with enterprise coaching, savings, finance and market connections. Participants train in areas suited to local demand, including agriculture, poultry, carpentry, tailoring, barbering, weaving and solar technology.',
                'The work continues beyond the training day. We help people test a business idea, understand costs, join a savings group, approach finance and find a route into the market.',
            ],
            'who'    => 'Refugees and host-community members, women and young people looking for a route into work or enterprise.',
            'activities' => ['Vocational skills training', 'Enterprise coaching', 'Savings groups', 'Access to finance and small loans', 'Mentorship and business support', 'Market connections'],
            'evidence' => [
                ['78%', 'Of participants moved into sustainable income pathways', 'SMILES, reported across target groups', 'smiles', 'confirm'],
                ['85%', 'Reported improved refugee-host relations', 'SMILES, reported across target groups', 'smiles', 'confirm'],
            ],
            'gallery' => [
                ['assets/img/betterlifeint-source/projects/project-smiles-alt.jpg', 'A group of women holding rolled mats at a SMILES activity'],
                ['assets/img/market-stall-vendor.webp', 'A woman standing at her market stall'],
                ['assets/img/about/yumbe-elder-smile.jpg', 'An older woman smiling outside her shop in Yumbe'],
                ['assets/img/grain-milling-machine.webp', 'Grain being processed with a milling machine in Rukungiri'],
                ['assets/img/about/rukungiri-poultry.jpg', 'A woman feeding chickens in a poultry house in Rukungiri'],
            ],
            'invite' => 'Back the step after training: start-up tools, small-loan guarantees, business mentoring or a market connection for producers.',
        ],
        'climate-education-youth-leadership' => [
            'short'  => 'Climate Education and Youth Leadership',
            'formal' => 'Climate Education, Youth Leadership and Innovation',
            'icon'   => 'book',
            'card'   => 'Young people will live longest with today’s climate decisions. Through Green Libraries, Eco Labs, school clubs, youth centres, Climate Academies and innovation challenges, they gain the knowledge and confidence to take part in those decisions.',
            'image'  => ['assets/img/betterlifeint-source/programs/program-photo-4.jpg', 'A facilitator leading an energetic school climate club session', '45% 30%'],
            'hero'   => ['assets/img/betterlifeint-source/programs/program-photo-5.jpg', 'Students holding placards at a school environment event', '50% 40%'],
            'lead'   => 'Young people will live longest with today’s climate decisions. They should be doing more than listening to adults explain the future to them.',
            'intro'  => [
                'BetterLife creates spaces where children and young people can learn, question, debate, build and take part in decisions. The work moves between classrooms, youth centres, digital spaces, policy conversations and practical community action.',
            ],
            'who'    => 'School learners, young people in rural communities and young climate leaders from Uganda and across Africa.',
            'activities' => ['Green Libraries and Eco Labs', 'School climate clubs and gardens', 'Tree planting and waste separation', 'Debates and public speaking', 'Youth centre learning and digital skills', 'Community radio', 'Climate academy', 'Innovation hackathons'],
            'evidence' => [
                ['4,500+', 'Students engaged through school climate education', 'Green Libraries, Eco Labs and School Climate Clubs', 'green-libraries-eco-labs'],
                ['3,000+', 'Books at the Apala One Stop Youth Centre', 'Opened in Alebtong in December 2023 with ten computers and space for around 400 young people', 'apala-youth-centre'],
            ],
            'gallery' => [
                ['assets/img/betterlifeint-source/programs/program-photo-4.jpg', 'A school climate club session'],
                ['assets/img/betterlifeint-source/programs/program-photo-5.jpg', 'Students holding placards at a school environment event'],
                ['assets/img/betterlifeint-source/programs/program-photo-8.jpg', 'Pupils gathered outdoors for a session'],
                ['assets/img/about/rukungiri-pupils.jpg', 'Four smiling pupils in school uniform in Rukungiri'],
            ],
            'invite' => 'Stock a Green Library, equip an Eco Lab, mentor a hackathon team or sponsor young people to join the climate academy.',
        ],
        'clean-energy-water-restoration' => [
            'short'  => 'Clean Energy, Water and Restoration',
            'formal' => 'Clean Energy, Water and Environmental Restoration',
            'icon'   => 'sun',
            'card'   => 'Energy poverty, water insecurity and environmental loss often sit inside the same household. We work with communities on tree nurseries, biogas, briquettes, waste recovery and water access, easing pressure on families and the land at the same time.',
            'image'  => ['assets/img/solar-panel-installation-1.webp', 'A BetterLife team member checking a solar panel that powers irrigation in Yumbe', '40% 30%'],
            'hero'   => ['assets/img/children-at-borehole.webp', 'Children collecting water at a borehole in Yumbe', '50% 45%'],
            'lead'   => 'Energy poverty, water insecurity and environmental loss often sit inside the same household.',
            'intro'  => [
                'When firewood is scarce, women and girls walk farther. When a water source dries up, food production and school attendance suffer. When land is degraded, a farmer’s options narrow with every season.',
                'BetterLife works on practical solutions that reduce those pressures while restoring the environment.',
            ],
            'who'    => 'Households, women, schools, farmers and communities.',
            // Activities with their own write-ups (from Admin -> Page Content)
            'blocks' => [
                ['clean-energy-water-restoration', 'Community Tree Nurseries and Agroforestry'],
                ['clean-energy-water-restoration', 'Community Biogas'],
                ['clean-energy-water-restoration', 'Water Access'],
                ['clean-energy-water-restoration', 'Briquette-Making'],
            ],
            'evidence' => [
                ['65', 'Community boreholes supported', 'Water Access'],
                ['48+', 'Household biogas systems supported', 'Community Biogas'],
                ['20,000+', 'Tree seedlings distributed to schools, farmers and communities', 'From more than 50,000 raised in BetterLife-supported nurseries'],
            ],
            'gallery' => [
                ['assets/img/solar-panel-installation-1.webp', 'Checking a solar panel that powers irrigation in Yumbe'],
                ['assets/img/children-at-borehole.webp', 'Children collecting water at a borehole in Yumbe'],
                ['assets/img/solar-panel-installation-2.webp', 'A solar panel in Rukungiri'],
                ['assets/img/betterlifeint-source/projects/project-renewable-pathways-alt.jpg', 'A plastic bank collecting bottles for recycling'],
            ],
            'invite' => 'Fund a borehole, a household biogas system or a tree nursery, or bring engineering skills to keep systems running.',
        ],
        'digital-innovation' => [
            'short'  => 'Digital Tools for Farmers',
            'formal' => 'Digital Innovation for Agriculture',
            'icon'   => 'phone',
            'card'   => 'Technology is useful when it shortens the distance between a farmer and a good decision. Soilla and Agribusiness Connekt bring soil advice, climate information, finance and buyers closer, always paired with face-to-face support.',
            'image'  => ['assets/img/soilla-app-portrait.webp', 'A young man holding up a phone showing the Soilla app', '50% 30%'],
            'hero'   => ['assets/img/soilla-app-field-demo.webp', 'The Soilla app open on a phone in a field', '50% 50%'],
            'lead'   => 'Technology is useful when it shortens the distance between a farmer and a good decision.',
            'intro'  => [
                'BetterLife develops digital tools around practical gaps: understanding soil, preparing for weather, finding a service, accessing finance and reaching a buyer.',
            ],
            'who'    => 'Smallholder farmers and small agricultural enterprises, supported by BetterLife field teams.',
            'activities' => ['Soil and crop guidance', 'Climate information', 'Market prices', 'Finding suppliers, experts and services', 'Links to buyers and finance', 'Field training alongside every tool'],
            'evidence' => [],
            'gallery' => [
                ['assets/img/soilla-app-field-demo.webp', 'The Soilla app open on a phone in a field'],
                ['assets/img/about/yumbe-connekt-app.jpg', 'Produce listings in the Agribusiness Connekt app'],
                ['assets/img/agribusiness-connekt-app.webp', 'Showing the Agribusiness Connekt app'],
                ['assets/img/betterlifeint-source/projects/project-soilla-app-alt.jpeg', 'Screens from the Soilla app'],
            ],
            'invite' => 'Help farmers reach better information and buyers: support connectivity, data partnerships or the field teams who train farmers to use the tools.',
        ],
    ];
}

/**
 * Projects. 'area' is the home programme area; 'also' lists other areas whose pages
 * link to it in context. 'href' sends a project with its own established page there.
 */
function pp_projects(): array
{
    return [
        'womens-climate-resilience-yumbe' => [
            'title'    => 'Women’s Climate Resilience in Yumbe',
            'formal'   => 'Strengthening the capacity of refugees and IDPs in agriculture, food security and climate action',
            'area'     => 'climate-resilient-agriculture',
            'location' => 'Yumbe, Uganda', 'country' => 'Uganda',
            'partner'  => 'Foundation S, The Sanofi Collective',
            'who'      => 'Refugee, displaced and host-community women',
            'summary'  => 'Before training began, we spoke with 100 women in Yumbe about how climate pressure was changing their lives. Their answers shaped local-language training, demonstration gardens, seedlings and simple digital tools for refugee, displaced and host-community women.',
            'image'    => ['assets/img/betterlifeint-source/programs/program-photo-11.jpg', 'Women laughing together at a programme session in Yumbe', '50% 40%'],
            'blocks'   => [['climate-resilient-agriculture', 'Women’s Climate Resilience in Yumbe']],
            'results'  => [
                ['22% → 92%', 'Knowledge of climate-smart agriculture', 'Among the 72 women who completed structured training'],
                ['72%', 'Adopted sack or box gardening', 'Women in the programme, reported after training', 'confirm'],
                ['63%', 'Took up composting', 'Women in the programme, reported after training', 'confirm'],
                ['35%', 'Average reduction in household spending on vegetables', 'Reported by participating households'],
            ],
            'gallery'  => [
                ['assets/img/about/yumbe-listening-circle.jpg', 'BetterLife staff and women seated in a circle for a session in Yumbe'],
                ['assets/img/betterlifeint-source/programs/program-photo-10.jpg', 'Women taking notes during a training session'],
                ['assets/img/about/yumbe-planting-demo.jpg', 'A planting demonstration in Yumbe'],
                ['assets/img/betterlifeint-source/programs/program-photo-9.jpg', 'A raised sack garden planted with seedlings'],
                ['assets/img/about/yumbe-greenhouse-aerial.jpg', 'Aerial view of a greenhouse and farm plots in Yumbe'],
                ['assets/img/about/yumbe-2025-women.jpg', 'Women in the programme in Yumbe, 2025'],
            ],
            'related'  => [['about.php#impact', 'Programme results on our About page'], ['project.php?slug=green-leaf-platforms-uganda', 'Green Leaf Platforms Uganda builds on this lesson']],
        ],
        'green-leaf-platforms-uganda' => [
            'title'    => 'Green Leaf Platforms Uganda',
            'area'     => 'climate-resilient-agriculture',
            'location' => '12 districts, Uganda', 'country' => 'Uganda',
            'partner'  => 'Farm Radio International',
            'status'   => 'Expanding in 2026',
            'who'      => 'Women in Women’s Action Circles, built on savings groups, farmer organisations, cooperatives and community networks',
            'summary'  => 'With Farm Radio International, we connect radio and digital farming content to Women’s Action Circles across 12 districts, so women can discuss what they hear, visit demonstration plots and test practices together in groups they already trust.',
            'image'    => ['assets/img/about/soroti-wac-2026.jpg', 'A facilitator speaking with women at a Women’s Action Circle session in Soroti', '55% 40%'],
            'blocks'   => [['climate-resilient-agriculture', 'Green Leaf Platforms Uganda']],
            'results'  => [],
            'gallery'  => [
                ['assets/img/about/soroti-wac-2026.jpg', 'A Women’s Action Circle session in Soroti, 2026'],
                ['assets/img/about/soroti-wac-gathering.jpg', 'Women gathered for a Women’s Action Circle session in Soroti, 2026'],
            ],
            'related'  => [['project.php?slug=womens-climate-resilience-yumbe', 'Where the model began: Women’s Climate Resilience in Yumbe']],
        ],
        'betterlife-spring' => [
            'title'    => 'BetterLife SPRING',
            'formal'   => 'Sustainable Powered Resilient Irrigation for Next Generation Farming',
            'area'     => 'climate-resilient-agriculture', 'also' => ['clean-energy-water-restoration'],
            'location' => 'South Sudan', 'country' => 'South Sudan',
            'who'      => 'Farmers, refugees and displaced families',
            'summary'  => 'In South Sudan, rainfall is becoming harder to predict. BetterLife SPRING combines solar-powered irrigation with training in soil health, crop planning and water management, so farmers, refugees and displaced families can produce more reliably through dry periods.',
            // Photograph from Yumbe, Uganda (no SPRING photograph supplied yet); the caption says so
            'image'    => ['assets/img/about/yumbe-solar-irrigation.jpg', 'Solar-powered irrigation above a maize crop at a BetterLife-supported site in Yumbe, Uganda', '50% 45%'],
            'image_note' => 'Photograph: solar-powered irrigation at a BetterLife-supported site in Yumbe, Uganda.',
            'blocks'   => [['climate-resilient-agriculture', 'BetterLife SPRING']],
            'results'  => [],
            'gallery'  => [],
            'related'  => [['program.php?slug=clean-energy-water-restoration', 'Clean energy and water across our work']],
        ],
        'betterlife-agro-tourism-farm' => [
            'title'    => 'BetterLife Agro Tourism Farm',
            'area'     => 'climate-resilient-agriculture', 'also' => ['green-skills-livelihoods'],
            'location' => 'Rukungiri, Uganda', 'country' => 'Uganda',
            'who'      => 'Farmers learning on a working farm and supplying produce',
            'summary'  => 'BetterLife’s working demonstration farm and market link. Farmers learn through solar-powered irrigation, greenhouse farming, beekeeping, dairy and livestock, then have a route to supply produce through BetterLife Agro Tourism Farm Ltd.',
            'image'    => ['assets/img/betterlifeint-source/projects/project-agro-tourism-alt.jpeg', 'A farmer walking through a banana plantation at BetterLife Agro Tourism Farm', '60% 45%'],
            'href'     => 'farm.php',
        ],
        'smiles' => [
            'title'    => 'SMILES',
            'formal'   => 'Market Inclusive Livelihood Pathways to Self-Reliance',
            'area'     => 'green-skills-livelihoods',
            'who'      => 'Refugees and host-community members',
            'summary'  => 'SMILES brings refugees and host-community members into the same training groups and local economy. Participants learn practical trades, then receive mentorship, business support, market connections and access to small loans, building relationships as they train, save and trade together.',
            'image'    => ['assets/img/betterlifeint-source/projects/project-smiles-alt.jpg', 'A group of women holding rolled mats at a SMILES activity', '50% 35%'],
            'blocks'   => [['green-skills-livelihoods', 'SMILES']],
            'results'  => [
                ['78%', 'Moved into sustainable income pathways', 'Reported across SMILES target groups', 'confirm'],
                ['85%', 'Reported improved refugee-host relations', 'Reported across SMILES target groups', 'confirm'],
                ['40%', 'Fall in food insecurity', 'Reported across SMILES target groups', 'confirm'],
            ],
            'gallery'  => [],
            'related'  => [['project.php?slug=agribusiness-connekt', 'Agribusiness Connekt: links to buyers and finance']],
        ],
        'green-libraries-eco-labs' => [
            'title'    => 'Green Libraries, Eco Labs and School Climate Clubs',
            'area'     => 'climate-education-youth-leadership',
            'location' => 'Schools in Uganda', 'country' => 'Uganda',
            'who'      => 'Learners at partner schools, including Lake Victoria School Entebbe and Buddo Junior School',
            'summary'  => 'Green Libraries and Eco Labs give learners books, digital resources and practical environmental activities, from school gardens and tree planting to plastic banks, debates and public speaking. More than 4,500 students have taken part.',
            'image'    => ['assets/img/betterlifeint-source/programs/program-photo-4.jpg', 'A facilitator leading a school climate club session', '45% 30%'],
            'blocks'   => [['climate-education-youth-leadership', 'Green Libraries, Eco Labs and School Climate Clubs']],
            'results'  => [
                ['4,500+', 'Students engaged through school climate education', 'Across BetterLife’s school work'],
                ['20+', 'Green Libraries and Eco Labs supported', 'Across BetterLife’s school work'],
            ],
            'gallery'  => [
                ['assets/img/betterlifeint-source/programs/program-photo-4.jpg', 'A school climate club session'],
                ['assets/img/betterlifeint-source/programs/program-photo-5.jpg', 'Students holding placards at a school environment event'],
            ],
            'related'  => [['project.php?slug=betterlife-renewable-pathways', 'School plastic banks: BetterLife Renewable Pathways']],
        ],
        'apala-youth-centre' => [
            'title'    => 'Apala One Stop Youth Centre',
            'area'     => 'climate-education-youth-leadership',
            'location' => 'Alebtong, Uganda', 'country' => 'Uganda',
            'status'   => 'Opened December 2023',
            'who'      => 'Young people in and around Alebtong',
            'summary'  => 'In Alebtong, the Apala One Stop Youth Centre gives young people a place to read, use computers, learn digital skills and develop ideas. It opened in December 2023 with ten computers and more than 3,000 books.',
            'blocks'   => [['climate-education-youth-leadership', 'Apala One Stop Youth Centre']],
            'results'  => [
                ['10', 'Computers when the centre opened', 'December 2023'],
                ['3,000+', 'Books', 'December 2023'],
                ['~400', 'Young people the centre has space to serve', 'Capacity at opening'],
            ],
            'gallery'  => [],
        ],
        'tanzania-climate-education' => [
            'title'    => 'Climate Education and Youth Enterprise in Tanzania',
            'area'     => 'climate-education-youth-leadership',
            'location' => 'Tanzania', 'country' => 'Tanzania',
            'partner'  => 'FADECO',
            'who'      => 'Students, young people and communities reached by FADECO Radio',
            'summary'  => 'Together with FADECO, we use schools, community radio and practical training to reach young people in Tanzania, from Eco Clubs and environmental leadership to reusable sanitary-pad production and liquid soap-making.',
            'blocks'   => [['climate-education-youth-leadership', 'Climate Education and Youth Enterprise in Tanzania']],
            'results'  => [],
            'gallery'  => [],
        ],
        'pre-cop-climate-academy' => [
            'title'    => 'BetterLife Pre-COP Climate Academy',
            'area'     => 'climate-education-youth-leadership',
            'location' => 'Virtual, Uganda and across Africa', 'country' => 'Regional',
            'partner'  => 'Moonshot',
            'who'      => 'Young people from Uganda and across Africa',
            'summary'  => 'A virtual academy, supported by Moonshot, that helps young Africans understand climate negotiations, from adaptation and climate finance to loss and damage, and connect them to what is happening in their own communities.',
            'blocks'   => [['climate-education-youth-leadership', 'BetterLife Pre-COP Climate Academy']],
            'results'  => [],
            'gallery'  => [],
        ],
        'climate-innovation-hackathons' => [
            'title'    => 'Climate Innovation Hackathons',
            'area'     => 'climate-education-youth-leadership',
            'partner'  => 'ICPAC innovation ecosystem',
            'who'      => 'Young innovators',
            'summary'  => 'Hackathons give young people a real problem, a team and room to build. Participants use technology, entrepreneurship and local knowledge to develop practical responses to climate and community challenges.',
            'blocks'   => [['climate-education-youth-leadership', 'Climate Innovation Hackathons']],
            'results'  => [],
            'gallery'  => [],
        ],
        'betterlife-renewable-pathways' => [
            'title'    => 'BetterLife Renewable Pathways',
            'area'     => 'clean-energy-water-restoration',
            'location' => 'Uganda', 'country' => 'Uganda',
            'who'      => 'Schools, learners and communities',
            'summary'  => 'We work with schools and communities on waste separation, plastic banks, recycling and the responsible reuse of materials. Four school plastic banks give learners a practical way to keep plastics out of the environment.',
            'image'    => ['assets/img/betterlifeint-source/projects/project-renewable-pathways-alt.jpg', 'A man adding a bottle to a plastic bank', '50% 25%'],
            'blocks'   => [['clean-energy-water-restoration', 'BetterLife Renewable Pathways']],
            'results'  => [['4', 'School plastic banks', 'Supported through Renewable Pathways']],
            'gallery'  => [],
            'related'  => [['project.php?slug=green-libraries-eco-labs', 'School climate clubs and Eco Labs']],
        ],
        'soilla' => [
            'title'    => 'Soilla',
            'area'     => 'digital-innovation',
            'location' => 'In use in Uganda', 'country' => 'Uganda',
            'partner'  => 'World Food Programme (engagement on farmer information and data)',
            'status'   => 'Launched 2023',
            'who'      => 'Smallholder farmers, supported by field training',
            'summary'  => 'Our digital agricultural advisory platform gives farmers soil and crop guidance, climate information, market prices and agricultural services. It is paired with field training, because a phone and data alone do not make a tool useful.',
            'image'    => ['assets/img/soilla-app-portrait.webp', 'A young man holding up a phone showing the Soilla app', '50% 30%'],
            'blocks'   => [['digital-innovation', 'Soilla']],
            'results'  => [],
            'gallery'  => [
                ['assets/img/soilla-app-field-demo.webp', 'The Soilla app open on a phone in a field'],
                ['assets/img/betterlifeint-source/projects/project-soilla-app-alt.jpeg', 'Screens from the Soilla app'],
            ],
            'related'  => [['project.php?slug=agribusiness-connekt', 'Agribusiness Connekt: the business side of the farm']],
        ],
        'agribusiness-connekt' => [
            'title'    => 'Agribusiness Connekt',
            'area'     => 'digital-innovation', 'also' => ['green-skills-livelihoods'],
            'location' => 'In use in Uganda', 'country' => 'Uganda',
            'status'   => 'Work continuing in 2026',
            'who'      => 'Farmers and small agricultural enterprises, including those trained through BetterLife programmes',
            'summary'  => 'Agribusiness Connekt links farmers and small agricultural enterprises to buyers, finance, services and market information, giving producers trained through BetterLife programmes a route from production into longer-term enterprise.',
            'image'    => ['assets/img/agribusiness-connekt-app.webp', 'A person holding up a phone showing the Agribusiness Connekt app', '50% 42%'],
            // One authoritative description, combined from the two earlier versions without repetition
            'blocks'   => [['green-skills-livelihoods', 'Agribusiness Connekt'], ['digital-innovation', 'Agribusiness Connekt', 1]],
            'results'  => [],
            'gallery'  => [
                ['assets/img/about/yumbe-connekt-app.jpg', 'Produce listings in the Agribusiness Connekt app'],
            ],
            'related'  => [['project.php?slug=soilla', 'Soilla: guidance for production decisions'], ['project.php?slug=smiles', 'SMILES: skills and enterprise']],
        ],
    ];
}

/** Narrative paragraphs for a project or area, read from Admin -> Page Content. */
function pp_paragraphs(PDO $pdo, array $blocks): array
{
    $out = [];
    foreach ($blocks as $b) {
        [$section, $title] = $b; $from = $b[2] ?? 0;
        $st = $pdo->prepare("SELECT body FROM content_items WHERE page = 'programs' AND section_key = ? AND title = ? AND status = 1 ORDER BY sort_order LIMIT 1");
        $st->execute([$section, $title]);
        $body = (string) $st->fetchColumn();
        $paras = array_values(array_filter(array_map('trim', preg_split('/\R\s*\R/u', $body))));
        $out = array_merge($out, array_slice($paras, $from));
    }
    return $out;
}

/** Link to a project: its own established page if it has one, otherwise project.php. */
function pp_project_url(string $slug, array $p): string
{
    return SITE_URL . '/' . ($p['href'] ?? 'project.php?slug=' . rawurlencode($slug));
}

function pp_area_url(string $slug): string
{
    return SITE_URL . '/program.php?slug=' . rawurlencode($slug);
}

/** Card for a project (overview, area pages and the directory). */
function pp_project_card(string $slug, array $p, array $areas, string $sizes = '(max-width: 720px) 100vw, (max-width: 1100px) 50vw, 380px', bool $showArea = true): string
{
    $url = pp_project_url($slug, $p);
    $area = $areas[$p['area']]['short'] ?? '';
    ob_start(); ?>
    <article class="pg-project ab-reveal">
      <div class="pg-project-media<?= empty($p['image']) ? ' is-text' : '' ?>">
        <?php if (!empty($p['image'])): ?>
          <?= ab_img($p['image'][0], $p['image'][1], '', true, 'style="object-position: ' . h($p['image'][2] ?? '50% 50%') . '"', $sizes) ?>
        <?php else: ?>
          <span class="pg-project-mono" aria-hidden="true"><?= icon($areas[$p['area']]['icon'] ?? 'leaf', 30) ?></span>
        <?php endif; ?>
        <?php if (!empty($p['location'])): ?><span class="pg-chip"><?= icon('map-pin', 13) ?> <?= h($p['location']) ?></span><?php endif; ?>
      </div>
      <div class="pg-project-body">
        <?php if ($showArea && $area): ?><span class="pg-kicker"><?= h($area) ?></span><?php endif; ?>
        <h3><a href="<?= h($url) ?>"><?= h($p['title']) ?></a></h3>
        <p><?= h($p['summary']) ?></p>
        <?php if (!empty($p['partner'])): ?><p class="pg-partner"><?= icon('heart', 14) ?> With <?= h($p['partner']) ?></p><?php endif; ?>
        <span class="pg-more" aria-hidden="true"><?= !empty($p['href']) ? 'Visit the farm' : 'Read the project' ?> <?= icon('arrow-right', 15) ?></span>
      </div>
    </article>
    <?php return ob_get_clean();
}

/** One documented result with its context kept attached. */
function pp_result(array $r, ?string $projectSlug = null, array $projects = []): string
{
    [$value, $label, $context] = $r;
    $link = $r[3] ?? $projectSlug;
    $linkIsProject = $link && isset($projects[$link]);
    ob_start(); ?>
    <li class="pg-result ab-reveal"<?= in_array('confirm', $r, true) ? ' data-confirm="true"' : '' ?>>
      <strong><?= h($value) ?></strong>
      <span class="pg-result-label"><?= h($label) ?></span>
      <span class="pg-result-context"><?php if ($linkIsProject): ?><a href="<?= h(pp_project_url($link, $projects[$link])) ?>"><?= h($projects[$link]['title']) ?></a> · <?php endif; ?><?= h($context) ?></span>
    </li>
    <?php return ob_get_clean();
}
