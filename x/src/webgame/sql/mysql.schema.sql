--
-- Table structure for table `cpuser`
--
CREATE TABLE IF NOT EXISTS `cpuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(128) NOT NULL,
  `webaddress` varchar(128) NOT NULL,
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
-- Table structure for table `UserAccounts`
--
CREATE TABLE IF NOT EXISTS `UserAccounts` (
  `UserID` int(11) NOT NULL AUTO_INCREMENT,
  `Accounts` varchar(32) NOT NULL,
  `RegAccounts` varchar(32) NOT NULL,
  `LogonPass` varchar(36) NOT NULL,
  `InsurePass` varchar(36) NOT NULL,
  `RegisterDate` datetime NOT NULL,
  PRIMARY KEY (`UserID`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8
AUTO_INCREMENT=100;
