-- Adminer 4.8.1 MySQL 5.7.33 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

USE `pecsf`;

SET NAMES utf8mb4;

TRUNCATE `f_s_pools`;
INSERT INTO `f_s_pools` (`id`, `region_id`, `start_date`, `status`, `created_by_id`, `updated_by_id`, `created_at`, `updated_at`) VALUES
(13,	3,	'2022-06-20',	'A',	1001,	1001,	'2022-06-20 12:25:20',	'2022-06-20 12:25:20'),
(14,	13,	'2022-06-20',	'A',	1001,	1001,	'2022-06-20 12:33:21',	'2022-06-20 12:33:21'),
(15,	5,	'2022-06-20',	'A',	1001,	1001,	'2022-06-20 12:37:44',	'2022-06-20 12:37:44'),
(16,	11,	'2022-07-21',	'A',	1334,	1334,	'2022-07-21 17:55:34',	'2022-07-21 17:55:34'),
(17,	3,	'2020-09-01',	'A',	1003,	1003,	'2022-08-08 11:00:24',	'2022-08-08 11:00:24'),
(19,	5,	'2023-01-01',	'I',	1003,	1003,	'2022-08-08 14:14:03',	'2022-08-08 14:14:03'),
(20,	13,	'2023-01-01',	'I',	1003,	999,	'2022-08-08 14:20:02',	'2022-08-10 19:26:30'),
(21,	17,	'2022-08-10',	'A',	999,	999,	'2022-08-09 14:11:40',	'2022-08-09 14:11:40'),
(22,	3,	'2022-09-30',	'A',	999,	999,	'2022-08-09 14:23:13',	'2022-08-09 14:23:13'),
(23,	17,	'2022-08-17',	'A',	999,	999,	'2022-08-10 08:34:12',	'2022-08-10 08:34:12'),
(24,	1,	'2022-08-10',	'I',	999,	999,	'2022-08-10 08:46:25',	'2022-08-10 08:46:25'),
(26,	1,	'2022-08-12',	'I',	999,	999,	'2022-08-12 12:23:20',	'2022-08-12 12:23:20'),
(27,	17,	'2022-08-12',	'A',	999,	999,	'2022-08-12 14:11:54',	'2022-08-12 14:11:54'),
(28,	11,	'2022-08-12',	'A',	999,	999,	'2022-08-12 14:12:45',	'2022-08-12 14:12:45'),
(30,	1,	'2022-08-16',	'I',	999,	999,	'2022-08-15 12:16:52',	'2022-08-15 12:16:52'),
(31,	21,	'2022-08-15',	'A',	999,	999,	'2022-08-15 12:23:49',	'2022-08-15 12:23:49'),
(32,	21,	'2022-08-14',	'I',	999,	999,	'2022-08-15 12:28:01',	'2022-08-15 12:28:01'),
(33,	21,	'2022-08-13',	'A',	999,	999,	'2022-08-15 13:05:01',	'2022-08-15 13:05:01'),
(34,	12,	'2023-01-01',	'A',	999,	999,	'2022-08-15 13:08:50',	'2022-08-15 13:08:50'),
(35,	23,	'2022-08-16',	'I',	999,	999,	'2022-08-15 13:10:12',	'2022-08-15 13:10:12'),
(36,	1,	'2022-08-15',	'I',	999,	999,	'2022-08-15 13:10:51',	'2022-08-15 13:10:51'),
(38,	28,	'2022-08-20',	'A',	1001,	1001,	'2022-08-19 09:49:42',	'2022-08-19 15:18:47'),
(39,	3,	'2022-08-19',	'A',	1001,	1001,	'2022-08-19 15:32:22',	'2022-08-19 15:32:22'),
(40,	1,	'2022-08-22',	'I',	1001,	1001,	'2022-08-19 15:34:24',	'2022-08-19 15:34:24'),
(41,	1,	'2022-08-19',	'I',	1001,	1001,	'2022-08-19 15:47:34',	'2022-08-19 15:47:34'),
(42,	1,	'2022-08-26',	'I',	1001,	1001,	'2022-08-19 15:47:57',	'2022-08-19 15:47:57');

TRUNCATE `f_s_pool_charities`;
INSERT INTO `f_s_pool_charities` (`id`, `f_s_pool_id`, `charity_id`, `percentage`, `status`, `name`, `description`, `contact_title`, `contact_name`, `contact_email`, `notes`, `image`, `created_at`, `updated_at`) VALUES
(14,	13,	7261,	11.60,	'A',	'Support Programs',	'Autism BC seeks to empower, support and connect people on the autism spectrum and their families through education, training, resources and support groups towards promoting the health and inc',	NULL,	'Cathy Nidoski',	'cnidoski@autismbc.ca',	NULL,	'20220620122520_AutismBC-Primary_Logo-4C.jpg',	'2022-06-20 12:25:20',	'2022-06-20 12:25:20'),
(15,	13,	55683,	50.00,	'A',	'Student Programs',	'Through partnerships with educators and volunteers from local businesses, JABC offers important interactive, hands-on learning experiences in financial literacy, entrepreneurship, and work re',	NULL,	'Susan Shepherd',	'susan.shepherd@jabc.org',	NULL,	'20220620122520_Junior-Achievement-BC.jpg',	'2022-06-20 12:25:20',	'2022-06-20 12:25:20'),
(16,	13,	19009,	38.40,	'A',	'Cariboo Peer Program',	'Spinal Cord Injury BC’s Peer Support Program gives people in the region with a spinal cord injury, their family and friends the opportunity to connect with others in similar situations, try a',	NULL,	'Susie Jackson',	'sjackson@sci-bc.ca',	NULL,	'20220620122520_PECSF_Icon.png',	'2022-06-20 12:25:20',	'2022-06-20 12:25:20'),
(17,	14,	51453,	30.70,	'A',	'Medical Flight Program',	'Hope Air provides free travel and accommodation for patients in financial need who must access \r\nmedical care far from home. This includes booking commercial flights or organizing private pil',	NULL,	'Elizabeth Taugher',	'etaugher@hopeair.ca',	NULL,	'20220620123321_HopeAir.png',	'2022-06-20 12:33:21',	'2022-06-20 12:33:21'),
(18,	14,	118538,	32.37,	'A',	'Food Recovery Program',	'Soon to be expired or nonsalable products from grocery stores are received and distributed as part of the Food Recovery Program. Products range from fresh produce, dairy, meat, deli, bakery i',	NULL,	'Lisa Hamblin',	'Lisa_Hamblin@can.salvationarmy.org',	NULL,	'20220620123321_sal_army.png',	'2022-06-20 12:33:21',	'2022-06-20 12:33:21'),
(19,	14,	19009,	36.93,	'A',	'Peer Support Program',	'Spinal Cord Injury BC’s Peer Support Program gives people in the Fraser Fort George region with a spinal cord injury, their family and friends the opportunity to connect with others in simila',	NULL,	'Susie Jackson',	'sjackson@sci-bc.ca',	NULL,	'20220620123321_PECSF_Icon.png',	'2022-06-20 12:33:21',	'2022-06-20 12:33:21'),
(20,	15,	7261,	26.69,	'A',	'Support Programs',	'Autism BC Programs seek to empower, support and connect people on the autism spectrum and their families through education, training, resources and support groups towards promoting the health',	NULL,	'Cathy Nidoski',	'cnidoski@autismbc.ca',	NULL,	'20220620123744_AutismBC-Primary_Logo-4C.jpg',	'2022-06-20 12:37:44',	'2022-06-20 12:37:44'),
(21,	15,	55683,	73.31,	'A',	'Student Programs',	'Through partnerships with educators and volunteers from local businesses, JABC will offer important interactive, hands-on learning experiences in financial literacy, entrepreneurship, and wor',	NULL,	'Susan Shepherd',	'susan.shepherd@jabc.org',	NULL,	'20220620123744_Junior-Achievement-BC.jpg',	'2022-06-20 12:37:44',	'2022-06-20 12:37:44'),
(22,	16,	623,	50.00,	'A',	'MNP TEST (1)',	'TESTING',	NULL,	'Sugar Smith',	'Sugar.Smith@Test.com',	NULL,	'20220721175534_BC1.png',	'2022-07-21 17:55:34',	'2022-07-21 17:55:34'),
(23,	16,	25,	50.00,	'A',	'MNP TEST (2)',	'TESTING (2)',	NULL,	'Bitter Smith',	'Bitter.Smith@Test.com',	NULL,	'20220721175534_BC1.png',	'2022-07-21 17:55:34',	'2022-07-21 17:55:34'),
(24,	17,	7261,	11.60,	'A',	'Support Programs',	'Autism BC seeks to empower, support and connect people on the autism spectrum and their families through education, training, resources and support groups towards promoting the health and inclusion of individuals with autism and related conditions in the region.',	NULL,	'Becs Brocken',	'bbrocken@autismbc.ca',	NULL,	'20220808110024_AutismBC-Primary_Logo-4C.jpg',	'2022-08-08 11:00:24',	'2022-08-08 11:00:24'),
(25,	17,	55683,	50.00,	'A',	'Student Programs',	'Through partnerships with educators and volunteers from local businesses, JABC offers important interactive, hands-on learning experiences in financial literacy, entrepreneurship, and work readiness to students in Grades 4 to 12 in all communities across the Cariboo region.',	'Development Officer',	'Susan Shepherd',	'susan.shepherd@jabc.org',	NULL,	'20220808110024_Junior-Achievement-BC.jpg',	'2022-08-08 11:00:24',	'2022-08-08 11:00:24'),
(26,	17,	19009,	38.40,	'A',	'Spinal Cord Injury BC - Peer Support Program',	'Spinal Cord Injury BC’s Peer Support Program gives people in the region with a spinal cord injury, their family and friends the opportunity to connect with others in similar situations, try activities they never imagined possible and continue learning about living well with an injury.',	'Fund Development Officer',	'Susie Jackson',	'sjackson@bcpara.org',	NULL,	'20220808110024_PECSF_2_line_logo_blue.png',	'2022-08-08 11:00:24',	'2022-08-08 11:00:24'),
(30,	19,	7261,	26.69,	'A',	'Support Programs',	'Autism BC Programs seek to empower, support and connect people on the autism spectrum and their families through education, training, resources and support groups towards promoting the health and inclusion of individuals with autism and related conditions in the region.',	NULL,	'Becs Brocken',	'bbrocken@autismbc.ca',	NULL,	'20220808141403_AutismSocietyBC.png',	'2022-08-08 14:14:03',	'2022-08-08 14:14:03'),
(31,	19,	55683,	73.31,	'A',	'Student Programs',	'Through partnerships with educators and volunteers from local businesses, JABC will offer important interactive, hands-on learning experiences in financial literacy, entrepreneurship, and work readiness to students in Grades 4 to 12 in all communities across the Fraser Valley area.',	'Development Officer',	'Susan Shepherd',	'susan.shepherd@jabc.org',	NULL,	'20220808141403_Junior-Achievement-BC.jpg',	'2022-08-08 14:14:03',	'2022-08-08 14:14:03'),
(32,	20,	51453,	32.36,	'A',	'Medical Flight Program',	'Hope Air provides free travel and accommodation for patients in financial need who must access medical care far from home. This includes booking commercial flights or organizing private pilots from across Canada to volunteer their time and aircraft to fly patients to their appointments. Hope Air also has a partnership with Airbnb to provide patients with free accommodation.',	NULL,	'Elizabeth Taugher',	'etaugher@hopeair.ca',	NULL,	'20220808142002_HopeAir.png',	'2022-08-08 14:20:02',	'2022-08-08 14:20:02'),
(33,	20,	118538,	30.71,	'A',	'Food Recovery Program',	'Soon to be expired or nonsalable products from grocery stores are received and distributed as part of the Food Recovery Program. Products range from fresh produce, dairy, meat, deli, bakery items, hygiene items, non-perishables, baby items, and pet food. This enables the Food Bank to offer guests more nutritious foods and essential items that result in more wholesome meals and products going to individuals and families that otherwise would not be able to afford them.',	'Finance Admin. Assistant',	'Lisa Hamblin',	'Lisa_Hamblin@can.salvationarmy.org',	NULL,	'20220808142002_sal_army.png',	'2022-08-08 14:20:02',	'2022-08-08 14:20:02'),
(34,	20,	19009,	36.93,	'A',	'Peer Support Program',	'Spinal Cord Injury BC’s Peer Support Program gives people in the Fraser Fort George region with a spinal cord injury, their family and friends the opportunity to connect with others in similar situations, try activities they never imagined possible and continue learning about living well with an injury.',	'Fund Development Officer',	'Susie Jackson',	'sjackson@bcpara.org',	NULL,	'20220810192630_BC-government-logo-NEW-768x728.jpeg',	'2022-08-08 14:20:02',	'2022-08-10 19:26:30'),
(35,	21,	131433,	100.00,	'A',	'Nanaimo',	'New supported program',	NULL,	'John Philip',	'johnphilip@hotmail.com',	NULL,	'20220809141140_Screen_Shot_2022-08-09_at_4.40.59_PM.png',	'2022-08-09 14:11:40',	'2022-08-09 14:11:40'),
(36,	22,	131335,	100.00,	'A',	'Cariboo Fund Supported Pool',	'Cariboo Fund Supported Pool',	NULL,	'Mike',	'mike@mail.com',	NULL,	'20220809142313_Screen_Shot_2022-08-09_at_3.56.15_PM.png',	'2022-08-09 14:23:13',	'2022-08-09 14:23:13'),
(37,	23,	131433,	100.00,	'A',	'Nanaimo',	'New supported program',	NULL,	'John Philip',	'johnphilip@hotmail.com',	NULL,	'2022081008341240_Screen_Shot_2022-08-09_at_4.40.59_PM.png',	'2022-08-10 08:34:12',	'2022-08-10 08:34:12'),
(38,	24,	131413,	0.00,	'I',	'Alberni Clayoquot Support Pool',	'Alberni Clayoquot Support Pool',	NULL,	'Mark James',	'mark@a',	NULL,	'20220810084625_pecsf-logo.png',	'2022-08-10 08:46:25',	'2022-08-10 08:46:25'),
(41,	26,	131413,	0.00,	'I',	'Alberni Clayoquot Support Pool',	'Alberni Clayoquot Support Pool',	NULL,	'Mark James',	'mark@a',	NULL,	'2022081212232025_pecsf-logo.png',	'2022-08-12 12:23:20',	'2022-08-12 12:23:20'),
(42,	27,	131433,	100.00,	'A',	'Nanaimo',	'New supported program',	NULL,	'John Philip',	'johnphilip@hotmail.com',	NULL,	'2022081214115440_Screen_Shot_2022-08-09_at_4.40.59_PM.png',	'2022-08-12 14:11:54',	'2022-08-12 14:11:54'),
(43,	28,	623,	50.00,	'A',	'MNP TEST (1)',	'TESTING',	NULL,	'Sugar Smith',	'Sugar.Smith@Test.com',	NULL,	'2022081214124534_BC1.png',	'2022-08-12 14:12:45',	'2022-08-12 14:12:45'),
(44,	28,	25,	50.00,	'A',	'MNP TEST (2)',	'TESTING (2)',	NULL,	'Bitter Smith',	'Bitter.Smith@Test.com',	NULL,	'2022081214124534_BC1.png',	'2022-08-12 14:12:45',	'2022-08-12 14:12:45'),
(46,	30,	131413,	0.00,	'I',	'Alberni Clayoquot Support Pool',	'Alberni Clayoquot Support Pool',	NULL,	'Mark James',	'mark@a',	NULL,	'202208151216522025_pecsf-logo.png',	'2022-08-15 12:16:52',	'2022-08-15 12:16:52'),
(47,	31,	623,	100.00,	'A',	'Test',	'Test',	NULL,	'Test',	'test@tes',	NULL,	'20220815122349_Screen_Shot_2022-08-10_at_4.50.26_PM.png',	'2022-08-15 12:23:49',	'2022-08-15 12:23:49'),
(48,	32,	43,	0.00,	'I',	'Test',	'Test',	'Test',	'Test',	'Test@test',	NULL,	'20220815122801_Screen_Shot_2022-08-10_at_4.50.26_PM.png',	'2022-08-15 12:28:01',	'2022-08-15 12:28:01'),
(49,	33,	623,	100.00,	'A',	'Test',	'Test',	NULL,	'Test',	'Test@test',	NULL,	'20220815130501_Screen_Shot_2022-08-12_at_9.44.11_AM.png',	'2022-08-15 13:05:01',	'2022-08-15 13:05:01'),
(50,	34,	23,	60.00,	'A',	'Test',	'Test',	NULL,	'Test',	'Test@test',	NULL,	'20220815130850_Screen_Shot_2022-08-12_at_9.44.11_AM.png',	'2022-08-15 13:08:50',	'2022-08-15 13:08:50'),
(51,	34,	25,	40.00,	'A',	'Test',	'Test',	NULL,	'Test',	'Test@test',	NULL,	'20220815130850_Screen_Shot_2022-08-12_at_9.44.07_AM.png',	'2022-08-15 13:08:50',	'2022-08-15 13:08:50'),
(52,	35,	43,	100.00,	'A',	'Start Date Tomorrow',	'Test to see if becomes active tomorrow',	NULL,	'Test',	'Test@test',	NULL,	'20220815131012_Screen_Shot_2022-08-12_at_9.44.11_AM.png',	'2022-08-15 13:10:12',	'2022-08-15 13:10:12'),
(53,	36,	131413,	0.00,	'I',	'Alberni Clayoquot Support Pool',	'Alberni Clayoquot Support Pool',	NULL,	'Mark James',	'mark@a',	NULL,	'202208151310512025_pecsf-logo.png',	'2022-08-15 13:10:51',	'2022-08-15 13:10:51'),
(55,	38,	106509,	33.33,	'A',	'Field to Plate Program',	'The society facilitates the preservation of Greater Victoria farmland and the enhancement of food\r\nsecurity to benefit current and future generations. Through unique partnerships with other not‐for‐\r\nprofits, the Field to Plate Program is expanding to support growing, maintaining and donating organic\r\nproduce from Newman Farm to food banks and local drop‐in centers to better serve Greater Victoria\r\npersons in need – aiding in poverty relief and food insecurity.',	NULL,	'Natasha Caverley',	'Natasha@FTL.bc.ca',	NULL,	'20220819094942_BC_wildlife_logo.jpg',	'2022-08-19 09:49:42',	'2022-08-19 09:49:42'),
(56,	38,	98898,	33.33,	'A',	'Pivot to Virtual Program for Young People',	'The Pivot to Virtual Program will offer online facilitator‐led support and counselling strategies for\r\nanxiety, depression, and loneliness, etc. to groups of 10‐12 children/youth. Facilitators will engage\r\nparticipants, grouped by age, in virtual activities, role plays, mindfulness practices, art, and music ‐ \r\ngeared towards helping individuals express their emotions, feel connected and supported. Parents can\r\nalso take part in the virtual Triple P Parenting Course to learn core parenting skills and simple strategies\r\nto encourage positive behaviour, and prevent or manage misbehaviour, in their children.',	NULL,	'Test Name',	'employee1@example.com',	NULL,	'20220819094942_SouthIslandCentreforTrain.jpg',	'2022-08-19 09:49:42',	'2022-08-19 09:49:42'),
(57,	38,	92354,	33.34,	'A',	'Test',	'The Shelbourne Community Kitchen provides a dignified and inclusive approach to building skills around\r\ngrowing and sourcing food, increasing access to nutritious, local produce, and providing meaningful\r\nvolunteer opportunities to contribute to the physical and social well‐being of individuals and families\r\nliving on low incomes. The Garden Program provides produce for Food Skills sessions and the Pantry\r\n(food bank). volunteer opportunities to contribute to the physical and social well‐being of individuals and families\r\nliving on low incomes. The Garden Program provides produce for Food Skills sessions and the Pantry\r\n(food bank).',	'Test',	'Test',	'employee1@example.com',	NULL,	'20220819094942_habitatforhumanity.jpg',	'2022-08-19 09:49:42',	'2022-08-19 09:49:42'),
(58,	39,	7261,	11.60,	'A',	'Support Programs',	'Autism BC seeks to empower, support and connect people on the autism spectrum and their families through education, training, resources and support groups towards promoting the health and inc',	NULL,	'Cathy Nidoski',	'cnidoski@autismbc.ca',	NULL,	'2022081915322220_AutismBC-Primary_Logo-4C.jpg',	'2022-08-19 15:32:22',	'2022-08-19 15:32:22'),
(59,	39,	55683,	50.00,	'A',	'Student Programs',	'Through partnerships with educators and volunteers from local businesses, JABC offers important interactive, hands-on learning experiences in financial literacy, entrepreneurship, and work re',	NULL,	'Susan Shepherd',	'susan.shepherd@jabc.org',	NULL,	'2022081915322220_Junior-Achievement-BC.jpg',	'2022-08-19 15:32:22',	'2022-08-19 15:32:22'),
(60,	39,	19009,	38.40,	'A',	'Cariboo Peer Program',	'Spinal Cord Injury BC’s Peer Support Program gives people in the region with a spinal cord injury, their family and friends the opportunity to connect with others in similar situations, try a',	NULL,	'Susie Jackson',	'sjackson@sci-bc.ca',	NULL,	'2022081915322220_PECSF_Icon.png',	'2022-08-19 15:32:22',	'2022-08-19 15:32:22'),
(61,	40,	131413,	75.00,	'I',	'Alberni Clayoquot Support Pool',	'Alberni Clayoquot Support Pool',	NULL,	'Mark James',	'mark@a',	NULL,	'20220819153424522025_pecsf-logo.png',	'2022-08-19 15:34:24',	'2022-08-19 15:36:18'),
(62,	41,	131413,	0.00,	'I',	'Alberni Clayoquot Support Pool',	'Alberni Clayoquot Support Pool',	NULL,	'Mark James',	'mark@a',	NULL,	'20220819154734522025_pecsf-logo.png',	'2022-08-19 15:47:34',	'2022-08-19 15:47:34'),
(63,	42,	131413,	0.00,	'I',	'Alberni Clayoquot Support Pool',	'Alberni Clayoquot Support Pool',	NULL,	'Mark James',	'mark@a',	NULL,	'2022081915475734522025_pecsf-logo.png',	'2022-08-19 15:47:57',	'2022-08-19 15:47:57');

TRUNCATE `regions`;
INSERT INTO `regions` (`id`, `code`, `effdt`, `name`, `status`, `notes`, `created_by_id`, `updated_by_id`, `created_at`, `updated_at`) VALUES
(1,	'001',	'1940-01-01',	'Alberni-Clayoquot',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(2,	'002',	'1940-01-01',	'Bulkley-Nechako',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(3,	'003',	'1940-01-01',	'Cariboo',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(4,	'004',	'1940-01-01',	'Central Coast',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(5,	'005',	'1940-01-01',	'Fraser Valley',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(6,	'006',	'1940-01-01',	'Central Kootenay',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(7,	'007',	'1940-01-01',	'Central Okanagan',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(8,	'008',	'1940-01-01',	'Columbia-Shuswap',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(9,	'009',	'2022-06-20',	'Comox',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-06-20 11:48:42'),
(10,	'010',	'1940-01-01',	'Cowichan Valley',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(11,	'011',	'1940-01-01',	'East Kootenay',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(12,	'012',	'1940-01-01',	'Northern Rockies',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(13,	'013',	'1940-01-01',	'Fraser-Fort George',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(14,	'014',	'1940-01-01',	'Kitimat-Stikine',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(15,	'015',	'1940-01-01',	'Kootenay Boundary',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(16,	'016',	'1940-01-01',	'Mount Waddington',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(17,	'017',	'1940-01-01',	'Nanaimo',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(18,	'018',	'1940-01-01',	'North Okanagan',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(19,	'019',	'1940-01-01',	'Okanagan-Similkameen',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(20,	'020',	'1940-01-01',	'Peace River',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(21,	'021',	'1940-01-01',	'qathet',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-06-20 11:50:57'),
(22,	'022',	'1940-01-01',	'North Coast',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-06-20 11:50:35'),
(23,	'023',	'1940-01-01',	'Squamish-Lillooet',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(24,	'024',	'1940-01-01',	'Stikine',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(25,	'025',	'1940-01-01',	'Sunshine Coast',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(26,	'026',	'1940-01-01',	'Thompson-Nicola',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(27,	'027',	'1940-01-01',	'Metro Vancouver',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-06-20 11:49:10'),
(28,	'028',	'1940-01-01',	'Capital',	'A',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(29,	'029',	'1940-01-01',	'Ontario',	'I',	NULL,	1,	1,	'2022-05-04 11:18:21',	'2022-05-04 11:18:21'),
(30,	'030',	'2021-10-20',	'Strathcona',	'A',	NULL,	1001,	1001,	'2022-06-20 11:40:54',	'2022-06-20 11:40:54');

-- 2022-09-02 21:01:01
