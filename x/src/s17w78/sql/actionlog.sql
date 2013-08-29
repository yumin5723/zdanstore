--
-- Table structure for table `action_log`
--
CREATE TABLE IF NOT EXISTS `action_log` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) DEFAULT NULL,
  `action` varchar(64) NOT NULL,
  `url` varchar(128) DEFAULT NULL,
  `referrer` varchar(128) DEFAULT NULL,
  `ip` varchar(128) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

alter table action_log add `u_from` varchar(256) DEFAULT "";
alter table action_log add `u_id` int(11) DEFAULT "0";