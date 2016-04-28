--
-- Table structure for table `#__bwpostman_campaigns`
--

DROP TABLE IF EXISTS `#__bwpostman_campaigns`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_campaigns` (
 `id` int(11) NOT NULL auto_increment,
 `asset_id` int(10) NOT NULL,
 `title` varchar(255) NOT NULL,
 `description` text NOT NULL,
 `access` int(11) NOT NULL,
 `published` tinyint(1) NOT NULL default '0',
 `created_date` datetime NOT NULL default '0000-00-00 00:00:00',
 `created_by` int(10) unsigned NOT NULL default '0',
 `modified_time` datetime NOT NULL default '0000-00-00 00:00:00',
 `modified_by` int(10) unsigned NOT NULL default '0',
 `checked_out` int(11) NOT NULL default '0',
 `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
 `archive_flag` tinyint(1) unsigned NOT NULL default '0',
 `archive_date` datetime NOT NULL default '0000-00-00 00:00:00',
 `archived_by` varchar(11) NOT NULL default '0',
 PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_mailinglists`
--

DROP TABLE IF EXISTS `#__bwpostman_mailinglists`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_mailinglists` (
 `id` int(11) NOT NULL auto_increment,
 `asset_id` int(10) NOT NULL,
 `title` varchar(255) NOT NULL,
 `description` text NOT NULL,
 `campaign_id` int(11) NOT NULL,
 `access` int(11) NOT NULL,
 `published` tinyint(1) NOT NULL default '0',
 `created_date` datetime NOT NULL default '0000-00-00 00:00:00',
 `created_by` int(10) unsigned NOT NULL default '0',
 `modified_time` datetime NOT NULL default '0000-00-00 00:00:00',
 `modified_by` int(10) unsigned NOT NULL default '0',
 `checked_out` int(11) NOT NULL default '0',
 `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
 `archive_flag` tinyint(1) unsigned NOT NULL default '0',
 `archive_date` datetime NOT NULL,
 `archived_by` varchar(11) NOT NULL default '0',
 PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_newsletters`
--

DROP TABLE IF EXISTS `#__bwpostman_newsletters`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_newsletters` (
 `id` int(11) NOT NULL auto_increment,
 `asset_id` int(10) NOT NULL,
 `from_name` varchar(255) NOT NULL,
 `from_email` varchar(100) NOT NULL,
 `reply_email` varchar(100) NOT NULL,
 `template_id` int(11) NOT NULL,
 `text_template_id` int(11) NOT NULL,
 `campaign_id` int(11) NOT NULL,
 `usergroups` varchar(255) NOT NULL,
 `selected_content` varchar(255) NOT NULL,
 `subject` varchar(255) NOT NULL,
 `description` text NOT NULL,
 `access` int(11) NOT NULL,
 `attachment` varchar(1000) NOT NULL,
 `intro_headline` varchar(1000) NOT NULL,
 `intro_text` text NOT NULL,
 `intro_text_headline` varchar(1000) NOT NULL,
 `intro_text_text` text NOT NULL,
 `html_version` longtext NOT NULL,
 `text_version` longtext NOT NULL,
 `created_date` datetime NOT NULL default '0000-00-00 00:00:00',
 `created_by` int(10) unsigned NOT NULL default '0',
 `modified_time` datetime NOT NULL default '0000-00-00 00:00:00',
 `modified_by` int(10) unsigned NOT NULL default '0',
 `mailing_date` datetime NOT NULL default '0000-00-00 00:00:00',
 `published` tinyint(1) unsigned NOT NULL default '0',
 `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
 `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
 `checked_out` int(11) NOT NULL default '0',
 `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
 `archive_flag` tinyint(1) unsigned NOT NULL default '0',
 `archive_date` datetime NOT NULL default '0000-00-00 00:00:00',
 `archived_by` varchar(11) NOT NULL default '0',
 `hits` int(11) NOT NULL,
 PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_newsletters_mailinglists`
--

DROP TABLE IF EXISTS `#__bwpostman_newsletters_mailinglists`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_newsletters_mailinglists` (
 `newsletter_id` int(11) NOT NULL,
 `mailinglist_id` int(11) NOT NULL,
 PRIMARY KEY (`newsletter_id`,`mailinglist_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_sendmailcontent`
--

DROP TABLE IF EXISTS `#__bwpostman_sendmailcontent`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_sendmailcontent` (
 `id` int(11) NOT NULL,
 `mode` int(1) NOT NULL,
 `nl_id` int(11) NOT NULL,
 `from_name` varchar(255) NOT NULL,
 `from_email` varchar(100) NOT NULL,
 `subject` varchar(255) NOT NULL,
 `body` longtext NOT NULL,
 `cc_email` varchar(100) NOT NULL,
 `bcc_email` varchar(100) NOT NULL,
 `attachment` text NOT NULL,
 `reply_email` varchar(100) NOT NULL,
 `reply_name` varchar(255) NOT NULL,
 PRIMARY KEY (`id`,`mode`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_sendmailqueue`
--

DROP TABLE IF EXISTS `#__bwpostman_sendmailqueue`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_sendmailqueue` (
 `id` int(11) NOT NULL auto_increment,
 `content_id` int(11) NOT NULL,
 `recipient` varchar(100) NOT NULL,
 `mode` int(1) NOT NULL,
 `name` varchar(255) NOT NULL,
 `firstname` varchar(255) NOT NULL,
 `subscriber_id` int(11) NOT NULL default '0',
 `trial` int(5) NOT NULL default '0',
 PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_subscribers`
--

DROP TABLE IF EXISTS `#__bwpostman_subscribers`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_subscribers` (
 `id` int(11) NOT NULL auto_increment,
 `asset_id` int(10) NOT NULL,
 `user_id` int(11) NOT NULL,
 `name` varchar(255) NOT NULL,
 `firstname` varchar(255) NOT NULL,
 `email` varchar(100) NOT NULL,
 `emailformat` tinyint(1) NOT NULL,
 `gender` tinyint(1) unsigned NULL,
 `special` varchar(255) NOT NULL,
 `status` int(1) NOT NULL,
 `activation` varchar(100) NOT NULL,
 `editlink` varchar(100) NOT NULL,
 `access` int(11) NOT NULL,
 `registration_date` datetime NOT NULL default '0000-00-00 00:00:00',
 `registered_by` int(11) NOT NULL default '0',
 `registration_ip` varchar(15) NOT NULL,
 `confirmation_date` datetime NOT NULL default '0000-00-00 00:00:00',
 `confirmed_by` int(11) NOT NULL default '0',
 `confirmation_ip` varchar(15) NOT NULL,
 `modified_time` datetime NOT NULL default '0000-00-00 00:00:00',
 `modified_by` int(10) unsigned NOT NULL default '0',
 `checked_out` int(11) NOT NULL default '0',
 `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
 `archive_flag` tinyint(1) NOT NULL default '0',
 `archive_date` datetime NOT NULL default '0000-00-00 00:00:00',
 `archived_by` varchar(11) NOT NULL default '-1',
 PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_subscribers_mailinglists`
--

DROP TABLE IF EXISTS `#__bwpostman_subscribers_mailinglists`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_subscribers_mailinglists` (
 `subscriber_id` int(11) NOT NULL,
 `mailinglist_id` int(11) NOT NULL,
 PRIMARY KEY (`subscriber_id`,`mailinglist_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_templates`
--

DROP TABLE IF EXISTS `#__bwpostman_templates`;
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
  `basics` text NOT NULL,
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
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_templates`
--

DROP TABLE IF EXISTS `#__bwpostman_templates_tpl`;
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
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `#__bwpostman_campaigns_mailinglists`
--

DROP TABLE IF EXISTS `#__bwpostman_campaigns_mailinglists`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_campaigns_mailinglists` (
 `campaign_id` int(11) NOT NULL,
 `mailinglist_id` int(11) NOT NULL,
 PRIMARY KEY (`campaign_id`,`mailinglist_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8 COLLATE utf8mb4_unicode_ci;

