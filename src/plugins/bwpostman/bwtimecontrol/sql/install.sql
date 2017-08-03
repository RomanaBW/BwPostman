-- --------------------------------------------------------

-- 
-- Table structure for table `#__bwpostman_tc_campaign`
-- 

DROP TABLE IF EXISTS `#__bwpostman_tc_campaign`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_tc_campaign` (
  `tc_id` int(11) NOT NULL auto_increment,
  `campaign_id` int(11) NOT NULL,
  `automailing` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `automailing_values` varchar(5120) NOT NULL,
  `chaining` tinyint(1) unsigned NOT NULL DEFAULT 1,
  `mail_ordering` varchar(5120) NOT NULL,
  `active` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `checked_out` int(11) NOT NULL default '0',
  `checked_out_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL default '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL default '0000-00-00 00:00:00',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(10) NOT NULL default '0',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` int(10) NOT NULL default '0',
  `archive_flag` tinyint(1) unsigned NOT NULL default '0',
  `archived_by` int(10) NOT NULL default '0',
  `archive_date` datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY (`tc_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `#__bwpostman_tc_sendmailcontent`
-- 

DROP TABLE IF EXISTS `#__bwpostman_tc_sendmailcontent`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_tc_sendmailcontent` (
  `id` int(11) NOT NULL auto_increment,
  `mode` int(1) NOT NULL,
  `nl_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `mail_number` int(11) NOT NULL,
  `sent` int(1) NOT NULL,
  `old` int(1) NOT NULL,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

-- --------------------------------------------------------

-- 
-- Table structure for table `#__bwpostman_tc_sendmailqueue`
-- 

DROP TABLE IF EXISTS `#__bwpostman_tc_sendmailqueue`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_tc_sendmailqueue` (
  `id` int(11) NOT NULL auto_increment,
  `tc_content_id` int(11) NOT NULL,
  `campaign_id` int(11) NOT NULL,
  `mail_number` int(11) NOT NULL,
  `sending_planned` datetime NOT NULL default '0000-00-00 00:00:00',
  `suspended` int(1) NOT NULL,
  `sent_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `trial` int(10) NOT NULL,
  `email` varchar(100) NOT NULL,
  `mode` int(1) NOT NULL,
  `name` varchar(255) NOT NULL,
  `firstname` varchar(255) NOT NULL,
  `subscriber_id` int(11) NOT NULL default '0',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `created_by` int(10) NOT NULL default '0',
  `modified` datetime NOT NULL default '0000-00-00 00:00:00',
  `modified_by` int(10) NOT NULL default '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE utf8_general_ci;

