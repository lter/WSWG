-- ----------------------------------------------------------------------
-- MySQL Migration Toolkit
-- SQL Create Script
-- ----------------------------------------------------------------------

SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `LTER_dbo`
  CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `LTER_dbo`;
-- -------------------------------------
-- Tables

DROP TABLE IF EXISTS `LTER_dbo`.`asm_gs_lunch`;
CREATE TABLE `LTER_dbo`.`asm_gs_lunch` (
  `asmgslunchid` INT(10) NOT NULL AUTO_INCREMENT,
  `host` INT(10) NOT NULL,
  `cohost` INT(10) NULL,
  `lunchday` VARCHAR(50) NULL,
  `numberofpeople` INT(10) NULL,
  `room` VARCHAR(50) NULL,
  `status` VARCHAR(50) NULL,
  PRIMARY KEY (`asmgslunchid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`asm_gs_lunch_schedule`;
CREATE TABLE `LTER_dbo`.`asm_gs_lunch_schedule` (
  `asmgslunchscheduleid` INT(10) NOT NULL AUTO_INCREMENT,
  `asmgslunchid` INT(10) NULL,
  `personid` INT(10) NULL,
  `insertdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `moddate` DATETIME NULL,
  PRIMARY KEY (`asmgslunchscheduleid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`asm_poster`;
CREATE TABLE `LTER_dbo`.`asm_poster` (
  `posterid` INT(10) NOT NULL AUTO_INCREMENT,
  `personid` INT(10) NOT NULL,
  `postertitle` VARCHAR(255) NOT NULL,
  `posterleadauthor` VARCHAR(50) NOT NULL,
  `postercoauthors` VARCHAR(255) NULL,
  `posterabstract` LONGTEXT NOT NULL,
  `institutions` VARCHAR(255) NULL,
  `preferreddate` VARCHAR(50) NULL,
  `assignedday` CHAR(10) NULL,
  `assignednumber` INT(10) NULL,
  `ipaddress` VARCHAR(255) NOT NULL,
  `assignedid` VARCHAR(3) NULL,
  `assignedgroup` VARCHAR(50) NULL,
  `insertdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `moddate` DATETIME NULL,
  `primarysite` VARCHAR(4) NULL,
  `comments` VARCHAR(255) NULL,
  `accepted` VARCHAR(12) NULL,
  PRIMARY KEY (`posterid`),
  CONSTRAINT `FK_asm_poster_person` FOREIGN KEY `FK_asm_poster_person` (`personid`)
    REFERENCES `dbo`.`person` (`personid`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`asm_workshop`;
CREATE TABLE `LTER_dbo`.`asm_workshop` (
  `workshopid` INT(10) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(300) NULL,
  `personid` INT(10) NOT NULL,
  `abstract` LONGTEXT NULL,
  `comments` LONGTEXT NULL,
  `proposeddate` VARCHAR(100) NULL,
  `keywords` VARCHAR(500) NULL,
  `moddate` DATETIME NULL,
  `insertdate` DATETIME NULL,
  `keyparticipants` LONGTEXT NULL,
  `assigneddate` VARCHAR(50) NULL,
  `assignedtime` VARCHAR(50) NULL,
  `sessionid` VARCHAR(50) NULL,
  `modifier` VARCHAR(50) NULL,
  `room` VARCHAR(50) NULL,
  PRIMARY KEY (`workshopid`),
  CONSTRAINT `FK_asm_workshop_person` FOREIGN KEY `FK_asm_workshop_person` (`personid`)
    REFERENCES `dbo`.`person` (`personid`)
    ON DELETE NO ACTION
    ON UPDATE CASCADE
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`asm_worshop_schedule`;
CREATE TABLE `LTER_dbo`.`asm_worshop_schedule` (
  `asmworshopscheduleid` INT(10) NOT NULL AUTO_INCREMENT,
  `personid` INT(10) NOT NULL,
  `fridaypm` INT(10) NULL,
  `saturdayam` INT(10) NULL,
  `saturdaypm` INT(10) NULL,
  `sundayam` INT(10) NULL,
  `sundaypm` INT(10) NULL,
  PRIMARY KEY (`asmworshopscheduleid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`bailey`;
CREATE TABLE `LTER_dbo`.`bailey` (
  `baileyid` INT(10) NOT NULL AUTO_INCREMENT,
  `baileynumber` VARCHAR(10) NOT NULL,
  `baileyname` VARCHAR(255) NULL,
  `moddate` DATETIME NULL,
  `insertdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`baileyid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`biome`;
CREATE TABLE `LTER_dbo`.`biome` (
  `biomeid` INT(10) NOT NULL AUTO_INCREMENT,
  `biomename` VARCHAR(255) NULL,
  `moddate` DATETIME NULL,
  `insertdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`biomeid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`comments`;
CREATE TABLE `LTER_dbo`.`comments` (
  `commentsid` INT(10) NOT NULL,
  `personid` INT(10) NOT NULL,
  `emailalias` VARCHAR(50) NULL,
  `commenttitle` VARCHAR(250) NOT NULL,
  `comment` LONGTEXT NOT NULL,
  `url` VARCHAR(250) NOT NULL,
  `insertdate` DATETIME NOT NULL
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`dtproperties`;
CREATE TABLE `LTER_dbo`.`dtproperties` (
  `id` INT(10) NOT NULL AUTO_INCREMENT,
  `objectid` INT(10) NULL,
  `property` VARCHAR(64) NOT NULL,
  `value` VARCHAR(255) NULL,
  `lvalue` LONGBLOB NULL,
  `version` INT(10) NOT NULL DEFAULT 0,
  `uvalue` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
  PRIMARY KEY (`id`, `property`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`emailalternate`;
CREATE TABLE `LTER_dbo`.`emailalternate` (
  `emailalternateid` INT(10) NOT NULL AUTO_INCREMENT,
  `emailalias` VARCHAR(50) NULL,
  `eaddress` VARCHAR(80) NULL,
  PRIMARY KEY (`emailalternateid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`field_station`;
CREATE TABLE `LTER_dbo`.`field_station` (
  `fieldstationid` INT(10) NOT NULL AUTO_INCREMENT,
  `siteid` INT(10) NULL,
  `fieldstationidtext` VARCHAR(5) NULL,
  `fieldstationname` VARCHAR(80) NULL,
  `latdegrees` CHAR(10) NULL,
  `latminutes` CHAR(10) NULL,
  `latseconds` CHAR(10) NULL,
  `longdegrees` CHAR(10) NULL,
  `longminutes` CHAR(10) NULL,
  `longseconds` CHAR(10) NULL,
  `lowelevation` CHAR(10) NULL,
  `highelevation` CHAR(10) NULL,
  `areaowned` VARCHAR(50) NULL,
  `researcharea` VARCHAR(50) NULL,
  `directions` LONGTEXT NULL,
  `busaccess` CHAR(1) NULL,
  `vehicleaccess` VARCHAR(500) NULL,
  `stationopen` CHAR(10) NULL,
  `stationclose` CHAR(10) NULL,
  `freshwaterbody` CHAR(1) NULL,
  `waterdescription` VARCHAR(200) NULL,
  `marineaccess` CHAR(1) NULL,
  `marineaccessdescription` VARCHAR(200) NULL,
  `dorms` CHAR(1) NULL,
  `cabins` CHAR(1) NULL,
  `camping` CHAR(1) NULL,
  `trailerhookups` CHAR(1) NULL,
  `occupancyfacilities` VARCHAR(200) NULL,
  `classrooms` CHAR(1) NULL,
  `avequipment` CHAR(1) NULL,
  `computers` CHAR(1) NULL,
  `netaccess` CHAR(1) NULL,
  `educationalfacilities` VARCHAR(200) NULL,
  `drylab` CHAR(1) NULL,
  `wetlab` CHAR(1) NULL,
  `researchfacilities` CHAR(10) NULL,
  `foodservice` CHAR(1) NULL,
  `foodfacilities` VARCHAR(200) NULL,
  `k12program` CHAR(1) NULL,
  `k12dayvisits` CHAR(1) NULL,
  `numberannualvisitors` CHAR(4) NULL,
  `k12extendeddayvisits` CHAR(4) NULL,
  `kto2percent` CHAR(3) NULL,
  `threetofivepercent` CHAR(3) NULL,
  `sixtoeightpercent` CHAR(3) NULL,
  `ninetotwelvepercent` CHAR(3) NULL,
  `k12teachertrainingdays` CHAR(7) NULL,
  `k12researchdays` CHAR(7) NULL,
  `k12activities` VARCHAR(200) NULL,
  `teachertraining` CHAR(1) NULL,
  `numberundergradcourses` VARCHAR(4) NULL,
  `numberundergradenrolled` VARCHAR(4) NULL,
  `teachingfirstmonth` VARCHAR(3) NULL,
  `teachinglastmonth` VARCHAR(3) NULL,
  `residentfaculty` CHAR(1) NULL,
  `residentfacultynumber` VARCHAR(4) NULL,
  `visitingfaculty` CHAR(1) NULL,
  `visitingfacultynumber` VARCHAR(4) NULL,
  `visitingclassfacilities` CHAR(1) NULL,
  `maxclasssize` VARCHAR(4) NULL,
  `researchplan` CHAR(1) NULL,
  `publicationlist` CHAR(1) NULL,
  `datamanagementplan` CHAR(1) NULL,
  `specieslist` CHAR(1) NULL,
  `climatedata` CHAR(1) NULL,
  `annualreport` CHAR(1) NULL,
  `sitemasterplan` CHAR(1) NULL,
  `undergradresearch` CHAR(1) NULL,
  `numberundergradresearch` VARCHAR(50) NULL,
  `paidundergradresearch` VARCHAR(1) NULL,
  `researchkeywords` VARCHAR(200) NULL,
  `facultykeywords` CHAR(10) NULL,
  `researchprogramdescription` LONGTEXT NULL,
  `contactfirstname` VARCHAR(30) NULL,
  `contactmiddlename` VARCHAR(20) NULL,
  `contactlastname` VARCHAR(50) NULL,
  `contactnameprefix` VARCHAR(12) NULL,
  `contactnamesuffix` VARCHAR(12) NULL,
  `contactnametitle` VARCHAR(80) NULL,
  `address1` VARCHAR(200) NULL,
  `address2` VARCHAR(200) NULL,
  `address3` VARCHAR(200) NULL,
  `city` VARCHAR(26) NULL,
  `state` VARCHAR(2) NULL,
  `province` VARCHAR(20) NULL,
  `zip` VARCHAR(12) NULL,
  `country` VARCHAR(30) NULL,
  `phone1` VARCHAR(30) NULL,
  `phone2` VARCHAR(30) NULL,
  `fax` VARCHAR(30) NULL,
  `mobilephone` VARCHAR(30) NULL,
  `email` VARCHAR(80) NULL,
  `url` VARCHAR(100) NULL,
  `comments` VARCHAR(50) NULL,
  `moddate` DATETIME NULL,
  `insertdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`fieldstationid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`group_organization`;
CREATE TABLE `LTER_dbo`.`group_organization` (
  `grouporganizationid` INT(10) NOT NULL AUTO_INCREMENT,
  `groupid` INT(10) NULL,
  `organization` VARCHAR(50) NULL,
  `insertdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`grouporganizationid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`groups`;
CREATE TABLE `LTER_dbo`.`groups` (
  `groupid` INT(10) NOT NULL AUTO_INCREMENT,
  `groupname` VARCHAR(200) NULL,
  `groupdescription` VARCHAR(200) NULL,
  `grouptype` VARCHAR(50) NULL DEFAULT 'basic',
  `groupownertext` VARCHAR(100) NULL,
  `groupowner` INT(10) NULL,
  `groupparent` VARCHAR(200) NULL,
  `publiclysubscribe` CHAR(1) NULL,
  `publiclyview` CHAR(1) NULL,
  `status` VARCHAR(1) NULL,
  `maillist` CHAR(1) NULL,
  `mailrestrictlist` VARCHAR(300) NULL,
  `mailmoderate` CHAR(1) NULL,
  `mailmoderater` VARCHAR(100) NULL,
  `maillistfronter` LONGTEXT NULL,
  `maillistfooter` LONGTEXT NULL,
  `mailarchive` CHAR(1) NULL,
  `moddate` DATETIME NULL,
  `insertdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`groupid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`iltertable`;
CREATE TABLE `LTER_dbo`.`iltertable` (
  `iltertableid` INT(10) NOT NULL AUTO_INCREMENT,
  `emailalias` VARCHAR(43) NULL,
  `primarysite` VARCHAR(5) NULL,
  `primaryrole` VARCHAR(50) NULL,
  `specialty` VARCHAR(120) NULL,
  `personid` INT(10) NULL,
  `primarynetwork` CHAR(2) NULL,
  `ilter` CHAR(1) NULL,
  `insertdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `moddate` DATETIME NULL,
  PRIMARY KEY (`iltertableid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`isocountrycodes`;
CREATE TABLE `LTER_dbo`.`isocountrycodes` (
  `autocountrycode` INT(10) NOT NULL AUTO_INCREMENT,
  `countrycode` CHAR(10) NULL,
  `countryname` VARCHAR(50) NULL,
  PRIMARY KEY (`autocountrycode`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`ldap`;
CREATE TABLE `LTER_dbo`.`ldap` (
  `ldaptableid` INT(10) NOT NULL AUTO_INCREMENT,
  `personid` INT(10) NOT NULL,
  `emailalias` VARCHAR(50) NULL,
  `obfsuid` VARCHAR(50) NULL,
  `actiontype` INT(10) NOT NULL,
  `firstname` VARCHAR(50) NULL,
  `lastname` VARCHAR(50) NULL,
  `primarysite` VARCHAR(50) NULL,
  `postaladdress1` VARCHAR(50) NULL,
  `postaladdress2` VARCHAR(50) NULL,
  `postaladdress3` VARCHAR(50) NULL,
  `city` VARCHAR(50) NULL,
  `state` VARCHAR(50) NULL,
  `province` VARCHAR(50) NULL,
  `zip` VARCHAR(50) NULL,
  `country` VARCHAR(50) NULL,
  `telephonenumber` VARCHAR(50) NULL,
  `fax` VARCHAR(50) NULL,
  `primaryemail` VARCHAR(50) NULL,
  `specialty` VARCHAR(50) NULL,
  `password` VARCHAR(50) NULL,
  `moddate` INT(10) NULL,
  PRIMARY KEY (`ldaptableid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`ltergroup_type`;
CREATE TABLE `LTER_dbo`.`ltergroup_type` (
  `ltergrouptypeid` INT(10) NOT NULL AUTO_INCREMENT,
  `grouptypename` VARCHAR(100) NULL,
  `grouptypedescription` LONGTEXT NULL,
  `insertdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`ltergrouptypeid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`metstationtype`;
CREATE TABLE `LTER_dbo`.`metstationtype` (
  `metstationtypeid` INT(10) NOT NULL AUTO_INCREMENT,
  `moddate` DATETIME NULL,
  `insertdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`metstationtypeid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`network`;
CREATE TABLE `LTER_dbo`.`network` (
  `networkid` INT(10) NOT NULL AUTO_INCREMENT,
  `networkname` VARCHAR(50) NULL,
  `networkdescription` LONGTEXT NULL,
  `countrycode` CHAR(2) NULL,
  PRIMARY KEY (`networkid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`newsletter_temp`;
CREATE TABLE `LTER_dbo`.`newsletter_temp` (
  `tempkey` INT(10) NOT NULL,
  `firstname` VARCHAR(50) NULL,
  `lastname` VARCHAR(50) NULL,
  `email` VARCHAR(50) NULL,
  `ipaddress` VARCHAR(255) NULL,
  `lternewsletter` TINYINT(3) NULL,
  `tempaction` VARCHAR(50) NULL
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`person_group`;
CREATE TABLE `LTER_dbo`.`person_group` (
  `persongroupid` INT(10) NOT NULL AUTO_INCREMENT,
  `personid` INT(10) NULL,
  `groupid` INT(10) NULL,
  `status` INT(10) NULL,
  `chair` CHAR(1) NULL DEFAULT 'n',
  `moddate` DATETIME NULL,
  `insertdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `site` CHAR(3) NULL,
  PRIMARY KEY (`persongroupid`),
  CONSTRAINT `FK_person_group_groups` FOREIGN KEY `FK_person_group_groups` (`groupid`)
    REFERENCES `LTER_dbo`.`groups` (`groupid`)
    ON DELETE CASCADE
    ON UPDATE NO ACTION
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`person_site_contact`;
CREATE TABLE `LTER_dbo`.`person_site_contact` (
  `personsitecontactid` INT(10) NOT NULL AUTO_INCREMENT,
  `siteid` INT(10) NULL,
  `personid` INT(10) NULL,
  `contacttype` VARCHAR(50) NULL,
  PRIMARY KEY (`personsitecontactid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`persontemp`;
CREATE TABLE `LTER_dbo`.`persontemp` (
  `persontempid` INT(10) NOT NULL AUTO_INCREMENT,
  `tempkey` INT(10) NULL,
  `firstname` VARCHAR(50) NOT NULL,
  `lastname` VARCHAR(50) NOT NULL,
  `primaryemail` VARCHAR(50) NOT NULL,
  `ipaddress` VARCHAR(255) NOT NULL,
  `insertdate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `confirmed` CHAR(3) NULL,
  `confirmdate` VARCHAR(50) NULL,
  `referer` VARCHAR(255) NULL,
  PRIMARY KEY (`persontempid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`research_site`;
CREATE TABLE `LTER_dbo`.`research_site` (
  `researchsiteid` INT(10) NOT NULL AUTO_INCREMENT,
  `siteid` INT(10) NOT NULL,
  `siteidtext` VARCHAR(3) NOT NULL,
  `research_site_name` VARCHAR(255) NULL,
  `type` VARCHAR(100) NOT NULL,
  `description` LONGTEXT NULL,
  `parentresearchsiteid` INT(10) NULL,
  `area` VARCHAR(53) NULL,
  `latitude_degrees` VARCHAR(50) NULL,
  `latitude_minutes` VARCHAR(50) NULL,
  `latitude_seconds` VARCHAR(50) NULL,
  `longitude_degrees` VARCHAR(50) NULL,
  `longitude_minutes` VARCHAR(50) NULL,
  `longitude_seconds` VARCHAR(50) NULL,
  `latitude_nw_degrees` VARCHAR(50) NULL,
  `latitude_nw_minutes` VARCHAR(50) NULL,
  `latitude_nw_seconds` VARCHAR(50) NULL,
  `longitude_nw_degrees` VARCHAR(50) NULL,
  `longitude_nw_minutes` VARCHAR(50) NULL,
  `longitude_nw_seconds` VARCHAR(50) NULL,
  `latitude_se_degrees` VARCHAR(50) NULL,
  `latitude_se_minutes` VARCHAR(50) NULL,
  `latitude_se_seconds` VARCHAR(50) NULL,
  `longitude_se_degrees` VARCHAR(50) NULL,
  `longitude_se_minutes` VARCHAR(50) NULL,
  `longitude_se_seconds` VARCHAR(50) NULL,
  `low_elevation` VARCHAR(53) NULL,
  `mean_elevation` VARCHAR(53) NULL,
  `high_elevation` VARCHAR(53) NULL,
  `disturbance_regime` LONGTEXT NULL,
  `research_site_history` LONGTEXT NULL,
  `research_site_url` VARCHAR(255) NULL,
  `moddate` DATETIME NULL,
  `insertdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`researchsiteid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`research_site_descriptor`;
CREATE TABLE `LTER_dbo`.`research_site_descriptor` (
  `researchsitedescriptorid` INT(10) NOT NULL AUTO_INCREMENT,
  `researchsiteid` INT(10) NULL,
  `descriptorname` VARCHAR(200) NULL,
  PRIMARY KEY (`researchsitedescriptorid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`research_site_descriptor_type`;
CREATE TABLE `LTER_dbo`.`research_site_descriptor_type` (
  `research_site_descriptor_typeid` INT(10) NOT NULL AUTO_INCREMENT,
  `researchsitetypeid` INT(10) NULL,
  `descriptorname` VARCHAR(200) NULL,
  `descriptordescription` VARCHAR(500) NULL,
  PRIMARY KEY (`research_site_descriptor_typeid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`research_site_type`;
CREATE TABLE `LTER_dbo`.`research_site_type` (
  `researchsitetypeid` INT(10) NOT NULL AUTO_INCREMENT,
  `typename` VARCHAR(100) NULL,
  `typedescription` VARCHAR(500) NULL,
  PRIMARY KEY (`researchsitetypeid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`security`;
CREATE TABLE `LTER_dbo`.`security` (
  `securityid` INT(10) NOT NULL AUTO_INCREMENT,
  `personid` INT(10) NULL,
  `emailalias` VARCHAR(50) NULL,
  `securitylevel` VARCHAR(50) NULL,
  `securitygroup` VARCHAR(50) NULL,
  PRIMARY KEY (`securityid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`site_affiliation`;
CREATE TABLE `LTER_dbo`.`site_affiliation` (
  `siteaffiliationid` INT(10) NOT NULL AUTO_INCREMENT,
  `siteid` VARCHAR(3) NULL,
  `affiliationid` VARCHAR(50) NULL,
  `moddate` DATETIME NULL,
  `insertdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`siteaffiliationid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`site_bailey`;
CREATE TABLE `LTER_dbo`.`site_bailey` (
  `sitebaileyid` INT(10) NOT NULL AUTO_INCREMENT,
  `siteid` INT(10) NOT NULL,
  `baileyid` VARCHAR(10) NULL,
  `moddate` DATETIME NULL,
  `insertdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sitebaileyid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`site_biome`;
CREATE TABLE `LTER_dbo`.`site_biome` (
  `sitebiomeid` INT(10) NULL,
  `siteid` INT(10) NULL,
  `biomeid` INT(10) NULL,
  `moddate` DATETIME NULL,
  `insertdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`site_contact`;
CREATE TABLE `LTER_dbo`.`site_contact` (
  `sitecontactid` INT(10) NOT NULL AUTO_INCREMENT,
  `siteid` INT(10) NULL,
  `address1` VARCHAR(200) NULL,
  `address2` VARCHAR(200) NULL,
  `address3` VARCHAR(200) NULL,
  `city` VARCHAR(26) NULL,
  `state` VARCHAR(2) NULL,
  `province` VARCHAR(20) NULL,
  `zip` VARCHAR(12) NULL,
  `country` VARCHAR(30) NULL,
  `phone1` VARCHAR(30) NULL,
  `phone2` VARCHAR(30) NULL,
  `fax` VARCHAR(30) NULL,
  `email1` VARCHAR(80) NULL,
  `email2` VARCHAR(80) NULL,
  `url` VARCHAR(80) NULL,
  `contacttypeid` INT(10) NOT NULL,
  `modifier` VARCHAR(50) NULL,
  `moddate` DATETIME NULL,
  `insertdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`sitecontactid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`site_organization`;
CREATE TABLE `LTER_dbo`.`site_organization` (
  `siteorganizationid` INT(10) NOT NULL AUTO_INCREMENT,
  `siteid` INT(10) NULL,
  `siteidtext` VARCHAR(50) NULL,
  `organization` VARCHAR(50) NULL,
  `insertdate` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`siteorganizationid`)
)
ENGINE = INNODB;

DROP TABLE IF EXISTS `LTER_dbo`.`workshop`;
CREATE TABLE `LTER_dbo`.`workshop` (
  `workshopid` INT(10) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(300) NULL,
  `organizer` VARCHAR(300) NULL,
  `abstract` VARCHAR(1000) NULL,
  `description` LONGTEXT NULL,
  `proposeddate` VARCHAR(100) NULL,
  `keywords` VARCHAR(500) NULL,
  PRIMARY KEY (`workshopid`)
)
ENGINE = INNODB;



SET FOREIGN_KEY_CHECKS = 1;

-- ----------------------------------------------------------------------
-- EOF

