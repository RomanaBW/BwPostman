UPDATE `jos_extensions`
	SET `ordering`='1'
	WHERE `type`='plugin'
	  AND `element`='packageinstaller'
;

UPDATE `jos_extensions`
SET `ordering`='22'
WHERE `type`='plugin'
  AND `element`='folderinstaller'
;

UPDATE `jos_extensions`
SET `ordering`='3'
WHERE `type`='plugin'
  AND `element`='urlinstaller'
;

UPDATE `jos_extensions`
SET `ordering`='4'
WHERE `type`='plugin'
  AND `element`='webinstaller'
;

