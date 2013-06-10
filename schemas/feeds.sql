SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;


DELIMITER $$
DROP FUNCTION IF EXISTS `SPLIT_STR`$$
CREATE DEFINER=`root`@`192.168.168.56` FUNCTION `SPLIT_STR`(x VARCHAR(255),delim VARCHAR(12),pos INT) RETURNS varchar(255) CHARSET latin1
    DETERMINISTIC
BEGINRETURN REPLACE(SUBSTRING(SUBSTRING_INDEX(X, DELIM, POS),       LENGTH(SUBSTRING_INDEX(x, delim, pos -1)) +1),        delim, '');END$$

DELIMITER ;

DROP TABLE IF EXISTS `BUSINESS_LISTINGS`;
CREATE TABLE IF NOT EXISTS `BUSINESS_LISTINGS` (
  `StoreID` int(11) NOT NULL AUTO_INCREMENT,
  `StoreName` text,
  `MainPhoneNum` text,
  `Address1` text,
  `Address2` text,
  `City` text,
  `State` text,
  `PostCode` text,
  `Country` char(2) DEFAULT NULL,
  `URL` text,
  `Category` text,
  `Hours` text,
  `Description` text,
  `Currency` text,
  `EstablishedDate` text,
  `Latitude` float DEFAULT NULL,
  `Longitude` float DEFAULT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`StoreID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=13 ;

DROP TABLE IF EXISTS `ITEM_COUNTS`;
CREATE TABLE IF NOT EXISTS `ITEM_COUNTS` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_type` varchar(255) NOT NULL,
  `item_count` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=839 ;

DROP TABLE IF EXISTS `LOCAL_PRODUCT_LISTINGS`;
CREATE TABLE IF NOT EXISTS `LOCAL_PRODUCT_LISTINGS` (
  `ProductID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` text,
  `webitemid` varchar(255) NOT NULL,
  `GTIN` text,
  `MPN` text,
  `Brand` text,
  `Price` float DEFAULT NULL,
  `ProductCondition` text,
  `URL` text,
  `ImageURL` text,
  `Size` text,
  `Color` text,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ProductID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5367 ;

DROP TABLE IF EXISTS `ONLINE_LISTINGS`;
CREATE TABLE IF NOT EXISTS `ONLINE_LISTINGS` (
  `ItemID` int(11) NOT NULL AUTO_INCREMENT,
  `Link` text,
  `ItemCondition` text,
  `Brand` text,
  `Title` text,
  `Description` text,
  `ImageLink` text,
  `ProductType` text,
  `GoogleProductCategory` text,
  `Price` float DEFAULT NULL,
  `Availability` text,
  `ExpirationDate` text,
  `OldID` text,
  `MPN` text,
  `GTIN` text,
  `Color` text,
  `Size` text,
  `ShippingWeight` text,
  `shipping` varchar(15) NOT NULL,
  `gender` varchar(20) NOT NULL,
  `age_group` varchar(20) NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ItemID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7323 ;

DROP TABLE IF EXISTS `PRICE_QUANTITY`;
CREATE TABLE IF NOT EXISTS `PRICE_QUANTITY` (
  `StoreProductID` int(11) NOT NULL AUTO_INCREMENT,
  `StoreID` int(11) DEFAULT NULL,
  `ProductID` int(11) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `PriceOverride` float DEFAULT NULL,
  `Availability` text,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`StoreProductID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=64393 ;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
