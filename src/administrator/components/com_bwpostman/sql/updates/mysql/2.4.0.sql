
--
--  Set missing default values for various text columns of template tables
--

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

