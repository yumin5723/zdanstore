-- Table structure for table `user`
--
-- CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) DEFAULT NULL,
  `nickname` varchar(64) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  `pass_str` varchar(128) NOT NULL,
  `email_confirmed` varchar(32) NOT NULL DEFAULT '0',
  `platform` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=1000000013 DEFAULT CHARSET=utf8 |

alter table user add `puid` varchar(128) NOT NULL;