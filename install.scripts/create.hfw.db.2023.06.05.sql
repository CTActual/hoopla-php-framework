-- phpMyAdmin SQL Dump
-- version 4.9.5deb2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jun 05, 2023 at 10:41 PM
-- Server version: 8.0.33-0ubuntu0.20.04.2
-- PHP Version: 7.4.3-4ubuntu2.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

--
-- Database: hooplafw
--
CREATE DATABASE IF NOT EXISTS hooplafw DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE hooplafw;

-- --------------------------------------------------------

--
-- Table structure for table ctx
--

CREATE TABLE IF NOT EXISTS ctx (
  id int UNSIGNED NOT NULL AUTO_INCREMENT,
  ctx_name varchar(31) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Full name for the context',
  ctx_lbl varchar(31) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Label for querying.',
  ctx_dsr varchar(127) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Description of the context.',
  ctx_type_id int UNSIGNED NOT NULL DEFAULT '30' COMMENT 'Context type id.',
  pg_id int UNSIGNED DEFAULT NULL COMMENT 'Page specific to the context',
  pg_obj_id int UNSIGNED DEFAULT NULL COMMENT 'Page object specific to the context',
  spc_ord int UNSIGNED DEFAULT NULL COMMENT 'Manually order the entries.',
  act_bit tinyint(1) NOT NULL DEFAULT '1' COMMENT 'True if active.',
  PRIMARY KEY (id),
  UNIQUE KEY ctx_name (ctx_name),
  UNIQUE KEY ctx_lbl (ctx_lbl),
  KEY ctx_type_id (ctx_type_id),
  KEY spc_ord (spc_ord),
  KEY pg_id (pg_id),
  KEY pg_obj_id (pg_obj_id)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Arbitrary contexts to span pages added during project development.';

--
-- Dumping data for table ctx
--

INSERT IGNORE INTO ctx (id, ctx_name, ctx_lbl, ctx_dsr, ctx_type_id, pg_id, pg_obj_id, spc_ord, act_bit) VALUES
(1, 'Default Context', 'def_ctx', 'This context is required for use in the settings value table for any non-contextual value as id=1.', 30, NULL, NULL, 1, 1),
(2, 'Default Page Context', 'def&lowbar;pg&lowbar;ctx', 'Pages with no special association&period;', 36, NULL, NULL, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table meta_types
--

CREATE TABLE IF NOT EXISTS meta_types (
  id int UNSIGNED NOT NULL AUTO_INCREMENT,
  spc_ord int UNSIGNED DEFAULT NULL COMMENT 'Allows for ordering lists entered in random order',
  meta_type_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Names are preset.',
  meta_type_dsr varchar(1023) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Full descriptions.',
  PRIMARY KEY (id)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Stores all meta or super types to sort the type table with.' ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table meta_types
--

INSERT IGNORE INTO meta_types (id, spc_ord, meta_type_name, meta_type_dsr) VALUES
(1, 1, 'Page Object', 'The type of page objects that can exist.'),
(2, 2, 'Page Object Setting', 'The type of page object properties that can exist'),
(3, 3, 'Context', 'Meta contexts or types of contexts.');

-- --------------------------------------------------------

--
-- Table structure for table pgs
--

CREATE TABLE IF NOT EXISTS pgs (
  id int UNSIGNED NOT NULL AUTO_INCREMENT,
  pg_obj_id int UNSIGNED NOT NULL COMMENT 'Stores the type of page as a page object id',
  pg_ctx_id int UNSIGNED NOT NULL DEFAULT '2' COMMENT 'Stores the page context id',
  PRIMARY KEY (id),
  UNIQUE KEY pg_obj_id (pg_obj_id),
  KEY pg_ctx_id (pg_ctx_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Keeps track of individual pages where the URL points to the ';

-- --------------------------------------------------------

--
-- Table structure for table pg_objs
--

CREATE TABLE IF NOT EXISTS pg_objs (
  id int UNSIGNED NOT NULL AUTO_INCREMENT,
  pg_obj_type_id int UNSIGNED NOT NULL,
  obj_name varchar(63) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'ID''s object',
  obj_dsr varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Describes object',
  acs_str varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Gives access based on role to object',
  act_bit tinyint(1) NOT NULL DEFAULT '0' COMMENT 'True if active',
  PRIMARY KEY (id),
  UNIQUE KEY unique_obj (pg_obj_type_id,obj_name),
  KEY pg_obj_type_id (pg_obj_type_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Keeps track of all objects that a page can use.' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table pg_obj_pg_obj_set_val_brg
--

CREATE TABLE IF NOT EXISTS pg_obj_pg_obj_set_val_brg (
  id int UNSIGNED NOT NULL AUTO_INCREMENT,
  pg_obj_id int UNSIGNED NOT NULL,
  pg_obj_set_type_id int UNSIGNED NOT NULL,
  pg_obj_set_val varchar(16000) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Store the setting (property) value.',
  pg_id int UNSIGNED DEFAULT NULL COMMENT 'Stores the page id for the setting, or null if default',
  ctx_id int UNSIGNED NOT NULL DEFAULT '1' COMMENT 'An arbitrary context can be chosen in addition to the default.',
  act_bit tinyint(1) NOT NULL COMMENT 'True = 1 if in use.',
  PRIMARY KEY (id),
  UNIQUE KEY unique_set (pg_obj_id,pg_obj_set_type_id,pg_id,ctx_id),
  KEY secondary_index (pg_obj_id,pg_obj_set_type_id),
  KEY context (ctx_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Stores the actual object values for reuse on pages.' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table pg_obj_type_pg_obj_set_type_brg
--

CREATE TABLE IF NOT EXISTS pg_obj_type_pg_obj_set_type_brg (
  id int UNSIGNED NOT NULL AUTO_INCREMENT,
  pg_obj_type_id int UNSIGNED NOT NULL,
  pg_obj_set_type_id int UNSIGNED NOT NULL,
  act_bit tinyint(1) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY pg_obj_type_id (pg_obj_type_id,pg_obj_set_type_id)
) ENGINE=MyISAM AUTO_INCREMENT=278 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Keeps track of what settings an object type uses.';

--
-- Dumping data for table pg_obj_type_pg_obj_set_type_brg
--

INSERT IGNORE INTO pg_obj_type_pg_obj_set_type_brg (id, pg_obj_type_id, pg_obj_set_type_id, act_bit) VALUES
(1, 1, 18, 1),
(2, 1, 19, 1),
(3, 1, 20, 1),
(4, 1, 21, 1),
(5, 1, 22, 1),
(6, 1, 23, 1),
(7, 1, 24, 1),
(8, 1, 26, 1),
(9, 1, 27, 1),
(10, 2, 19, 1),
(11, 2, 22, 1),
(12, 2, 24, 1),
(13, 2, 26, 1),
(14, 2, 27, 1),
(15, 3, 19, 1),
(16, 3, 22, 1),
(17, 3, 24, 1),
(18, 3, 26, 1),
(19, 3, 27, 1),
(20, 4, 19, 1),
(21, 4, 22, 1),
(22, 4, 24, 1),
(23, 4, 26, 1),
(24, 4, 27, 1),
(25, 5, 19, 1),
(26, 5, 22, 1),
(27, 5, 24, 1),
(28, 5, 26, 1),
(29, 5, 27, 1),
(30, 6, 18, 1),
(31, 6, 19, 1),
(32, 6, 20, 1),
(33, 6, 21, 1),
(34, 6, 22, 1),
(35, 6, 23, 1),
(36, 6, 24, 1),
(37, 6, 26, 1),
(38, 6, 27, 1),
(39, 7, 19, 1),
(40, 7, 22, 1),
(41, 7, 23, 1),
(42, 7, 24, 1),
(43, 7, 26, 1),
(44, 7, 27, 1),
(45, 8, 19, 1),
(46, 8, 22, 1),
(47, 8, 23, 1),
(117, 14, 28, 1),
(49, 8, 27, 1),
(50, 9, 19, 1),
(51, 9, 22, 1),
(52, 9, 23, 1),
(53, 9, 24, 1),
(54, 9, 26, 1),
(55, 9, 27, 1),
(56, 10, 18, 1),
(57, 10, 19, 1),
(58, 10, 20, 1),
(59, 10, 21, 1),
(60, 10, 22, 1),
(61, 10, 23, 1),
(62, 10, 24, 1),
(63, 10, 26, 1),
(64, 10, 27, 1),
(65, 11, 18, 1),
(66, 11, 19, 1),
(67, 11, 20, 1),
(68, 11, 21, 1),
(69, 11, 22, 1),
(70, 11, 23, 1),
(71, 11, 24, 1),
(72, 11, 26, 1),
(73, 11, 27, 1),
(74, 12, 18, 1),
(75, 12, 19, 1),
(76, 12, 20, 1),
(77, 12, 21, 1),
(78, 12, 22, 1),
(79, 12, 23, 1),
(80, 12, 24, 1),
(81, 12, 26, 1),
(82, 12, 27, 1),
(83, 13, 18, 1),
(84, 13, 19, 1),
(85, 13, 20, 1),
(86, 13, 21, 1),
(87, 13, 22, 1),
(88, 13, 23, 1),
(89, 13, 24, 1),
(90, 13, 26, 1),
(91, 13, 27, 1),
(92, 14, 18, 1),
(93, 14, 19, 1),
(94, 14, 20, 1),
(95, 14, 21, 1),
(96, 14, 22, 1),
(97, 15, 18, 1),
(98, 15, 19, 1),
(99, 15, 20, 1),
(100, 15, 21, 1),
(101, 15, 22, 1),
(102, 15, 23, 1),
(103, 15, 24, 1),
(104, 15, 25, 1),
(105, 15, 26, 1),
(106, 15, 27, 1),
(107, 16, 25, 1),
(108, 17, 19, 1),
(109, 17, 22, 1),
(110, 17, 23, 1),
(111, 17, 24, 1),
(112, 17, 26, 1),
(113, 17, 27, 1),
(114, 15, 125, 1),
(115, 8, 125, 1),
(116, 126, 24, 1),
(118, 14, 34, 1),
(119, 14, 35, 1),
(120, 15, 33, 1),
(121, 15, 34, 1),
(122, 15, 35, 1),
(123, 9, 33, 1),
(124, 9, 34, 1),
(125, 9, 35, 1),
(126, 17, 33, 1),
(127, 17, 34, 1),
(128, 17, 35, 1),
(129, 7, 33, 1),
(130, 7, 34, 1),
(131, 7, 35, 1),
(132, 2, 33, 1),
(133, 2, 34, 1),
(134, 2, 35, 1),
(135, 13, 33, 1),
(136, 13, 34, 1),
(137, 13, 35, 1),
(138, 1, 33, 1),
(139, 1, 34, 1),
(140, 1, 35, 1),
(141, 6, 33, 1),
(142, 6, 34, 1),
(143, 6, 35, 1),
(144, 10, 33, 1),
(145, 10, 34, 1),
(146, 10, 35, 1),
(147, 8, 33, 1),
(148, 8, 34, 1),
(149, 8, 35, 1),
(150, 29, 26, 1),
(151, 15, 32, 1),
(152, 15, 28, 1),
(153, 13, 32, 1),
(154, 13, 28, 1),
(155, 1, 32, 1),
(156, 1, 28, 1),
(157, 6, 32, 1),
(158, 6, 28, 1),
(159, 10, 32, 1),
(160, 10, 28, 1),
(161, 37, 38, 1),
(162, 39, 19, 1),
(163, 39, 18, 1),
(164, 39, 20, 1),
(165, 39, 21, 1),
(166, 39, 32, 1),
(167, 39, 33, 1),
(168, 39, 34, 1),
(169, 39, 35, 1),
(170, 39, 22, 1),
(171, 39, 23, 1),
(172, 39, 24, 1),
(173, 39, 28, 1),
(174, 40, 26, 1),
(175, 40, 19, 1),
(176, 40, 27, 1),
(177, 40, 33, 1),
(178, 40, 34, 1),
(179, 40, 35, 1),
(180, 40, 22, 1),
(181, 40, 23, 1),
(182, 40, 24, 1),
(183, 40, 25, 1),
(184, 40, 28, 1),
(185, 41, 26, 1),
(186, 41, 27, 1),
(187, 41, 33, 1),
(188, 41, 34, 1),
(189, 41, 35, 1),
(190, 41, 22, 1),
(191, 48, 26, 1),
(192, 48, 19, 1),
(193, 48, 27, 1),
(194, 48, 33, 1),
(195, 48, 34, 1),
(196, 48, 35, 1),
(197, 48, 42, 1),
(198, 48, 22, 1),
(199, 48, 23, 1),
(200, 48, 28, 1),
(201, 8, 42, 1),
(202, 45, 26, 0),
(203, 45, 19, 1),
(204, 45, 27, 1),
(205, 45, 33, 1),
(206, 45, 34, 1),
(207, 45, 35, 1),
(208, 45, 42, 1),
(209, 45, 22, 1),
(210, 45, 23, 1),
(211, 45, 24, 0),
(212, 45, 28, 1),
(213, 46, 19, 1),
(214, 46, 27, 1),
(215, 46, 33, 1),
(216, 46, 34, 1),
(217, 46, 35, 1),
(218, 46, 42, 1),
(219, 46, 22, 1),
(220, 46, 23, 1),
(221, 46, 28, 1),
(222, 47, 19, 1),
(223, 47, 27, 1),
(224, 47, 33, 1),
(225, 47, 34, 1),
(226, 47, 35, 1),
(227, 47, 42, 1),
(228, 47, 22, 1),
(229, 47, 23, 1),
(230, 47, 28, 1),
(231, 14, 27, 1),
(232, 14, 33, 1),
(233, 48, 49, 1),
(234, 8, 49, 1),
(235, 45, 49, 1),
(236, 46, 49, 1),
(237, 47, 49, 1),
(238, 50, 26, 0),
(239, 50, 19, 1),
(240, 50, 27, 1),
(241, 50, 33, 1),
(242, 50, 34, 1),
(243, 50, 35, 1),
(244, 50, 42, 1),
(245, 50, 49, 1),
(246, 50, 23, 1),
(247, 50, 24, 0),
(248, 50, 22, 1),
(249, 50, 28, 1),
(250, 50, 51, 1),
(251, 45, 51, 1),
(252, 8, 51, 1),
(253, 48, 51, 1),
(254, 52, 26, 1),
(255, 52, 27, 1),
(256, 52, 33, 1),
(257, 52, 34, 1),
(258, 52, 35, 1),
(259, 6, 51, 1),
(260, 6, 53, 1),
(261, 10, 51, 1),
(262, 10, 53, 1),
(263, 48, 53, 1),
(264, 8, 53, 1),
(265, 45, 53, 1),
(266, 50, 53, 1),
(267, 46, 51, 1),
(268, 46, 53, 1),
(269, 47, 51, 1),
(270, 47, 53, 1),
(271, 6, 54, 1),
(272, 46, 54, 1),
(273, 55, 26, 1),
(274, 55, 27, 1),
(275, 55, 33, 1),
(276, 55, 34, 1),
(277, 55, 35, 1);

-- --------------------------------------------------------

--
-- Table structure for table pg_pg_obj_brg
--

CREATE TABLE IF NOT EXISTS pg_pg_obj_brg (
  id int UNSIGNED NOT NULL AUTO_INCREMENT,
  pg_id int UNSIGNED NOT NULL,
  pg_obj_id int UNSIGNED NOT NULL,
  pg_obj_loc varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Stores the variable name of the placeholder on the page.',
  spc_ord smallint DEFAULT NULL COMMENT 'For objects that coalesce',
  use_def_bit tinyint(1) NOT NULL DEFAULT '1' COMMENT 'True if default values allowed.',
  act_bit tinyint(1) NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY unique_id (pg_id,pg_obj_id)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Keeps track of objects on a particular page.' ROW_FORMAT=DYNAMIC;

-- --------------------------------------------------------

--
-- Table structure for table srv_meta_data
--

CREATE TABLE IF NOT EXISTS srv_meta_data (
  srv_name varchar(63) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL COMMENT 'Full Server Name',
  srv_lbl varchar(63) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Server Nickname',
  srv_dsr varchar(1023) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Platform description',
  PRIMARY KEY (srv_name)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Keeps track of server name';

--
-- Dumping data for table srv_meta_data
--

INSERT IGNORE INTO srv_meta_data (srv_name, srv_lbl, srv_dsr) VALUES
('Hoopla', 'Hoopla Server 1', 'Hoopla Database Project Server');

-- --------------------------------------------------------

--
-- Table structure for table `types`
--

CREATE TABLE IF NOT EXISTS `types` (
  id int UNSIGNED NOT NULL AUTO_INCREMENT,
  spc_ord int UNSIGNED DEFAULT NULL COMMENT 'Allows for ordering lists entered in random order',
  type_name varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'A given type name.',
  std_type_lbl varchar(63) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Used for web service communications',
  type_dsr varchar(1023) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Full type description.',
  meta_type_id int UNSIGNED NOT NULL COMMENT 'Type-types or super-types.',
  act_bit tinyint(1) NOT NULL DEFAULT '1' COMMENT 'True if active',
  PRIMARY KEY (id),
  UNIQUE KEY std_type_lbl (std_type_lbl),
  UNIQUE KEY unique_name (meta_type_id,type_name(63)),
  KEY meta_type_id (meta_type_id),
  KEY name (type_name(63))
) ENGINE=MyISAM AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Stores all types except roles, under meta-type' ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `types`
--

INSERT IGNORE INTO `types` (id, spc_ord, type_name, std_type_lbl, type_dsr, meta_type_id, act_bit) VALUES
(1, 90, 'Admin Menu', 'amenu', 'An alternative list of links for admin users.', 1, 1),
(2, 70, 'Data Set', 'data', 'A block of data shown on the page. May contain HTML input elements.', 1, 1),
(6, 100, 'Form', 'form', 'A form encountered on the page.', 1, 1),
(7, 60, 'Footer', 'ftr', 'The footer at the bottom of the page.', 1, 1),
(8, 130, 'Form Element', 'felement', 'Text boxes and other user entry form objects (use to segregate from all elements).', 1, 1),
(9, 40, 'Header', 'hdr', 'The header on the page, as seen in the browser.', 1, 1),
(10, 110, 'Block', 'block', 'A section of a page.', 1, 1),
(13, 80, 'Menu', 'menu', 'A list of links.', 1, 1),
(14, 240, 'Page', 'pg', 'Page as an object with properties.', 1, 1),
(15, 10, 'Page Body', 'body', 'Anything that goes in the main body of the page but not a sub-section of the body, such as a form', 1, 1),
(16, 30, 'DB Query', 'dbq', 'Database query.', 1, 1),
(17, 50, 'Sub Header', 'shdr', 'The sub-header on the page, as seen in the browser.', 1, 1),
(18, 40, 'CSS Include', 'css_file', 'Some reference to a CSS file name or link url.', 2, 1),
(19, 20, 'HTML String', 'html', 'HTML insert with entities and/or tags.', 2, 1),
(20, 50, 'JS File Include', 'js_file', 'Some reference to a JS file name or link url.', 2, 1),
(21, 60, 'JS Library Include', 'js_lib', 'Some reference to a JS library name or link url.', 2, 1),
(22, 160, 'PHP Include', 'php_file', 'Some reference to a PHP file (use this feature to associate one page with another).', 2, 1),
(23, 170, 'PHP Code String', 'php_code', 'PHP to be directly evaluated.', 2, 1),
(24, 180, 'PHP Variable', 'php_var_val', 'The value of a PHP variable, such as a string.  Not for template variables, but true page variables.', 2, 1),
(25, 210, 'DB Query String', 'dbqs', 'A database query string to assign to a variable.', 2, 1),
(26, 10, 'Text', 'txt', 'Presentation text string.', 2, 1),
(27, 80, 'XML String', 'xml', 'XML to be unserialized or expressed directly.', 2, 1),
(28, 220, 'External Code', 'ext_code', '3rd Party Code Reference file name or link url.', 2, 1),
(29, 180, 'Page Query Value', 'pqv', 'Allows one to query pgs with this value.', 1, 1),
(30, 1, 'Default Context', 'def_ctx', 'The default context for a setting value.', 3, 1),
(31, 3, 'Arbitrary Context', 'arb_ctx', 'Any global context for a setting value, accessible to all pages and objects.', 3, 1),
(32, 70, 'JS Code', 'js_code', 'Javascript code to add to a page.', 2, 1),
(33, 90, 'JSON String', 'json', 'JSON string to be pulled into the page or sent out.', 2, 1),
(34, 100, 'CSV List String', 'csv', 'A delimited separated values list string.', 2, 1),
(35, 110, 'NSV List String', 'nsv', 'A newline separated values list string.', 2, 1),
(36, 2, 'Page Context', 'pg_ctx', 'Any group of pages, such as those belonging to a template.', 3, 1),
(37, 230, 'Page Detail', 'detail', 'Supplemental page information with a value.', 1, 1),
(38, 230, 'Detail Value', 'det_val', 'The value of any supplemental detail.', 2, 1),
(39, 20, 'Page Head', 'head', 'Anything within head tags.', 1, 1),
(40, 190, 'PHP', 'php', 'PHP scripting or code outside the scope of the HTML.', 1, 1),
(41, 200, 'Procedure-Callback List', 'pcbl', 'A list of procedures, functions or callbacks to call.', 1, 1),
(42, 120, 'Attribute Value Pair', 'avp', 'Attribute=Value pair', 2, 1),
(43, 150, 'URL Query String', 'uqs', 'URL Query String (typically after ? with &amp; separated lists of attribute=value pairs)', 2, 1),
(44, 30, 'URL', 'url', 'A full or partial URL.', 2, 1),
(45, 140, 'Form Input Element', 'input', 'Any HTML form input element (use to segregate from other elements)', 1, 1),
(46, 160, 'Form Button', 'button', 'Any clickable HTML element (use to segregate from other elements)', 1, 1),
(47, 170, 'Hidden Element', 'hidden', 'A hidden HTML element (use to segregate from other elements)', 1, 1),
(48, 120, 'Element', 'element', 'Any HTML element.', 1, 1),
(49, 130, 'Post-Get-Request Name', 'postname', 'The lookup name of any element returned after submission.', 2, 1),
(50, 150, 'Form Input Empty Element', 'empty', 'Any form input element that has no current value, such as for a new row of data entry.', 1, 1),
(51, 190, 'PHP Function Call', 'php_func_call', 'A call to a user defined or stock function based on some pattern to return a result.', 2, 1),
(52, 220, 'Context List', 'ctxlist', 'A list of contexts', 1, 1),
(53, 200, 'PHP Function Reference', 'php_func_ref', 'A reference to a PCBL object instead of a more direct value.', 2, 1),
(54, 140, 'Post-Get-Request List', 'postlist', 'The lookup names of any elements of interest returned after submission.', 2, 1),
(55, 210, 'Object List', 'objlist', 'A list of objects', 1, 1),
(56, 4, 'Specific Context', 'spcf_ctx', 'A context that is specific to a page and/or an object.', 3, 1);
