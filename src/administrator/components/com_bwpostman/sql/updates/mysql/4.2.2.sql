/* Remove NOT NULL from DATETIME columns which may hold NULL */

ALTER TABLE `#__bwpostman_campaigns` MODIFY `modified_time` DATETIME;
ALTER TABLE `#__bwpostman_campaigns` MODIFY `checked_out_time` DATETIME;
ALTER TABLE `#__bwpostman_campaigns` MODIFY `archive_date` DATETIME;

ALTER TABLE `#__bwpostman_mailinglists` MODIFY `modified_time` DATETIME;
ALTER TABLE `#__bwpostman_mailinglists` MODIFY `checked_out_time` DATETIME;
ALTER TABLE `#__bwpostman_mailinglists` MODIFY `archive_date` DATETIME;

ALTER TABLE `#__bwpostman_newsletters` MODIFY `modified_time` DATETIME;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `mailing_date` DATETIME;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `publish_up` DATETIME;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `publish_down` DATETIME;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `checked_out_time` DATETIME;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `archive_date` DATETIME;

ALTER TABLE `#__bwpostman_subscribers` MODIFY `confirmation_date` DATETIME;
ALTER TABLE `#__bwpostman_subscribers` MODIFY `modified_time` DATETIME;
ALTER TABLE `#__bwpostman_subscribers` MODIFY `checked_out_time` DATETIME;
ALTER TABLE `#__bwpostman_subscribers` MODIFY `archive_date` DATETIME;

ALTER TABLE `#__bwpostman_templates` MODIFY `modified_time` DATETIME;
ALTER TABLE `#__bwpostman_templates` MODIFY `checked_out_time` DATETIME;
ALTER TABLE `#__bwpostman_templates` MODIFY `archive_date` DATETIME;
