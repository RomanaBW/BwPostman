/* Remove NOT NULL from DATETIME columns which may hold NULL */

ALTER TABLE `#__bwpostman_campaigns`
	CHANGE `created_date` `created_date` DATETIME NOT NULL,
	CHANGE `archive_date` `archive_date` DATETIME NULL,
	CHANGE `modified_time` `modified_time` DATETIME NULL,
	CHANGE `checked_out_time` `checked_out_time` DATETIME NULL;

ALTER TABLE `#__bwpostman_mailinglists`
	CHANGE `created_date` `created_date` DATETIME NOT NULL,
	CHANGE `archive_date` `archive_date` DATETIME NULL,
	CHANGE `modified_time` `modified_time` DATETIME NULL,
	CHANGE `checked_out_time` `checked_out_time` DATETIME NULL;

ALTER TABLE `#__bwpostman_newsletters`
	CHANGE `created_date` `created_date` DATETIME NOT NULL,
	CHANGE `archive_date` `archive_date` DATETIME NULL,
	CHANGE `mailing_date` `mailing_date` DATETIME NULL,
	CHANGE `publish_up` `publish_up` DATETIME NULL,
	CHANGE `publish_down` `publish_down` DATETIME NULL,
	CHANGE `modified_time` `modified_time` DATETIME NULL,
	CHANGE `checked_out_time` `checked_out_time` DATETIME NULL;

ALTER TABLE `#__bwpostman_subscribers`
	CHANGE `registration_date` `registration_date` DATETIME NOT NULL,
	CHANGE `archive_date` `archive_date` DATETIME NULL,
	CHANGE `confirmation_date` `confirmation_date` DATETIME NULL,
	CHANGE `modified_time` `modified_time` DATETIME NULL,
	CHANGE `checked_out_time` `checked_out_time` DATETIME NULL;

ALTER TABLE `#__bwpostman_templates`
	CHANGE `created_date` `created_date` DATETIME NOT NULL,
	CHANGE `archive_date` `archive_date` DATETIME NULL,
	CHANGE `modified_time` `modified_time` DATETIME NULL,
	CHANGE `checked_out_time` `checked_out_time` DATETIME NULL;
