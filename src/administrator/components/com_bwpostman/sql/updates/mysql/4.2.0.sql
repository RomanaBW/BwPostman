/* Change column basics at table #__bwpostman_templates from varchar to text */

ALTER TABLE `#__bwpostman_templates` MODIFY `basics` TEXT NOT NULL DEFAULT '';
