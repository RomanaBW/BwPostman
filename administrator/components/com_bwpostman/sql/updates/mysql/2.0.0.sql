ALTER TABLE `#__bwpostman_campaigns` COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_campaigns` MODIFY COLUMN `title` VARCHAR(400) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_campaigns` MODIFY COLUMN `description` varchar(2000) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_campaigns` MODIFY COLUMN `archived_by` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `#__bwpostman_mailinglists` COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_mailinglists` MODIFY COLUMN `title` VARCHAR(400) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_mailinglists` MODIFY COLUMN `description` varchar(2000) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_mailinglists` MODIFY COLUMN `archived_by` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `#__bwpostman_newsletters` COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `from_name` VARCHAR(400) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `from_email` VARCHAR(320) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `reply_email` VARCHAR(320) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `usergroups` VARCHAR(400) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `selected_content` VARCHAR(400) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `subject` VARCHAR(1000) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `description` varchar(2000) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `attachment` VARCHAR(1000) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `intro_headline` VARCHAR(1000) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `intro_text` TEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `intro_text_headline` VARCHAR(1000) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `intro_text_text` TEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `html_version` LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `text_version` LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_newsletters` MODIFY COLUMN `archived_by` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `#__bwpostman_newsletters_mailinglists` COLLATE `utf8mb4_unicode_ci`;

ALTER TABLE `#__bwpostman_sendmailcontent` COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY COLUMN `from_name` VARCHAR(400) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY COLUMN `from_email` VARCHAR(320) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY COLUMN `subject` VARCHAR(1000) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY COLUMN `body` LONGTEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY COLUMN `cc_email` VARCHAR(320) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY COLUMN `bcc_email` VARCHAR(320) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY COLUMN `attachment` TEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY COLUMN `reply_email` VARCHAR(320) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY COLUMN `reply_name` VARCHAR(400) NOT NULL COLLATE `utf8mb4_unicode_ci`;

ALTER TABLE `#__bwpostman_sendmailqueue` COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_sendmailqueue` MODIFY COLUMN `recipient` VARCHAR(320) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_sendmailqueue` MODIFY COLUMN `name` VARCHAR(400) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_sendmailqueue` MODIFY COLUMN `firstname` VARCHAR(400) NOT NULL COLLATE `utf8mb4_unicode_ci`;

ALTER TABLE `#__bwpostman_subscribers` COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_subscribers` MODIFY COLUMN `name` VARCHAR(400) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_subscribers` MODIFY COLUMN `firstname` VARCHAR(400) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_subscribers` MODIFY COLUMN `email` VARCHAR(320) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_subscribers` MODIFY COLUMN `special` VARCHAR(400) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_subscribers` MODIFY COLUMN `activation` VARCHAR(400) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_subscribers` MODIFY COLUMN `editlink` VARCHAR(400) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_subscribers` MODIFY COLUMN `registration_ip` VARCHAR(156) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_subscribers` MODIFY COLUMN `confirmation_ip` VARCHAR(156) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_subscribers` MODIFY COLUMN `archived_by` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `#__bwpostman_subscribers_mailinglists` COLLATE `utf8mb4_unicode_ci`;

ALTER TABLE `#__bwpostman_templates` COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `title` VARCHAR(400) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `description` VARCHAR(1000) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `thumbnail` VARCHAR(1000) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `tpl_html` TEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `tpl_css` TEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `tpl_article` TEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `tpl_divider` TEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `basics` TEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `header` VARCHAR(1000) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `intro` TEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `article` VARCHAR(1000) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `footer` VARCHAR(1000) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `button1` VARCHAR(1000) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `button2` VARCHAR(1000) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `button3` VARCHAR(1000) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `button4` VARCHAR(1000) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `button5` VARCHAR(1000) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates` MODIFY COLUMN `archived_by` INT(11) NOT NULL DEFAULT '0';

ALTER TABLE `#__bwpostman_templates_tpl` COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY COLUMN `title` VARCHAR(400) NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY COLUMN `css` TEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY COLUMN `header_tpl` TEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY COLUMN `intro_tpl` TEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY COLUMN `divider_tpl` TEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY COLUMN `article_tpl` TEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY COLUMN `readon_tpl` TEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY COLUMN `footer_tpl` TEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY COLUMN `button_tpl` TEXT NOT NULL COLLATE `utf8mb4_unicode_ci`;

ALTER TABLE `#__bwpostman_campaigns_mailinglists` COLLATE `utf8mb4_unicode_ci`;

UPDATE `#__bwpostman_templates` SET `tpl_html` = REPLACE(`tpl_html`,'<div class="shadow" style="','<div class="shadow" style="height: 2px; ');
UPDATE `#__bwpostman_templates_tpl` SET `header_tpl` = REPLACE(`header_tpl`,'<div class=\\"shadow\\" style=\\"','<div class=\\"shadow\\" style=\\"height: 2px; ');
UPDATE `#__bwpostman_templates_tpl` SET `footer_tpl` = REPLACE(`footer_tpl`,'<div class="shadow" style="','<div class="shadow" style="height: 2px; ');
