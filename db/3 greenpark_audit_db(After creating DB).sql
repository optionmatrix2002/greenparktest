
INSERT INTO `tbl_gp_user` (`user_id`, `email`, `password_hash`, `auth_token`, `is_email_verified`, `password_requested_date`, `last_login_time`, `created_date`, `modified_date`) VALUES
(1,	'admin@greenpark.com',	'$2y$13$YSvfOLaMGGtIDW5rFiyrKuD9WGHYKXdy5XtBRSFmi4nGQmPa1HM7W',	'$2y$13$YSvfOLaMGGtIDW5rFiyrKuD9WGHYKXdy5XtBRSFmi4nGQmPa1HM7W',	1,	NULL,	NULL,	'2017-11-06 11:29:59',	NULL)
ON DUPLICATE KEY UPDATE `user_id` = VALUES(`user_id`), `email` = VALUES(`email`), `password_hash` = VALUES(`password_hash`), `auth_token` = VALUES(`auth_token`), `is_email_verified` = VALUES(`is_email_verified`), `password_requested_date` = VALUES(`password_requested_date`), `last_login_time` = VALUES(`last_login_time`), `created_date` = VALUES(`created_date`), `modified_date` = VALUES(`modified_date`);
-- saritha 10-Nov-2017
ALTER TABLE `tbl_gp_locations`
CHANGE `created_by` `created_by` int(11) NULL COMMENT 'Created by user id' AFTER `location_description`,
CHANGE `modified_by` `modified_by` int(11) NULL COMMENT 'Modified by user id' AFTER `created_by`,
CHANGE `created_date` `created_date` datetime NULL COMMENT 'created date' AFTER `modified_by`,
CHANGE `modified_date` `modified_date` datetime NULL COMMENT 'modified date' AFTER `created_date`;

ALTER TABLE `tbl_gp_hotels`
CHANGE `created_by` `created_by` int(11) NULL COMMENT 'created by user id' AFTER `hotel_status`,
CHANGE `modified_by` `modified_by` int(11) NULL COMMENT 'modified by user id' AFTER `created_by`,
CHANGE `created_date` `created_date` datetime NULL COMMENT 'created date' AFTER `modified_by`,
CHANGE `modified_date` `modified_date` datetime NULL COMMENT 'modified date' AFTER `created_date`;

ALTER TABLE `tbl_gp_departments`
CHANGE `created_by` `created_by` int(11) NULL COMMENT 'created by user id' AFTER `department_name`,
CHANGE `modified_by` `modified_by` int(11) NULL COMMENT 'modified by user id' AFTER `created_by`,
CHANGE `created_date` `created_date` datetime NULL COMMENT 'created date' AFTER `modified_by`,
CHANGE `modified_date` `modified_date` datetime NULL COMMENT 'modified date' AFTER `created_date`;

ALTER TABLE `tbl_gp_hotels`
DROP FOREIGN KEY `tbl_gp_hotels_ibfk_1`,
ADD FOREIGN KEY (`location_id`) REFERENCES `tbl_gp_locations` (`location_city_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

-- Removing  tbl_gp_departments_map starts 
ALTER TABLE `tbl_gp_departments`
ADD `department_hotel_id` int(11) NOT NULL COMMENT 'FK Hotels' AFTER `department_id`,
ADD FOREIGN KEY (`department_hotel_id`) REFERENCES `tbl_gp_hotels` (`hotel_id`);

ALTER TABLE `tbl_gp_departments_map`
DROP FOREIGN KEY `tbl_gp_departments_map_ibfk_1`;
ALTER TABLE `tbl_gp_departments_map`
DROP FOREIGN KEY `tbl_gp_departments_map_ibfk_2`;
ALTER TABLE `tbl_gp_departments_map`
DROP FOREIGN KEY `tbl_gp_departments_map_ibfk_3`;
ALTER TABLE `tbl_gp_departments_map`
DROP `dm_location_id`,
DROP `dm_hotel_id`,
DROP `dm_department_id`;
ALTER TABLE `tbl_gp_sections`
DROP FOREIGN KEY `tbl_gp_sections_ibfk_1`;
DROP TABLE `tbl_gp_departments_map`;
-- Removing  tbl_gp_departments_map ends

ALTER TABLE `tbl_gp_locations`
ADD `location_state_id` int(11) NULL COMMENT 'FK- states' AFTER `location_city_id`;
ALTER TABLE `tbl_gp_locations`
ADD FOREIGN KEY (`location_state_id`) REFERENCES `tbl_gp_states` (`id`);

ALTER TABLE `tbl_gp_hotels`
DROP FOREIGN KEY `tbl_gp_hotels_ibfk_4`,
ADD FOREIGN KEY (`location_id`) REFERENCES `tbl_gp_locations` (`location_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `tbl_gp_locations`
CHANGE `location_description` `location_description` text COLLATE 'latin1_swedish_ci' NULL COMMENT 'location description' AFTER `location_state_id`;

ALTER TABLE `tbl_gp_departments`
ADD `department_description` text COLLATE 'latin1_swedish_ci' NOT NULL COMMENT 'department description' AFTER `department_name`;
