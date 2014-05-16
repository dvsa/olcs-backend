-- MySQL Administrator dump 1.4
--
-- ------------------------------------------------------
-- Server version	5.0.32-Debian_7etch5-log


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;


--
-- Create schema chxmlgateway
--

CREATE DATABASE IF NOT EXISTS chxmlgateway;
USE chxmlgateway;

--
-- Definition of table `company_details`
--

DROP TABLE IF EXISTS `company_details`;
CREATE TABLE `company_details` (
  `company_details_id` int(10) unsigned NOT NULL auto_increment,
  `request_id` int(10) unsigned NOT NULL,
  `cnumber` char(8) NOT NULL,
  `mtotals` tinyint(1) unsigned default NULL,
  PRIMARY KEY  (`company_details_id`),
  KEY `Index_2` (`request_id`),
  CONSTRAINT `FK_company_details_1` FOREIGN KEY (`request_id`) REFERENCES `request` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Definition of table `mortgages`
--

DROP TABLE IF EXISTS `mortgages`;
CREATE TABLE `mortgages` (
  `mortgages_id` int(10) unsigned NOT NULL auto_increment,
  `request_id` int(10) unsigned NOT NULL,
  `cname` varchar(160) NOT NULL,
  `cnumber` char(8) NOT NULL,
  `sat_charges` tinyint(1) unsigned default NULL,
  `start_date` date default NULL,
  `end_date` date default NULL,
  PRIMARY KEY  (`mortgages_id`),
  KEY `Index_2` (`request_id`),
  CONSTRAINT `FK_mortgages_1` FOREIGN KEY (`request_id`) REFERENCES `request` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Definition of table `name_search`
--

DROP TABLE IF EXISTS `name_search`;
CREATE TABLE `name_search` (
  `name_search_id` int(10) unsigned NOT NULL auto_increment,
  `request_id` int(10) unsigned NOT NULL,
  `cname` varchar(160) NOT NULL,
  `data_set` varchar(10) NOT NULL,
  `search_rows` smallint(5) unsigned default NULL,
  PRIMARY KEY  (`name_search_id`),
  KEY `Index_2` (`request_id`),
  CONSTRAINT `FK_name_search_1` FOREIGN KEY (`request_id`) REFERENCES `request` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Definition of table `number_search`
--

DROP TABLE IF EXISTS `number_search`;
CREATE TABLE `number_search` (
  `number_search_id` int(10) unsigned NOT NULL auto_increment,
  `request_id` int(10) unsigned NOT NULL,
  `part_cnumber` varchar(8) NOT NULL,
  `data_set` varchar(50) NOT NULL,
  `search_rows` smallint(5) unsigned default NULL,
  PRIMARY KEY  (`number_search_id`),
  KEY `Index_2` (`request_id`),
  CONSTRAINT `FK_number_search_1` FOREIGN KEY (`request_id`) REFERENCES `request` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Definition of table `officer_search`
--

DROP TABLE IF EXISTS `officer_search`;
CREATE TABLE `officer_search` (
  `officer_search_id` int(10) unsigned NOT NULL auto_increment,
  `request_id` int(10) unsigned NOT NULL,
  `surname` varchar(160) NOT NULL,
  `forename` varchar(50) default NULL,
  `forename2` varchar(50) default NULL,
  `post_town` varchar(50) default NULL,
  `officer_type` char(3) NOT NULL,
  `resigned` tinyint(1) unsigned default '0',
  PRIMARY KEY  (`officer_search_id`),
  KEY `Index_2` (`request_id`),
  CONSTRAINT `FK_officer_search_1` FOREIGN KEY (`request_id`) REFERENCES `request` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Definition of table `request`
--

DROP TABLE IF EXISTS `request`;
CREATE TABLE `request` (
  `request_id` int(10) unsigned NOT NULL auto_increment,
  `ip` varchar(23) NOT NULL,
  `host` varchar(50) NOT NULL,
  `xml_error` smallint(5) unsigned default NULL,
  `request_time` datetime NOT NULL,
  PRIMARY KEY  (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
