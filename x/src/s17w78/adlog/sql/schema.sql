--
-- Table structure for table `adlog`
--

CREATE TABLE IF NOT EXISTS `adlog` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link_id` int(11) NOT NULL,
  `ip` varchar(32) NOT NULL,
  `ua` text,
  `refer` varchar(256) NOT NULL DEFAULT '',
  `l_from` varchar(256) NOT NULL DEFAULT '',
  `click_time` int(11),
  PRIMARY KEY (`id`),
  KEY (`link_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;


--
-- Table structure for table `adlinks`
--

CREATE TABLE IF NOT EXISTS `adlink` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `prefix` varchar(128) NOT NULL,
  `description` text,
  `link` varchar(256),
  PRIMARY KEY (`id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;
