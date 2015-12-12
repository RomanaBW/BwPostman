UPDATE #__bwpostman_templates SET tpl_html = REPLACE(tpl_html,'<div class="spacer" style="font-size: 1px','<div class="spacer" style="font-size: 10px');
UPDATE #__bwpostman_templates SET tpl_article = REPLACE(tpl_article,'<div class="spacer" style="font-size: 1px','<div class="spacer" style="font-size: 10px');
UPDATE #__bwpostman_templates_tpl SET intro_tpl = REPLACE(intro_tpl,'<div class="spacer" style="font-size: 1px','<div class="spacer" style="font-size: 10px');
UPDATE #__bwpostman_templates_tpl SET article_tpl = REPLACE(article_tpl,'<div class="spacer" style="font-size: 1px','<div class="spacer" style="font-size: 10px');
UPDATE #__bwpostman_templates_tpl SET footer_tpl = REPLACE(footer_tpl,'<div class="spacer" style="font-size: 1px','<div class="spacer" style="font-size: 10px');
