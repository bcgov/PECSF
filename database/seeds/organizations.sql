-- Adminer 4.8.1 MySQL 5.7.38 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

TRUNCATE `organizations`;
INSERT INTO `organizations` (`id`, `code`, `name`, `status`, `effdt`, `created_by_id`, `updated_by_id`, `created_at`, `updated_at`) VALUES
(41,	'FSA',	'BC Financial Services Authority',	'I',	'2019-10-27',	NULL,	NULL,	'2022-05-13 08:51:33',	'2022-09-06 15:51:37'),
(42,	'GOV',	'Government of B.C.',	'A',	'2004-01-01',	NULL,	NULL,	'2022-05-13 08:51:33',	'2022-09-06 15:52:19'),
(43,	'BCA',	'BC Ambulance',	'I',	'2015-07-30',	NULL,	NULL,	'2022-05-13 08:51:33',	'2022-05-13 08:51:33'),
(44,	'LA',	'Legislative Assembly of BC',	'A',	'2004-01-01',	NULL,	NULL,	'2022-05-13 08:51:33',	'2022-09-06 15:55:10'),
(45,	'FP',	'Forensic Psychiatric',	'I',	'2015-07-30',	NULL,	NULL,	'2022-05-13 08:51:33',	'2022-05-13 08:51:33'),
(46,	'LDB',	'BC Liquor Distribution Branch',	'A',	'2004-01-01',	NULL,	NULL,	'2022-05-13 08:51:33',	'2022-09-06 15:55:23'),
(47,	'BCS',	'BC Securities Commission',	'A',	'2004-01-01',	NULL,	NULL,	'2022-05-13 08:51:33',	'2022-06-14 14:35:33'),
(48,	'PAR',	'Partnerships BC',	'I',	'2006-03-01',	NULL,	NULL,	'2022-05-13 08:51:33',	'2022-05-13 08:51:33'),
(49,	'TSS',	'Telus Sourcing Solutions',	'I',	'2006-03-01',	NULL,	NULL,	'2022-05-13 08:51:33',	'2022-05-13 08:51:33'),
(50,	'HLN',	'Health Link Nurses',	'I',	'2009-01-01',	NULL,	NULL,	'2022-05-13 08:51:33',	'2022-06-14 14:35:20'),
(51,	'RET',	'Retirees',	'A',	'2010-09-15',	NULL,	NULL,	'2022-05-13 08:51:33',	'2022-05-13 08:51:33');

-- 2022-09-16 18:19:18
