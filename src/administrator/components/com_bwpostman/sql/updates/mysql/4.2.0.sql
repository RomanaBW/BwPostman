/* Change column basics at table #__bwpostman_templates from varchar to text */

ALTER TABLE `#__bwpostman_templates` MODIFY `basics` TEXT NOT NULL;

/* Remove default value from DATETIME columns */

ALTER TABLE `#__bwpostman_campaigns` MODIFY `created_date` DATETIME NOT NULL;
ALTER TABLE `#__bwpostman_campaigns` MODIFY `modified_time` DATETIME NOT NULL;
ALTER TABLE `#__bwpostman_campaigns` MODIFY `checked_out_time` DATETIME NOT NULL;
ALTER TABLE `#__bwpostman_campaigns` MODIFY `archive_date` DATETIME NOT NULL;

ALTER TABLE `#__bwpostman_mailinglists` MODIFY `created_date` DATETIME NOT NULL;
ALTER TABLE `#__bwpostman_mailinglists` MODIFY `modified_time` DATETIME NOT NULL;
ALTER TABLE `#__bwpostman_mailinglists` MODIFY `checked_out_time` DATETIME NOT NULL;
ALTER TABLE `#__bwpostman_mailinglists` MODIFY `archive_date` DATETIME NOT NULL;

ALTER TABLE `#__bwpostman_newsletters` MODIFY `created_date` DATETIME NOT NULL;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `modified_time` DATETIME NOT NULL;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `mailing_date` DATETIME NOT NULL;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `publish_up` DATETIME NOT NULL;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `publish_down` DATETIME NOT NULL;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `checked_out_time` DATETIME NOT NULL;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `archive_date` DATETIME NOT NULL;

ALTER TABLE `#__bwpostman_subscribers` MODIFY `registration_date` DATETIME NOT NULL;
ALTER TABLE `#__bwpostman_subscribers` MODIFY `confirmation_date` DATETIME NOT NULL;
ALTER TABLE `#__bwpostman_subscribers` MODIFY `modified_time` DATETIME NOT NULL;
ALTER TABLE `#__bwpostman_subscribers` MODIFY `checked_out_time` DATETIME NOT NULL;
ALTER TABLE `#__bwpostman_subscribers` MODIFY `archive_date` DATETIME NOT NULL;

ALTER TABLE `#__bwpostman_templates` MODIFY `created_date` DATETIME NOT NULL;
ALTER TABLE `#__bwpostman_templates` MODIFY `modified_time` DATETIME NOT NULL;
ALTER TABLE `#__bwpostman_templates` MODIFY `checked_out_time` DATETIME NOT NULL;
ALTER TABLE `#__bwpostman_templates` MODIFY `archive_date` DATETIME NOT NULL;
