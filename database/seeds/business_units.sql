-- Adminer 4.8.1 MySQL 5.7.33 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

TRUNCATE `business_units`;
INSERT INTO `business_units` (`id`, `code`, `effdt`, `status`, `name`, `notes`, `created_by_id`, `updated_by_id`, `created_at`, `updated_at`) VALUES
(1,	'BC115',	'2015-01-01',	'A',	'Environmental Assessment Office',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:36:23'),
(5,	'BC000',	'2015-01-01',	'A',	'Government of B.C.',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-07 13:34:59'),
(6,	'BC002',	'2015-01-01',	'A',	'Legislative Assembly of BC',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:36:53'),
(7,	'BC003',	'2015-01-01',	'A',	'Office of the Auditor General',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-07 13:34:59'),
(8,	'BC004',	'2015-01-01',	'A',	'Office of the Premier',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-07 13:34:59'),
(11,	'BC007',	'2015-01-01',	'A',	'Office of the Ombudsperson',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-07 13:34:59'),
(12,	'BC009',	'2015-01-01',	'A',	'Office of Information and Privacy Commissioner',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:10:59'),
(13,	'BC010',	'2015-01-01',	'A',	'Ministry of Public Safety and Solicitor General',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:37:51'),
(15,	'BC015',	'2015-01-01',	'A',	'Elections BC',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-07 13:34:59'),
(16,	'BC019',	'2015-01-01',	'A',	'Ministry of Advanced Education, Skills and Training',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:31:47'),
(18,	'BC022',	'2015-01-01',	'A',	'Ministry of Finance',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-07 13:34:59'),
(22,	'BC026',	'2015-01-01',	'A',	'Ministry of Health',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:38:24'),
(24,	'BC031',	'2015-01-01',	'A',	'Ministry of Social Development and Poverty Reduction',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:38:43'),
(25,	'BC034',	'2015-01-01',	'A',	'Ministry of Transportation and Infrastructure',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:39:11'),
(27,	'BC039',	'2015-01-01',	'A',	'Ministry of Children and Family Development',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:39:31'),
(30,	'BC048',	'2015-01-01',	'A',	'Ministry of Environment and Climate Change Strategy',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:39:54'),
(33,	'BC057',	'2015-01-01',	'A',	'Ministry of Energy, Mines and Low Carbon Innovation',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 11:57:22'),
(34,	'BC060',	'2015-01-01',	'A',	'Ministry of Municipal Affairs',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:40:41'),
(35,	'BC062',	'2015-01-01',	'A',	'Ministry of Education',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:41:08'),
(36,	'BC067',	'2015-01-01',	'A',	'Ministry of Citizens\' Services',	'Product Services',	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 12:09:09'),
(37,	'BC068',	'2015-01-01',	'A',	'Public Sector Employers\' Council Secretariat',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 11:58:13'),
(39,	'BC077',	'2015-01-01',	'A',	'Royal BC Museum',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-07 13:34:59'),
(40,	'BC079',	'2015-01-01',	'A',	'Forest Practices Board',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-07 13:34:59'),
(43,	'BC088',	'2015-01-01',	'A',	'BC Pension Corporation',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:34:06'),
(52,	'BC105',	'2015-01-01',	'A',	'Ministry of Attorney General',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:33:26'),
(54,	'BC112',	'2015-01-01',	'A',	'Ministry of Citizens\' Services',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:43:19'),
(55,	'BC130',	'2015-01-01',	'A',	'Ministry of Agriculture and Food',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:32:33'),
(63,	'BC805',	'2015-01-01',	'A',	'Community Living BC',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-07 13:34:59'),
(64,	'BC100',	'2015-01-01',	'A',	'BC Public Service Agency',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-07 13:34:59'),
(65,	'BC120',	'2015-01-01',	'A',	'Ministry of Indigenous Relations and Reconciliation',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:44:08'),
(66,	'BC125',	'2015-01-01',	'A',	'Ministry of Jobs, Economic Recovery and Innovation',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:44:22'),
(68,	'BC106',	'2015-01-01',	'A',	'Office of the Merit Commissioner',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:50:02'),
(70,	'BC109',	'2015-01-01',	'A',	'BC Representative for Children and Youth',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:34:41'),
(74,	'BC127',	'2015-01-01',	'A',	'Ministry of Labour',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-07 13:34:59'),
(75,	'BC128',	'2022-04-01',	'A',	'Ministry of Forests',	'NV - 01APR2022 - BU created.',	1,	1,	'2022-07-07 13:34:59',	'2022-09-06 15:50:12'),
(77,	'BC131',	'2015-01-01',	'A',	'Ministry of Attorney General',	'Ministry of Housing',	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 12:09:53'),
(78,	'BC104',	'2015-01-01',	'A',	'Public Guardian and Trustee of BC',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:44:57'),
(79,	'BC063',	'2015-01-01',	'A',	'Ministry of Education',	'Teachers Act Special Account',	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 12:08:48'),
(81,	'BC126',	'2017-09-21',	'A',	'Ministry of Tourism, Arts, Culture and Sport',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:45:21'),
(82,	'BC029',	'2018-09-05',	'A',	'Ministry of Mental Health and Addictions',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:45:38'),
(83,	'BC825',	'2015-01-01',	'A',	'Destination BC Corporation',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:45:57'),
(84,	'BC601',	'2019-10-27',	'A',	'BC Financial Services Authority',	NULL,	1,	1,	'2022-07-07 13:34:59',	'2022-07-26 10:34:53'),
(118,	'BC725',	'2022-01-03',	'A',	'Ministry of Transportation and Infrastructure',	'Transportation Investment Corp',	1001,	1001,	'2022-07-26 12:01:58',	'2022-07-26 12:01:58'),
(119,	'BCLDB',	'2022-01-03',	'A',	'BC Liquor Distribution Branch  ',	NULL,	1001,	1001,	'2022-07-26 12:04:22',	'2022-07-26 12:04:22'),
(120,	'BCSC',	'2022-01-03',	'A',	'BC Securities Commission',	NULL,	1001,	1001,	'2022-07-26 12:05:07',	'2022-07-26 12:05:07'),
(121,	'BC113',	'2022-01-03',	'A',	'Office of the BC Human Rights Commissioner',	NULL,	1001,	1001,	'2022-07-26 12:06:36',	'2022-07-26 12:06:36'),
(122,	'GCPE',	'2022-01-03',	'A',	'Government Communications and Public Engagement',	NULL,	1001,	1001,	'2022-07-26 12:11:51',	'2022-07-26 12:11:51'),
(123,	'EMBC',	'2022-01-03',	'A',	'Emergency Management BC',	NULL,	1001,	1001,	'2022-07-26 12:13:04',	'2022-07-26 12:13:04'),
(124,	'BC133',	'2022-04-01',	'A',	'Ministry of Land, Water and Resource Stewardship',	'NV - 01APR2022 - BU created.',	1001,	1001,	'2022-08-04 15:26:26',	'2022-09-06 15:50:34');

-- 2022-09-13 02:49:14
