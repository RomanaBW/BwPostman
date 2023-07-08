/* Increase size of column basics  at table #__bwpostman_templates */

ALTER TABLE `#__bwpostman_templates` MODIFY `basics` VARCHAR(2000) NOT NULL DEFAULT '';
