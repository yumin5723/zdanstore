-- MySQL dump 10.13  Distrib 5.5.29, for Linux (x86_64)
--
-- Host: localhost    Database: fhback
-- ------------------------------------------------------
-- Server version 5.5.29-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `game`
--

-- DROP TABLE IF EXISTS `game`;
-- /*!40101 SET @saved_cs_client     = @@character_set_client */;
-- /*!40101 SET character_set_client = utf8 */;
-- CREATE TABLE `game` (
--   `id` int(11) NOT NULL AUTO_INCREMENT,
--   `name` varchar(255) NOT NULL,
--   `introduction` varchar(255) NOT NULL,
--   `downurl` varchar(255) NOT NULL,
--   `term` varchar(64) NOT NULL,
--   `created` datetime DEFAULT NULL,
--   `modified` datetime DEFAULT NULL,
--   PRIMARY KEY (`id`)
-- ) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
-- /*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `object`
--

DROP TABLE IF EXISTS `object`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `object` (
  `object_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `object_author` bigint(20) unsigned DEFAULT '0',
  `object_modified_uid` bigint(20) unsigned DEFAULT '0',
  `object_date` datetime NOT NULL,
  `object_date_gmt` datetime NOT NULL,
  `object_content` longtext,
  `ishot` tinyint(4) NOT NULL DEFAULT '0',
  `object_title` text,
  `object_excerpt` text,
  `object_status` tinyint(4) NOT NULL DEFAULT '1',
  `comment_status` tinyint(4) NOT NULL DEFAULT '1',
  `object_password` varchar(20) DEFAULT NULL,
  `object_name` varchar(255) NOT NULL,
  `object_modified` datetime NOT NULL,
  `object_modified_gmt` datetime NOT NULL,
  `object_content_filtered` text,
  `object_parent` bigint(20) unsigned NOT NULL DEFAULT '0',
  `guid` varchar(255) NOT NULL DEFAULT '',
  `object_type` varchar(20) NOT NULL DEFAULT 'object',
  `comment_count` bigint(20) NOT NULL DEFAULT '0',
  `object_slug` varchar(255) DEFAULT NULL,
  `object_description` text,
  `object_keywords` text,
  `lang` tinyint(4) DEFAULT '1',
  `object_author_name` varchar(255) DEFAULT NULL,
  `total_number_meta` tinyint(3) NOT NULL,
  `total_number_resource` tinyint(3) NOT NULL,
  `tags` text,
  `object_view` int(11) NOT NULL DEFAULT '0',
  `like` int(11) NOT NULL DEFAULT '0',
  `dislike` int(11) NOT NULL DEFAULT '0',
  `rating_scores` int(11) NOT NULL DEFAULT '0',
  `rating_average` float NOT NULL DEFAULT '0',
  `layout` varchar(125) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `istop` tinyint(4) NOT NULL DEFAULT '0',
  `isred` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`),
  KEY `type_status_date` (`object_type`,`object_status`,`object_date`,`object_id`),
  KEY `object_parent` (`object_parent`),
  KEY `object_author` (`object_author`),
  KEY `object_name` (`object_name`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
--
-- Table structure for table `gxc_object_meta`
--

CREATE TABLE `object_meta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `meta_object_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  PRIMARY KEY (`meta_id`),
  KEY `object_id` (`meta_object_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `object_bak`
--

DROP TABLE IF EXISTS `object_bak`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `object_bak` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `object_id` bigint(20) NOT NULL,
  `object_modified_uid` bigint(20) unsigned DEFAULT '0',
  `object_content` longtext,
  `object_title` text,
  `object_excerpt` text,
  `object_author_name` varchar(255) DEFAULT NULL,
  `tags` text,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `object_resource`
--

DROP TABLE IF EXISTS `object_resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `object_resource` (
  `object_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `resource_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `resource_order` int(11) NOT NULL DEFAULT '0',
  `description` longtext,
  `type` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`object_id`,`resource_id`),
  KEY `resource_id` (`resource_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `object_templete`
--

DROP TABLE IF EXISTS `object_templete`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `object_templete` (
  `object_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `templete_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `data` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`templete_id`),
  KEY `templete_id` (`templete_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `object_term`
--

DROP TABLE IF EXISTS `object_term`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `object_term` (
  `object_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `data` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`object_id`,`term_id`),
  KEY `term_id` (`term_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oterm`
--

DROP TABLE IF EXISTS `oterm`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oterm` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `root` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `short_name` varchar(255) DEFAULT '',
  `description` varchar(255) DEFAULT '',
  `url` varchar(255) NOT NULL,
  `admin_id` int(10) unsigned NOT NULL,
  `left_id` int(10) unsigned NOT NULL,
  `right_id` int(10) unsigned NOT NULL,
  `level` smallint(5) unsigned NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `left_id` (`left_id`),
  KEY `right_id` (`right_id`),
  KEY `level` (`level`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `page`
--

DROP TABLE IF EXISTS `page`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `path` varchar(255) NOT NULL,
  `rar_file` varchar(128) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `content` longblob NOT NULL,
  `title` varchar(255) NOT NULL,
  `keywords` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `modified_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `page_term`
--

DROP TABLE IF EXISTS `page_term`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `page_term` (
  `page_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `type` varchar(64) NOT NULL DEFAULT 'active',
  `data` tinyint(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`page_id`,`term_id`),
  KEY `term_id` (`term_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `resource`
--

DROP TABLE IF EXISTS `resource`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `resource` (
  `resource_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `resource_name` varchar(255) DEFAULT '',
  `resource_body` text,
  `resource_path` varchar(255) DEFAULT '',
  `resource_type` varchar(50) DEFAULT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `creator` bigint(20) NOT NULL,
  `where` varchar(50) NOT NULL,
  PRIMARY KEY (`resource_id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tag`
--

DROP TABLE IF EXISTS `tag`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `frequency` int(11) DEFAULT '1',
  `slug` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `name` (`name`),
  KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `tag_relationships`
--

DROP TABLE IF EXISTS `tag_relationships`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `tag_relationships` (
  `tag_id` bigint(20) NOT NULL,
  `object_id` bigint(20) NOT NULL,
  PRIMARY KEY (`tag_id`,`object_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `templete`
--

DROP TABLE IF EXISTS `templete`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `templete` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `type` varchar(64) NOT NULL,
  `rar_file` varchar(128) NOT NULL,
  `admin_id` int(11) NOT NULL,
  `modified_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `content` blob NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

CREATE TABLE `obj_dependence` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `obj_type` VARCHAR(16) NOT NULL,
  `dep_type` VARCHAR(16) NOT NULL,
  `obj_id` INT(11) NOT NULL,
  `dep_id` INT(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`obj_type`,`obj_id`,`dep_type`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `block`
--
CREATE TABLE `block` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `params` text NOT NULL DEFAULT '',
  `content` blob NOT NULL,
  `html` blob NOT NULL,
  `updated` datetime DEFAULT NULL,
  `created_id` int(11) NOT NULL,
  `modified_id` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-04-24 11:10:43

INSERT INTO `page`(`id`,`name`,`domain`,`status`,`path`) values('1','首页','www.fhgame.com','1','index.html');
INSERT INTO `page`(`id`,`name`,`domain`,`status`,`path`) values('2','下载页','www.fhgame.com','1','download.html');
INSERT INTO `page`(`id`,`name`,`domain`,`status`,`path`) values('3','活动专区页','www.fhgame.com','1','activity.html');
INSERT INTO `page`(`id`,`name`,`domain`,`status`,`path`) values('4','帮助中心','www.fhgame.com','1','help.html');