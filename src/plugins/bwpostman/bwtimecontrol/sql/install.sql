-- --------------------------------------------------------
--
-- Table structure for table `#__bwpostman_tc_schedule`
--

DROP TABLE IF EXISTS `#__bwpostman_tc_schedule`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_tc_schedule` (
  `newsletter_id` INT(11) NOT NULL,
  `scheduled_date` datetime NOT NULL default '0000-00-00 00:00:00',
  `ready_to_send` TINYINT(1) NOT NULL DEFAULT '0',
  `sent` TINYINT(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`newsletter_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

--
-- Table structure for table `#__bwpostman_tc_settings`
--

DROP TABLE IF EXISTS `#__bwpostman_tc_settings`;
CREATE TABLE IF NOT EXISTS `#__bwpostman_tc_settings` (
  `id` INT(11) NOT NULL,
  `nonce` BLOB NOT NULL DEFAULT '',
  `priv` BLOB NOT NULL DEFAULT '',
  `pub` BLOB NOT NULL DEFAULT '',
  `type` VARCHAR(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;

