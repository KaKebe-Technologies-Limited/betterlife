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
            'image'  => ['assets/img/programmes/rukungiri-maize-woman.jpg', 'A woman standing in her maize field in Rukungiri', '50% 50%'],
            'hero_side' => 'right',
            'hero'   => ['assets/img/programmes/yumbe-trellis-garden.jpg', 'A BetterLife team member in a trellised vegetable garden in Yumbe', '62% 50%'],
            'collage' => [
                ['assets/img/programmes/yumbe-sack-garden.jpg', 'A tiered sack garden in Yumbe'],
                ['assets/img/programmes/yumbe-cabbage-mulch.jpg', 'A cabbage growing through straw mulch in Yumbe'],
                ['assets/img/programmes/rukungiri-hoeing.jpg', 'Preparing the ground with a hoe in Rukungiri'],
            ],
            'feature' => 'womens-climate-resilience-yumbe',
            'evidence_photo' => ['assets/img/programmes/yumbe-participant-red.jpg', 'Women in the programme at a session in Yumbe'],
            'invite_bg' => 'assets/img/programmes/yumbe-planted-field.jpg',
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
                ['assets/img/programmes/yumbe-drip-rows.jpg', 'Drip-irrigated seedlings in Yumbe'],
                ['assets/img/programmes/yumbe-seedling-trays.jpg', 'Seedling trays in a nursery in Yumbe'],
                ['assets/img/programmes/vertical-pipe-garden.jpg', 'A vertical garden made from pipes'],
                ['assets/img/programmes/rukungiri-leafy-crop.jpg', 'A leafy crop in Rukungiri'],
                ['assets/img/programmes/yumbe-greenhouse-frame.jpg', 'A greenhouse in Yumbe'],
                ['assets/img/about/yumbe-beehives.jpg', 'Beehives at a BetterLife-supported site in Yumbe'],
            ],
            'invite' => 'Help more families grow food through a dry season: fund demonstration gardens, seedlings and irrigation, or share agronomy expertise.',
        ],
        'green-skills-livelihoods' => [
            'short'  => 'RISE: Resilience through Inclusive Skills and Enterprise',
            'formal' => 'RISE: Resilience through Inclusive Skills and Enterprise',
            'icon'   => 'basket',
            'card'   => 'Learning a trade is one step. Finding tools, capital and customers is another. We pair practical skills, from tailoring and carpentry to poultry and solar technology, with savings groups, enterprise coaching, finance and routes to market.',
            'image'  => ['assets/img/programmes/yumbe-market-shade.jpg', 'Women selling produce under a shade shelter at a market in Yumbe', '40% 55%'],
            'hero'   => ['assets/img/market-stall-vendor.webp', 'A woman standing at her market stall in Yumbe', '70% 35%'],
            'collage' => [
                ['assets/img/programmes/yumbe-shop-counter.jpg', 'A woman serving at her shop counter in Yumbe'],
                ['assets/img/programmes/carpentry-workshop.jpg', 'A carpentry workshop in Yumbe'],
                ['assets/img/programmes/rukungiri-scale.jpg', 'A hanging scale used to weigh produce in Rukungiri'],
            ],
            'feature' => 'rise',
            'evidence_photo' => ['assets/img/vendor-and-children-food-stall.webp', 'A woman preparing food at her stall in Yumbe'],
            'invite_bg' => 'assets/img/programmes/yumbe-group-trees.jpg',
            'lead'   => 'Learning a trade is one step. Finding tools, capital and customers is another.',
            'intro'  => [
                'BetterLife combines practical skills with enterprise coaching, savings, finance and market connections. Participants train in areas suited to local demand, including agriculture, poultry, carpentry, tailoring, barbering, weaving and solar technology.',
                'The work continues beyond the training day. We help people test a business idea, understand costs, join a savings group, approach finance and find a route into the market.',
            ],
            'who'    => 'Refugees and host-community members, women and young people looking for a route into work or enterprise.',
            'activities' => ['Vocational skills training', 'Enterprise coaching', 'Savings groups', 'Access to finance and small loans', 'Mentorship and business support', 'Market connections'],
            'evidence' => [
                ['78%', 'Of participants moved into sustainable income pathways', 'RISE, reported across target groups', 'rise'],
                ['85%', 'Reported improved refugee-host relations', 'RISE, reported across target groups', 'rise'],
            ],
            'gallery' => [
                ['assets/img/programmes/yumbe-poultry-house.jpg', 'A woman standing in the doorway of her poultry house in Yumbe'],
                ['assets/img/programmes/yumbe-poultry-flock.jpg', 'A poultry house in Yumbe'],
                ['assets/img/programmes/yumbe-coaching-tree.jpg', 'A BetterLife team member speaking with a women’s group in Yumbe'],
                ['assets/img/programmes/yumbe-poultry-closeup.jpg', 'Chickens at a feeder in Yumbe'],
                ['assets/img/programmes/egg-incubator.jpg', 'Trays of eggs in an incubator, with the BetterLife team looking on'],
            ],
            'invite' => 'Back the step after training: start-up tools, small-loan guarantees, business mentoring or a market connection for producers.',
        ],
        'climate-education-youth-leadership' => [
            'short'  => 'Climate Education and Youth Leadership',
            'formal' => 'Climate Education, Youth Leadership and Innovation',
            'icon'   => 'book',
            'card'   => 'Young people will live longest with today’s climate decisions. Through Green Libraries, Eco Labs, school clubs, youth centres, Climate Academies and innovation challenges, they gain the knowledge and confidence to take part in those decisions.',
            'image'  => ['assets/img/programmes/lcoy-youth-panel.jpg', 'A young speaker on a BetterLife youth panel at LCOY Uganda 2026', '62% 40%'],
            'hero_side' => 'right',
            'hero'   => ['assets/img/about/rukungiri-pupils-desks.jpg', 'Pupils writing at their desks in a classroom in Rukungiri', '70% 50%'],
            'collage' => [
                ['assets/img/programmes/lcoy-group.jpg', 'Speakers and participants at a BetterLife session at LCOY Uganda 2026'],
                ['assets/img/programmes/lcoy-speaker-banner.jpg', 'Speaking beside the BetterLife banner at LCOY Uganda 2026'],
                ['assets/img/programmes/rukungiri-boy-writing.jpg', 'A pupil writing at his desk in Rukungiri'],
            ],
            'feature' => 'green-libraries-eco-labs',
            'evidence_photo' => ['assets/img/betterlifeint-source/programs/program-photo-8.jpg', 'Pupils gathered outdoors for a school session'],
            'invite_bg' => 'assets/img/programmes/lcoy-cheer.jpg',
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
                ['assets/img/programmes/lcoy-speaker.jpg', 'A young speaker at LCOY Uganda 2026'],
                ['assets/img/programmes/rukungiri-pupil-cup.jpg', 'A pupil with his cup at school in Rukungiri'],
                ['assets/img/programmes/lcoy-panel-seated.jpg', 'Panellists at a BetterLife session at LCOY Uganda 2026'],
                ['assets/img/programmes/lcoy-audience.jpg', 'Young people in the audience at LCOY Uganda 2026'],
            ],
            'invite' => 'Stock a Green Library, equip an Eco Lab, mentor a hackathon team or sponsor young people to join the climate academy.',
        ],
        'clean-energy-water-restoration' => [
            'short'  => 'Clean Energy, Water and Restoration',
            'formal' => 'Clean Energy, Water and Environmental Restoration',
            'icon'   => 'sun',
            'card'   => 'Energy poverty, water insecurity and environmental loss often sit inside the same household. We work with communities on tree nurseries, biogas, briquettes, waste recovery and water access, easing pressure on families and the land at the same time.',
            'image'  => ['assets/img/programmes/clean-cooking-cookoff.jpg', 'BetterLife team members with students at a clean cooking cook-off', '60% 40%'],
            'hero'   => ['assets/img/programmes/rukungiri-solar-sky.jpg', 'A solar panel under a wide sky in Rukungiri', '65% 40%'],
            'collage' => [
                ['assets/img/programmes/yumbe-tree-nursery.jpg', 'Tree seedlings under a shade net in Yumbe'],
                ['assets/img/programmes/yumbe-water-point.jpg', 'Women collecting water at a water point in Yumbe'],
                ['assets/img/programmes/yumbe-nursery-shade.jpg', 'A shaded seedling nursery in Yumbe'],
            ],
            'feature' => 'betterlife-renewable-pathways',
            'evidence_photo' => ['assets/img/programmes/yumbe-water-bucket.jpg', 'A woman carrying water past a maize field in Yumbe'],
            'invite_bg' => 'assets/img/programmes/rukungiri-eucalyptus.jpg',
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
                ['assets/img/programmes/yumbe-handpump.jpg', 'Using a hand pump at a water point in Yumbe'],
                ['assets/img/programmes/yumbe-wetland.jpg', 'A wetland in Yumbe'],
                ['assets/img/programmes/rukungiri-stream.jpg', 'A BetterLife team member at a stream in Rukungiri'],
                ['assets/img/programmes/yumbe-jerrycans.jpg', 'Jerrycans lined up at a water point in Yumbe'],
                ['assets/img/programmes/yumbe-water-girl.jpg', 'A girl at a water point in Yumbe'],
            ],
            'invite' => 'Fund a borehole, a household biogas system or a tree nursery, or bring engineering skills to keep systems running.',
        ],
        'digital-innovation' => [
            'short'  => 'Digital Tools for Farmers',
            'formal' => 'Digital Innovation for Agriculture',
            'icon'   => 'phone',
            'card'   => 'Technology is useful when it shortens the distance between a farmer and a good decision. Soilla and Agribusiness Connekt bring soil advice, climate information, finance and buyers closer, always paired with face-to-face support.',
            'image'  => ['assets/img/programmes/rukungiri-soilla-phone.jpg', 'A young farmer smiling as he shows the Soilla app on his phone in Rukungiri', '50% 30%'],
            'hero_side' => 'narrow',
            'hero'   => ['assets/img/programmes/yumbe-weather-app.jpg', 'A weather forecast open on a phone held up in a field in Yumbe', '50% 40%'],
            'collage' => [
                ['assets/img/programmes/yumbe-phones-session.jpg', 'Two participants checking their phones during a training session in Yumbe'],
                ['assets/img/programmes/yumbe-phone-session.jpg', 'Women looking at a phone together during a session in Yumbe'],
                ['assets/img/betterlifeint-source/projects/project-soilla-app-alt.jpeg', 'Screens from the Soilla app'],
            ],
            'feature' => 'soilla',
            'evidence_photo' => null,
            'invite_bg' => null,
            'lead'   => 'Technology is useful when it shortens the distance between a farmer and a good decision.',
            'intro'  => [
                'BetterLife develops digital tools around practical gaps: understanding soil, preparing for weather, finding a service, accessing finance and reaching a buyer.',
            ],
            'who'    => 'Smallholder farmers and small agricultural enterprises, supported by BetterLife field teams.',
            'activities' => ['Soil and crop guidance', 'Climate information', 'Market prices', 'Finding suppliers, experts and services', 'Links to buyers and finance', 'Field training alongside every tool'],
            'evidence' => [],
            'gallery' => [],
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
            'image'    => ['assets/img/programmes/yumbe-sack-garden-session.jpg', 'Women laughing as a BetterLife trainer teaches beside their sack gardens in Yumbe', '50% 40%'],
            'hero'     => ['assets/img/betterlifeint-source/programs/program-photo-11.jpg', 'Women laughing together at a programme session in Yumbe', '50% 30%'],
            'blocks'   => [['climate-resilient-agriculture', 'Women’s Climate Resilience in Yumbe']],
            'results'  => [
                ['22% → 92%', 'Knowledge of climate-smart agriculture', 'Among the 72 women who completed structured training'],
                ['72%', 'Adopted sack or box gardening', 'Women in the programme, reported after training'],
                ['63%', 'Took up composting', 'Women in the programme, reported after training'],
                ['35%', 'Average reduction in household spending on vegetables', 'Reported by participating households'],
            ],
            'gallery'  => [
                ['assets/img/programmes/yumbe-session-laughter.jpg', 'Women and the BetterLife team laughing together at a session in Yumbe'],
                ['assets/img/programmes/yumbe-training-banner.jpg', 'Women at an outdoor training session in Yumbe'],
                ['assets/img/programmes/yumbe-mother-baby.jpg', 'A mother and her baby at a programme session in Yumbe'],
                ['assets/img/programmes/yumbe-greenhouse-ladder.jpg', 'Building a greenhouse in Yumbe'],
                ['assets/img/programmes/yumbe-participant-a.jpg', 'A participant in the programme in Yumbe'],
                ['assets/img/programmes/yumbe-greenhouse-cover.jpg', 'Pulling the cover over the greenhouse in Yumbe'],
                ['assets/img/programmes/yumbe-trainer-flipchart.jpg', 'A trainer at a flip chart during a session in Yumbe'],
                ['assets/img/programmes/yumbe-greenhouse-vent.jpg', 'Fitting a roof vent on the greenhouse in Yumbe'],
                ['assets/img/programmes/yumbe-large-session.jpg', 'Women in programme T-shirts at a training session in Yumbe'],
                ['assets/img/programmes/yumbe-team-four.jpg', 'Four members of the BetterLife team in Yumbe'],
                ['assets/img/programmes/yumbe-participant-b.jpg', 'A participant in the programme in Yumbe'],
                ['assets/img/programmes/yumbe-cabbage-rows.jpg', 'Rows of cabbages in Yumbe'],
            ],
            'related'  => [['about.php#impact', 'Programme results on our About page'], ['project.php?slug=green-leaf-platforms-uganda', 'Green Leaf Platforms Uganda builds on this lesson']],
        ],
        'green-leaf-platforms-uganda' => [
            'title'    => 'Green Leaf Platforms Uganda',
            'area'     => 'climate-resilient-agriculture',
            'location' => '12 districts, Uganda', 'country' => 'Uganda',
            'partner'  => 'Farm Radio International',
            'status'   => 'Model launched in 2026',
            'who'      => 'Women in Women’s Action Circles, built on savings groups, farmer organisations, cooperatives and community networks',
            'summary'  => 'With Farm Radio International, we connect radio and digital farming content to Women’s Action Circles across 12 districts, so women can discuss what they hear, visit demonstration plots and test practices together in groups they already trust.',
            'image'    => ['assets/img/programmes/wac-model-launch.jpg', 'Signing the board at the launch of the Women’s Action Circle model in 12 districts', '55% 40%'],
            'hero'     => ['assets/img/about/soroti-wac-gathering.jpg', 'Women gathered for a Women’s Action Circle session in Soroti', '50% 45%'],
            'launch'   => 'The Women’s Action Circle model was launched at a BetterLife session at LCOY Uganda 2026.',
            'blocks'   => [['climate-resilient-agriculture', 'Green Leaf Platforms Uganda']],
            'results'  => [],
            'gallery'  => [
                ['assets/img/programmes/wac-launch-signing.jpg', 'Signing the launch board for the Women’s Action Circle model at LCOY Uganda 2026'],
                ['assets/img/programmes/mukono-wac-notes.jpg', 'Taking notes at a Women’s Action Circle session in Mukono'],
                ['assets/img/programmes/mukono-wac-circle.jpg', 'Participants at a Women’s Action Circle session in Mukono'],
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
        'rise' => [
            'title'    => 'RISE',
            'formal'   => 'Resilience through Inclusive Skills and Enterprise',
            'area'     => 'green-skills-livelihoods',
            'who'      => 'Refugees and host-community members',
            'summary'  => 'RISE brings refugees and host-community members into the same training groups and local economy. Participants learn practical trades, then receive mentorship, business support, market connections and access to small loans, building relationships as they train, save and trade together.',
            'image'    => ['assets/img/programmes/yumbe-poultry-care.jpg', 'A young man refilling a drinker in a poultry house in Yumbe, Uganda', '45% 8%'],
            'hero'     => ['assets/img/betterlifeint-source/projects/project-smiles-alt.jpg', 'Participants holding rolled mats at a RISE activity', '50% 35%'],
            'blocks'   => [['green-skills-livelihoods', 'RISE']],
            'results'  => [
                ['78%', 'Moved into sustainable income pathways', 'Reported across RISE target groups'],
                ['85%', 'Reported improved refugee-host relations', 'Reported across RISE target groups'],
                ['40%', 'Fall in food insecurity', 'Reported across RISE target groups'],
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
            'hero'     => ['assets/img/betterlifeint-source/programs/program-photo-5.jpg', 'Students holding placards at a school environment event', '50% 40%'],
            'blocks'   => [['climate-education-youth-leadership', 'Green Libraries, Eco Labs and School Climate Clubs']],
            'results'  => [
                ['4,500+', 'Students engaged through school climate education', 'Across BetterLife’s school work'],
                ['20+', 'Green Libraries and Eco Labs supported', 'Across BetterLife’s school work'],
            ],
            'gallery'  => [],
            'related'  => [['project.php?slug=betterlife-renewable-pathways', 'School plastic banks: BetterLife Renewable Pathways']],
        ],
        'apala-youth-centre' => [
            'title'    => 'Apala One Stop Youth Centre',
            'area'     => 'climate-education-youth-leadership',
            'location' => 'Alebtong, Uganda', 'country' => 'Uganda',
            'partner'  => 'COMESA, Save the Children and Uganda’s National Youth Council',
            'status'   => 'Opened December 2023',
            'who'      => 'Young people in and around Alebtong',
            'summary'  => 'In Alebtong, the Apala One Stop Youth Centre gives young people a place to read, use computers, learn digital skills, develop ideas and build peace. It opened in December 2023, with support from COMESA, Save the Children and Uganda’s National Youth Council, with ten computers and more than 3,000 books.',
            'blocks'   => [['climate-education-youth-leadership', 'Apala One Stop Youth Centre']],
            'results'  => [
                ['10', 'Computers when the centre opened', 'December 2023'],
                ['3,000+', 'Books', 'December 2023'],
                ['~400', 'Young people the centre has space to serve', 'Capacity at opening'],
                ['20', 'Young people trained to manage the hub', 'Before the launch, December 2023'],
            ],
            'image'    => ['assets/img/programmes/apala-photo-frame.jpg', 'Two young people smiling through an Apala One Stop Youth Centre photo frame', '50% 45%'],
            'hero'     => ['assets/img/programmes/apala-ribbon-cutting.jpg', 'Cutting the ribbon at the launch of the Apala One Stop Youth Centre in Alebtong', '50% 35%'],
            'gallery'  => [
                ['assets/img/programmes/apala-photo-frame.jpg', 'At the Apala launch: a photo frame reading Innovate, Educate, Elevate, with the partners’ logos'],
            ],
        ],
        'tanzania-climate-education' => [
            'title'    => 'Climate Education and Youth Enterprise in Tanzania',
            'area'     => 'climate-education-youth-leadership',
            'location' => 'Karagwe, Tanzania', 'country' => 'Tanzania',
            'partner'  => 'FADECO, with support from AllPeopleBeHappy',
            'who'      => 'Students, young people and communities reached by FADECO Radio',
            'summary'  => 'Together with FADECO in Karagwe, we use schools, community radio and practical training to reach young people in Tanzania, from Eco Clubs and climate-smart agriculture to reusable sanitary-pad production and liquid soap-making.',
            'blocks'   => [['climate-education-youth-leadership', 'Climate Education and Youth Enterprise in Tanzania']],
            'results'  => [
                ['8,000+', 'Trees planted', 'Empowering Communities Through Climate Action, by July 2025'],
                ['30,000+', 'People reached through FADECO Radio', 'Empowering Communities Through Climate Action, by July 2025'],
                ['25+', 'Young entrepreneurs trained in climate-smart and green business', 'Empowering Communities Through Climate Action, by July 2025'],
            ],
            // Photographs from FADECO's Empowering Communities Through Climate Action, as published by AllPeopleBeHappy
            'image'    => ['assets/img/programmes/tanzania-nursery-seedlings.jpg', 'Two men holding seedlings in a greenhouse tree nursery in Karagwe', '50% 40%'],
            'hero'     => ['assets/img/programmes/tanzania-tree-nursery.jpg', 'A group working among the raised beds of a community tree nursery in Karagwe', '55% 55%'],
            'gallery'  => [
                ['assets/img/programmes/tanzania-launch-students.jpg', 'Students and teachers at the launch of Empowering Communities Through Climate Action in Karagwe'],
                ['assets/img/programmes/tanzania-youth-training.jpg', 'Young people at a training session under a tent in Karagwe'],
                ['assets/img/programmes/tanzania-compost.jpg', 'Preparing natural fertiliser in a compost heap'],
                ['assets/img/programmes/tanzania-biogas-training.jpg', 'A group at a tubular biogas installation during training'],
                ['assets/img/programmes/tanzania-nursery-seedlings.jpg', 'Two men holding seedlings in a greenhouse tree nursery'],
                ['assets/img/programmes/tanzania-fadeco-group.jpg', 'Participants and partners gathered outside FADECO in Karagwe'],
            ],
            'credit'   => 'Photographs: FADECO / AllPeopleBeHappy',
        ],
        'climate-leadership-academy' => [
            'title'    => 'BetterLife Climate Leadership Academy',
            'area'     => 'climate-education-youth-leadership',
            'location' => 'Virtual, Uganda and across Africa', 'country' => 'Regional',
            'partner'  => 'Moonshot',
            'who'      => 'Young people from Uganda and across Africa',
            'summary'  => 'A virtual academy, supported by Moonshot, that helps young Africans understand climate negotiations, from adaptation and climate finance to loss and damage, and connect them to what is happening in their own communities.',
            'blocks'   => [['climate-education-youth-leadership', 'BetterLife Climate Leadership Academy']],
            'results'  => [],
            'gallery'  => [],
        ],
        'climate-innovation-hackathons' => [
            'title'    => 'Climate Innovation Hackathons',
            'area'     => 'climate-education-youth-leadership',
            'location' => 'The IGAD region', 'country' => 'Regional',
            'partner'  => 'ICPAC and IGAD’s IDDRSI',
            'who'      => 'Young innovators, mentored by BetterLife',
            'summary'  => 'Hackathons give young people a real problem, a team and room to build. BetterLife mentors teams in IGAD’s climate hackathons, hosted by ICPAC, as they use technology, entrepreneurship and local knowledge to develop practical responses to climate and community challenges.',
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
            'image'    => ['assets/img/soil-sample-in-hand.webp', 'Soil falling from a farmer’s hand in Rukungiri', '50% 45%'],
            'blocks'   => [['digital-innovation', 'Soilla']],
            'results'  => [],
            'gallery'  => [],
            'related'  => [['project.php?slug=agribusiness-connekt', 'Agribusiness Connekt: the business side of the farm']],
        ],
        'agribusiness-connekt' => [
            'title'    => 'Agribusiness Connekt',
            'area'     => 'digital-innovation', 'also' => ['green-skills-livelihoods'],
            'location' => 'In use in Uganda', 'country' => 'Uganda',
            'status'   => 'Work continuing in 2026',
            'who'      => 'Farmers and small agricultural enterprises, including those trained through BetterLife programmes',
            'summary'  => 'Agribusiness Connekt links farmers and small agricultural enterprises to buyers, finance, services and market information, giving producers trained through BetterLife programmes a route from production into longer-term enterprise.',
            'image'    => ['assets/img/about/yumbe-connekt-app.jpg', 'Produce listings in the Agribusiness Connekt app on a phone in Yumbe', '50% 50%'],
            // One authoritative description, combined from the two earlier versions without repetition
            'blocks'   => [['green-skills-livelihoods', 'Agribusiness Connekt'], ['digital-innovation', 'Agribusiness Connekt', 1]],
            'results'  => [],
            'gallery'  => [],
            'related'  => [['project.php?slug=soilla', 'Soilla: guidance for production decisions'], ['project.php?slug=rise', 'RISE: skills and enterprise']],
        ],
    ];
}

/** Narrative paragraphs for a project or area, read from Admin -> Page Content. */
function pp_paragraphs(PDO $pdo, array $blocks): array
{
    $out = [];
    foreach ($blocks as $b) {
        [$section, $title] = $b; $from = $b[2] ?? 0;
        try {
            $st = $pdo->prepare("SELECT body FROM content_items WHERE page = 'programs' AND section_key = ? AND title = ? AND status = 1 ORDER BY sort_order LIMIT 1");
            $st->execute([$section, $title]);
            $body = (string) $st->fetchColumn();
        } catch (PDOException $e) {
            error_log('pp_paragraphs: ' . $e->getMessage());
            $body = '';
        }
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
      </div>
      <div class="pg-project-body">
        <?php if ($showArea && $area): ?><span class="pg-kicker"><?= h($area) ?></span><?php endif; ?>
        <h3><a href="<?= h($url) ?>"><?= h($p['title']) ?></a></h3>
        <?php if (!empty($p['location'])): ?><p class="pg-card-place"><?= icon('map-pin', 13) ?> <?= h($p['location']) ?></p><?php endif; ?>
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

/**
 * A quotation set large with a painted underline. Quotes in Denise Ayebare's voice are drafts
 * for her approval; participant quotes are only added once someone has said them and agreed
 * to their use (add them to a project as 'voices' => [[quote, name, role], ...]).
 */
function pp_voice(string $quote, string $name, string $role, int $seed = 141): string
{
    ob_start(); ?>
    <figure class="pg-voice ab-reveal">
      <span class="pg-voice-mark" aria-hidden="true">“</span>
      <blockquote><p><?= h($quote) ?></p></blockquote>
      <svg class="pg-voice-stroke" viewBox="0 0 240 30" preserveAspectRatio="none" aria-hidden="true" focusable="false"><path filter="url(#lpBrush)" class="f-green" d="<?= lp_brush_d(4, 17, 238, 14, 22, $seed, 0.02) ?>"/></svg>
      <figcaption><strong><?= h($name) ?></strong><span><?= h($role) ?></span></figcaption>
    </figure>
    <?php return ob_get_clean();
}

const PP_FOUNDER = ['Denise Ayebare', 'Founder and Executive Director'];

/**
 * Participants in their own words, as supplied by BetterLife (Denise Ayebare confirmed in October 2026 that
 * these were said, and gave the speakers' names). Quotes are kept exactly as given, in the language they were said;
 * French and Arabic carry the English translation BetterLife supplied.
 */
function pp_quotes(): array
{
    return [
        'grace'     => ['name' => 'Grace Uwimana', 'role' => 'Farming participant', 'lang' => 'en',
            'q' => '“During the training, I wanted to know what I could grow with the little space I had at home. Seeing the demonstration garden helped me understand where to start.”'],
        'peter'     => ['name' => 'Peter Lado', 'role' => 'Livelihoods participant', 'lang' => 'en',
            'q' => '“My first question was what would happen after the training. I wanted to understand how to find customers and what I would need to begin working.”'],
        'amina'     => ['name' => 'Amina Hassan', 'role' => 'Community group participant', 'lang' => 'en',
            'q' => '“We came to the group with different experiences, but many of our questions were the same: how to earn, how to save and how to provide for our families.”'],
        'chantal'   => ['name' => 'Chantal Mukamana', 'role' => 'Agricultural training participant', 'lang' => 'fr',
            'q' => '« J’avais besoin de voir comment faire, pas seulement d’écouter les explications. Dans le jardin de démonstration, je pouvais essayer et poser mes questions. »',
            'en' => '“I needed to see how to do it, not just listen to explanations. In the demonstration garden, I could try and ask my questions.”'],
        'josephine' => ['name' => 'Josephine Ilunga', 'role' => 'Livelihoods participant', 'lang' => 'fr',
            'q' => '« Quand on doit recommencer sa vie ailleurs, on apporte aussi ses compétences. J’aimerais pouvoir les utiliser pour gagner ma vie ici. »',
            'en' => '“When you have to start your life again elsewhere, you also bring your skills. I would like to use mine to earn a living here.”'],
        'esther'    => ['name' => 'Esther Kabeya', 'role' => 'Savings group participant', 'lang' => 'fr',
            'q' => '« Dans le groupe, nous pouvons parler de nos difficultés et réfléchir ensemble. Pour moi, épargner commence par savoir ce que je peux mettre de côté sans priver ma famille. »',
            'en' => '“In the group, we can discuss our difficulties and think together. For me, saving starts with knowing what I can set aside without depriving my family.”'],
        'mariam'    => ['name' => 'Mariam Adam', 'name_ar' => 'مريم آدم', 'role' => 'Farming participant', 'lang' => 'ar',
            'q' => '«أريد أن أزرع شيئًا نستطيع أن نأكله في البيت. وإذا بقي جزء من المحصول، يمكنني بيعه لتغطية بعض المصاريف.»',
            'en' => '“I want to grow something we can eat at home. If some of the harvest remains, I can sell it to cover some expenses.”'],
        'ahmed'     => ['name' => 'Ahmed Musa', 'name_ar' => 'أحمد موسى', 'role' => 'Skills training participant', 'lang' => 'ar',
            'q' => '«لديّ مهارة، لكن بدء العمل يحتاج أيضًا إلى أدوات وزبائن. هذا ما أريد أن أعرفه: كيف أبدأ بالإمكانيات الموجودة عندي؟»',
            'en' => '“I have a skill, but starting work also requires tools and customers. That is what I want to know: how do I begin with the resources I have?”'],
        'fatima'    => ['name' => 'Fatima Idris', 'name_ar' => 'فاطمة إدريس', 'role' => 'Community group participant', 'lang' => 'ar',
            'q' => '«في المجموعة أستطيع أن أسأل عندما لا أفهم. وأحيانًا يشرح أحد المشاركين الفكرة بطريقة أقرب إلى تجربتي.»',
            'en' => '“In the group, I can ask when I do not understand. Sometimes another participant explains the idea in a way that is closer to my experience.”'],
    ];
}

/** Arabic typeface, loaded only when an Arabic quote is on the page. */
function pp_quotes_head(array $keys): string
{
    $all = pp_quotes();
    foreach ($keys as $k) if (($all[$k]['lang'] ?? '') === 'ar') return '<link href="https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400;500&display=swap" rel="stylesheet">';
    return '';
}

/** Participants' words as a conversation thread beside a short introduction. */
function pp_quotes_section(array $keys, string $title = 'Voices from the programmes', string $id = 'pgVoicesTitle'): string
{
    $all = pp_quotes();
    $items = array_values(array_filter(array_map(fn($k) => $all[$k] ?? null, $keys)));
    if (!$items) return '';
    $langName = ['en' => 'English', 'fr' => 'Français', 'ar' => 'العربية'];
    ob_start(); ?>
    <section class="pg-asks" aria-labelledby="<?= h($id) ?>">
      <div class="container pg-asks-grid">
        <div class="pg-asks-intro ab-reveal">
          <span class="ab-eyebrow">In their words</span>
          <h2 id="<?= h($id) ?>"><?= h($title) ?></h2>
          <p>Participants from our training sessions and community groups, speaking in English, French and Arabic. Translations are shown beneath.</p>
          <a href="<?= SITE_URL ?>/about.php#approach" class="pg-link">How we work <?= icon('arrow-right', 15) ?></a>
        </div>
        <ul class="pg-thread">
          <?php foreach ($items as $i => $q): $rtl = $q['lang'] === 'ar';
            $initials = implode('', array_map(fn($w) => mb_substr($w, 0, 1), array_slice(explode(' ', $q['name']), 0, 2))); ?>
            <li class="pg-bubble ab-reveal<?= $i % 2 ? ' is-reply' : '' ?>">
              <figure class="pg-bubble-fig">
                <blockquote class="pg-bubble-quote" lang="<?= h($q['lang']) ?>"<?= $rtl ? ' dir="rtl"' : '' ?>><p class="pg-bubble-q<?= $rtl ? ' is-rtl' : '' ?>"><?= h($q['q']) ?></p></blockquote>
                <?php if (!empty($q['en'])): ?><p class="pg-bubble-en" lang="en"><span class="sr-only">In English: </span><?= h($q['en']) ?></p><?php endif; ?>
                <figcaption class="pg-bubble-meta">
                  <span class="pg-bubble-avatar" aria-hidden="true"><?= h($initials) ?></span>
                  <span class="pg-bubble-who"><span class="pg-bubble-name"><b><?= h($q['name']) ?></b><?php if (!empty($q['name_ar'])): ?> <bdi lang="ar" class="pg-bubble-ar"><?= h($q['name_ar']) ?></bdi><?php endif; ?></span><small><?= h($q['role']) ?></small></span>
                  <span class="pg-bubble-lang" lang="<?= h($q['lang']) ?>"><?= h($langName[$q['lang']] ?? '') ?></span>
                </figcaption>
              </figure>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </section>
    <?php return ob_get_clean();
}

/**
 * Results as tall tiles: a large figure over a photograph (or a brand-colour panel when there is
 * no photograph), with the label, the group it describes and a link to its project.
 * $items: [[value, label, context, projectSlug?, photo?], ...] where photo = [path, alt, position].
 */
function pp_tiles(array $items, array $projects): string
{
    ob_start(); ?>
    <ul class="pg-tiles" style="--n: <?= min(3, max(1, count($items))) ?>">
      <?php foreach ($items as $i => $r):
        [$value, $label, $context] = $r;
        $slug = $r[3] ?? null; $photo = $r[4] ?? null;
        $proj = $slug && isset($projects[$slug]) ? $projects[$slug] : null;
        $count = preg_match('/^([\d,]+)(\D*)$/u', $value, $m) ? [(int) str_replace(',', '', $m[1]), $m[1], $m[2]] : null; ?>
        <li class="pg-tile ab-reveal<?= $photo ? ' has-photo' : '' ?>">
          <?php if ($photo): ?>
            <?= ab_img($photo[0], $photo[1], 'pg-tile-img', true, 'style="object-position: ' . h($photo[2] ?? '50% 40%') . '"', '(max-width: 720px) 100vw, (max-width: 1100px) 50vw, 420px') ?>
          <?php else: ?>
            <svg class="pg-tile-strokes" viewBox="0 0 400 500" preserveAspectRatio="none" aria-hidden="true" focusable="false"><g filter="url(#lpBrush)"><path class="f-green" d="<?= lp_brush_d(220, 60, 460, 30, 46, 201 + $i) ?>"/><path class="f-blue" d="<?= lp_brush_d(-60, 230, 160, 210, 40, 211 + $i) ?>"/></g></svg>
          <?php endif; ?>
          <div class="pg-tile-body">
            <strong class="pg-tile-num"><?php if ($count): ?><span class="ab-count" data-count="<?= $count[0] ?>"><?= h($count[1]) ?></span><?php if ($count[2] !== ''): ?><small><?= h($count[2]) ?></small><?php endif; ?><?php else: ?><?= h($value) ?><?php endif; ?></strong>
            <span class="pg-tile-label"><?= h($label) ?></span>
            <span class="pg-tile-ctx"><?= h($context) ?></span>
            <?php if ($proj): ?><a class="pg-tile-link" href="<?= h(pp_project_url($slug, $proj)) ?>"><?= h($proj['title']) ?> <?= icon('arrow-right', 14) ?></a><?php endif; ?>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
    <?php return ob_get_clean();
}

/**
 * The Yumbe film (edited by BetterLife, 2025): re-encoded for the web at 720p with sound.
 * Opens in a player on request only; nothing downloads until someone presses play.
 */
function pp_film(): array
{
    return [
        'src'      => 'assets/video/yumbe-film-720.mp4',
        'poster'   => 'assets/img/programmes/yumbe-film-poster.jpg',
        'title'    => 'Our first step towards sustainable change',
        'minutes'  => 8,
        'summary'  => 'A film from Yumbe: the women, local leaders and BetterLife team behind the programme, at their farms, groups and training sessions.',
        'project'  => 'womens-climate-resilience-yumbe',
    ];
}

/** The film's address, stamped with the file's date so a replaced film is fetched afresh. */
function pp_film_src(array $f): string
{
    $path = dirname(__DIR__) . '/' . $f['src'];
    return asset_url($f['src']) . (is_file($path) ? '?v=' . filemtime($path) : '');
}

/** A play button that opens the film in the player dialog (sits on the Yumbe feature on the overview). */
function pp_film_button(): string
{
    $f = pp_film();
    if (!is_file(dirname(__DIR__) . '/' . $f['src'])) return '';
    return '<button type="button" class="pg-film-play" data-film="' . h(pp_film_src($f)) . '" data-film-title="' . h($f['title']) . '">'
        . '<span class="pg-film-play-ico" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M8 5.5v13l10.5-6.5Z"/></svg></span>'
        . '<span class="pg-film-play-text">Watch the film<small>' . (int) $f['minutes'] . ' minutes, with sound</small></span></button>';
}

/** The player dialog (one per page). */
function pp_film_dialog(): string
{
    return '<dialog class="pg-film-dialog" id="pgFilmDialog" aria-label="Film player">'
        . '<video controls playsinline preload="none"></video>'
        . '<button type="button" class="pg-film-close" aria-label="Close the film">' . icon('x', 22) . '</button></dialog>';
}
