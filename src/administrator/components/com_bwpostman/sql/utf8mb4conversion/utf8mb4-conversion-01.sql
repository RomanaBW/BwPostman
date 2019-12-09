--
-- UTF-8 Multibyte (utf8mb4) conversion for MySQL
--
-- Enlarge some table columns to avoid data losses, then convert all tables
-- to utf8mb4, then set default character sets and collations for all tables.
--
-- Do not rename this file or any other of the utf8mb4-conversion-*.sql
-- files unless you want to change PHP code, too.
--
-- IMPORTANT: When adding an index modification to this file for limiting the
-- length by which one or more columns go into that index,
--
-- 1. remember to add the statement to drop the index before step 1 and
--
-- 2. check if the index is created or modified in some old schema
--    update sql in an "ALTER TABLE" statement and limit the column length
--    there, too ("CREATE TABLE" is ok, no need to modify those).
--
-- This file here will the be processed with reporting exceptions
--

--
-- Step 1: Enlarge columns to avoid data loss on later conversion to utf8mb4
--

ALTER TABLE `#__bwpostman_campaigns` MODIFY `title` VARCHAR(400) NOT NULL;
ALTER TABLE `#__bwpostman_campaigns` MODIFY `description` varchar(2000) NOT NULL;

ALTER TABLE `#__bwpostman_mailinglists` MODIFY `title` VARCHAR(400) NOT NULL;
ALTER TABLE `#__bwpostman_mailinglists` MODIFY `description` varchar(2000) NOT NULL;

ALTER TABLE `#__bwpostman_newsletters` MODIFY `from_name` VARCHAR(400) NOT NULL;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `from_email` VARCHAR(320) NOT NULL;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `reply_email` VARCHAR(320) NOT NULL;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `usergroups` VARCHAR(400) NOT NULL;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `selected_content` VARCHAR(400) NOT NULL;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `subject` VARCHAR(1000) NOT NULL;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `description` varchar(2000) NOT NULL;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `attachment` VARCHAR(1000) NOT NULL;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `intro_headline` VARCHAR(1000) NOT NULL;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `intro_text` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `intro_text_headline` VARCHAR(1000) NOT NULL;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `intro_text_text` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `html_version` LONGTEXT NOT NULL;
ALTER TABLE `#__bwpostman_newsletters` MODIFY `text_version` LONGTEXT NOT NULL;

ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY `from_name` VARCHAR(400) NOT NULL;
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY `from_email` VARCHAR(320) NOT NULL;
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY `subject` VARCHAR(1000) NOT NULL;
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY `body` LONGTEXT NOT NULL ;
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY `cc_email` VARCHAR(320) NOT NULL;
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY `bcc_email` VARCHAR(320) NOT NULL;
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY `attachment` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY `reply_email` VARCHAR(320) NOT NULL;
ALTER TABLE `#__bwpostman_sendmailcontent` MODIFY `reply_name` VARCHAR(400) NOT NULL;

ALTER TABLE `#__bwpostman_sendmailqueue` MODIFY `recipient` VARCHAR(320) NOT NULL;
ALTER TABLE `#__bwpostman_sendmailqueue` MODIFY `name` VARCHAR(400) NOT NULL;
ALTER TABLE `#__bwpostman_sendmailqueue` MODIFY `firstname` VARCHAR(400) NOT NULL;

ALTER TABLE `#__bwpostman_subscribers` MODIFY `name` VARCHAR(400) NOT NULL;
ALTER TABLE `#__bwpostman_subscribers` MODIFY `firstname` VARCHAR(400) NOT NULL;
ALTER TABLE `#__bwpostman_subscribers` MODIFY `email` VARCHAR(320) NOT NULL;
ALTER TABLE `#__bwpostman_subscribers` MODIFY `special` VARCHAR(400) NOT NULL;
ALTER TABLE `#__bwpostman_subscribers` MODIFY `activation` VARCHAR(400) NOT NULL;
ALTER TABLE `#__bwpostman_subscribers` MODIFY `editlink` VARCHAR(400) NOT NULL;
ALTER TABLE `#__bwpostman_subscribers` MODIFY `registration_ip` VARCHAR(156) NOT NULL;
ALTER TABLE `#__bwpostman_subscribers` MODIFY `confirmation_ip` VARCHAR(156) NOT NULL;

ALTER TABLE `#__bwpostman_templates` MODIFY `title` VARCHAR(400) NOT NULL;
ALTER TABLE `#__bwpostman_templates` MODIFY `description` VARCHAR(2000) NOT NULL;
ALTER TABLE `#__bwpostman_templates` MODIFY `thumbnail` VARCHAR(2000) NOT NULL;
ALTER TABLE `#__bwpostman_templates` MODIFY `tpl_html` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_templates` MODIFY `tpl_css` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_templates` MODIFY `tpl_article` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_templates` MODIFY `tpl_divider` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_templates` MODIFY `basics` VARCHAR(400) NOT NULL;
ALTER TABLE `#__bwpostman_templates` MODIFY `header` VARCHAR(1600) NOT NULL;
ALTER TABLE `#__bwpostman_templates` MODIFY `intro` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_templates` MODIFY `article` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_templates` MODIFY `footer` VARCHAR(1600) NOT NULL;
ALTER TABLE `#__bwpostman_templates` MODIFY `button1` VARCHAR(1600) NOT NULL;
ALTER TABLE `#__bwpostman_templates` MODIFY `button2` VARCHAR(1600) NOT NULL;
ALTER TABLE `#__bwpostman_templates` MODIFY `button3` VARCHAR(1600) NOT NULL;
ALTER TABLE `#__bwpostman_templates` MODIFY `button4` VARCHAR(1600) NOT NULL;
ALTER TABLE `#__bwpostman_templates` MODIFY `button5` VARCHAR(1600) NOT NULL;

ALTER TABLE `#__bwpostman_templates_tpl` MODIFY `title` VARCHAR(400) NOT NULL;
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY `css` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY `header_tpl` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY `intro_tpl` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY `divider_tpl` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY `article_tpl` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY `readon_tpl` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY `footer_tpl` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_templates_tpl` MODIFY `button_tpl` TEXT NOT NULL;

ALTER TABLE `#__bwpostman_templates_tags` MODIFY `tpl_tags_head_advanced` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_templates_tags` MODIFY `tpl_tags_body_advanced` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_templates_tags` MODIFY `tpl_tags_article_advanced_b` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_templates_tags` MODIFY `tpl_tags_article_advanced_e` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_templates_tags` MODIFY `tpl_tags_readon_advanced` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_templates_tags` MODIFY `tpl_tags_legal_advanced_b` TEXT NOT NULL;
ALTER TABLE `#__bwpostman_templates_tags` MODIFY `tpl_tags_legal_advanced_e` TEXT NOT NULL;

--
-- Step 2: Convert all tables to utf8mb4 character set with utf8mb4_unicode_ci collation
-- Note: The database driver for mysql will change utf8mb4 to utf8 if utf8mb4 is not supported
--
ALTER TABLE `#__bwpostman_campaigns` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_campaigns_mailinglists` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_mailinglists` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_newsletters` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_newsletters_mailinglists` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_sendmailcontent` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_sendmailqueue` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_subscribers` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_subscribers_mailinglists` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_templates` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_templates_tpl` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_templates_tags` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

--
-- Step 3: Set default character set and collation for all tables
--

ALTER TABLE `#__bwpostman_campaigns` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_campaigns_mailinglists` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_mailinglists` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_newsletters` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_newsletters_mailinglists` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_sendmailcontent` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_sendmailqueue` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_subscribers` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_subscribers_mailinglists` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_templates` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_templates_tpl` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
ALTER TABLE `#__bwpostman_templates_tags` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
