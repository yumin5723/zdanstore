--
-- Table structure for table `category`
--
CREATE TABLE `category` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `root` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `name` varchar(64) NOT NULL,
  `short_name` varchar(64) NOT NULL,
  `parent_id` int(11) NOT NULL,
  `left_id` int(11) DEFAULT '0',
  `right_id` int(11) DEFAULT '0',
  `admin_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `root` (`root`),
  KEY `left_id` (`left_id`),
  KEY `right_id` (`right_id`),
  KEY `level` (`level`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

--
-- Table structure for table `page`
--

DROP TABLE IF EXISTS `page`;
CREATE TABLE `page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `page_name` varchar(255) NOT NULL,
  `path` varchar(128) NOT NULL,
  `file` text,
  `domain` varchar(128) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 

--
-- Table structure for table `content`
--

DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text,
  `excerpt` text,
  `date` datetime NOT NULL,
  `author` varchar(255) DEFAULT NULL,
  `admin_id` int(11) NOT NULL,
  `modified_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8; 

--
-- Table structure for table `gxc_object_term`
--

CREATE TABLE `article_category` (
  `article_id` bigint(20) NOT NULL DEFAULT '0',
  `category_id` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`article_id`,`category_id`),
  KEY `category_id` (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `article_resource`
--
CREATE TABLE `article_resource` (
  `article_id` bigint(20) NOT NULL,
  `resource_id` bigint(20) NOT NULL,
  `resource_order` int(11) NOT NULL DEFAULT '0',
  `description` longtext,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`article_id`,`resource_id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `taxonomy`
--
CREATE TABLE `taxonomy` (
  `taxonomy_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'article',
  `lang` tinyint(4) DEFAULT '1',
  `guid` varchar(255) NOT NULL,
  PRIMARY KEY (`taxonomy_id`),
  KEY `taxonomy` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `term`
--
CREATE TABLE `term` (
  `term_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `taxonomy_id` int(20) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `slug` varchar(255) NOT NULL DEFAULT '',
  `parent` bigint(20) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`term_id`),
  KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

