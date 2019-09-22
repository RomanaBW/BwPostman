
--
--  Set missing default values for various text columns of template tables
--

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

