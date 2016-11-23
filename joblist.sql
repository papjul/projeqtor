-- WARNING: this script was not tested on a fresh install
-- You may also want to allow access to the menu AND the report to some users profiles afterwards

CREATE TABLE IF NOT EXISTS `job` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `idJoblistDefinition` int(12) unsigned DEFAULT NULL,
  `idJobDefinition` int(12) unsigned DEFAULT NULL,
  `value` int(1) unsigned DEFAULT '0',
  `idUser` int(12) unsigned DEFAULT NULL,
  `refType` varchar(100) DEFAULT NULL,
  `refId` int(12) unsigned DEFAULT NULL,
  `creationDate` datetime DEFAULT NULL,
  `checkTime` datetime DEFAULT NULL,
  `comment` varchar(4000) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jobJobDefinition` (`idJobDefinition`),
  KEY `jobJoblistDefinition` (`idJoblistDefinition`),
  KEY `jobReference` (`refType`,`refId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `jobdefinition` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `idJoblistDefinition` int(12) unsigned DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `title` varchar(1000) DEFAULT NULL,
  `sortOrder` int(3) DEFAULT '0',
  `daysBeforeWarning` int(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `jobdefinitionJoblistDefinition` (`idJoblistDefinition`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `joblistdefinition` (
  `id` int(12) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `idChecklistable` int(12) unsigned DEFAULT NULL,
  `nameChecklistable` varchar(100) DEFAULT NULL,
  `idType` int(12) unsigned DEFAULT NULL,
  `lineCount` int(3) DEFAULT '0',
  `idle` int(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `checklistdefinitionChecklistable` (`idChecklistable`),
  KEY `checklistdefinitionNameChecklistable` (`nameChecklistable`),
  KEY `checklistdefinitionType` (`idType`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- IDs were selected high so it is not impacted by new versions of ProjeQtOr
INSERT INTO `menu` (`id`, `name`, `idMenu`, `type`, `sortOrder`, `level`, `idle`, `menuClass`) VALUES (935, 'menuJoblistDefinition', 88, 'object', 640, 'ReadWriteEnvironment', 0, 'Automation ');

INSERT INTO `report` (`id`, `name`, `idReportCategory`, `file`, `hasCsv`, `sortOrder`, `idle`, `orientation`) VALUES (907, 'reportMacroJoblist', 1, 'joblist.php', 1, 99, 0, 'L');
INSERT INTO `reportparameter` (`id`, `idReport`, `name`, `paramType`, `sortOrder`, `idle`, `defaultValue`, `multiple`) VALUES (913, 907, 'idActivity', 'activityList', 20, 0, NULL, 0);
INSERT INTO `reportparameter` (`id`, `idReport`, `name`, `paramType`, `sortOrder`, `idle`, `defaultValue`, `multiple`) VALUES (912, 907, 'idProject', 'projectList', 10, 0, 'currentProject', 0);

-- Currently, joblistable doesn't exist and share the same as checklistable, but a "joblistable" can be added