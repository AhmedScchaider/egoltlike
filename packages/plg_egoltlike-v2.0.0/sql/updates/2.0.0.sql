ALTER TABLE  `#__egoltlike` ADD  `lastdate` DATETIME NOT NULL AFTER `lastip`;
UPDATE  `#__egoltlike` SET  `lastdate` =  NOW();

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

INSERT INTO `#__egoltlike_logs` (`service`, `cid`, `logip`, `logdate`, `rate`) 
SELECT `service`, `cid`, `lastip`, `lastdate`, `sum` from `#__egoltlike`
 