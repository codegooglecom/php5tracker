-- Table structure for table `peers`
-- use one of the commands bellow to create the table


--- Default db engine
CREATE TABLE IF NOT EXISTS `peers` 
(
  `info_hash` binary(20) NOT NULL,
  `compact` binary(6) NOT NULL,
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updated` int(10) unsigned NOT NULL,
  PRIMARY KEY (`info_hash`,`compact`)
) DEFAULT CHARSET=utf8;


--- MyISAM db engine
CREATE TABLE IF NOT EXISTS `peers` 
(
  `info_hash` binary(20) NOT NULL,
  `compact` binary(6) NOT NULL,
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updated` int(10) unsigned NOT NULL,
  PRIMARY KEY (`info_hash`,`compact`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8


--- MyISAM db engine
CREATE TABLE IF NOT EXISTS `peers` 
(
  `info_hash` binary(20) NOT NULL,
  `compact` binary(6) NOT NULL,
  `state` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `updated` int(10) unsigned NOT NULL,
  PRIMARY KEY (`info_hash`,`compact`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8