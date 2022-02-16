UPDATE `jos_extensions`
	SET `enabled`=0
	WHERE `name`='plg_system_schedulerunner';

UPDATE `jos_extensions`
SET `enabled`=0
WHERE `name`='com_scheduler';
