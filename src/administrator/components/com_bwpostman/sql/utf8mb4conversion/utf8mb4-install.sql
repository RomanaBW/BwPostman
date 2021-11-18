--
-- Table structure for table `#__bwpostman_campaigns`
--

DROP TABLE IF EXISTS `#__bwpostman_campaigns`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_campaigns` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `asset_id` INT(10) NOT NULL DEFAULT '0',
  `title` VARCHAR(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` VARCHAR(2000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `access` INT(11) NOT NULL DEFAULT '0',
  `published` TINYINT(1) NOT NULL DEFAULT '0',
  `created_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(10) UNSIGNED NOT NULL DEFAULT '0',
  `modified_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(10) unsigned NOT NULL DEFAULT '0',
  `checked_out` INT(11) NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `archive_flag` TINYINT(1) unsigned NOT NULL DEFAULT '0',
  `archive_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `archived_by` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_mailinglists`
--

DROP TABLE IF EXISTS `#__bwpostman_mailinglists`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_mailinglists` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `asset_id` INT(10) NOT NULL DEFAULT '0',
  `title` VARCHAR(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` VARCHAR(2000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `campaign_id` INT(11) NOT NULL DEFAULT '0',
  `access` INT(11) NOT NULL DEFAULT '0',
  `published` TINYINT(1) NOT NULL DEFAULT '0',
  `created_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(10) unsigned NOT NULL DEFAULT '0',
  `checked_out` INT(11) NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `archive_flag` TINYINT(1) unsigned NOT NULL DEFAULT '0',
  `archive_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `archived_by` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_newsletters`
--

DROP TABLE IF EXISTS `#__bwpostman_newsletters`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_newsletters` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `asset_id` INT(10) NOT NULL DEFAULT '0',
  `from_name` VARCHAR(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `from_email` VARCHAR(320) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `reply_email` VARCHAR(320) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `template_id` INT(11) NOT NULL DEFAULT '0',
  `text_template_id` INT(11) NOT NULL DEFAULT '0',
  `campaign_id` INT(11) NOT NULL DEFAULT '0',
  `usergroups` VARCHAR(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `selected_content` VARCHAR(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `subject` VARCHAR(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` VARCHAR(2000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `access` INT(11) NOT NULL DEFAULT '0',
  `attachment` VARCHAR(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `intro_headline` VARCHAR(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `intro_text` TEXT COLLATE utf8mb4_unicode_ci ,
  `intro_text_headline` VARCHAR(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `intro_text_text` TEXT COLLATE utf8mb4_unicode_ci ,
  `html_version` LONGTEXT COLLATE utf8mb4_unicode_ci ,
  `text_version` LONGTEXT COLLATE utf8mb4_unicode_ci ,
  `is_template` TINYINT(1) unsigned NOT NULL DEFAULT '0',
  `created_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(10) unsigned NOT NULL DEFAULT '0',
  `mailing_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` TINYINT(1) unsigned NOT NULL DEFAULT '0',
  `publish_up` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `checked_out` INT(11) NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `archive_flag` TINYINT(1) unsigned NOT NULL DEFAULT '0',
  `archive_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `archived_by` INT(11) NOT NULL DEFAULT '0',
  `hits` INT(11) NOT NULL DEFAULT '0',
  `substitute_links` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_newsletters_mailinglists`
--

DROP TABLE IF EXISTS `#__bwpostman_newsletters_mailinglists`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_newsletters_mailinglists` (
  `newsletter_id` INT(11) NOT NULL,
  `mailinglist_id` INT(11) NOT NULL,
  PRIMARY KEY (`newsletter_id`,`mailinglist_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_sendmailcontent`
--

DROP TABLE IF EXISTS `#__bwpostman_sendmailcontent`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_sendmailcontent` (
  `id` INT(11) NOT NULL,
  `mode` INT(1) NOT NULL,
  `nl_id` INT(11) NOT NULL DEFAULT '0',
  `from_name` VARCHAR(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `from_email` VARCHAR(320) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `subject` VARCHAR(1000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `body` LONGTEXT COLLATE utf8mb4_unicode_ci ,
  `cc_email` VARCHAR(320) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `bcc_email` VARCHAR(320) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `attachment` TEXT COLLATE utf8mb4_unicode_ci ,
  `reply_email` VARCHAR(320) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `reply_name` VARCHAR(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `substitute_links` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`,`mode`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_sendmailqueue`
--

DROP TABLE IF EXISTS `#__bwpostman_sendmailqueue`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_sendmailqueue` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `content_id` INT(11) NOT NULL DEFAULT '0',
  `recipient` VARCHAR(320) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `mode` INT(1) NOT NULL DEFAULT '0',
  `name` VARCHAR(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `firstname` VARCHAR(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `subscriber_id` INT(11) NOT NULL DEFAULT '0',
  `trial` INT(5) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_subscribers`
--

DROP TABLE IF EXISTS `#__bwpostman_subscribers`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_subscribers` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `asset_id` INT(10) NOT NULL DEFAULT '0',
  `user_id` INT(11) NOT NULL DEFAULT '0',
  `name` VARCHAR(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `firstname` VARCHAR(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` VARCHAR(320) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `emailformat` TINYINT(1) NOT NULL DEFAULT '1',
  `gender` TINYINT(1) unsigned NULL,
  `special` VARCHAR(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `status` INT(1) NOT NULL DEFAULT '0',
  `activation` VARCHAR(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `editlink` VARCHAR(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `access` INT(11) NOT NULL DEFAULT '0',
  `registration_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `registered_by` INT(11) NOT NULL DEFAULT '0',
  `registration_ip` VARCHAR(156) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `confirmation_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `confirmed_by` INT(11) NOT NULL DEFAULT '0',
  `confirmation_ip` VARCHAR(156) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `modified_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(10) unsigned NOT NULL DEFAULT '0',
  `checked_out` INT(11) NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `archive_flag` TINYINT(1) NOT NULL DEFAULT '0',
  `archive_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `archived_by` INT(11) NOT NULL DEFAULT '-1',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_subscribers_mailinglists`
--

DROP TABLE IF EXISTS `#__bwpostman_subscribers_mailinglists`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_subscribers_mailinglists` (
  `subscriber_id` INT(11) NOT NULL,
  `mailinglist_id` INT(11) NOT NULL,
  PRIMARY KEY (`subscriber_id`,`mailinglist_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_templates`
--

DROP TABLE IF EXISTS `#__bwpostman_templates`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_templates` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `asset_id` INT(10) NOT NULL DEFAULT '0',
  `standard` TINYINT(1) NOT NULL DEFAULT '0',
  `title` VARCHAR(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `description` VARCHAR(2000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `thumbnail` VARCHAR(2000) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `tpl_html` TEXT COLLATE utf8mb4_unicode_ci,
  `tpl_css` TEXT COLLATE utf8mb4_unicode_ci,
  `tpl_article` TEXT COLLATE utf8mb4_unicode_ci,
  `tpl_divider` TEXT COLLATE utf8mb4_unicode_ci,
  `tpl_id` INT(11) NOT NULL DEFAULT '0',
  `basics` VARCHAR(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' DEFAULT '',
  `header` VARCHAR(1600) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' DEFAULT '',
  `intro` TEXT COLLATE utf8mb4_unicode_ci,
  `article` TEXT COLLATE utf8mb4_unicode_ci,
  `footer` VARCHAR(1600) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `button1` VARCHAR(1600) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `button2` VARCHAR(1600) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `button3` VARCHAR(1600) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `button4` VARCHAR(1600) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `button5` VARCHAR(1600) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `access` INT(11) NOT NULL DEFAULT '0',
  `published` TINYINT(1) NOT NULL DEFAULT '0',
  `created_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` INT(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` INT(10) unsigned NOT NULL DEFAULT '0',
  `checked_out` INT(11) NOT NULL DEFAULT '0',
  `checked_out_time` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `archive_flag` TINYINT(1) unsigned NOT NULL DEFAULT '0',
  `archive_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `archived_by` INT(11) NOT NULL DEFAULT '0',
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_templates`
--

DROP TABLE IF EXISTS `#__bwpostman_templates_tpl`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_templates_tpl` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `css` TEXT COLLATE utf8mb4_unicode_ci ,
  `header_tpl` TEXT COLLATE utf8mb4_unicode_ci ,
  `intro_tpl` TEXT COLLATE utf8mb4_unicode_ci ,
  `divider_tpl` TEXT COLLATE utf8mb4_unicode_ci ,
  `article_tpl` TEXT COLLATE utf8mb4_unicode_ci ,
  `readon_tpl` TEXT COLLATE utf8mb4_unicode_ci ,
  `footer_tpl` TEXT COLLATE utf8mb4_unicode_ci ,
  `button_tpl` TEXT COLLATE utf8mb4_unicode_ci ,
  PRIMARY KEY  (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_templates_tags`
--

DROP TABLE IF EXISTS `#__bwpostman_templates_tags`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_templates_tags` (
  `templates_table_id` INT(11) NOT NULL,
  `tpl_tags_head` TINYINT(1) NOT NULL,
  `tpl_tags_head_advanced` TEXT COLLATE utf8mb4_unicode_ci,
  `tpl_tags_body` TINYINT(1) NOT NULL,
  `tpl_tags_body_advanced` TEXT COLLATE utf8mb4_unicode_ci,
  `tpl_tags_article` TINYINT(1) NOT NULL,
  `tpl_tags_article_advanced_b` TEXT COLLATE utf8mb4_unicode_ci,
  `tpl_tags_article_advanced_e` TEXT COLLATE utf8mb4_unicode_ci,
  `tpl_tags_readon` TINYINT(1) NOT NULL,
  `tpl_tags_readon_advanced` TEXT COLLATE utf8mb4_unicode_ci,
  `tpl_tags_legal` TINYINT(1) NOT NULL,
  `tpl_tags_legal_advanced_b` TEXT COLLATE utf8mb4_unicode_ci,
  `tpl_tags_legal_advanced_e` TEXT COLLATE utf8mb4_unicode_ci,
  PRIMARY KEY (`templates_table_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_campaigns_mailinglists`
--

DROP TABLE IF EXISTS `#__bwpostman_campaigns_mailinglists`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_campaigns_mailinglists` (
  `campaign_id` INT(11) NOT NULL,
  `mailinglist_id` INT(11) NOT NULL,
  PRIMARY KEY (`campaign_id`,`mailinglist_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

