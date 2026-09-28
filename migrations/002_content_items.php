<?php
/**
 * One-time migration: moves the hardcoded content arrays that used to live
 * inside about.php, programs.php and farm.php into the `content_items`
 * table, so they become editable (and extendable) from the admin panel.
 *
 * Safe to re-run: it deletes existing rows for each (page, section_key)
 * pair before re-inserting, so running it twice does not duplicate data.
 *
 * Run once locally:   php migrations/002_content_items.php
 * Run once on live:   same command over SSH, or import content_items via
 *                      phpMyAdmin using a mysqldump of this table.
 */

require_once __DIR__ . '/../includes/functions.php';

function dec(string $s): string
{
    return html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/** Each row: [title, subtitle/note, body (paragraphs joined by \n\n), image, cta_label, cta_href, extra] */
$data = [];

/* ===================== about.php ===================== */

$data['about'] = [];

$data['about']['how_we_work'] = array_map(fn($b) => [dec($b[0]), '', dec($b[1]), null, null, null, null], [
    ['We Start by Listening', 'We work through women&rsquo;s groups, farmer groups, refugee and host-community structures, schools, community organisations and local facilitators. These groups help us understand what is changing, what has already been tried and what people can realistically sustain.'],
    ['People Learn by Seeing and Doing', 'A technique explained in a workshop can remain abstract. A garden that is producing through a dry spell is harder to dismiss. We use demonstration sites, local-language facilitation, peer learning and community champions so that people can see, question and test new practices.'],
    ['Training Must Lead Somewhere', 'Knowledge matters, but so do tools, money and customers. We connect learning to starter inputs, savings, finance, business support, services and markets wherever possible.'],
    ['We Build on What Already Exists', 'We strengthen existing groups and local institutions instead of creating temporary committees that disappear when a project closes. Local ownership is not an exit strategy added at the end. It is part of the design from the beginning.'],
    ['We Pay Attention to What Changes', 'We use monitoring data, participant feedback and field observation to understand whether people are applying what they learnt and whether that application is improving food, income, confidence or resilience.'],
]);

$data['about']['who_we_work_with'] = array_map(fn($b) => [dec($b[0]), '', dec($b[1]), null, null, null, null], [
    ['Women and Girls', 'Women hold much of the responsibility for food, water and household care, yet often have less access to land, money, technology and decision-making. Our programmes pay attention to the barriers that shape participation, including time, phone access, mobility, confidence and social norms.'],
    ['Children and Young People', 'We work with children and young people through schools, youth centres, climate education, digital learning, livelihoods, leadership and innovation programmes. They are not only preparing for the future; they already have ideas and decisions to contribute today.'],
    ['Refugees and Displaced Families', 'Our programmes support people affected by displacement to rebuild their ability to produce food, earn, save and take part in the local economy. We work with refugees and host communities together whenever possible.'],
    ['Smallholder Farmers', 'We help farmers improve soil and water management, access climate and market information, diversify production and connect to services, finance and buyers.'],
]);

$data['about']['who_we_work_with_gallery'] = [
    ['Women & girls', '', '', 'assets/img/betterlifeint-source/programs/program-photo-6.jpg', null, null, 'A woman participating in a BetterLife community programme'],
    ['Refugees & smallholder farmers', '', '', 'assets/img/project-women-idps.jpg', null, null, 'A smallholder farmer tending crops'],
];

$data['about']['where_we_work'] = array_map(fn($b) => [dec($b[0]), '', dec($b[1]), null, null, null, null], [
    ['Uganda', 'Uganda is where BetterLife began and where much of our work is based. Programmes include climate-resilient agriculture, refugee and host-community livelihoods, women&rsquo;s economic empowerment, Green Libraries, tree nurseries, clean energy, water access, digital agriculture and BetterLife Agro Tourism Farm. Our West Nile work is coordinated through Yumbe and Bidi Bidi.'],
    ['South Sudan', 'Through our Juba office and field presence in Yambio, we work on food security, solar-powered irrigation, climate-resilient agriculture and practical livelihoods for young people, women and communities affected by conflict and displacement.'],
    ['Tanzania', 'Our Tanzania work is based in Kayanga Town, Karagwe District. Together with FADECO, we use schools, community radio and practical training to support climate education, health, green skills and youth enterprise.'],
    ['Ghana', 'In Ghana, our work focuses on youth leadership, climate action, sustainable livelihoods and community-led environmental solutions.'],
    ['Democratic Republic of Congo', 'Our work in the Democratic Republic of Congo supports young people and communities affected by poverty, conflict and displacement through livelihoods, environmental action and local participation.'],
]);

$data['about']['where_we_work_gallery'] = [
    ['Ghana', '', '', 'assets/img/betterlifeint-source/projects/project-climate-education-alt.jpg', null, null, 'Climate education programme in Ghana'],
    ['Uganda', '', '', 'assets/img/betterlifeint-source/programs/program-photo-9.jpg', null, null, 'Community programme activity in Uganda'],
];

$data['about']['journey'] = array_map(fn($j) => [$j[0], '', dec($j[1]), null, null, null, null], [
    ['2021', 'BetterLife International was founded in Uganda with USD 200 and a small team of young people.'],
    ['2022', 'The organisation expanded its community programmes and began establishing work in South Sudan and Tanzania.'],
    ['2023', 'BetterLife launched Soilla, expanded its school and environmental work and opened the Apala One Stop Youth Centre in Alebtong.'],
    ['2024', 'Green Libraries, community restoration, clean energy, water access and livelihoods programmes continued to grow.'],
    ['2025', 'BetterLife deepened its work with women in Yumbe through the Foundation S-supported climate-resilience programme and strengthened BetterLife Agro Tourism Farm as a bridge from training to markets.'],
    ['2026', 'The Women&rsquo;s Action Circle approach began expanding through Farm Radio International&rsquo;s Green Leaf Platforms Uganda. Work also continued on Soilla, Agribusiness Connekt, the BetterLife Climate Academy and regional youth innovation.'],
]);

$data['about']['guides'] = array_map(fn($b) => [dec($b[0]), '', dec($b[1]), null, null, null, null], [
    ['Dignity', 'People are partners with knowledge and ability, not passive recipients of help.'],
    ['Community Leadership', 'Those living with a problem should have real influence over the response.'],
    ['Usefulness', 'An idea matters when people can put it to work in their own circumstances.'],
    ['Accountability', 'We are responsible to the communities, partners and institutions that place their trust in us.'],
    ['Inclusion', 'Participation must be designed around the barriers people face, not simply offered in theory.'],
    ['Learning', 'We change course when evidence and experience show us a better way.'],
]);

/* ===================== programs.php (workblocks) ===================== */

$stories = 'blog.php';
$farmHref = 'farm.php';

function wb(string $title, string $note, array $paras, ?string $ctaLabel, ?string $ctaHref): array
{
    return [dec($title), dec($note), dec(implode("\n\n", $paras)), null, $ctaLabel ? dec($ctaLabel) : null, $ctaHref, null];
}

$data['programs'] = [];

$data['programs']['climate-resilient-agriculture'] = [
    wb('Women&rsquo;s Climate Resilience in Yumbe', 'Implemented with support from Foundation S &ndash; The Sanofi Collective', [
        'Before the work began, we spoke with 100 women in Yumbe about the way climate pressure was changing their lives. Many were spending around four hours a day collecting water. Some travelled as far as 24 kilometres in search of firewood. Eighty-one per cent had experienced crop failure, and 63 per cent reported having withdrawn a child from school at some point because the household could not meet the cost.',
        'Those answers changed the shape of the project. This could not be a farming course alone.',
        'BetterLife worked with refugee, displaced and host-community women through local-language training and gardens where they could see each practice working. The women learnt composting, mulching, sack and box gardening, trellising, drought-tolerant crops, agroforestry and briquette-making. They received seedlings and support to use simple digital tools for soil and market information.',
        'Among the 72 women who completed structured training, knowledge of climate-smart agriculture rose from 22 per cent to 92 per cent. Seventy-two per cent adopted sack or box gardening, 63 per cent took up composting, 54 per cent used mulching and 57 per cent introduced drought-tolerant crops. Participating households reported an average 35 per cent reduction in spending on vegetables as home production improved.',
        'The figures matter, but so did the way the change happened. Women learnt in groups, saw the methods before risking their own resources and could return with questions after trying them at home. Confidence grew alongside knowledge.',
    ], 'Read the Project Story', $stories),
    wb('Green Leaf Platforms Uganda', 'In partnership with Farm Radio International', [
        'What happens after a radio programme ends?',
        'A woman may hear about a regenerative farming practice and understand it perfectly, yet still be unable to try it. She may not control the land, own a phone or have the materials to risk on an unfamiliar method. She may need to see the practice working nearby before she trusts it with a small harvest.',
        'BetterLife&rsquo;s partnership with Farm Radio International is designed around that gap between receiving information and acting on it.',
        'Under Green Leaf Platforms Uganda, radio and digital agricultural content is connected to Women&rsquo;s Action Circles across 12 districts. These circles build on groups women already trust, including savings groups, farmer organisations, cooperatives and community networks.',
        'Women listen, discuss what the information means in their setting, ask questions, visit demonstration plots and test practices together. Peer champions support those with limited access to phones or extension services. Women&rsquo;s feedback also travels back into the programme, helping shape content around the questions and barriers they are actually facing.',
        'Farm Radio International brings its radio, digital and farmer-learning experience. BetterLife leads the women&rsquo;s inclusion work on the ground, connecting listening to learning, practice and adoption.',
        'The model grows from a lesson we first saw clearly in Yumbe: women adopt what they can see, and groups often carry change farther than individuals working alone.',
    ], 'Learn About Green Leaf Platforms', $stories),
    wb('BetterLife SPRING', 'Sustainable Powered Resilient Irrigation for Next Generation Farming', [
        'In South Sudan, farming families are expected to plan around rainfall that is becoming harder to predict. When rain stops too early, households lose food and the income they hoped to earn from the season.',
        'BetterLife SPRING combines solar-powered irrigation with practical training in soil health, crop planning and water management. Farmers, refugees and displaced families can produce more reliably through dry periods without depending on fuel-powered pumping.',
        'The project brings together clean energy, food production and livelihoods. The aim is not simply to install irrigation equipment, but to help communities use it well, maintain it and turn more reliable production into stronger household security.',
    ], 'Explore BetterLife SPRING', $stories),
    wb('BetterLife Agro Tourism Farm', '', [
        'The farm is BetterLife&rsquo;s working demonstration and market link. Farmers learn through solar-powered irrigation, greenhouse farming, beekeeping, dairy production and livestock rearing, then have a route to supply produce through BetterLife Agro Tourism Farm Ltd.',
    ], 'Discover the Farm', $farmHref),
];

$data['programs']['green-skills-livelihoods'] = [
    wb('SMILES', 'Market Inclusive Livelihood Pathways to Self-Reliance', [
        'SMILES brings refugees and host-community members into the same training groups and local economy.',
        'Participants learn practical skills such as poultry, farming, carpentry, tailoring, barbering and solar technology. They then receive mentorship, business support, market connections and access to small loans. Where appropriate, BetterLife helps participants who lack conventional collateral by acting as a guarantor.',
        'The economic work also creates room for social cohesion. Refugees and host-community members build relationships as they train, save, trade and solve business problems together. Peacebuilding becomes part of ordinary economic life rather than a separate conversation in a workshop.',
        'Across target groups, 78 per cent of participants moved into sustainable income pathways, 85 per cent reported improved refugee-host relations and food insecurity fell by 40 per cent.',
    ], 'Explore SMILES', $stories),
    wb('Agribusiness Connekt', '', [
        'Farmers are often trained to produce and then left alone at the point where business begins.',
        'Agribusiness Connekt links farmers and small agricultural enterprises to buyers, finance, services and market information. It helps producers understand what customers need, find opportunities beyond their immediate location and make better decisions about when and where to sell.',
        'The platform also supports farmers trained through BetterLife programmes, giving them a route from production into longer-term enterprise.',
    ], 'Explore Agribusiness Connekt', $stories),
];

$data['programs']['climate-education-youth-leadership'] = [
    wb('Green Libraries, Eco Labs and School Climate Clubs', '', [
        'Green Libraries and Eco Labs give learners access to books, digital resources and practical environmental activities. Students take part in school gardens, tree planting, waste separation, plastic banks, debates and public speaking.',
        'More than 4,500 students have been engaged through school climate education, and BetterLife has supported over 20 Green Libraries and Eco Labs. Our school work has included Lake Victoria School Entebbe and Buddo Junior School.',
        'The aim is not to turn every child into a climate expert. It is to make the subject understandable enough for them to connect it to their home, school and community, and confident enough for them to act.',
    ], 'Explore Green Libraries', $stories),
    wb('Apala One Stop Youth Centre', '', [
        'Talent exists in rural communities. Access often does not.',
        'The Apala One Stop Youth Centre in Alebtong gives young people a place to read, use computers, learn digital skills and develop ideas. Launched in December 2023, the centre opened with ten computers, more than 3,000 books and space to serve around 400 young people.',
        'Apala is both a learning centre and a statement about opportunity: where a young person is born should not decide how far their curiosity can take them.',
    ], 'Visit the Apala Story', $stories),
    wb('Climate Education and Youth Enterprise in Tanzania', '', [
        'Together with FADECO, BetterLife uses schools, community radio and practical training to reach young people and communities in Tanzania.',
        'Eco Clubs involve students in tree planting, waste management and environmental leadership. FADECO Radio carries information on climate, agriculture, health and livelihoods into communities that formal training may not reach.',
        'Young people also learn skills such as reusable sanitary-pad production and liquid soap-making. The activities respond to health and dignity while opening small routes into enterprise.',
    ], 'Explore Our Work in Tanzania', $stories),
    wb('BetterLife Pre-COP Climate Academy', '', [
        'Climate negotiations can feel distant from the places already living with the consequences. The BetterLife Pre-COP Climate Academy helps young Africans understand those negotiations and connect them to what is happening in their own communities.',
        'Participants learn about adaptation, climate finance, loss and damage, negotiations and advocacy. The academy also asks a more grounded question: what do these decisions mean for a farmer facing drought, a family displaced by floods or a young person trying to build a future?',
        'Supported by Moonshot, the virtual academy brings together young people from Uganda and across Africa to learn from experts and one another.',
    ], 'Explore the Climate Academy', $stories),
    wb('Climate Innovation Hackathons', '', [
        'BetterLife&rsquo;s hackathons give young people a real problem, a team and room to build. Participants use technology, entrepreneurship and local knowledge to develop practical responses to climate and community challenges.',
        'Our youth climate-innovation work has included support from ICPAC&rsquo;s innovation ecosystem, connecting young people to regional climate knowledge and new opportunities to test their ideas.',
    ], 'Explore Youth Innovation', $stories),
];

$data['programs']['clean-energy-water-restoration'] = [
    wb('Community Tree Nurseries and Agroforestry', '', [
        'BetterLife-supported nurseries have raised more than 50,000 indigenous and fruit-tree seedlings, with over 20,000 distributed to schools, farmers and communities.',
        'Fruit trees can contribute to nutrition and income. Indigenous trees protect soil, provide shade and support biodiversity. Through agroforestry, trees become part of the farm rather than competing with it.',
        'We focus on what happens after distribution, including care, monitoring and survival. A seedling in the ground is a beginning, not a result by itself.',
    ], 'Explore Nature Restoration', $stories),
    wb('Community Biogas', '', [
        'BetterLife has supported more than 48 household biogas systems. Families turn organic waste into cleaner cooking energy, reducing smoke inside the home and dependence on firewood and charcoal.',
        'The process also produces an organic by-product that can be returned to the soil. One household system therefore connects waste, energy, health and farming.',
    ], 'Explore Clean Energy', $stories),
    wb('Water Access', '', [
        'BetterLife has supported 65 community boreholes. Reliable water reduces the time and distance people travel, improves household health and makes small-scale food production more possible.',
        'For us, water is not a side issue. It sits at the centre of health, food, education and climate resilience.',
    ], null, null),
    wb('BetterLife Renewable Pathways', '', [
        'BetterLife Renewable Pathways works with schools and communities on waste separation, plastic banks, recycling and the responsible reuse of materials.',
        'Four school plastic banks give learners a practical way to understand waste and participate in keeping plastics out of the environment. The programme also explores safe, locally appropriate ways of recovering value from materials that would otherwise be dumped or burnt.',
    ], 'Explore Renewable Pathways', $stories),
    wb('Briquette-Making', '', [
        'Women and vulnerable households learn to make briquettes from suitable agricultural and organic waste. Briquettes can reduce dependence on firewood and charcoal while creating a product for household use or small-scale sale.',
        'For a woman who spends hours collecting fuel, an alternative made closer to home can mean saved time as well as income.',
    ], null, null),
];

$data['programs']['digital-innovation'] = [
    wb('Soilla', '', [
        'Soilla is BetterLife&rsquo;s digital agricultural advisory platform. It helps farmers access soil and crop guidance, climate information, market prices and agricultural services.',
        'Farmers can use the platform to better understand what may grow in their soil, monitor farm conditions, locate suppliers and experts and connect with other producers. The aim is to make information that is often expensive or distant more accessible to smallholder farmers.',
        'Soilla is paired with field training because owning a phone or receiving data does not automatically make a tool useful. Farmers need confidence, local support and information that fits their crops and circumstances.',
        'Our engagement with the World Food Programme has contributed to work around farmer information, verification and the responsible use of agricultural and climate data.',
    ], 'Explore Soilla', $stories),
    wb('Agribusiness Connekt', '', [
        'Where Soilla supports production decisions, Agribusiness Connekt focuses on the business around the farm. It links producers to buyers, finance, services and market opportunities.',
        'Together, the two platforms respond to a gap we see repeatedly: farmers learn how to produce, but remain disconnected from the systems that determine whether production becomes income.',
    ], 'Explore Agribusiness Connekt', $stories),
];

$data['programs']['our-projects'] = [
    wb('Dovetail Impact Foundation', 'Growing without losing what made the work local', [
        'As BetterLife expanded, we had to answer a difficult question: how do we reach more people without becoming distant from the communities that shaped us?',
        'Dovetail Impact Foundation has supported BetterLife through strategic acceleration and institutional strengthening. The relationship has helped us clarify our theory of change, strengthen how we measure and communicate impact and build more deliberate systems for growth and resource mobilisation.',
        'This support sits behind all our programmes rather than inside one field project. It helps us examine the full path from training to application, from application to income and from individual progress to stronger community systems.',
        'Growth, for BetterLife, is not simply a larger number. It is the ability to reach more people while protecting the dignity, usefulness and local ownership of the work.',
    ], null, null),
];

/* ===================== farm.php ===================== */

$data['farm'] = [];
$data['farm']['on_the_farm'] = array_map(fn($b) => [dec($b[0]), '', dec($b[1]), null, null, null, null], [
    ['Solar-Powered Irrigation', 'Water is pumped and distributed using solar energy, helping crops survive when rainfall is unreliable.'],
    ['Greenhouse and Crop Farming', 'Farmers learn water-efficient production, soil management, crop care and methods suited to limited land.'],
    ['Dairy and Livestock', 'Livestock supports food, manure, household income and the farm&rsquo;s dairy value chain.'],
    ['Beekeeping', 'Community beekeeping creates income while encouraging the protection of trees and flowering plants.'],
    ['Poultry and Aquaculture', 'Diversified production reduces the risk of depending on one crop or one season.'],
    ['Seedlings', 'Participants receive free seedlings to establish gardens of their own and begin moving towards independent production.'],
]);

/* ===================== products.php ===================== */

$data['products'] = [];
$data['products']['farm_gallery'] = [
    ['Refugees training on the farm', '', '', 'assets/img/betterlifeint-source/programs/program-photo-1.jpg', null, null, null],
];

/* ===================== Insert ===================== */

$inserted = 0;
foreach ($data as $page => $sections) {
    foreach ($sections as $sectionKey => $items) {
        $pdo->prepare("DELETE FROM content_items WHERE page = ? AND section_key = ?")->execute([$page, $sectionKey]);
        $stmt = $pdo->prepare("INSERT INTO content_items (page, section_key, title, subtitle, body, image, cta_label, cta_href, extra, sort_order, status) VALUES (?,?,?,?,?,?,?,?,?,?,1)");
        $order = 0;
        foreach ($items as $row) {
            [$title, $subtitle, $body, $image, $ctaLabel, $ctaHref, $extra] = $row;
            $stmt->execute([$page, $sectionKey, $title, $subtitle, $body, $image, $ctaLabel, $ctaHref, $extra, $order]);
            $order += 10;
            $inserted++;
        }
    }
}

echo "Inserted/refreshed $inserted content_items rows.\n";
