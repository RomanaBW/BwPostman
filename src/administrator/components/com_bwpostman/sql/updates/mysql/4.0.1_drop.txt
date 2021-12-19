ALTER TABLE `#__bwpostman_newsletters` ALTER `intro_text` DROP DEFAULT;
ALTER TABLE `#__bwpostman_newsletters` ALTER `intro_text_text` DROP DEFAULT;
ALTER TABLE `#__bwpostman_newsletters` ALTER `html_version` DROP DEFAULT;
ALTER TABLE `#__bwpostman_newsletters` ALTER `text_version` DROP DEFAULT;

ALTER TABLE `#__bwpostman_sendmailcontent` ALTER `body` DROP DEFAULT;
ALTER TABLE `#__bwpostman_sendmailcontent` ALTER `attachment` DROP DEFAULT;

ALTER TABLE `#__bwpostman_templates` ALTER `tpl_html` DROP DEFAULT;
ALTER TABLE `#__bwpostman_templates` ALTER `tpl_css` DROP DEFAULT;
ALTER TABLE `#__bwpostman_templates` ALTER `tpl_article` DROP DEFAULT;
ALTER TABLE `#__bwpostman_templates` ALTER `tpl_divider` DROP DEFAULT;
ALTER TABLE `#__bwpostman_templates` ALTER `intro` DROP DEFAULT;
ALTER TABLE `#__bwpostman_templates` ALTER `article` DROP DEFAULT;

ALTER TABLE `#__bwpostman_templates_tpl` ALTER `css` DROP DEFAULT;
ALTER TABLE `#__bwpostman_templates_tpl` ALTER `header_tpl` DROP DEFAULT;
ALTER TABLE `#__bwpostman_templates_tpl` ALTER `intro_tpl` DROP DEFAULT;
ALTER TABLE `#__bwpostman_templates_tpl` ALTER `divider_tpl` DROP DEFAULT;
ALTER TABLE `#__bwpostman_templates_tpl` ALTER `article_tpl` DROP DEFAULT;
ALTER TABLE `#__bwpostman_templates_tpl` ALTER `readon_tpl` DROP DEFAULT;
ALTER TABLE `#__bwpostman_templates_tpl` ALTER `footer_tpl` DROP DEFAULT;
ALTER TABLE `#__bwpostman_templates_tpl` ALTER `button_tpl` DROP DEFAULT;

ALTER TABLE `#__bwpostman_templates_tags` ALTER `tpl_tags_head_advanced` DROP DEFAULT;
ALTER TABLE `#__bwpostman_templates_tags` ALTER `tpl_tags_body_advanced` DROP DEFAULT;
ALTER TABLE `#__bwpostman_templates_tags` ALTER `tpl_tags_article_advanced_b` DROP DEFAULT;
ALTER TABLE `#__bwpostman_templates_tags` ALTER `tpl_tags_article_advanced_e` DROP DEFAULT;
ALTER TABLE `#__bwpostman_templates_tags` ALTER `tpl_tags_readon_advanced` DROP DEFAULT;
ALTER TABLE `#__bwpostman_templates_tags` ALTER `tpl_tags_legal_advanced_b` DROP DEFAULT;
ALTER TABLE `#__bwpostman_templates_tags` ALTER `tpl_tags_legal_advanced_e` DROP DEFAULT;
