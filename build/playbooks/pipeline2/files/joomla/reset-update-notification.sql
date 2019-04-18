UPDATE `jos_extensions`
	SET `enabled`=0
	WHERE `element`='updatenotification'
	OR `element`='joomlaupdate'
	OR `element`='extensionupdate';

UPDATE `jos_extensions`
SET `params`='{"show_jed_info":"0","cachetimeout":6,"minimum_stability":"4"}'
WHERE `element`='com_installer';
