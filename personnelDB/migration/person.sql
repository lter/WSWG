/*
Navicat MySQL Data Transfer

Source Server         : marsh
Source Server Version : 50045
Source Host           : localhost:3306
Source Database       : lter_dbo

Target Server Type    : MYSQL
Target Server Version : 50045
File Encoding         : 65001

Date: 2011-02-23 15:21:48
*/

SET FOREIGN_KEY_CHECKS=0;
-- ----------------------------
-- Table structure for `person`
-- ----------------------------
DROP TABLE IF EXISTS `person`;
CREATE TABLE `person` (
  `personid` int(10) NOT NULL auto_increment,
  `password` varchar(100) default NULL,
  `title` varchar(80) default NULL,
  `firstname` varchar(30) default NULL,
  `middlename` varchar(20) default NULL,
  `lastname` varchar(50) default NULL,
  `nickname` varchar(50) default NULL,
  `nameprefix` varchar(12) default NULL,
  `namesuffix` varchar(12) default NULL,
  `primaryemail` varchar(80) default NULL,
  `username` varchar(50) default NULL,
  `address1` varchar(200) default NULL,
  `address2` varchar(200) default NULL,
  `address3` varchar(200) default NULL,
  `city` varchar(26) default NULL,
  `state` char(2) default NULL,
  `province` varchar(20) default NULL,
  `zip` varchar(12) default NULL,
  `country` varchar(30) default NULL,
  `phone1` varchar(30) default NULL,
  `phone2` varchar(30) default NULL,
  `pagernumber` varchar(30) default NULL,
  `fax` varchar(30) default NULL,
  `mobilephone` varchar(30) default NULL,
  `email1` varchar(80) default NULL,
  `email2` varchar(80) default NULL,
  `url` varchar(80) default NULL,
  `newsletter` varchar(3) default NULL,
  `password_hash` varchar(255) default NULL,
  `sessionid` varchar(50) default NULL,
  `newsletterold` varchar(3) default NULL,
  `persid` int(10) default NULL,
  `lternewsletter` tinyint(3) default NULL,
  `interim` tinyint(1) default NULL,
  `id` varchar(43) default NULL,
  `modifier` varchar(50) default NULL,
  `moddate` datetime default NULL,
  `insertdate` timestamp NULL default CURRENT_TIMESTAMP,
  `ipaddress` varchar(255) default NULL,
  `status` int(10) default '1',
  `md5password` varchar(50) default NULL,
  PRIMARY KEY  (`personid`),
  KEY `IX_person` (`personid`)
) ENGINE=InnoDB AUTO_INCREMENT=13299 DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of person
-- ----------------------------
