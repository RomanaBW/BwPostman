/* Change column basics at table #__bwpostman_templates from varchar to text */

ALTER TABLE `#__bwpostman_templates` MODIFY `basics` TEXT NOT NULL;

/* Remove default value from DATETIME columns */

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
