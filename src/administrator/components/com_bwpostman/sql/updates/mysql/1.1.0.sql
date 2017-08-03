ALTER TABLE `#__bwpostman_newsletters` ADD `access` int(11) NOT NULL AFTER `description`;
ALTER TABLE `#__bwpostman_newsletters` ADD `template_id` int(11) NOT NULL AFTER `reply_email`;
ALTER TABLE `#__bwpostman_newsletters` ADD `text_template_id` int(11) NOT NULL AFTER `template_id`;
ALTER TABLE `#__bwpostman_newsletters` ADD `intro_headline` varchar(1000) NOT NULL AFTER `attachment`;
ALTER TABLE `#__bwpostman_newsletters` ADD `intro_text` text NOT NULL AFTER `intro_headline`;
ALTER TABLE `#__bwpostman_newsletters` ADD `intro_text_headline` varchar(1000) NOT NULL AFTER `intro_text`;
ALTER TABLE `#__bwpostman_newsletters` ADD `intro_text_text` text NOT NULL AFTER `intro_text_headline`;

ALTER TABLE `#__bwpostman_subscribers` ADD `access` int(11) NOT NULL AFTER `editlink`;

CREATE TABLE IF NOT EXISTS `#__bwpostman_templates` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `asset_id` int(10) NOT NULL,
  `standard` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL,
  `description` varchar(1000) NOT NULL,
  `thumbnail` varchar(1000) NOT NULL,
  `tpl_html` text NOT NULL,
  `tpl_css` text NOT NULL,
  `tpl_article` text NOT NULL,
  `tpl_divider` text NOT NULL,
  `tpl_id` int(11) NOT NULL DEFAULT '0',
  `basics` varchar(255) NOT NULL,
  `header` varchar(1000) NOT NULL,
  `intro` text NOT NULL,
  `article` varchar(1000) NOT NULL,
  `footer` varchar(1000) NOT NULL,
  `button1` varchar(1000) NOT NULL,
  `button2` varchar(1000) NOT NULL,
  `button3` varchar(1000) NOT NULL,
  `button4` varchar(1000) NOT NULL,
  `button5` varchar(1000) NOT NULL,
  `access` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `archive_flag` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `archive_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `archived_by` varchar(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `#__bwpostman_templates_tpl` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `css` text NOT NULL,
  `header_tpl` text NOT NULL,
  `intro_tpl` text NOT NULL,
  `divider_tpl` text NOT NULL,
  `article_tpl` text NOT NULL,
  `readon_tpl` text NOT NULL,
  `footer_tpl` text NOT NULL,
  `button_tpl` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;
