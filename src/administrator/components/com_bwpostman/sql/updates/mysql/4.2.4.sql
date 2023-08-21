/* Enable NULL for created_date/registration_date */

ALTER TABLE `#__bwpostman_campaigns`
	CHANGE `created_date` `created_date` DATETIME NULL;

ALTER TABLE `#__bwpostman_mailinglists`
	CHANGE `created_date` `created_date` DATETIME NULL;

ALTER TABLE `#__bwpostman_newsletters`
	CHANGE `created_date` `created_date` DATETIME NULL;

ALTER TABLE `#__bwpostman_subscribers`
	CHANGE `registration_date` `registration_date` DATETIME NULL;

ALTER TABLE `#__bwpostman_templates`
	CHANGE `created_date` `created_date` DATETIME NULL;
