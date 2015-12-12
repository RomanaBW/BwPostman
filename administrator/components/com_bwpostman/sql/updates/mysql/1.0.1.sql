ALTER TABLE `#__bwpostman_lists` RENAME `#__bwpostman_mailinglists`;
ALTER TABLE `#__bwpostman_mailinglists` ADD `created_date` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `published`;
ALTER TABLE `#__bwpostman_mailinglists` ADD `created_by` int(10) unsigned NOT NULL default '0' AFTER `created_date`;
ALTER TABLE `#__bwpostman_mailinglists` ADD `modified_time` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `created_by`;
ALTER TABLE `#__bwpostman_mailinglists` ADD `modified_by` int(10) unsigned NOT NULL default '0' AFTER `modified_time`;
ALTER TABLE `#__bwpostman_mailinglists` ADD `archived_by`  varchar(11) NOT NULL default '0' AFTER `archive_date`;

ALTER TABLE `#__bwpostman_campaigns` ADD `access` int(11) NOT NULL default '0' AFTER `description`;
ALTER TABLE `#__bwpostman_campaigns` ADD `published` tinyint(1) NOT NULL default '0' AFTER `access`;
ALTER TABLE `#__bwpostman_campaigns` ADD `created_date` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `published`;
ALTER TABLE `#__bwpostman_campaigns` ADD `created_by` int(10) unsigned NOT NULL default '0' AFTER `created_date`;
ALTER TABLE `#__bwpostman_campaigns` ADD `modified_time` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `created_by`;
ALTER TABLE `#__bwpostman_campaigns` ADD `modified_by` int(10) unsigned NOT NULL default '0' AFTER `modified_time`;
ALTER TABLE `#__bwpostman_campaigns` ADD `archived_by`  varchar(11) NOT NULL default '0' AFTER `archive_date`;

ALTER TABLE `#__bwpostman_subscribers` ADD `asset_id` int(10) unsigned NOT NULL default '0' AFTER `id`;
ALTER TABLE `#__bwpostman_subscribers` ADD `modified_time` datetime NOT NULL default '0000-00-00 00:00:00' AFTER `confirmation_ip`;
ALTER TABLE `#__bwpostman_subscribers` ADD `modified_by` int(10) unsigned NOT NULL default '0' AFTER `modified_time`;


ALTER TABLE `#__bwpostman_newsletters` CHANGE `creation_date` `created_date` datetime NOT NULL default '0000-00-00 00:00:00';
ALTER TABLE `#__bwpostman_newsletters` MODIFY `author` int(11) NOT NULL default '0' AFTER `created_date`;
ALTER TABLE `#__bwpostman_newsletters` CHANGE `author` `created_by` int(11) NOT NULL default '0';
ALTER TABLE `#__bwpostman_newsletters` CHANGE `last_modification` `modified_time` datetime NOT NULL default '0000-00-00 00:00:00';
ALTER TABLE `#__bwpostman_newsletters` ADD `usergroups` varchar(255) NOT NULL default '' AFTER `campaign_id`;
ALTER TABLE `#__bwpostman_newsletters` ADD `modified_by` int(10) unsigned NOT NULL default '0' AFTER `modified_time`;
ALTER TABLE `#__bwpostman_newsletters` ADD `archived_by`  varchar(11) NOT NULL default '0' AFTER `archive_date`;

ALTER TABLE `#__bwpostman_newsletters_lists` RENAME `#__bwpostman_newsletters_mailinglists`;
ALTER TABLE `#__bwpostman_newsletters_mailinglists` CHANGE `list_id` `mailinglist_id`  int(11) NOT NULL;

ALTER TABLE `#__bwpostman_subscribers_lists` RENAME `#__bwpostman_subscribers_mailinglists`;
ALTER TABLE `#__bwpostman_subscribers_mailinglists` CHANGE `list_id` `mailinglist_id`  int(11) NOT NULL;

ALTER TABLE `#__bwpostman_mailinglists` ENGINE=INNODB DEFAULT CHARSET=utf8;
ALTER TABLE `#__bwpostman_campaigns` ENGINE=INNODB DEFAULT CHARSET=utf8;
ALTER TABLE `#__bwpostman_newsletters` ENGINE=INNODB DEFAULT CHARSET=utf8;
ALTER TABLE `#__bwpostman_newsletters_mailinglists` ENGINE=INNODB DEFAULT CHARSET=utf8;
ALTER TABLE `#__bwpostman_sendmailcontent` ENGINE=INNODB DEFAULT CHARSET=utf8;
ALTER TABLE `#__bwpostman_sendmailqueue` ENGINE=INNODB DEFAULT CHARSET=utf8;
ALTER TABLE `#__bwpostman_subscribers` ENGINE=INNODB DEFAULT CHARSET=utf8;
ALTER TABLE `#__bwpostman_subscribers_mailinglists` ENGINE=INNODB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `#__bwpostman_newsletters_tmp`;
