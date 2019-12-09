
--
-- set archived_by to INT
--
ALTER TABLE `#__bwpostman_campaigns` MODIFY `archived_by` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__bwpostman_mailinglists` MODIFY `archived_by` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__bwpostman_newsletters` MODIFY `archived_by` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `#__bwpostman_subscribers` MODIFY `archived_by` INT(11) NOT NULL DEFAULT '-1';
ALTER TABLE `#__bwpostman_templates` MODIFY `archived_by` INT(11) NOT NULL DEFAULT '0';

--
-- Enlarge string columns for full utf8 support with imagined number of characters
--
ALTER TABLE `#__bwpostman_campaigns` MODIFY `title` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_campaigns` MODIFY `description` VARCHAR(1500) NOT NULL DEFAULT '';

ALTER TABLE `#__bwpostman_mailinglists` MODIFY `title` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_mailinglists` MODIFY `description` VARCHAR(1500) NOT NULL DEFAULT '';

ALTER TABLE `#__bwpostman_newsletters` MODIFY `from_name` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY `from_email` VARCHAR(240) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY `reply_email` VARCHAR(240) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY `usergroups` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY `selected_content` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY `subject` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY `description` VARCHAR(1500) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY `attachment` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY `intro_headline` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY `intro_text` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY `intro_text_headline` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY `intro_text_text` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY `html_version` LONGTEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_newsletters` MODIFY `text_version` LONGTEXT NOT NULL DEFAULT '';

SET @tablename = "#__bwpostman_newsletters";
SET @dbname = DATABASE();
SET @columnname = "substitute_links";
PREPARE alterIfNotExists FROM 'SELECT IF(
	(
		SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
		WHERE
		(table_name = @tablename)
		AND (table_schema = @dbname)
		AND (column_name = @columnname)
	) > 0,
	"SELECT 1",
	CONCAT("ALTER TABLE ", ?, " ADD ", @columnname, " INT(11);")
)';
EXECUTE alterIfNotExists USING @tablename;

ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY `from_name` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY `from_email` VARCHAR(240) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY `subject` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY `body` LONGTEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY `cc_email` VARCHAR(240) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY `bcc_email` VARCHAR(240) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY `attachment` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY `reply_email` VARCHAR(240) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY `reply_name` VARCHAR(300) NOT NULL DEFAULT '';

SET @tablename = "# __bwpostman_sendmailcontent";
SET @dbname = DATABASE();
SET @columnname = "substitute_links";
PREPARE alterIfNotExists FROM 'SELECT IF(
	(
		SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
		WHERE
		(table_name = @tablename)
		AND (table_schema = @dbname)
		AND (column_name = @columnname)
	) > 0,
	"SELECT 1",
	CONCAT("ALTER TABLE ", ?, " ADD ", @columnname, " INT(11);")
)';
EXECUTE alterIfNotExists USING @tablename;

ALTER TABLE `#__bwpostman_sendmailqueue` MODIFY `recipient` VARCHAR(240) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailqueue` MODIFY `name` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_sendmailqueue` MODIFY `firstname` VARCHAR(300) NOT NULL DEFAULT '';

ALTER TABLE `#__bwpostman_subscribers` MODIFY `name` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_subscribers` MODIFY `firstname` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_subscribers` MODIFY `email` VARCHAR(240) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_subscribers` MODIFY `special` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_subscribers` MODIFY `activation` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_subscribers` MODIFY `editlink` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_subscribers` MODIFY `registration_ip` VARCHAR(156) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_subscribers` MODIFY `confirmation_ip` VARCHAR(156) NOT NULL DEFAULT '';

ALTER TABLE `#__bwpostman_templates` MODIFY `title` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY `description` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY `thumbnail` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY `tpl_html` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY `tpl_css` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY `tpl_article` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY `tpl_divider` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY `basics` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY `header` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY `intro` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY `article` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY `footer` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY `button1` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY `button2` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY `button3` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY `button4` VARCHAR(1000) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates` MODIFY `button5` VARCHAR(1000) NOT NULL DEFAULT '';

ALTER TABLE `#__bwpostman_templates_tpl` MODIFY `title` VARCHAR(300) NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY `css` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY `header_tpl` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY `intro_tpl` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY `divider_tpl` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY `article_tpl` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY `readon_tpl` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY `footer_tpl` TEXT NOT NULL DEFAULT '';
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY `button_tpl` TEXT NOT NULL DEFAULT '';

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


DEALLOCATE PREPARE alterIfNotExists;
