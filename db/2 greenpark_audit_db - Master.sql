-- Adminer 4.2.5 MySQL dump

SET NAMES utf8;
SET time_zone = '+05:30';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `tbl_gp_audit_methods`;
CREATE TABLE `tbl_gp_audit_methods` (
  `audit_method_id` int(11) NOT NULL AUTO_INCREMENT,
  `audit_method_name` varchar(100) NOT NULL COMMENT 'audit method name (1->Routine,2->Surprise,3->Special)',
  PRIMARY KEY (`audit_method_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO `tbl_gp_audit_methods` (`audit_method_id`, `audit_method_name`) VALUES
(1,	'Routine'),
(2,	'Surprise'),
(3,	'Special')
ON DUPLICATE KEY UPDATE `audit_method_id` = VALUES(`audit_method_id`), `audit_method_name` = VALUES(`audit_method_name`);

DROP TABLE IF EXISTS `tbl_gp_auth_assignment`;
CREATE TABLE `tbl_gp_auth_assignment` (
  `item_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `user_id` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`item_name`,`user_id`),
  CONSTRAINT `tbl_gp_auth_assignment_ibfk_1` FOREIGN KEY (`item_name`) REFERENCES `tbl_gp_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `tbl_gp_auth_item`;
CREATE TABLE `tbl_gp_auth_item` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `rule_name` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`),
  KEY `rule_name` (`rule_name`),
  KEY `idx-auth_item-type` (`type`),
  CONSTRAINT `tbl_gp_auth_item_ibfk_1` FOREIGN KEY (`rule_name`) REFERENCES `tbl_gp_auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `tbl_gp_auth_item_child`;
CREATE TABLE `tbl_gp_auth_item_child` (
  `parent` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `child` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`parent`,`child`),
  KEY `child` (`child`),
  CONSTRAINT `tbl_gp_auth_item_child_ibfk_1` FOREIGN KEY (`parent`) REFERENCES `tbl_gp_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `tbl_gp_auth_item_child_ibfk_2` FOREIGN KEY (`child`) REFERENCES `tbl_gp_auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `tbl_gp_auth_rule`;
CREATE TABLE `tbl_gp_auth_rule` (
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `data` blob,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


DROP TABLE IF EXISTS `tbl_gp_checklists`;
CREATE TABLE `tbl_gp_checklists` (
  `checklist_id` int(11) NOT NULL AUTO_INCREMENT,
  `cl_name` varchar(100) NOT NULL COMMENT 'checklist name',
  `cl_audit_type` tinyint(1) NOT NULL COMMENT 'audti type (0->Internal,1->External)',
  `cl_audit_method` int(11) NOT NULL COMMENT 'FK - audit_methods',
  `cl_department_id` int(11) NOT NULL COMMENT 'FK - departments',
  `cl_frequency_value` int(11) NOT NULL COMMENT 'frequency value',
  `cl_frequency_duration` int(11) NOT NULL COMMENT 'FK - interval',
  `cl_audit_span` tinyint(1) NOT NULL COMMENT 'audit span (1->Section Specific,2->Across Section)',
  `cl_status` tinyint(1) NOT NULL COMMENT 'checklist status (0-Inactive,1-Active)',
  PRIMARY KEY (`checklist_id`),
  KEY `cl_audit_method` (`cl_audit_method`),
  KEY `cl_department_id` (`cl_department_id`),
  KEY `cl_frequency_duration` (`cl_frequency_duration`),
  CONSTRAINT `tbl_gp_checklists_ibfk_1` FOREIGN KEY (`cl_audit_method`) REFERENCES `tbl_gp_audit_methods` (`audit_method_id`),
  CONSTRAINT `tbl_gp_checklists_ibfk_2` FOREIGN KEY (`cl_department_id`) REFERENCES `tbl_gp_departments` (`department_id`),
  CONSTRAINT `tbl_gp_checklists_ibfk_3` FOREIGN KEY (`cl_frequency_duration`) REFERENCES `tbl_gp_interval` (`interval_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS `tbl_gp_departments`;
CREATE TABLE `tbl_gp_departments` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(100) NOT NULL COMMENT 'department name',
  `created_by` int(11) NOT NULL COMMENT 'created by user id',
  `modified_by` int(11) NOT NULL COMMENT 'modified by user id',
  `created_date` datetime NOT NULL COMMENT 'created date',
  `modified_date` datetime NOT NULL COMMENT 'modified date',
  PRIMARY KEY (`department_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  CONSTRAINT `tbl_gp_departments_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `tbl_gp_user` (`user_id`),
  CONSTRAINT `tbl_gp_departments_ibfk_2` FOREIGN KEY (`modified_by`) REFERENCES `tbl_gp_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `tbl_gp_departments_map`;
CREATE TABLE `tbl_gp_departments_map` (
  `department_map_id` int(11) NOT NULL AUTO_INCREMENT,
  `dm_location_id` int(11) NOT NULL COMMENT 'FK- locations',
  `dm_hotel_id` int(11) NOT NULL COMMENT 'FK - hotels',
  `dm_department_id` int(11) NOT NULL COMMENT 'FK - departments',
  PRIMARY KEY (`department_map_id`),
  KEY `dm_location_id` (`dm_location_id`),
  KEY `dm_hotel_id` (`dm_hotel_id`),
  KEY `dm_department_id` (`dm_department_id`),
  CONSTRAINT `tbl_gp_departments_map_ibfk_1` FOREIGN KEY (`dm_location_id`) REFERENCES `tbl_gp_locations` (`location_id`),
  CONSTRAINT `tbl_gp_departments_map_ibfk_2` FOREIGN KEY (`dm_hotel_id`) REFERENCES `tbl_gp_hotels` (`hotel_id`),
  CONSTRAINT `tbl_gp_departments_map_ibfk_3` FOREIGN KEY (`dm_department_id`) REFERENCES `tbl_gp_departments` (`department_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `tbl_gp_hotels`;
CREATE TABLE `tbl_gp_hotels` (
  `hotel_id` int(11) NOT NULL AUTO_INCREMENT,
  `location_id` int(11) NOT NULL COMMENT 'FK- locations',
  `hotel_name` varchar(100) NOT NULL COMMENT 'hotel name',
  `hotel_phone_number` bigint(11) NOT NULL COMMENT 'hotel phone number',
  `hotel_address` text NOT NULL COMMENT 'hotel address',
  `hotel_status` tinyint(1) NOT NULL COMMENT 'hotel status - (1-Active , 0-Inactive)',
  `created_by` int(11) NOT NULL COMMENT 'created by user id',
  `modified_by` int(11) NOT NULL COMMENT 'modified by user id',
  `created_date` datetime NOT NULL COMMENT 'created date',
  `modified_date` datetime NOT NULL COMMENT 'modified date',
  PRIMARY KEY (`hotel_id`),
  KEY `location_id` (`location_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  CONSTRAINT `tbl_gp_hotels_ibfk_1` FOREIGN KEY (`location_id`) REFERENCES `tbl_gp_locations` (`location_id`),
  CONSTRAINT `tbl_gp_hotels_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `tbl_gp_user` (`user_id`),
  CONSTRAINT `tbl_gp_hotels_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `tbl_gp_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `tbl_gp_interval`;
CREATE TABLE `tbl_gp_interval` (
  `interval_id` int(11) NOT NULL AUTO_INCREMENT,
  `interval_name` varchar(100) NOT NULL COMMENT 'interval name (1->Day2->Week,3->Month,4->Year)',
  PRIMARY KEY (`interval_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

INSERT INTO `tbl_gp_interval` (`interval_id`, `interval_name`) VALUES
(1,	'Day'),
(2,	'Week'),
(3,	'Month'),
(4,	'Year')
ON DUPLICATE KEY UPDATE `interval_id` = VALUES(`interval_id`), `interval_name` = VALUES(`interval_name`);

DROP TABLE IF EXISTS `tbl_gp_locations`;
CREATE TABLE `tbl_gp_locations` (
  `location_id` int(11) NOT NULL AUTO_INCREMENT,
  `location_city_id` int(11) NOT NULL COMMENT 'FK - cities',
  `location_description` text NOT NULL COMMENT 'location description',
  `created_by` int(11) NOT NULL COMMENT 'Created by user id',
  `modified_by` int(11) NOT NULL COMMENT 'Modified by user id',
  `created_date` datetime NOT NULL COMMENT 'created date',
  `modified_date` datetime NOT NULL COMMENT 'modified date',
  PRIMARY KEY (`location_id`),
  KEY `location_city_id` (`location_city_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  CONSTRAINT `tbl_gp_locations_ibfk_1` FOREIGN KEY (`location_city_id`) REFERENCES `tbl_gp_cities` (`id`),
  CONSTRAINT `tbl_gp_locations_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `tbl_gp_user` (`user_id`),
  CONSTRAINT `tbl_gp_locations_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `tbl_gp_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `tbl_gp_questions`;
CREATE TABLE `tbl_gp_questions` (
  `question_id` int(11) NOT NULL AUTO_INCREMENT,
  `q_text` text NOT NULL COMMENT 'question text',
  `q_checklist_id` int(11) NOT NULL COMMENT 'FK - checklists',
  `q_section` int(11) NOT NULL COMMENT 'FK - sections',
  `q_sub_section` int(11) DEFAULT NULL COMMENT 'FK - sub_sections',
  `q_sub-section_is_dynamic` tinyint(1) DEFAULT NULL COMMENT 'sub section is dynamic (1->Yes, 0-> No)',
  `q_access_type` text COMMENT 'choosen access   types for question - json values from question_access_types',
  `q_priority_type` int(11) NOT NULL COMMENT 'FK - question_priority_types',
  `q_response_type` int(11) NOT NULL COMMENT 'FK - question_response_types',
  PRIMARY KEY (`question_id`),
  KEY `q_checklist_id` (`q_checklist_id`),
  KEY `q_section` (`q_section`),
  KEY `q_sub_section` (`q_sub_section`),
  KEY `q_priority_type` (`q_priority_type`),
  KEY `q_response_type` (`q_response_type`),
  CONSTRAINT `tbl_gp_questions_ibfk_1` FOREIGN KEY (`q_checklist_id`) REFERENCES `tbl_gp_checklists` (`checklist_id`),
  CONSTRAINT `tbl_gp_questions_ibfk_2` FOREIGN KEY (`q_section`) REFERENCES `tbl_gp_sections` (`section_id`),
  CONSTRAINT `tbl_gp_questions_ibfk_3` FOREIGN KEY (`q_sub_section`) REFERENCES `tbl_gp_sub_sections` (`sub_section_id`),
  CONSTRAINT `tbl_gp_questions_ibfk_4` FOREIGN KEY (`q_priority_type`) REFERENCES `tbl_gp_question_priority_types` (`priority_type_id`),
  CONSTRAINT `tbl_gp_questions_ibfk_5` FOREIGN KEY (`q_response_type`) REFERENCES `tbl_gp_question_response_types` (`response_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `tbl_gp_question_access_types`;
CREATE TABLE `tbl_gp_question_access_types` (
  `access_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `access_name` varchar(50) NOT NULL COMMENT 'short name of the access to give for a question',
  `display_name` varchar(50) NOT NULL COMMENT 'name to showoff in UI',
  `description` varchar(100) DEFAULT NULL COMMENT 'access desciption',
  PRIMARY KEY (`access_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

INSERT INTO `tbl_gp_question_access_types` (`access_type_id`, `access_name`, `display_name`, `description`) VALUES
(1,	'camera',	'Camera Access',	'access to camera'),
(2,	'gallery',	'Gallery Access',	'access to gallery'),
(3,	'file',	'File Browser',	'access to file gallery'),
(4,	'all',	'All Access',	'all of the above')
ON DUPLICATE KEY UPDATE `access_type_id` = VALUES(`access_type_id`), `access_name` = VALUES(`access_name`), `display_name` = VALUES(`display_name`), `description` = VALUES(`description`);

DROP TABLE IF EXISTS `tbl_gp_question_priority_types`;
CREATE TABLE `tbl_gp_question_priority_types` (
  `priority_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `priority_name` varchar(50) NOT NULL COMMENT 'question priority name',
  PRIMARY KEY (`priority_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=latin1;

INSERT INTO `tbl_gp_question_priority_types` (`priority_type_id`, `priority_name`) VALUES
(1,	'High'),
(2,	'Medium'),
(3,	'Low')
ON DUPLICATE KEY UPDATE `priority_type_id` = VALUES(`priority_type_id`), `priority_name` = VALUES(`priority_name`);

DROP TABLE IF EXISTS `tbl_gp_question_response_types`;
CREATE TABLE `tbl_gp_question_response_types` (
  `response_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `response_name` varchar(50) NOT NULL COMMENT 'question response name',
  PRIMARY KEY (`response_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;

INSERT INTO `tbl_gp_question_response_types` (`response_type_id`, `response_name`) VALUES
(1,	'True/False'),
(2,	'Yes/No'),
(3,	'Rating Scale'),
(4,	'Single Choice'),
(5,	'Multiple Choice')
ON DUPLICATE KEY UPDATE `response_type_id` = VALUES(`response_type_id`), `response_name` = VALUES(`response_name`);

DROP TABLE IF EXISTS `tbl_gp_roles`;
CREATE TABLE `tbl_gp_roles` (
  `role_id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(100) NOT NULL COMMENT 'role name',
  `role_main` varchar(100) DEFAULT NULL COMMENT 'role name similar to Roles entries in auth item',
  `role_feature_access_list` text COMMENT 'role features access list',
  `role_properties_access_list` text COMMENT 'role properties access list',
  PRIMARY KEY (`role_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `tbl_gp_sections`;
CREATE TABLE `tbl_gp_sections` (
  `section_id` int(11) NOT NULL AUTO_INCREMENT,
  `s_department_id` int(11) NOT NULL COMMENT 'FK - departments',
  `s_section_name` varchar(100) NOT NULL COMMENT 'section name',
  `s_section_remarks` varchar(200) DEFAULT NULL COMMENT 'section remarks',
  `created_by` int(11) DEFAULT NULL COMMENT 'created by user id',
  `modified_by` int(11) DEFAULT NULL COMMENT 'modified by user id',
  `created_date` datetime DEFAULT NULL COMMENT 'created date',
  `modified_date` datetime DEFAULT NULL COMMENT 'modified date',
  PRIMARY KEY (`section_id`),
  KEY `s_department_id` (`s_department_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  CONSTRAINT `tbl_gp_sections_ibfk_1` FOREIGN KEY (`s_department_id`) REFERENCES `tbl_gp_departments_map` (`department_map_id`),
  CONSTRAINT `tbl_gp_sections_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `tbl_gp_user` (`user_id`),
  CONSTRAINT `tbl_gp_sections_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `tbl_gp_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `tbl_gp_sub_sections`;
CREATE TABLE `tbl_gp_sub_sections` (
  `sub_section_id` int(11) NOT NULL AUTO_INCREMENT,
  `ss_section_id` int(11) NOT NULL COMMENT 'FK - sections',
  `ss_subsection_name` varchar(100) NOT NULL COMMENT 'subsection name',
  `ss_subsection_remarks` varchar(200) DEFAULT NULL COMMENT 'section remarks',
  `created_by` int(11) DEFAULT NULL COMMENT 'created by user id',
  `modified_by` int(11) DEFAULT NULL COMMENT 'modified by user id',
  `created_date` datetime DEFAULT NULL COMMENT 'created date',
  `modified_date` datetime DEFAULT NULL COMMENT 'modified date',
  PRIMARY KEY (`sub_section_id`),
  KEY `ss_section_id` (`ss_section_id`),
  KEY `created_by` (`created_by`),
  KEY `modified_by` (`modified_by`),
  CONSTRAINT `tbl_gp_sub_sections_ibfk_1` FOREIGN KEY (`ss_section_id`) REFERENCES `tbl_gp_sections` (`section_id`),
  CONSTRAINT `tbl_gp_sub_sections_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `tbl_gp_user` (`user_id`),
  CONSTRAINT `tbl_gp_sub_sections_ibfk_3` FOREIGN KEY (`modified_by`) REFERENCES `tbl_gp_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `tbl_gp_user`;
CREATE TABLE `tbl_gp_user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(100) NOT NULL COMMENT 'user email',
  `password_hash` varchar(200) NOT NULL COMMENT 'user password hashed',
  `auth_token` varchar(200) NOT NULL COMMENT 'authentication token for app',
  `is_email_verified` tinyint(1) DEFAULT NULL COMMENT 'email status (0->Inactive,1->Active)',
  `password_requested_date` date DEFAULT NULL COMMENT 'password request date',
  `last_login_time` datetime DEFAULT NULL COMMENT 'last login time',
  `created_date` datetime DEFAULT NULL COMMENT 'created date',
  `modified_date` datetime DEFAULT NULL COMMENT 'modified date',
  PRIMARY KEY (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `tbl_gp_user_info`;
CREATE TABLE `tbl_gp_user_info` (
  `user_info_id` int(11) NOT NULL AUTO_INCREMENT,
  `ui_first_name` varchar(100) NOT NULL COMMENT 'first name',
  `ui_last_name` varchar(100) NOT NULL COMMENT 'last name',
  `ui_phone` bigint(10) NOT NULL COMMENT 'phone number',
  `ui_is_user_active` tinyint(1) NOT NULL COMMENT 'status (0-Inactive, 1->Active)',
  `ui_user_id` int(11) NOT NULL COMMENT 'FK - user',
  `ui_user_type_id` int(11) NOT NULL COMMENT 'FK - user_types (1->admin,2->auditor,3->staff,4->other)',
  `ui_hotel_id` int(11) NOT NULL COMMENT 'FK - hotels',
  `ui_location_id` int(11) NOT NULL COMMENT 'FK - locations',
  `ui_role_id` int(11) NOT NULL COMMENT 'FK - roles',
  PRIMARY KEY (`user_info_id`),
  KEY `ui_hotel_id` (`ui_hotel_id`),
  KEY `ui_location_id` (`ui_location_id`),
  KEY `ui_role_id` (`ui_role_id`),
  KEY `ui_user_type_id` (`ui_user_type_id`),
  KEY `ui_user_id` (`ui_user_id`),
  CONSTRAINT `tbl_gp_user_info_ibfk_1` FOREIGN KEY (`ui_hotel_id`) REFERENCES `tbl_gp_hotels` (`hotel_id`),
  CONSTRAINT `tbl_gp_user_info_ibfk_2` FOREIGN KEY (`ui_location_id`) REFERENCES `tbl_gp_locations` (`location_id`),
  CONSTRAINT `tbl_gp_user_info_ibfk_3` FOREIGN KEY (`ui_role_id`) REFERENCES `tbl_gp_roles` (`role_id`),
  CONSTRAINT `tbl_gp_user_info_ibfk_4` FOREIGN KEY (`ui_user_type_id`) REFERENCES `tbl_gp_user_types` (`user_type_id`),
  CONSTRAINT `tbl_gp_user_info_ibfk_5` FOREIGN KEY (`ui_user_id`) REFERENCES `tbl_gp_user` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `tbl_gp_user_types`;
CREATE TABLE `tbl_gp_user_types` (
  `user_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `ut_name` varchar(50) NOT NULL COMMENT 'user type name (1->admin,2->auditor,3->staff,4->other)',
  `ut_description` varchar(100) DEFAULT NULL COMMENT 'user type description',
  PRIMARY KEY (`user_type_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;

INSERT INTO `tbl_gp_user_types` (`user_type_id`, `ut_name`, `ut_description`) VALUES
(1,	'admin',	'admin is the super power to the website'),
(2,	'auditor',	'auditor to the website'),
(3,	'staff',	'regular staff member to the website'),
(4,	'other',	'other user than admin, staff and auditor')
ON DUPLICATE KEY UPDATE `user_type_id` = VALUES(`user_type_id`), `ut_name` = VALUES(`ut_name`), `ut_description` = VALUES(`ut_description`);

-- 2017-11-08 15:16:27
