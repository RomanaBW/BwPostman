ALTER TABLE `#__bwpostman_newsletters_tmp` ADD `attachment` varchar(1000) AFTER `subject`;
ALTER TABLE `#__bwpostman_newsletters` ADD `attachment` varchar(1000) AFTER `subject`;
ALTER TABLE `#__bwpostman_sendmailcontent` DROP COLUMN `asset_id`;
ALTER TABLE `#__bwpostman_subscribers` DROP COLUMN `asset_id`;
ALTER TABLE `#__bwpostman_subscribers` ADD `confirmation_ip` varchar(15) NOT NULL AFTER `confirmed_by`;
ALTER TABLE `#__bwpostman_lists` ADD `campaign_id` int(11) NOT NULL AFTER `description`;
