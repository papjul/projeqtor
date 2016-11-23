-- Not tested on a fresh install
ALTER TABLE `reportparameter`
ADD COLUMN `multiple` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0'

ALTER TABLE `report`
ADD COLUMN `hasCsv` INT(1) UNSIGNED NOT NULL DEFAULT '0' AFTER `file`;

UPDATE report SET hasCsv = 1 WHERE `id` = 49; -- Portfolio gantt (jsonplanning.php)