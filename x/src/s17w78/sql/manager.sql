--
-- Table structure for table `manager`
--

CREATE TABLE IF NOT EXISTS `manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL,
  `username` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `last_login_time` datetime NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `manager`
--

INSERT INTO `manager` (`id`, `email`, `username`, `password`, `last_login_time`, `created`, `modified`) VALUES
(1, 'admin@mhgame.com', 'admin', 'deca41cb6617c38bf54e5748979ee1dfc8fa51d9', '0000-00-00 00:00:00', '0000-00-00 00:00:00', '2011-03-29 17:04:19');
