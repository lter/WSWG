SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `person`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `person` ;

CREATE  TABLE IF NOT EXISTS `person` (
  `personID` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `prefix` VARCHAR(255) NULL ,
  `firstName` VARCHAR(255) NULL ,
  `middleName` VARCHAR(255) NULL ,
  `lastName` VARCHAR(255) NOT NULL ,
  `suffix` VARCHAR(255) NULL ,
  `preferredName` VARCHAR(255) NULL ,
  `primaryEmail` VARCHAR(255) NOT NULL ,
  `optOut` BIT(1) NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`personID`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `nameAlias`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `nameAlias` ;

CREATE  TABLE IF NOT EXISTS `nameAlias` (
  `aliasID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `personID` INT(10) UNSIGNED NOT NULL ,
  `nameAlias` VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (`aliasID`, `personID`) ,
  CONSTRAINT `fk_person_nameAlias`
    FOREIGN KEY (`personID` )
    REFERENCES `person` (`personID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE INDEX `fk_person_nameAlias` ON `nameAlias` (`personID` ASC) ;


-- -----------------------------------------------------
-- Table `nsfRoleType`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `nsfRoleType` ;

CREATE  TABLE IF NOT EXISTS `nsfRoleType` (
  `nsfRoleTypeID` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `nsfRoleType` VARCHAR(255) NOT NULL ,
  `isRepeatable` BIT(1) NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`nsfRoleTypeID`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `site`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `site` ;

CREATE  TABLE IF NOT EXISTS `site` (
  `siteID` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `site` VARCHAR(255) NOT NULL ,
  `siteAcronym` CHAR(3) NOT NULL ,
  `notificationEmail` VARCHAR(255) NULL ,
  PRIMARY KEY (`siteID`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `localRoleType`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `localRoleType` ;

CREATE  TABLE IF NOT EXISTS `localRoleType` (
  `localRoleTypeID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `scope` INT(10) UNSIGNED NOT NULL ,
  `localRoleType` VARCHAR(255) NOT NULL ,
  `isRepeatable` BIT(1) NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`localRoleTypeID`, `scope`) ,
  CONSTRAINT `fk_site_localRoleType`
    FOREIGN KEY (`scope` )
    REFERENCES `site` (`siteID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE INDEX `fk_site_localRoleType` ON `localRoleType` (`scope` ASC) ;


-- -----------------------------------------------------
-- Table `nsfRole`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `nsfRole` ;

CREATE  TABLE IF NOT EXISTS `nsfRole` (
  `nsfRoleID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `personID` INT(10) UNSIGNED NOT NULL ,
  `nsfRoleTypeID` INT(10) UNSIGNED NOT NULL ,
  `siteID` INT(10) UNSIGNED NOT NULL ,
  `beginDate` DATE NULL DEFAULT NULL ,
  `endDate` DATE NULL DEFAULT NULL ,
  `isActive` BIT(1) NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`nsfRoleID`, `personID`, `nsfRoleTypeID`, `siteID`) ,
  CONSTRAINT `fk_nsfRoleType_nsfRole`
    FOREIGN KEY (`nsfRoleTypeID` )
    REFERENCES `nsfRoleType` (`nsfRoleTypeID` ),
  CONSTRAINT `fk_person_nsfRole`
    FOREIGN KEY (`personID` )
    REFERENCES `person` (`personID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_site_nsfRole`
    FOREIGN KEY (`siteID` )
    REFERENCES `site` (`siteID` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE INDEX `fk_person_nsfRole` ON `nsfRole` (`personID` ASC) ;

CREATE INDEX `fk_nsfRoleType_nsfRole` ON `nsfRole` (`nsfRoleTypeID` ASC) ;

CREATE INDEX `fk_site_nsfRole` ON `nsfRole` (`siteID` ASC) ;


-- -----------------------------------------------------
-- Table `localRole`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `localRole` ;

CREATE  TABLE IF NOT EXISTS `localRole` (
  `localRoleID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `personID` INT(10) UNSIGNED NOT NULL ,
  `localRoleTypeID` INT(10) UNSIGNED NOT NULL ,
  `siteID` INT(10) UNSIGNED NOT NULL ,
  `beginDate` DATE NULL DEFAULT NULL ,
  `endDate` DATE NULL DEFAULT NULL ,
  `isActive` BIT(1) NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`localRoleID`, `personID`, `localRoleTypeID`, `siteID`) ,
  CONSTRAINT `fk_localRoleType_localRole`
    FOREIGN KEY (`localRoleTypeID` )
    REFERENCES `localRoleType` (`localRoleTypeID` ),
  CONSTRAINT `fk_person_localRole`
    FOREIGN KEY (`personID` )
    REFERENCES `person` (`personID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_site_localRole`
    FOREIGN KEY (`siteID` )
    REFERENCES `site` (`siteID` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE INDEX `fk_person_localRole` ON `localRole` (`personID` ASC) ;

CREATE INDEX `fk_localRoleType_localRole` ON `localRole` (`localRoleTypeID` ASC) ;

CREATE INDEX `fk_site_localRole` ON `localRole` (`siteID` ASC) ;


-- -----------------------------------------------------
-- Table `contactInfo`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `contactInfo` ;

CREATE  TABLE IF NOT EXISTS `contactInfo` (
  `contactInfoID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `personID` INT(10) UNSIGNED NOT NULL ,
  `siteID` INT(10) UNSIGNED NOT NULL ,
  `label` VARCHAR(255) NOT NULL ,
  `isPrimary` BIT(1) NOT NULL DEFAULT 0 ,
  `beginDate` DATE NULL DEFAULT NULL ,
  `endDate` DATE NULL DEFAULT NULL ,
  `isActive` BIT(1) NOT NULL DEFAULT 1 ,
  PRIMARY KEY (`contactInfoID`, `personID`, `siteID`) ,
  CONSTRAINT `fk_person_contactInfo`
    FOREIGN KEY (`personID` )
    REFERENCES `person` (`personID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_site_contactInfo`
    FOREIGN KEY (`siteID` )
    REFERENCES `site` (`siteID` ))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE INDEX `fk_person_contactInfo` ON `contactInfo` (`personID` ASC) ;

CREATE INDEX `fk_site_contactInfo` ON `contactInfo` (`siteID` ASC) ;


-- -----------------------------------------------------
-- Table `contactInfoFieldType`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `contactInfoFieldType` ;

CREATE  TABLE IF NOT EXISTS `contactInfoFieldType` (
  `contactInfoFieldTypeID` INT UNSIGNED NOT NULL AUTO_INCREMENT ,
  `contactInfoFieldType` VARCHAR(255) NOT NULL ,
  `isRepeatable` BIT(1) NOT NULL DEFAULT 1 ,
  `validationExpression` VARCHAR(255) NULL ,
  `emlType` VARCHAR(255) NULL ,
  PRIMARY KEY (`contactInfoFieldTypeID`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `contactInfoField`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `contactInfoField` ;

CREATE  TABLE IF NOT EXISTS `contactInfoField` (
  `contactInfoFieldID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `contactInfoID` INT(10) UNSIGNED NOT NULL ,
  `contactInfoFieldTypeID` INT(10) UNSIGNED NOT NULL ,
  `value` VARCHAR(255) NOT NULL ,
  `sortOrder` INT(10) UNSIGNED NULL DEFAULT NULL ,
  PRIMARY KEY (`contactInfoFieldID`, `contactInfoID`, `contactInfoFieldTypeID`) ,
  CONSTRAINT `fk_contactInfoFieldType_contactInfoField`
    FOREIGN KEY (`contactInfoFieldTypeID` )
    REFERENCES `contactInfoFieldType` (`contactInfoFieldTypeID` ),
  CONSTRAINT `fk_contactInfo_contactInfoField`
    FOREIGN KEY (`contactInfoID` )
    REFERENCES `contactInfo` (`contactInfoID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE INDEX `fk_contactInfo_contactInfoField` ON `contactInfoField` (`contactInfoID` ASC) ;

CREATE INDEX `fk_contactInfoFieldType_contactInfoField` ON `contactInfoField` (`contactInfoFieldTypeID` ASC) ;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
