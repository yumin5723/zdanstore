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
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

alter table brand add `ad_image` varchar(255) NOT NULL;


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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;


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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;



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
) ENGINE=MyISAM AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `brand_id` int(11) NOT NULL,
  `rank` tinyint(4) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `batch_number` varchar(64) NOT NULL, --pi hao --
  `quantity` int(11) NOT NULL,
  `total_price` Decimal(32, 2) NOT NULL, --yuan jia--
  `shop_price` Decimal(32,2) NOT NULL, --now price--
  `desc` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL, 
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
alter table product add `logo` varchar(255) NOT NULL;

--
-- Table structure for table `product_term`
--

CREATE TABLE `product_term` (
  `product_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `data` tinyint(2) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`product_id`,`term_id`),
  KEY `term_id` (`term_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `product_image`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `product_meta`
--

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `order`
--
CREATE TABLE IF NOT EXISTS `order` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `ip` varchar(255) NOT NULL,
  `address` int(11) NOT NULL,
  `billing_address` int(11) NOT NULL,
  `shipping` tinyint(4) NOT NULL DEFAULT '0',
  `payment` tinyint(4) NOT NULL DEFAULT '0',
  `payaccount` varchar(255) DEFAULT NULL, 
  `total_price` Decimal(32, 2) NOT NULL,
  `modified_uid` bigint(20) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`)
) AUTO_INCREMENT = 1000000000
ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `order_product`
--
CREATE TABLE IF NOT EXISTS `order_product` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `order_id` bigint(20) NOT NULL,
  `product_id` bigint(20),
  `product_quantity` int(11) NOT NULL,
  `product_price` Decimal(32, 2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`)
) AUTO_INCREMENT = 1000000000
ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into `order`(`uid`,`status`,`ip`,`address`,`total_price`,`modified_uid`,`created`,`modified`) values('19090','0','192.168.1.1','us sf','90.00','1','2013-07-18 10:22:54','2013-07-18 10:22:54');
insert into `order_product` (`order_id`,`product_id`,`product_quantity`,`product_price`) values('1000000000','1','2','25.00');
insert into `order_product` (`order_id`,`product_id`,`product_quantity`,`product_price`) values('1000000000','2','1','22.00');

  --
-- Table structure for table `cart`
--
CREATE TABLE IF NOT EXISTS `cart` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) NOT NULL,
  `product_id` bigint(20) NOT NULL,
  `quantity` int(11) NOT NULL,
  `meta` text NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  kEY `uid` (`uid`)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

alter table product add `is_new` tinyint(4) NOT NULL DEFAULT '0';
alter table product add `order` int(11) NOT NULL DEFAULT '0';
alter table product add `weight` int(11) NOT NULL DEFAULT '0';
alter table product add `give_points` int(11) NOT NULL DEFAULT '0';
alter table product add `points_buy` int(11) NOT NULL DEFAULT '0';
alter table product add `special_price` Decimal(32, 2) NOT NULL;
alter table product add `special_begin` datetime DEFAULT NULL;
alter table product add `special_end` datetime DEFAULT NULL; 
alter table product add `need_postage` tinyint(4) NOT NULL DEFAULT '0';
alter table product add `is_recommond` tinyint(4) NOT NULL DEFAULT '0';
alter table product add `is_recommond_mans` tinyint(4) NOT NULL DEFAULT '0';
alter table product add `is_recommond_womens` tinyint(4) NOT NULL DEFAULT '0';
alter table product add `is_recommond_hats` tinyint(4) NOT NULL DEFAULT '0';


--
-- Table structure for table `deliverynote`
--
CREATE TABLE IF NOT EXISTS `deliverynote` (
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
  kEY `uid` (`uid`)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `click`;
CREATE TABLE `click` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `url` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

--
-- Table structure for table `subject`
--
CREATE TABLE `subject` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL,
  `begin` datetime NOT NULL,
  `end` datetime NOT NULL,
  `params` text NOT NULL DEFAULT '',
  `product` varchar(255) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `brand_term` (
  `brand_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `term_id` bigint(20) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`brand_id`,`term_id`),
  KEY `term_id` (`term_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `term_profile`
--
CREATE TABLE `term_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `term_id` int(4) NOT NULL,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `term_id` (`term_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `newarrivals`
--
CREATE TABLE `newarrivals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `product_profile`
--
CREATE TABLE `product_profile` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `profile_id` int(11) NOT NULL,
  `profile_value` varchar(255) NOT NULL,
  `profile_image` varchar(255) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 CREATE TABLE `address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `firstname` varchar(200) NOT NULL,
  `lastname` varchar(200) NOT NULL,
  `address` varchar(255) NOT NULL,
  `zipcode` int(10) NOT NULL,
  `city` varchar(255) NOT NULL,
  `country` varchar(200) NOT NULL,
  `phone` varchar(150) NOT NULL,
  `default` tinyint(2) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;

CREATE TABLE `billing_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `firstname` varchar(200) NOT NULL,
  `lastname` varchar(200) NOT NULL,
  `address` varchar(255) NOT NULL,
  `zipcode` int(10) NOT NULL,
  `city` varchar(255) NOT NULL,
  `country` varchar(200) NOT NULL,
  `phone` varchar(150) NOT NULL,
  `default` tinyint(2) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;


CREATE TABLE `message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `content` text NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

