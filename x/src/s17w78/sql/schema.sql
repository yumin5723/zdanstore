--
-- Table structure for table `game`
--

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
alter table game add `recommend_value` int(11) DEFAULT "" after `tag_image`;
alter table game add `rank_value` int(11) DEFAULT "" after `recommend_value`;
alter table game add `recommend_image` varchar(255) DEFAULT "" after `tag_image`;
alter table game add `top_image` varchar(255) DEFAULT "" after `recommend_image`;

--
-- Table structure for table `game_taxonomy`
--
CREATE TABLE `game_taxonomy` (
  `taxonomy_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'game',
  `lang` tinyint(4) DEFAULT '1',
  `guid` varchar(255) NOT NULL,
  PRIMARY KEY (`taxonomy_id`),
  KEY `taxonomy` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `game_term`
--
CREATE TABLE `game_term` (
  `term_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `taxonomy_id` int(20) unsigned NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `slug` varchar(255) NOT NULL DEFAULT '',
  `parent` bigint(20) NOT NULL DEFAULT '0',
  `order` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`term_id`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `mobile_game` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `version` varchar(255) NOT NULL,
  `apply` varchar(255) NOT NULL,
  `size` varchar(255) NOT NULL,
  `lang` varchar(255) NOT NULL,
  `developers` varchar(255) NOT NULL,
  `resolution` varchar(255) NOT NULL,
  `advertising` varchar(255) NOT NULL,
  `times` int(11) NOT NULL DEFAULT '0',
  `category_id` int(11) NOT NULL,
  `url_to_pc` varchar(255) NOT NULL,
  `url_to_mobile` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `tag_image` varchar(255) NOT NULL,
  `recommend_image` varchar(255) NOT NULL,
  `top_image` varchar(255) NOT NULL,
  `created_uid` int(11) NOT NULL,
  `modified_uid` int(11) NOT NULL,
  `publish_date` datetime NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `category` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `short_name` varchar(255) DEFAULT '',
  `description` varchar(255) DEFAULT '',
  `admin_id` INT(10) UNSIGNED NOT NULL,
  `left_id` INT(10) UNSIGNED NOT NULL,
  `right_id` INT(10) UNSIGNED NOT NULL,
  `level` SMALLINT(5) UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `left_id` (`left_id`),
  KEY `right_id` (`right_id`),
  KEY `level` (`level`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;
alter table category add `root` int(11) NOT NULL after `id`;
insert into category(`root`,`name`,`short_name`,`description`,`admin_id`,`left_id`,`right_id`,`level`) values('1','游戏库','游戏库','游戏库','1','1','2','1');

CREATE TABLE `product_category` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `short_name` varchar(255) DEFAULT '',
  `description` varchar(255) DEFAULT '',
  `admin_id` INT(10) UNSIGNED NOT NULL,
  `left_id` INT(10) UNSIGNED NOT NULL,
  `right_id` INT(10) UNSIGNED NOT NULL,
  `level` SMALLINT(5) UNSIGNED NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `left_id` (`left_id`),
  KEY `right_id` (`right_id`),
  KEY `level` (`level`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

alter table product_category add `root` int(11) NOT NULL after `id`;
insert into product_category(`root`,`name`,`short_name`,`description`,`admin_id`,`left_id`,`right_id`,`level`) values('1','游戏库','游戏库','游戏库','1','1','2','1');

alter table mobile_game add `recommend_image` varchar(255) DEFAULT NULL after `tag_image`;
alter table mobile_game add `top_image` varchar(255) DEFAULT NULL after `recommend_image`;
alter table mobile_game add `price` varchar(255) DEFAULT "" after `times`;
alter table mobile_game add `recommend_value` int(11) DEFAULT NULL after `recommend_image`;
alter table mobile_game add `rank_value` int(11) DEFAULT NULL after `recommend_value`;

alter table mobile_game change url_to_mobile url_to_mobile varchar(255) NOT NULL DEFAULT '';
alter table mobile_game change price price varchar(255) NOT NULL DEFAULT '';
alter table mobile_game change recommend_value recommend_value int(11) NOT NULL DEFAULT '1';
alter table mobile_game change rank_value rank_value int(11) NOT NULL DEFAULT '1';


CREATE TABLE `product` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `name_pinyin` varchar(255) NOT NULL,
  `category_id` int(11) NOT NULL,
  `down_url` varchar(255) NOT NULL,
  `desc` text NOT NULL,
  `image` varchar(255) NOT NULL,
  `tag_image` varchar(255) NOT NULL,
  `index_image` varchar(255) NOT NULL,
  `created_uid` int(11) NOT NULL,
  `modified_uid` int(11) NOT NULL,
  `publish_date` datetime NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `game_related` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `group_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE `game_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `area_id` int(11) NOT NULL,
  `type_id` varchar(255) NOT NULL,
  `values` text NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

alter table game_rules change `values` `desc` text NOT NULL;

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
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
) 
ENGINE=InnoDB  DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `user_reset_pass`;
CREATE TABLE IF NOT EXISTS `user_reset_pass` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `code` varchar(128) NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `profile`
--

CREATE TABLE IF NOT EXISTS `profile` (
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


--
-- Table structure for table `activecode`
--
CREATE TABLE `activecode` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `package_id` int(11) NOT NULL,
  `code` varchar(128) NOT NULL,
  `batch_number` varchar(64) NOT NULL,
  `uid` int(11) DEFAULT NULL,
  `ip` varchar(128) NOT NULL,
  `used_time` datetime NOT NULL,
  `status` tinyint(4) DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`),
  KEY `packpage_id` (`package_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;


CREATE TABLE `activecode_batch` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `batch_number` varchar(64) NOT NULL,
  `package_id` int(11) NOT NULL,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `package_id` (`package_id`),
  KEY `batch_number` (`batch_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `package` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `game_name` varchar(64) NOT NULL DEFAULT '',
  `related` varchar(64) NOT NULL,
  `down_url` varchar(64) NOT NULL,
  `index_url` varchar(64) NOT NULL,
  `desc` text NOT NULL,
  `detail` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `publish_date` datetime NOT NULL,
  `image` varchar(255) NOT NULL,
  `tag_image` varchar(255) NOT NULL,
  `recommend_image` varchar(255) DEFAULT '',
  `created_uid` int(11) NOT NULL,
  `modified_uid` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

alter table package add `activate_url` varchar(64) NOT NULL DEFAULT "" after `index_url`;

alter table package change related related varchar(255) NOT NULL;
alter table package change down_url down_url varchar(255) NOT NULL;
alter table package change index_url index_url varchar(255) NOT NULL;
alter table package change activate_url activate_url varchar(255) NOT NULL;


CREATE TABLE `package_rules` (
  `package_id` int(11) NOT NULL AUTO_INCREMENT,
  `probability` int(11) NOT NULL DEFAULT '100',
  `limit` int(11) NOT NULL DEFAULT '50',
  PRIMARY KEY (`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `package_recommend` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `value` varchar(30) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

insert into package_recommend(`value`) values("");

CREATE TABLE `rank` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL DEFAULT '0',
  `value` text NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `cpuser`
--
CREATE TABLE IF NOT EXISTS `cpuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL,
  `cpname` varchar(128) NOT NULL,
  `password` varchar(128) NOT NULL,
  `cp_key` varchar(255) NOT NULL,
  `md_pwd` tinyint(1) DEFAULT '0',
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY (`email`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8
AUTO_INCREMENT=100;

--
-- Table structure for table `apps`
--
CREATE TABLE IF NOT EXISTS `app` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cp_id` int(11) NOT NULL,
  `app_name` varchar(128) NOT NULL,
  `short_name` varchar(32) NOT NULL,
  `charge_chn` varchar(32) NOT NULL,
  `charge_url` varchar(255) NOT NULL,
  `charge_id`  varchar(128) NOT NULL,
  `charge_key` varchar(128) NOT NULL,
  `gate_chn` varchar(32) NOT NULL,
  `gate_url` varchar(255) NOT NULL,
  `gate_id` varchar(128) NOT NULL,
  `gate_key` varchar(128) NOT NULL,
  `created` datetime NOT NULL,
  `modified` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY (`cp_id`)
)AUTO_INCREMENT=1000
ENGINE=InnoDB DEFAULT CHARSET=utf8;

alter table `app` add `tel` varchar(255) NOT NULL after `gate_key`;
alter table `app` add `charge_amount` varchar(255) DEFAULT "" after `status`;

-- Table structure for table `usergold`
-- Holds the user gold
--
CREATE TABLE IF NOT EXISTS `usergold` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,                       -- User ID
  `gold` decimal(32,2) NOT NULL,        -- Current gold
  `max_gold` decimal(32,2) NOT NULL,    -- Out of a maximum gold
  `tid` int(11) NOT NULL DEFAULT '0',           -- Category ID
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `modified` (`modified`),
  KEY `gold` (`gold`),
  UNIQUE KEY `uid_tid` (`uid`, `tid`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `usergold_total`
-- Holds the total user gold
--
CREATE TABLE IF NOT EXISTS `usergold_total` (
  `uid` int(11) NOT NULL,                       -- User ID
  `gold` decimal(32,2) NOT NULL,        -- Current gold
  `max_gold` decimal(32,2) NOT NULL, -- Out of a maximum gold
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`uid`),
  KEY `modified` (`modified`),
  KEY `gold` (`gold`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `usergold_txn`
-- UserGold Transactions
--
CREATE TABLE IF NOT EXISTS `usergold_txn` (
  `id` int(11) NOT NULL AUTO_INCREMENT,         -- Transaction ID
  `uid` int(11) NOT NULL,                       -- User ID
  `approver_uid` int(11) NOT NULL DEFAULT '0',  -- Moderator User ID
  `gold` decimal(32,2) NOT NULL,        -- gold
  `status` tinyint(4) NOT NULL DEFAULT '0',     -- Status
  `description` text,
  `reference` varchar(128),
  `expirydate` datetime NOT NULL,               -- Expirydate
  `expired` tinyint(4) NOT NULL DEFAULT '0',    -- Expiration status
  `parent_txn_id` int(11) NOT NULL DEFAULT '0',  -- Parent Transaction ID
  `tid` int(11) NOT NULL DEFAULT '0',            -- Category ID
  `entity_id` INT(11) NOT NULL DEFAULT '0'  ,    -- ID of an entity in the database
  `entity_type` varchar(128)   ,                 -- Type of entity
  `operation` varchar(48)   ,                    -- Operation being carried out
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY (`operation`),
  KEY (`reference`),
  KEY `status_expired_expiry` (`status`, `expired`, `expirydate`),
  KEY (`modified`),
  KEY (`uid`),
  KEY (`approver_uid`),
  KEY (`gold`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `order`
--
CREATE TABLE IF NOT EXISTS `order` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `subject` varchar(128) NOT NULL,
  `charge_param` varchar(255) NOT NULL,
  `pay_param` varchar(255) NOT NULL,
  `order_type` tinyint(4) NOT NULL,
  `order_from` varchar(32) NOT NULL,
  `order_amt` Decimal(32, 2) NOT NULL, -- 订单面值
  `pay_amt` Decimal(32, 2) NOT NULL,   -- 支付面值
  `real_amt` Decimal(32, 2) NOT NULL,  -- 实际可收取面值
  `charge_amt` Decimal(32, 2) NOT NULL,-- 充值面值
  `pay_status` tinyint(4) NOT NULL,
  `pay_id` int(11) DEFAULT NULL,
  `app_id` int(11) DEFAULT '0',
  `cp_id` int(11) DEFAULT '0',
  `charge_id` int(11) DEFAULT NULL,
  `charge_status` tinyint(4) NOT NULL,
  `next_charge_time` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY (`pay_status`, `charge_status`),
  KEY (`cp_id`)
) AUTO_INCREMENT = 1000000000
ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `pay`
--
CREATE TABLE IF NOT EXISTS `pay` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `pay_method` varchar(255) NOT NULL,
  `pay_return` blob DEFAULT NULL,
  `pay_msg` varchar(255) DEFAULT "",
  `pay_time` datetime DEFAULT NULL,
  `pay_callback` blob DEFAULT NULL,
  `pay_callback_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `charge`
--
CREATE TABLE IF NOT EXISTS `charge` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `charge_time` datetime NOT NULL,
  `charge_return` blob DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY (`order_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table structure for table `openuser`
--
CREATE TABLE IF NOT EXISTS `openuser` (
  `app_id` int(21) NOT NULL,
  `uid` int(21) NOT NULL,
  `openid` varchar(64) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`app_id`, `uid`),
  UNIQUE KEY (`openid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Table structure for table `userbind`
--

CREATE TABLE IF NOT EXISTS `userbind` (
  `platform_uid` varchar(255) NOT NULL,
  `platform` varchar(64) NOT NULL,
  `uid` int(21) DEFAULT NULL,
  `platform_name` varchar(128) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`platform_uid`)
) 
ENGINE=InnoDB  DEFAULT CHARSET=utf8;

CREATE TABLE `feedback` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `contact` varchar(30) NOT NULL,
  `created` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
--
-- Table structure for table `userplayed`
--

CREATE TABLE IF NOT EXISTS `userplayed` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `app_id` int(21) NOT NULL,
  `playtime` varchar(128) NOT NULL,
  `last_play_time` datetime DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) 
ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Table structure for table `testuser`
--

CREATE TABLE IF NOT EXISTS `testuser` (
  `uid` int(11) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`uid`)
) 
ENGINE=InnoDB  DEFAULT CHARSET=utf8;

--
-- Table structure for table `userplayed`
--

CREATE TABLE IF NOT EXISTS `webgame_term` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) 
ENGINE=InnoDB  DEFAULT CHARSET=utf8;

alter table app add `type` int(11) NOT NULL;
alter table app add `status` tinyint(4) NOT NULL;

-- Table structure for table `gameactive`
--

CREATE TABLE IF NOT EXISTS `gameactive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL,
  `name` varchar(64) NOT NULL,
  `gname` varchar(64) NOT NULL,
  `desc` text NOT NULL,
  `time` varchar(64) NOT NULL,
  `image` varchar(255) NOT NULL,
  `url` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT "0",
  `created` datetime DEFAULT NULL,
  `modified` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;
--
-- Table structure for table `user_play_log`
--
CREATE TABLE IF NOT EXISTS `user_play_log` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `uid` int(11) NOT NULL,
   `app_id` int(11) NOT NULL,
   `start_time` datetime NOT NULL,
   `end_time`  datetime NOT NULL,
   PRIMARY KEY (`id`),
   KEY (`uid`, `app_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;


--
-- Table structure for table `user_play_time`
--
CREATE TABLE IF NOT EXISTS `user_play_time` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `uid` int(11) NOT NULL,
   `app_id` int(11) NOT NULL,
   `all_time` int(11) NOT NULL DEFAULT '0',
   PRIMARY KEY (`id`),
   UNIQUE KEY (`uid`, `app_id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;

alter table user add `is_reg` tinyint(4) DEFAULT "0";

--
-- Table structure for table `user_play_log`
--
CREATE TABLE IF NOT EXISTS `test_atlas_part_table_0` (
   `id` int(11) NOT NULL AUTO_INCREMENT,
   `tid` int(11) NOT NULL,
   PRIMARY KEY (`id`)
)ENGINE=InnoDB  DEFAULT CHARSET=utf8;



