SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';


-- -----------------------------------------------------
-- Table `person`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `person` ;

CREATE  TABLE IF NOT EXISTS `person` (
  `personID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `prefix` VARCHAR(255) NULL DEFAULT NULL ,
  `firstName` VARCHAR(255) NULL DEFAULT NULL ,
  `middleName` VARCHAR(255) NULL DEFAULT NULL ,
  `lastName` VARCHAR(255) NOT NULL ,
  `suffix` VARCHAR(255) NULL DEFAULT NULL ,
  `preferredName` VARCHAR(255) NULL DEFAULT NULL ,
  `primaryEmail` VARCHAR(255) NOT NULL ,
  `title` VARCHAR(255) NULL DEFAULT NULL ,
  `optOut` BIT(1) NOT NULL DEFAULT b'0' ,
  `changeDate` DATE NULL ,
  PRIMARY KEY (`personID`) )
ENGINE = InnoDB
AUTO_INCREMENT = 179
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


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
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE INDEX `fk_person_nameAlias` ON `nameAlias` (`personID` ASC) ;


-- -----------------------------------------------------
-- Table `nsfRoleType`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `nsfRoleType` ;

CREATE  TABLE IF NOT EXISTS `nsfRoleType` (
  `nsfRoleTypeID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `roleType` VARCHAR(255) NOT NULL ,
  `isRepeatable` BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (`nsfRoleTypeID`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `site`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `site` ;

CREATE  TABLE IF NOT EXISTS `site` (
  `siteID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `site` VARCHAR(255) NOT NULL ,
  `siteAcronym` CHAR(3) NOT NULL ,
  `notificationEmail` VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (`siteID`) )
ENGINE = InnoDB
AUTO_INCREMENT = 341
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `localRoleType`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `localRoleType` ;

CREATE  TABLE IF NOT EXISTS `localRoleType` (
  `localRoleTypeID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `siteID` INT(10) UNSIGNED NOT NULL ,
  `roleType` VARCHAR(255) NOT NULL ,
  `isRepeatable` BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (`localRoleTypeID`, `siteID`) ,
  CONSTRAINT `fk_site_localRoleType`
    FOREIGN KEY (`siteID` )
    REFERENCES `site` (`siteID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE INDEX `fk_site_localRoleType` ON `localRoleType` (`siteID` ASC) ;


-- -----------------------------------------------------
-- Table `nsfRole`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `nsfRole` ;

CREATE  TABLE IF NOT EXISTS `nsfRole` (
  `roleID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `personID` INT(10) UNSIGNED NOT NULL ,
  `roleTypeID` INT(10) UNSIGNED NOT NULL ,
  `siteID` INT(10) UNSIGNED NOT NULL ,
  `beginDate` DATE NULL DEFAULT NULL ,
  `endDate` DATE NULL DEFAULT NULL ,
  `isActive` BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (`roleID`, `personID`, `roleTypeID`, `siteID`) ,
  CONSTRAINT `fk_nsfRoleType_nsfRole`
    FOREIGN KEY (`roleTypeID` )
    REFERENCES `nsfRoleType` (`nsfRoleTypeID` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_person_nsfRole`
    FOREIGN KEY (`personID` )
    REFERENCES `person` (`personID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_site_nsfRole`
    FOREIGN KEY (`siteID` )
    REFERENCES `site` (`siteID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE INDEX `fk_person_nsfRole` ON `nsfRole` (`personID` ASC) ;

CREATE INDEX `fk_nsfRoleType_nsfRole` ON `nsfRole` (`roleTypeID` ASC) ;

CREATE INDEX `fk_site_nsfRole` ON `nsfRole` (`siteID` ASC) ;


-- -----------------------------------------------------
-- Table `localRole`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `localRole` ;

CREATE  TABLE IF NOT EXISTS `localRole` (
  `roleID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `personID` INT(10) UNSIGNED NOT NULL ,
  `roleTypeID` INT(10) UNSIGNED NOT NULL ,
  `siteID` INT(10) UNSIGNED NOT NULL ,
  `beginDate` DATE NULL DEFAULT NULL ,
  `endDate` DATE NULL DEFAULT NULL ,
  `isActive` BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (`roleID`, `personID`, `roleTypeID`, `siteID`) ,
  CONSTRAINT `fk_localRoleType_localRole`
    FOREIGN KEY (`roleTypeID` )
    REFERENCES `localRoleType` (`localRoleTypeID` )
    ON DELETE RESTRICT
    ON UPDATE RESTRICT,
  CONSTRAINT `fk_person_localRole`
    FOREIGN KEY (`personID` )
    REFERENCES `person` (`personID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_site_localRole`
    FOREIGN KEY (`siteID` )
    REFERENCES `site` (`siteID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;

CREATE INDEX `fk_person_localRole` ON `localRole` (`personID` ASC) ;

CREATE INDEX `fk_localRoleType_localRole` ON `localRole` (`roleTypeID` ASC) ;

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
  `isPrimary` BIT(1) NOT NULL DEFAULT b'0' ,
  `beginDate` DATE NULL DEFAULT NULL ,
  `endDate` DATE NULL DEFAULT NULL ,
  `isActive` BIT(1) NOT NULL DEFAULT b'1' ,
  PRIMARY KEY (`contactInfoID`, `personID`, `siteID`) ,
  CONSTRAINT `fk_person_contactInfo`
    FOREIGN KEY (`personID` )
    REFERENCES `person` (`personID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
  CONSTRAINT `fk_site_contactInfo`
    FOREIGN KEY (`siteID` )
    REFERENCES `site` (`siteID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE)
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
  `contactInfoFieldTypeID` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `contactInfoFieldType` VARCHAR(255) NOT NULL ,
  `isRepeatable` BIT(1) NOT NULL DEFAULT b'1' ,
  `validationExpression` VARCHAR(255) NULL DEFAULT NULL ,
  `emlType` VARCHAR(255) NULL DEFAULT NULL ,
  PRIMARY KEY (`contactInfoFieldTypeID`) )
ENGINE = InnoDB
AUTO_INCREMENT = 27
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


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
    REFERENCES `contactInfoFieldType` (`contactInfoFieldTypeID` )
    ON DELETE CASCADE
    ON UPDATE CASCADE,
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
