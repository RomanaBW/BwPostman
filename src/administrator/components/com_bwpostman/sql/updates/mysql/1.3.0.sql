UPDATE `#__bwpostman_templates` SET `tpl_html` = REPLACE(`tpl_html`,'<div class="shadow" style="','<div class="shadow" style="height: 2px; ');
UPDATE `#__bwpostman_templates_tpl` SET `header_tpl` = REPLACE(`header_tpl`,'<div class=\\"shadow\\" style=\\"','<div class=\\"shadow\\" style=\\"height: 2px; ');
UPDATE `#__bwpostman_templates_tpl` SET `footer_tpl` = REPLACE(`footer_tpl`,'<div class="shadow" style="','<div class="shadow" style="height: 2px; ');

ALTER TABLE `#__bwpostman_subscribers` ADD `gender` tinyint(1) unsigned NOT NULL default '0' AFTER `emailformat`;
ALTER TABLE `#__bwpostman_subscribers` ADD `special` varchar(255) NOT NULL AFTER `gender`;
