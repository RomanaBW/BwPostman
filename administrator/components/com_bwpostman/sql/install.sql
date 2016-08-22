--
-- Table structure for table `#__bwpostman_campaigns`
--

DROP TABLE IF EXISTS `#__bwpostman_campaigns`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_campaigns` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `asset_id` INT(10) NOT NULL DEFAULT '0',
  `title` VARCHAR(400) NOT NULL DEFAULT '',
  `description` VARCHAR(2000) NOT NULL DEFAULT '',
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
  `title` VARCHAR(400) NOT NULL DEFAULT '',
  `description` VARCHAR(2000) NOT NULL DEFAULT '',
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
  `from_name` VARCHAR(400) NOT NULL DEFAULT '',
  `from_email` VARCHAR(320) NOT NULL DEFAULT '',
  `reply_email` VARCHAR(320) NOT NULL DEFAULT '',
  `template_id` INT(11) NOT NULL DEFAULT '0',
  `text_template_id` INT(11) NOT NULL DEFAULT '0',
  `campaign_id` INT(11) NOT NULL DEFAULT '0',
  `usergroups` VARCHAR(400) NOT NULL DEFAULT '',
  `selected_content` VARCHAR(400) NOT NULL DEFAULT '',
  `subject` VARCHAR(1000) NOT NULL DEFAULT '',
  `description` VARCHAR(2000) NOT NULL DEFAULT '',
  `access` INT(11) NOT NULL DEFAULT '0',
  `attachment` VARCHAR(1000) NOT NULL DEFAULT '',
  `intro_headline` VARCHAR(1000) NOT NULL DEFAULT '',
  `intro_text` TEXT NOT NULL DEFAULT '',
  `intro_text_headline` VARCHAR(1000) NOT NULL DEFAULT '',
  `intro_text_text` TEXT NOT NULL DEFAULT '',
  `html_version` LONGTEXT NOT NULL DEFAULT '',
  `text_version` LONGTEXT NOT NULL DEFAULT '',
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
  `from_name` VARCHAR(400) NOT NULL DEFAULT '',
  `from_email` VARCHAR(320) NOT NULL DEFAULT '',
  `subject` VARCHAR(1000) NOT NULL DEFAULT '',
  `body` LONGTEXT NOT NULL DEFAULT '',
  `cc_email` VARCHAR(320) NOT NULL DEFAULT '',
  `bcc_email` VARCHAR(320) NOT NULL DEFAULT '',
  `attachment` TEXT NOT NULL DEFAULT '',
  `reply_email` VARCHAR(320) NOT NULL DEFAULT '',
  `reply_name` VARCHAR(400) NOT NULL DEFAULT '',
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
  `recipient` VARCHAR(320) NOT NULL DEFAULT '',
  `mode` INT(1) NOT NULL DEFAULT '0',
  `name` VARCHAR(400) NOT NULL DEFAULT '',
  `firstname` VARCHAR(400) NOT NULL DEFAULT '',
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
  `name` VARCHAR(400) NOT NULL DEFAULT '',
  `firstname` VARCHAR(400) NOT NULL DEFAULT '',
  `email` VARCHAR(320) NOT NULL DEFAULT '',
  `emailformat` TINYINT(1) NOT NULL DEFAULT '1',
  `gender` TINYINT(1) unsigned NULL,
  `special` VARCHAR(400) NOT NULL DEFAULT '',
  `status` INT(1) NOT NULL DEFAULT '0',
  `activation` VARCHAR(400) NOT NULL DEFAULT '',
  `editlink` VARCHAR(400) NOT NULL DEFAULT '',
  `access` INT(11) NOT NULL DEFAULT '0',
  `registration_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `registered_by` INT(11) NOT NULL DEFAULT '0',
  `registration_ip` VARCHAR(156) NOT NULL DEFAULT '',
  `confirmation_date` DATETIME NOT NULL DEFAULT '0000-00-00 00:00:00',
  `confirmed_by` INT(11) NOT NULL DEFAULT '0',
  `confirmation_ip` VARCHAR(156) NOT NULL DEFAULT '',
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
  `title` VARCHAR(400) NOT NULL DEFAULT '',
  `description` VARCHAR(2000) NOT NULL DEFAULT '',
  `thumbnail` VARCHAR(2000) NOT NULL DEFAULT '',
  `tpl_html` TEXT NOT NULL DEFAULT '',
  `tpl_css` TEXT NOT NULL DEFAULT '',
  `tpl_article` TEXT NOT NULL DEFAULT '',
  `tpl_divider` TEXT NOT NULL DEFAULT '',
  `tpl_id` INT(11) NOT NULL DEFAULT '0',
  `basics` VARCHAR(400) NOT NULL DEFAULT '',
  `header` VARCHAR(1600) NOT NULL DEFAULT '',
  `intro` TEXT NOT NULL DEFAULT '',
  `article` TEXT NOT NULL DEFAULT '',
  `footer` VARCHAR(1600) NOT NULL DEFAULT '',
  `button1` VARCHAR(1600) NOT NULL DEFAULT '',
  `button2` VARCHAR(1600) NOT NULL DEFAULT '',
  `button3` VARCHAR(1600) NOT NULL DEFAULT '',
  `button4` VARCHAR(1600) NOT NULL DEFAULT '',
  `button5` VARCHAR(1600) NOT NULL DEFAULT '',
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
  `title` VARCHAR(400) NOT NULL DEFAULT '',
  `css` TEXT NOT NULL DEFAULT '',
  `header_tpl` TEXT NOT NULL DEFAULT '',
  `intro_tpl` TEXT NOT NULL DEFAULT '',
  `divider_tpl` TEXT NOT NULL DEFAULT '',
  `article_tpl` TEXT NOT NULL DEFAULT '',
  `readon_tpl` TEXT NOT NULL DEFAULT '',
  `footer_tpl` TEXT NOT NULL DEFAULT '',
  `button_tpl` TEXT NOT NULL DEFAULT '',
  PRIMARY KEY  (`id`)
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

