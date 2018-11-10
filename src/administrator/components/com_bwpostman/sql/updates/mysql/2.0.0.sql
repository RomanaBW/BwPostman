--
-- set archived_by to INT
--
ALTER TABLE `#__bwpostman_campaigns` MODIFY COLUMN `archived_by` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__bwpostman_mailinglists` MODIFY COLUMN `archived_by` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `archived_by` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__bwpostman_subscribers` MODIFY COLUMN `archived_by` INT(11) NOT NULL DEFAULT '-1';
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `archived_by` INT(11) NOT NULL DEFAULT '0';

--
-- Enlarge string columns for full utf8 support with imagined number of characters
--
ALTER TABLE `#__bwpostman_campaigns` MODIFY COLUMN `title` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_campaigns` MODIFY COLUMN `description` VARCHAR(1500) NOT NULL DEFAULT '';

ALTER TABLE `#__bwpostman_mailinglists` MODIFY COLUMN `title` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_mailinglists` MODIFY COLUMN `description` VARCHAR(1500) NOT NULL DEFAULT '';

ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `from_name` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `from_email` VARCHAR(240) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `reply_email` VARCHAR(240) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `usergroups` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `selected_content` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `subject` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `description` VARCHAR(1500) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `attachment` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `intro_headline` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `intro_text` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `intro_text_headline` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `intro_text_text` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `html_version` LONGTEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `text_version` LONGTEXT NOT NULL DEFAULT '';

SET @dbname = DATABASE();
SET @tablename = "#__bwpostman_newsletters";
SET @columnname = "substitute_links";
SET @preparedStatement = (SELECT IF(
																	 (
																	 SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
																	 WHERE
																			 (table_name = @tablename)
																		 AND (table_schema = @dbname)
																		 AND (column_name = @columnname)
																	 ) > 0,
																	 "SELECT 1",
																	 CONCAT("ALTER TABLE ", @tablename, " ADD ", @columnname, " INT(11);")
																		 ));
PREPARE alterIfNotExists FROM @preparedStatement;
EXECUTE alterIfNotExists;
DEALLOCATE PREPARE alterIfNotExists;


ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY COLUMN `from_name` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY COLUMN `from_email` VARCHAR(240) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY COLUMN `subject` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY COLUMN `body` LONGTEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY COLUMN `cc_email` VARCHAR(240) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY COLUMN `bcc_email` VARCHAR(240) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY COLUMN `attachment` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY COLUMN `reply_email` VARCHAR(240) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY COLUMN `reply_name` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailcontent` ADD COLUMN IF NOT EXISTS `substitute_links` TINYINT(1) NOT NULL DEFAULT '0';

ALTER TABLE `#__bwpostman_sendmailqueue` MODIFY COLUMN `recipient` VARCHAR(240) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailqueue` MODIFY COLUMN `name` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailqueue` MODIFY COLUMN `firstname` VARCHAR(300) NOT NULL DEFAULT '';

ALTER TABLE `#__bwpostman_subscribers` MODIFY COLUMN `name` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_subscribers` MODIFY COLUMN `firstname` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_subscribers` MODIFY COLUMN `email` VARCHAR(240) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_subscribers` MODIFY COLUMN `special` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_subscribers` MODIFY COLUMN `activation` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_subscribers` MODIFY COLUMN `editlink` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_subscribers` MODIFY COLUMN `registration_ip` VARCHAR(156) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_subscribers` MODIFY COLUMN `confirmation_ip` VARCHAR(156) NOT NULL DEFAULT '';

ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `title` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `description` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `thumbnail` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `tpl_html` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `tpl_css` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `tpl_article` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `tpl_divider` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `basics` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `header` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `intro` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `article` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `footer` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `button1` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `button2` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `button3` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `button4` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `button5` VARCHAR(1000) NOT NULL DEFAULT '';

ALTER TABLE `#__bwpostman_templates_tpl` MODIFY COLUMN `title` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY COLUMN `css` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY COLUMN `header_tpl` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY COLUMN `intro_tpl` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY COLUMN `divider_tpl` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY COLUMN `article_tpl` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY COLUMN `readon_tpl` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY COLUMN `footer_tpl` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY COLUMN `button_tpl` TEXT NOT NULL DEFAULT '';

--
-- Update template tables
--

UPDATE `#__bwpostman_templates` SET `tpl_html` = REPLACE(`tpl_html`,'<div class="shadow" style="','<div class="shadow" style="height: 2px; ');
UPDATE `#__bwpostman_templates_tpl` SET `header_tpl` = REPLACE(`header_tpl`,'<div class=\\"shadow\\" style=\\"','<div class=\\"shadow\\" style=\\"height: 2px; ');
UPDATE `#__bwpostman_templates_tpl` SET `footer_tpl` = REPLACE(`footer_tpl`,'<div class="shadow" style="','<div class="shadow" style="height: 2px; ');

--
-- Create Table structure for table `#__bwpostman_templates_tags`
--

CREATE TABLE IF NOT EXISTS `#__bwpostman_templates_tags` (
	`templates_table_id` INT(11) NOT NULL,
	`tpl_tags_head` TINYINT(1) NOT NULL,
	`tpl_tags_head_advanced` TEXT NOT NULL DEFAULT '',
	`tpl_tags_body` TINYINT(1) NOT NULL,
	`tpl_tags_body_advanced` TEXT NOT NULL DEFAULT '',
	`tpl_tags_article` TINYINT(1) NOT NULL,
	`tpl_tags_article_advanced_b` TEXT NOT NULL DEFAULT '',
	`tpl_tags_article_advanced_e` TEXT NOT NULL DEFAULT '',
	`tpl_tags_readon` TINYINT(1) NOT NULL,
	`tpl_tags_readon_advanced` TEXT NOT NULL DEFAULT '',
	`tpl_tags_legal` TINYINT(1) NOT NULL,
	`tpl_tags_legal_advanced_b` TEXT NOT NULL DEFAULT '',
	`tpl_tags_legal_advanced_e` TEXT NOT NULL DEFAULT '',
	PRIMARY KEY (`templates_table_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8mb4 COLLATE utf8mb4_unicode_ci;


