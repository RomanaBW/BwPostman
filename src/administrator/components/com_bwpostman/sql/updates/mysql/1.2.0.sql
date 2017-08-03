ALTER TABLE `#__bwpostman_newsletters` ADD `publish_up` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `published`;
ALTER TABLE `#__bwpostman_newsletters` ADD `publish_down` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `publish_up`;

CREATE TABLE IF NOT EXISTS `#__bwpostman_campaigns_mailinglists` (
 `campaign_id` int(11) NOT NULL,
 `mailinglist_id` int(11) NOT NULL,
 PRIMARY KEY (`campaign_id`,`mailinglist_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
