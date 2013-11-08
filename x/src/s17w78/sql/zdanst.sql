CREATE TABLE IF NOT EXISTS `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `username` varchar(120) NOT NULL,
  `email` varchar(128) NOT NULL,
  `content` text NOT NULL,
  `isreplys` tinyint(2) NOT NULL,
  `isreply` tinyint(2) NOT NULL,
  `reply` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=35 ;