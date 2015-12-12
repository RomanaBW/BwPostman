ALTER TABLE `#__bwpostman_campaigns` ADD `asset_id` int(10) NOT NULL AFTER `id`;
ALTER TABLE `#__bwpostman_lists` ADD `asset_id` int(10) NOT NULL AFTER `id`;
ALTER TABLE `#__bwpostman_newsletters` ADD `asset_id` int(10) NOT NULL AFTER `id`;
ALTER TABLE `#__bwpostman_newsletters_tmp` ADD `asset_id` int(10) NOT NULL AFTER `nl_id`;
ALTER TABLE `#__bwpostman_sendmailcontent` ADD `asset_id` int(10) NOT NULL AFTER `id`;
ALTER TABLE `#__bwpostman_subscribers` ADD `asset_id` int(10) NOT NULL AFTER `id`;
