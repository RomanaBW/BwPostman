# Versanddatum ohne Veröffentlichungsdatum mitziehen
UPDATE `jos_bwpostman_newsletters` SET `mailing_date`=DATE_ADD(`mailing_date`,interval 1 MONTH) WHERE `id` IN (4, 12, 13, 17, 19, 20, 21, 23, 25, 26, 27, 28, 29, 32, 34, 38, 41, 43, 45, 47, 48, 49, 50, 52, 57, 58, 60);

# Versanddatum mit Veröffentlichungsdatum mitziehen
UPDATE `jos_bwpostman_newsletters` SET `mailing_date`=DATE_ADD(`mailing_date`,interval 1 MONTH) WHERE `id` IN (14, 16, 18, 24, 35, 39, 40, 42, 44, 46, 53, 55, 56, 59, 62, 63);
# Veröffentlichungsdatum Start mitziehen
UPDATE `jos_bwpostman_newsletters` SET `publish_up`=DATE_ADD(`publish_up`,interval 1 MONTH) WHERE `id` IN (14, 16, 18, 24, 35, 39, 40, 42, 44, 46, 53, 55, 56, 59, 62, 63);

# Veröffentlichungsdatum Ende mitziehen
UPDATE `jos_bwpostman_newsletters` SET `publish_down`=DATE_ADD(`publish_down`,interval 1 MONTH) WHERE `id` IN (14, 18, 40, 44, 62, 63);
