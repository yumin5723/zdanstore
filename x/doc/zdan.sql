-- MySQL dump 10.13  Distrib 5.1.69, for redhat-linux-gnu (x86_64)
--
-- Host: localhost    Database: zdan
-- ------------------------------------------------------
-- Server version	5.1.69

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
-- Table structure for table `brand`
--

DROP TABLE IF EXISTS `brand`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brand` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `desc` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `ad_image` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brand`
--

LOCK TABLES `brand` WRITE;
/*!40000 ALTER TABLE `brand` DISABLE KEYS */;
INSERT INTO `brand` VALUES (6,'ebay','ebay','http://www.zdanstore-test.com/upload/image/2013/10/b1--dbf27--.jpg','2013-10-25 13:45:50','2013-10-26 10:50:53','http://www.zdanstore-test.com/upload/image/2013/10/mans7--d29c2--.jpg'),(7,'Volcom','VolcomVolcom','http://www.zdanstore-test.com/upload/image/2013/10/b2--6b07b--.jpg','2013-10-26 23:17:23','2013-10-26 23:17:47','');
/*!40000 ALTER TABLE `brand` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `brand_term`
--

DROP TABLE IF EXISTS `brand_term`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `brand_term` (
  `brand_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`brand_id`,`term_id`),
  KEY `term_id` (`term_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `brand_term`
--

LOCK TABLES `brand_term` WRITE;
/*!40000 ALTER TABLE `brand_term` DISABLE KEYS */;
INSERT INTO `brand_term` VALUES (5,8,'2013-10-23 22:54:54','2013-10-23 22:54:54'),(5,12,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(6,16,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(6,17,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(6,28,'0000-00-00 00:00:00','0000-00-00 00:00:00'),(7,16,'0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `brand_term` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cart`
--

DROP TABLE IF EXISTS `cart`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cart` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `quantity` int(11) NOT NULL,
  `meta` text NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cart`
--

LOCK TABLES `cart` WRITE;
/*!40000 ALTER TABLE `cart` DISABLE KEYS */;
INSERT INTO `cart` VALUES (1,1,2,8,'a:2:{s:5:\"color\";s:3:\"red\";s:4:\"size\";s:1:\"x\";}','2013-09-04 20:00:45','2013-09-04 22:41:46'),(2,1,2,2,'a:2:{s:5:\"color\";s:5:\"black\";s:4:\"size\";s:1:\"x\";}','2013-09-04 20:01:04','2013-09-04 20:01:04'),(3,1,2,2,'a:2:{s:5:\"color\";s:5:\"white\";s:4:\"size\";s:1:\"m\";}','2013-09-04 20:01:48','2013-09-04 20:01:48'),(4,1,1,1,'s:0:\"\";','2013-09-04 22:40:36','2013-09-04 22:40:36'),(5,1,2,1,'s:0:\"\";','2013-09-04 22:40:39','2013-09-04 22:40:39'),(6,1,3,2,'s:0:\"\";','2013-09-04 22:41:53','2013-09-04 22:41:53');
/*!40000 ALTER TABLE `cart` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `click`
--

DROP TABLE IF EXISTS `click`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `click` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `url` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `click`
--

LOCK TABLES `click` WRITE;
/*!40000 ALTER TABLE `click` DISABLE KEYS */;
INSERT INTO `click` VALUES (1,'首页焦点图1','http://www.baidu.com','http://www.zdanstore-test.com/upload/image/2013/10/p1--ebb66--.jpg',1,'2013-10-25 13:32:36','2013-10-25 13:32:36'),(2,'首页焦点图2','http://www.baidu.com','http://www.zdanstore-test.com/upload/image/2013/10/p2--89b34--.jpg',1,'2013-10-25 13:32:56','2013-10-25 13:32:56'),(3,'首页右侧广告1','http://www.baidu.com','http://www.zdanstore-test.com/upload/image/2013/10/p3--dac1a--.jpg',2,'2013-10-25 13:36:49','2013-10-25 13:36:49'),(4,'首页右侧广告2','http://www.baidu.com','http://www.zdanstore-test.com/upload/image/2013/10/p4--baeb9--.jpg',2,'2013-10-25 13:37:07','2013-10-25 13:37:07'),(5,'首页尾部广告1','http://www.baidu.com','http://www.zdanstore-test.com/upload/image/2013/10/p4--baeb9--.jpg',3,'2013-10-25 13:41:12','2013-10-25 13:41:12'),(6,'首页尾部广告2','http://www.baidu.com','http://www.zdanstore-test.com/upload/image/2013/10/p5--18c63--.jpg',3,'2013-10-25 13:41:30','2013-10-25 13:41:30'),(7,'品牌页banner','http://www.baidu.com','http://www.zdanstore-test.com/upload/image/2013/10/mans7--d29c2--.jpg',4,'2013-10-25 21:19:08','2013-10-25 21:19:08'),(8,'I LOVE Haters ! Collections','http://www.baidu.com','http://www.zdanstore-test.com/upload/image/2013/10/m1--47a8b--.jpg',5,'2013-10-26 22:56:17','2013-10-26 22:56:17'),(9,'I LOVE Haters ! Collections','http://www.baidu.com','http://www.zdanstore-test.com/upload/image/2013/10/m1--47a8b--.jpg',6,'2013-10-26 22:56:52','2013-10-26 22:56:52'),(10,'I LOVE Haters ! Collections','http://www.baidu.com','http://www.zdanstore-test.com/upload/image/2013/10/m2--c756d--.jpg',6,'2013-10-26 22:57:11','2013-10-26 22:57:11'),(11,'I LOVE Haters ! Collections','http://www.baidu.com','http://www.zdanstore-test.com/upload/image/2013/10/m2--c756d--.jpg',7,'2013-10-26 22:57:30','2013-10-26 22:57:30');
/*!40000 ALTER TABLE `click` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `deliverynote`
--

DROP TABLE IF EXISTS `deliverynote`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deliverynote` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) NOT NULL,
  `express_number` varchar(64) NOT NULL,
  `uid` int(11) NOT NULL,
  `delivery_time` datetime DEFAULT NULL,
  `admin_uid` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`),
  KEY `uid` (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `deliverynote`
--

LOCK TABLES `deliverynote` WRITE;
/*!40000 ALTER TABLE `deliverynote` DISABLE KEYS */;
/*!40000 ALTER TABLE `deliverynote` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `game`
--

DROP TABLE IF EXISTS `game`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `game` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `tags` text,
  `source` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `desc` text NOT NULL,
  `operations_guide` text NOT NULL,
  `how_begin` text NOT NULL,
  `target` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `tag_image` varchar(255) NOT NULL,
  `recommend_image` varchar(255) DEFAULT '',
  `top_image` varchar(255) DEFAULT '',
  `recommend_value` int(11) DEFAULT NULL,
  `rank_value` int(11) DEFAULT NULL,
  `url` varchar(255) NOT NULL,
  `weights` tinyint(4) NOT NULL DEFAULT '0',
  `created_uid` int(11) NOT NULL,
  `modified_uid` int(11) NOT NULL,
  `publish_date` datetime NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `game`
--

LOCK TABLES `game` WRITE;
/*!40000 ALTER TABLE `game` DISABLE KEYS */;
/*!40000 ALTER TABLE `game` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `manager`
--

DROP TABLE IF EXISTS `manager`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL,
  `username` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `last_login_time` datetime NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `manager`
--

LOCK TABLES `manager` WRITE;
/*!40000 ALTER TABLE `manager` DISABLE KEYS */;
INSERT INTO `manager` VALUES (1,'admin@mhgame.com','admin','deca41cb6617c38bf54e5748979ee1dfc8fa51d9','2013-10-26 22:16:43','0000-00-00 00:00:00','2013-10-26 22:16:43');
/*!40000 ALTER TABLE `manager` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `newarrivals`
--

DROP TABLE IF EXISTS `newarrivals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `newarrivals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `newarrivals`
--

LOCK TABLES `newarrivals` WRITE;
/*!40000 ALTER TABLE `newarrivals` DISABLE KEYS */;
INSERT INTO `newarrivals` VALUES (3,'Vans 2013 Fall Shirts','http://www.baidu.com','2013-10-26 22:39:04','2013-10-26 22:39:04'),(4,'Beach Towels','http://www.baidu.com','2013-10-26 22:39:27','2013-10-26 22:39:27');
/*!40000 ALTER TABLE `newarrivals` ENABLE KEYS */;
UNLOCK TABLES;

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
  PRIMARY KEY (`object_id`),
  KEY `type_status_date` (`object_type`,`object_status`,`object_date`,`object_id`),
  KEY `object_parent` (`object_parent`),
  KEY `object_author` (`object_author`),
  KEY `object_name` (`object_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `object`
--

LOCK TABLES `object` WRITE;
/*!40000 ALTER TABLE `object` DISABLE KEYS */;
/*!40000 ALTER TABLE `object` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order`
--

DROP TABLE IF EXISTS `order`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `ip` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `total_price` decimal(32,2) NOT NULL,
  `modified_uid` bigint(20) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=1000000001 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order`
--

LOCK TABLES `order` WRITE;
/*!40000 ALTER TABLE `order` DISABLE KEYS */;
INSERT INTO `order` VALUES (1000000000,19090,2,'192.168.1.1','us sf','90.00',1,'2013-07-18 10:22:54','2013-09-05 23:33:54');
/*!40000 ALTER TABLE `order` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `order_product`
--

DROP TABLE IF EXISTS `order_product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `order_product` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) NOT NULL,
  `product_id` bigint(20) DEFAULT NULL,
  `product_quantity` int(11) NOT NULL,
  `product_price` decimal(32,2) NOT NULL,
  `product_meta` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1000000002 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `order_product`
--

LOCK TABLES `order_product` WRITE;
/*!40000 ALTER TABLE `order_product` DISABLE KEYS */;
INSERT INTO `order_product` VALUES (1000000000,1000000000,1,2,'25.00',''),(1000000001,1000000000,2,1,'22.00','');
/*!40000 ALTER TABLE `order_product` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `oterm`
--

LOCK TABLES `oterm` WRITE;
/*!40000 ALTER TABLE `oterm` DISABLE KEYS */;
INSERT INTO `oterm` VALUES (14,14,'商品','商品','','',0,1,34,1,'2013-10-25 14:36:25','2013-10-25 14:36:25'),(15,14,'Mens','Mens','Mens','',1,2,23,2,'2013-10-25 14:36:57','2013-10-25 14:36:57'),(16,14,'Man\'s Clothing','Man\'s Clothing','Man\'s Clothing','',1,3,14,3,'2013-10-25 22:06:01','2013-10-25 22:06:01'),(17,14,'Man\'s Accessories','Man\'s Accessories','Man\'s Accessories','',1,15,22,3,'2013-10-25 22:06:24','2013-10-25 22:06:24'),(18,14,'T-Shirts','T-Shirts','T-Shirts','',1,4,9,4,'2013-10-25 22:06:43','2013-10-25 22:06:43'),(19,14,'Polos','Polos','Polos','',1,10,11,4,'2013-10-25 22:07:01','2013-10-25 22:07:01'),(20,14,'Shirts','Shirts','Shirts','',1,12,13,4,'2013-10-25 22:07:19','2013-10-25 22:07:19'),(21,14,'Hats','Hats','Hats','',1,16,17,4,'2013-10-25 22:07:49','2013-10-25 22:07:49'),(22,14,'Shoes','Shoes','Shoes','',1,18,19,4,'2013-10-25 22:08:05','2013-10-25 22:08:05'),(23,14,'Underwears','Underwears','Underwears','',1,20,21,4,'2013-10-25 22:08:18','2013-10-25 22:08:18'),(24,14,'Long Slleeve T-shirt','Long Slleeve T-shirt','Long Slleeve T-shirt','',1,5,6,5,'2013-10-25 22:39:15','2013-10-25 22:39:15'),(25,14,'WOMENS','WOMENS','WOMENS','',1,24,27,2,'2013-10-26 10:28:14','2013-10-26 10:28:14'),(26,14,'Women\'s Clothing','Women\'s Clothing','Women\'s Clothing','',1,25,26,3,'2013-10-26 10:28:59','2013-10-26 10:28:59'),(27,14,'HATS','HATS','HATS','',1,28,33,2,'2013-10-26 10:39:53','2013-10-26 10:39:53'),(28,14,'hats','hats','hats','',1,29,32,3,'2013-10-26 10:40:14','2013-10-26 10:40:14'),(29,14,'Snapbacks','Snapbacks','Snapbacks','',1,30,31,4,'2013-10-26 10:48:27','2013-10-26 10:48:27'),(30,14,'Short Slleeve T-shirt','Short Slleeve T-shirt','Short Slleeve T-shirt','',1,7,8,5,'2013-10-26 11:53:41','2013-10-26 11:53:41');
/*!40000 ALTER TABLE `oterm` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product`
--

DROP TABLE IF EXISTS `product`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `rank` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `batch_number` varchar(64) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(32,2) NOT NULL,
  `shop_price` decimal(32,2) NOT NULL,
  `desc` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  `logo` varchar(255) NOT NULL,
  `is_new` tinyint(4) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '0',
  `weight` int(11) NOT NULL DEFAULT '0',
  `give_points` int(11) NOT NULL DEFAULT '0',
  `points_buy` int(11) NOT NULL DEFAULT '0',
  `special_end` datetime DEFAULT NULL,
  `special_begin` datetime DEFAULT NULL,
  `need_postage` tinyint(4) NOT NULL DEFAULT '0',
  `special_price` decimal(32,2) NOT NULL,
  `is_recommond` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product`
--

LOCK TABLES `product` WRITE;
/*!40000 ALTER TABLE `product` DISABLE KEYS */;
INSERT INTO `product` VALUES (6,'男士v领上衣',6,0,0,'',35,'50.00','11.00','sgsdgdfgdgdgsdfgsdfgsgdgd','2013-10-25 22:52:07','2013-10-25 22:53:52','http://www.zdanstore-test.com/upload/image/2013/10/s1--76917--.jpg',1,0,0,0,0,'0000-00-00 00:00:00','0000-00-00 00:00:00',0,'0.00',1),(7,'男士33333',6,0,0,'',43,'33.00','50.00','斯蒂芬打算发送','2013-10-25 23:01:52','2013-10-26 11:35:32','http://www.zdanstore-test.com/upload/image/2013/10/s1--76917--.jpg',1,0,0,0,0,'0000-00-00 00:00:00','0000-00-00 00:00:00',0,'0.00',1);
/*!40000 ALTER TABLE `product` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_image`
--

DROP TABLE IF EXISTS `product_image`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_image` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `image` varchar(255) NOT NULL,
  `image_type` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `image_type` (`image_type`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_image`
--

LOCK TABLES `product_image` WRITE;
/*!40000 ALTER TABLE `product_image` DISABLE KEYS */;
INSERT INTO `product_image` VALUES (16,2,'http://www.zdanstore-test.com/upload/image/2013/09/1_dreamzml--737c7--.jpg',0,'2013-09-01 16:31:03','2013-09-01 16:31:03'),(17,2,'http://www.zdanstore-test.com/upload/image/2013/09/b3119313b07eca807f914038902397dda044835c--8ec20--.jpg',0,'2013-09-01 16:31:03','2013-09-01 16:31:03'),(18,3,'http://www.zdanstore-test.com/upload/image/2013/09/1_dreamzml--737c7--.jpg',0,'2013-09-04 17:44:07','2013-09-04 17:44:07');
/*!40000 ALTER TABLE `product_image` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_meta`
--

DROP TABLE IF EXISTS `product_meta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_meta` (
  `meta_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `meta_product_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `meta_key` varchar(255) DEFAULT NULL,
  `meta_value` longtext,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`meta_id`),
  KEY `product_id` (`meta_product_id`),
  KEY `meta_key` (`meta_key`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_meta`
--

LOCK TABLES `product_meta` WRITE;
/*!40000 ALTER TABLE `product_meta` DISABLE KEYS */;
INSERT INTO `product_meta` VALUES (2,2,'color','red,black,white','2013-09-01 13:08:18','2013-09-01 13:08:18'),(3,2,'size','x,xxl,m','2013-09-01 13:52:28','2013-09-01 13:52:28');
/*!40000 ALTER TABLE `product_meta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `product_term`
--

DROP TABLE IF EXISTS `product_term`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `product_term` (
  `product_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `data` tinyint(2) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`product_id`,`term_id`),
  KEY `term_id` (`term_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `product_term`
--

LOCK TABLES `product_term` WRITE;
/*!40000 ALTER TABLE `product_term` DISABLE KEYS */;
INSERT INTO `product_term` VALUES (6,16,0,'2013-10-25 22:52:07','2013-10-25 22:52:07'),(7,16,0,'2013-10-25 23:01:52','2013-10-25 23:01:52'),(7,18,0,'0000-00-00 00:00:00','0000-00-00 00:00:00');
/*!40000 ALTER TABLE `product_term` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `profile`
--

DROP TABLE IF EXISTS `profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `profile` (
  `uid` int(11) NOT NULL,
  `avatar` varchar(255) NOT NULL,
  `small_avatar` varchar(255) NOT NULL,
  `birthday` varchar(64) NOT NULL,
  `address` varchar(64) NOT NULL,
  `game_stage` varchar(64) NOT NULL,
  `realname` varchar(64) NOT NULL,
  `idnumber` varchar(18) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `qq` varchar(11) NOT NULL,
  `edu` varchar(32) NOT NULL,
  `gender` int(10) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `profile`
--

LOCK TABLES `profile` WRITE;
/*!40000 ALTER TABLE `profile` DISABLE KEYS */;
/*!40000 ALTER TABLE `profile` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=MyISAM AUTO_INCREMENT=82 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `resource`
--

LOCK TABLES `resource` WRITE;
/*!40000 ALTER TABLE `resource` DISABLE KEYS */;
INSERT INTO `resource` VALUES (8,'1_dreamzml.jpg','','image/2013/08/1_dreamzml--737c7--.jpg','image','2013-08-30 12:11:48','2013-08-30 12:11:48',1,''),(9,'1_dreamzml.jpg','','image/2013/08/1_dreamzml--737c7--.jpg','image','2013-08-30 12:17:12','2013-08-30 12:17:12',1,''),(10,'1_dreamzml.jpg','','image/2013/08/1_dreamzml--737c7--.jpg','image','2013-08-30 12:31:03','2013-08-30 12:31:03',1,''),(11,'1_dreamzml.jpg','','image/2013/08/1_dreamzml--737c7--.jpg','image','2013-08-30 12:32:10','2013-08-30 12:32:10',1,''),(12,'1_dreamzml.jpg','','image/2013/08/1_dreamzml--737c7--.jpg','image','2013-08-30 13:14:13','2013-08-30 13:14:13',1,''),(13,'1_dreamzml.jpg','','image/2013/08/1_dreamzml--737c7--.jpg','image','2013-08-30 13:15:12','2013-08-30 13:15:12',1,''),(14,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 11:00:41','2013-09-01 11:00:41',1,''),(15,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 11:06:58','2013-09-01 11:06:58',1,''),(16,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 11:14:19','2013-09-01 11:14:19',1,''),(17,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 11:44:40','2013-09-01 11:44:40',1,''),(18,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 13:00:02','2013-09-01 13:00:02',1,''),(19,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 15:25:02','2013-09-01 15:25:02',1,''),(20,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 15:25:11','2013-09-01 15:25:11',1,''),(21,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 15:37:49','2013-09-01 15:37:49',1,''),(22,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 15:37:59','2013-09-01 15:37:59',1,''),(23,'b3119313b07eca807f914038902397dda044835c.jpg','','image/2013/09/b3119313b07eca807f914038902397dda044835c--8ec20--.jpg','image','2013-09-01 15:56:42','2013-09-01 15:56:42',1,''),(24,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 15:58:11','2013-09-01 15:58:11',1,''),(25,'b3119313b07eca807f914038902397dda044835c.jpg','','image/2013/09/b3119313b07eca807f914038902397dda044835c--8ec20--.jpg','image','2013-09-01 15:58:20','2013-09-01 15:58:20',1,''),(26,'b3119313b07eca807f914038902397dda044835c.jpg','','image/2013/09/b3119313b07eca807f914038902397dda044835c--8ec20--.jpg','image','2013-09-01 16:00:44','2013-09-01 16:00:44',1,''),(27,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 16:02:05','2013-09-01 16:02:05',1,''),(28,'b3119313b07eca807f914038902397dda044835c.jpg','','image/2013/09/b3119313b07eca807f914038902397dda044835c--8ec20--.jpg','image','2013-09-01 16:02:21','2013-09-01 16:02:21',1,''),(29,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 16:03:52','2013-09-01 16:03:52',1,''),(30,'b3119313b07eca807f914038902397dda044835c.jpg','','image/2013/09/b3119313b07eca807f914038902397dda044835c--8ec20--.jpg','image','2013-09-01 16:04:01','2013-09-01 16:04:01',1,''),(31,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 16:09:46','2013-09-01 16:09:46',1,''),(32,'b3119313b07eca807f914038902397dda044835c.jpg','','image/2013/09/b3119313b07eca807f914038902397dda044835c--8ec20--.jpg','image','2013-09-01 16:09:58','2013-09-01 16:09:58',1,''),(33,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 16:11:31','2013-09-01 16:11:31',1,''),(34,'b3119313b07eca807f914038902397dda044835c.jpg','','image/2013/09/b3119313b07eca807f914038902397dda044835c--8ec20--.jpg','image','2013-09-01 16:11:39','2013-09-01 16:11:39',1,''),(35,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 16:12:19','2013-09-01 16:12:19',1,''),(36,'b3119313b07eca807f914038902397dda044835c.jpg','','image/2013/09/b3119313b07eca807f914038902397dda044835c--8ec20--.jpg','image','2013-09-01 16:12:26','2013-09-01 16:12:26',1,''),(37,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 16:21:16','2013-09-01 16:21:16',1,''),(38,'b3119313b07eca807f914038902397dda044835c.jpg','','image/2013/09/b3119313b07eca807f914038902397dda044835c--8ec20--.jpg','image','2013-09-01 16:21:25','2013-09-01 16:21:25',1,''),(39,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 16:22:16','2013-09-01 16:22:16',1,''),(40,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 16:23:09','2013-09-01 16:23:09',1,''),(41,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 16:30:11','2013-09-01 16:30:11',1,''),(42,'b3119313b07eca807f914038902397dda044835c.jpg','','image/2013/09/b3119313b07eca807f914038902397dda044835c--8ec20--.jpg','image','2013-09-01 16:31:00','2013-09-01 16:31:00',1,''),(43,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 22:49:37','2013-09-01 22:49:37',1,''),(44,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 22:54:40','2013-09-01 22:54:40',1,''),(45,'b3119313b07eca807f914038902397dda044835c.jpg','','image/2013/09/b3119313b07eca807f914038902397dda044835c--8ec20--.jpg','image','2013-09-01 22:54:48','2013-09-01 22:54:48',1,''),(46,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-01 22:56:10','2013-09-01 22:56:10',1,''),(47,'b3119313b07eca807f914038902397dda044835c.jpg','','image/2013/09/b3119313b07eca807f914038902397dda044835c--8ec20--.jpg','image','2013-09-01 22:59:45','2013-09-01 22:59:45',1,''),(48,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-04 17:29:30','2013-09-04 17:29:30',1,''),(49,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-04 17:29:37','2013-09-04 17:29:37',1,''),(50,'b3119313b07eca807f914038902397dda044835c.jpg','','image/2013/09/b3119313b07eca807f914038902397dda044835c--8ec20--.jpg','image','2013-09-04 17:31:10','2013-09-04 17:31:10',1,''),(51,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-04 17:31:17','2013-09-04 17:31:17',1,''),(52,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-04 17:32:17','2013-09-04 17:32:17',1,''),(53,'b3119313b07eca807f914038902397dda044835c.jpg','','image/2013/09/b3119313b07eca807f914038902397dda044835c--8ec20--.jpg','image','2013-09-04 17:32:23','2013-09-04 17:32:23',1,''),(54,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-04 17:44:06','2013-09-04 17:44:06',1,''),(55,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-05 18:27:21','2013-09-05 18:27:21',1,''),(56,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-06 00:43:00','2013-09-06 00:43:00',1,''),(57,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-06 00:43:31','2013-09-06 00:43:31',1,''),(58,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-06 00:43:48','2013-09-06 00:43:48',1,''),(59,'1_dreamzml.jpg','','image/2013/09/1_dreamzml--737c7--.jpg','image','2013-09-06 00:44:35','2013-09-06 00:44:35',1,''),(60,'1_dreamzml.jpg','','image/2013/10/1_dreamzml--737c7--.jpg','image','2013-10-23 22:37:10','2013-10-23 22:37:10',1,''),(61,'1_dreamzml.jpg','','image/2013/10/1_dreamzml--737c7--.jpg','image','2013-10-23 22:37:21','2013-10-23 22:37:21',1,''),(62,'1_dreamzml.jpg','','image/2013/10/1_dreamzml--737c7--.jpg','image','2013-10-23 22:38:32','2013-10-23 22:38:32',1,''),(63,'bf48756361bcf00c0d33fa22.png','','image/2013/10/bf48756361bcf00c0d33fa22--c6ec3--.png','image','2013-10-23 22:38:47','2013-10-23 22:38:47',1,''),(64,'1_dreamzml.jpg','','image/2013/10/1_dreamzml--737c7--.jpg','image','2013-10-23 22:54:43','2013-10-23 22:54:43',1,''),(65,'bf48756361bcf00c0d33fa22.png','','image/2013/10/bf48756361bcf00c0d33fa22--c6ec3--.png','image','2013-10-23 22:54:51','2013-10-23 22:54:51',1,''),(66,'p1.jpg','','image/2013/10/p1--ebb66--.jpg','image','2013-10-25 13:32:33','2013-10-25 13:32:33',1,''),(67,'p2.jpg','','image/2013/10/p2--89b34--.jpg','image','2013-10-25 13:32:55','2013-10-25 13:32:55',1,''),(68,'p3.jpg','','image/2013/10/p3--dac1a--.jpg','image','2013-10-25 13:36:48','2013-10-25 13:36:48',1,''),(69,'p4.jpg','','image/2013/10/p4--baeb9--.jpg','image','2013-10-25 13:37:06','2013-10-25 13:37:06',1,''),(70,'p4.jpg','','image/2013/10/p4--baeb9--.jpg','image','2013-10-25 13:41:10','2013-10-25 13:41:10',1,''),(71,'p5.jpg','','image/2013/10/p5--18c63--.jpg','image','2013-10-25 13:41:29','2013-10-25 13:41:29',1,''),(72,'b1.jpg','','image/2013/10/b1--dbf27--.jpg','image','2013-10-25 13:45:45','2013-10-25 13:45:45',1,''),(73,'mans7.jpg','','image/2013/10/mans7--d29c2--.jpg','image','2013-10-25 21:19:06','2013-10-25 21:19:06',1,''),(74,'s1.jpg','','image/2013/10/s1--76917--.jpg','image','2013-10-25 22:51:38','2013-10-25 22:51:38',1,''),(75,'mans7.jpg','','image/2013/10/mans7--d29c2--.jpg','image','2013-10-25 22:55:07','2013-10-25 22:55:07',1,''),(76,'s1.jpg','','image/2013/10/s1--76917--.jpg','image','2013-10-25 23:01:29','2013-10-25 23:01:29',1,''),(77,'m1.jpg','','image/2013/10/m1--47a8b--.jpg','image','2013-10-26 22:56:15','2013-10-26 22:56:15',1,''),(78,'m1.jpg','','image/2013/10/m1--47a8b--.jpg','image','2013-10-26 22:56:51','2013-10-26 22:56:51',1,''),(79,'m2.jpg','','image/2013/10/m2--c756d--.jpg','image','2013-10-26 22:57:09','2013-10-26 22:57:09',1,''),(80,'m2.jpg','','image/2013/10/m2--c756d--.jpg','image','2013-10-26 22:57:29','2013-10-26 22:57:29',1,''),(81,'b2.jpg','','image/2013/10/b2--6b07b--.jpg','image','2013-10-26 23:17:11','2013-10-26 23:17:11',1,'');
/*!40000 ALTER TABLE `resource` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `subject`
--

DROP TABLE IF EXISTS `subject`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `subject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL,
  `begin` datetime NOT NULL,
  `end` datetime NOT NULL,
  `params` text NOT NULL,
  `product` varchar(255) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `subject`
--

LOCK TABLES `subject` WRITE;
/*!40000 ALTER TABLE `subject` DISABLE KEYS */;
INSERT INTO `subject` VALUES (2,1,'2013-09-09 00:00:00','2013-09-17 00:00:00','a:1:{s:9:\"order_amt\";s:3:\"100\";}','a:1:{s:5:\"brand\";a:1:{i:0;s:1:\"3\";}}','2013-09-09 16:30:41','2013-09-09 16:30:41'),(3,1,'2013-09-09 00:00:00','2013-09-17 00:00:00','a:1:{s:9:\"order_amt\";s:3:\"100\";}','a:1:{s:3:\"all\";s:3:\"all\";}','2013-09-09 16:31:48','2013-09-09 16:31:48'),(4,1,'2013-09-09 00:00:00','2013-09-17 00:00:00','a:2:{s:9:\"order_amt\";s:3:\"100\";s:7:\"cut_amt\";s:2:\"30\";}','a:1:{s:5:\"oterm\";s:0:\"\";}','2013-09-09 16:32:43','2013-09-09 16:32:43'),(5,1,'2013-09-09 00:00:00','2013-09-19 00:00:00','a:2:{s:9:\"order_amt\";s:3:\"100\";s:7:\"cut_amt\";s:2:\"30\";}','a:1:{s:5:\"brand\";a:2:{i:0;s:1:\"2\";i:1;s:1:\"3\";}}','2013-09-09 16:34:29','2013-09-09 16:34:29'),(6,1,'2013-09-09 00:00:00','2013-09-19 00:00:00','a:2:{s:9:\"order_amt\";s:3:\"100\";s:7:\"cut_amt\";s:2:\"30\";}','a:1:{s:5:\"oterm\";a:1:{i:0;s:2:\"11\";}}','2013-09-09 16:35:25','2013-09-09 16:35:25'),(7,1,'2013-09-09 00:00:00','2013-09-12 00:00:00','a:2:{s:9:\"order_amt\";s:3:\"100\";s:7:\"cut_amt\";s:2:\"30\";}','a:1:{s:3:\"all\";s:3:\"all\";}','2013-09-09 16:37:01','2013-09-09 16:37:01'),(8,1,'2013-09-09 00:00:00','2013-09-25 00:00:00','a:2:{s:9:\"order_amt\";s:3:\"100\";s:7:\"cut_amt\";s:2:\"30\";}','a:1:{s:3:\"all\";s:3:\"all\";}','2013-09-09 16:37:28','2013-09-09 16:37:28');
/*!40000 ALTER TABLE `subject` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `term_profile`
--

DROP TABLE IF EXISTS `term_profile`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `term_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term_id` int(4) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `term_id` (`term_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `term_profile`
--

LOCK TABLES `term_profile` WRITE;
/*!40000 ALTER TABLE `term_profile` DISABLE KEYS */;
INSERT INTO `term_profile` VALUES (9,16,'COLOR','RED,WHITE,BLACK','2013-10-26 18:59:06','2013-10-26 18:59:06'),(10,16,'SIZE','X,XXL,XXXL','2013-10-26 19:02:31','2013-10-26 19:02:31');
/*!40000 ALTER TABLE `term_profile` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(64) DEFAULT NULL,
  `password` varchar(128) NOT NULL,
  `email` varchar(128) NOT NULL,
  `email_confirmed` varchar(32) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  `nickname` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=19091 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (19090,'taylor swift','','','0',NULL,NULL,'');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_reset_pass`
--

DROP TABLE IF EXISTS `user_reset_pass`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_reset_pass` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `code` varchar(128) NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_reset_pass`
--

LOCK TABLES `user_reset_pass` WRITE;
/*!40000 ALTER TABLE `user_reset_pass` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_reset_pass` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-10-29 16:56:02
