CREATE TABLE IF NOT EXISTS `#__egoltlike` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service` int(3) NOT NULL,
  `cid` int(11) NOT NULL,
  `lastip` varchar(50) NOT NULL,
  `lastdate` DATETIME NOT NULL,
  `pos` int(10) NOT NULL DEFAULT '0',
  `neg` int(10) NOT NULL DEFAULT '0',
  `sum` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `egoltlike_idx` (`cid`)
);

CREATE TABLE IF NOT EXISTS `#__egoltlike_logs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `service` int(3) NOT NULL,
  `cid` int(11) NOT NULL,
  `logip` varchar(50) NOT NULL,
  `logdate` datetime NOT NULL,
  `rate` int(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `egoltlike_idx` (`cid`)
);