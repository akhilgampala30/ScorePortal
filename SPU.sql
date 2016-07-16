-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema spdev_scoreportalunity
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema spdev_scoreportalunity
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `spdev_scoreportalunity` DEFAULT CHARACTER SET utf8 ;
USE `spdev_scoreportalunity` ;

-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`schools`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`schools` (
  `idSchools` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `SchoolName` VARCHAR(45) NULL,
  `SchoolStreetAddress` VARCHAR(45) NULL,
  `SchoolState` VARCHAR(45) NULL,
  `SchoolCity` VARCHAR(45) NULL,
  `SchoolZipCode` VARCHAR(15) NULL,
  `Population` SMALLINT UNSIGNED NULL,
  `PowerschoolRootURL` VARCHAR(100) NULL,
  PRIMARY KEY (`idSchools`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`globalcourses`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`globalcourses` (
  `idGlobalCourses` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `GlobalCourseName` VARCHAR(45) NULL,
  `GlobalCourseDescription` TEXT NULL,
  `Population` SMALLINT UNSIGNED NULL,
  PRIMARY KEY (`idGlobalCourses`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`courses`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`courses` (
  `idCourses` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Schools_idSchools` INT UNSIGNED NOT NULL,
  `GlobalCourses_idGlobalCourses` INT UNSIGNED NULL,
  `CourseName` VARBINARY(100) NULL,
  `CourseDescription` LONGTEXT NULL,
  `Population` SMALLINT UNSIGNED NULL,
  PRIMARY KEY (`idCourses`),
  INDEX `fk_Courses_Schools_idx` (`Schools_idSchools` ASC),
  INDEX `fk_Courses_GlobalCourses1_idx` (`GlobalCourses_idGlobalCourses` ASC),
  CONSTRAINT `fk_Courses_Schools`
    FOREIGN KEY (`Schools_idSchools`)
    REFERENCES `spdev_scoreportalunity`.`schools` (`idSchools`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Courses_GlobalCourses1`
    FOREIGN KEY (`GlobalCourses_idGlobalCourses`)
    REFERENCES `spdev_scoreportalunity`.`globalcourses` (`idGlobalCourses`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`courseattributes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`courseattributes` (
  `idCourseAttributes` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Attribute` VARCHAR(45) NULL,
  PRIMARY KEY (`idCourseAttributes`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`teachers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`teachers` (
  `idTeachers` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `TeacherName` VARBINARY(100) NULL,
  `TeacherEmail` VARBINARY(300) NULL,
  `Schools_idSchools` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idTeachers`),
  INDEX `fk_Teachers_Schools1_idx` (`Schools_idSchools` ASC),
  CONSTRAINT `fk_Teachers_Schools1`
    FOREIGN KEY (`Schools_idSchools`)
    REFERENCES `spdev_scoreportalunity`.`schools` (`idSchools`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`termnumbers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`termnumbers` (
  `idTermNumbers` INT NOT NULL,
  `StartDate` DATE NULL,
  `EndDate` DATE NULL,
  `TermYear` INT NULL,
  `Name` VARCHAR(45) NULL,
  `Schools_idSchools` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idTermNumbers`),
  INDEX `fk_termnumbers_schools1_idx` (`Schools_idSchools` ASC),
  CONSTRAINT `fk_termnumbers_schools1`
    FOREIGN KEY (`Schools_idSchools`)
    REFERENCES `spdev_scoreportalunity`.`schools` (`idSchools`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`classes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`classes` (
  `idClasses` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Courses_idCourses` INT UNSIGNED NOT NULL,
  `Teachers_idTeachers` INT UNSIGNED NOT NULL,
  `RoomNumber` VARCHAR(15) NULL,
  `Population` SMALLINT NULL,
  `isWeighted` TINYINT(1) NULL,
  `TermNumbers_idTermNumbers` INT NOT NULL,
  PRIMARY KEY (`idClasses`),
  INDEX `fk_Classes_Courses1_idx` (`Courses_idCourses` ASC),
  INDEX `fk_Classes_Teachers1_idx` (`Teachers_idTeachers` ASC),
  INDEX `fk_classes_termnumbers1_idx` (`TermNumbers_idTermNumbers` ASC),
  CONSTRAINT `fk_Classes_Courses1`
    FOREIGN KEY (`Courses_idCourses`)
    REFERENCES `spdev_scoreportalunity`.`courses` (`idCourses`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Classes_Teachers1`
    FOREIGN KEY (`Teachers_idTeachers`)
    REFERENCES `spdev_scoreportalunity`.`teachers` (`idTeachers`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_classes_termnumbers1`
    FOREIGN KEY (`TermNumbers_idTermNumbers`)
    REFERENCES `spdev_scoreportalunity`.`termnumbers` (`idTermNumbers`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`mobilecarrier`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`mobilecarrier` (
  `idMobileCarrier` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `MobileCarrierName` VARCHAR(45) NULL,
  `MobileCarrierGateway` VARCHAR(45) NULL COMMENT 'has the \'@gateway.com\' address to append to phone number.',
  PRIMARY KEY (`idMobileCarrier`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`students`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`students` (
  `idStudents` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `FirstName` VARBINARY(100) NULL,
  `LastName` VARBINARY(100) NULL,
  `Email` VARBINARY(300) NULL,
  `GPA` FLOAT NULL,
  `StudentDistrictID` VARBINARY(100) NULL,
  `StudentDistrictPassword` VARBINARY(100) NULL,
  `MobileNumber` VARBINARY(40) NULL,
  `MobileCarrier_idMobileCarrier` INT UNSIGNED NULL,
  `Schools_idSchools` INT UNSIGNED NOT NULL,
  `GradesLastUpdated` TIMESTAMP NULL,
  `Points` INT NULL COMMENT 'Points accumulated from Achievements',
  `GradeLevel` INT NULL,
  `LegacyID` VARCHAR(45) NULL,
  PRIMARY KEY (`idStudents`),
  INDEX `fk_Student_MobileCarrier1_idx` (`MobileCarrier_idMobileCarrier` ASC),
  INDEX `fk_Students_Schools1_idx` (`Schools_idSchools` ASC),
  CONSTRAINT `fk_Student_MobileCarrier1`
    FOREIGN KEY (`MobileCarrier_idMobileCarrier`)
    REFERENCES `spdev_scoreportalunity`.`mobilecarrier` (`idMobileCarrier`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Students_Schools1`
    FOREIGN KEY (`Schools_idSchools`)
    REFERENCES `spdev_scoreportalunity`.`schools` (`idSchools`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`student_classes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`student_classes` (
  `Student_idStudent` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Classes_idClasses` INT UNSIGNED NOT NULL,
  `Student_Classes_Period` TINYINT UNSIGNED NULL,
  `Student_Classes_NumericGrade` FLOAT NULL,
  `Student_Classes_LetterGrade` VARCHAR(4) NULL,
  `Student_Classes_GradeDiff` FLOAT NULL,
  PRIMARY KEY (`Student_idStudent`, `Classes_idClasses`),
  INDEX `fk_Student_has_Classes_Classes1_idx` (`Classes_idClasses` ASC),
  INDEX `fk_Student_has_Classes_Student1_idx` (`Student_idStudent` ASC),
  CONSTRAINT `fk_Student_has_Classes_Student1`
    FOREIGN KEY (`Student_idStudent`)
    REFERENCES `spdev_scoreportalunity`.`students` (`idStudents`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Student_has_Classes_Classes1`
    FOREIGN KEY (`Classes_idClasses`)
    REFERENCES `spdev_scoreportalunity`.`classes` (`idClasses`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`category`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`category` (
  `idCategory` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `CategoryName` VARBINARY(100) NULL,
  `CategoryWeight` FLOAT NULL,
  `Classes_idClasses` INT UNSIGNED NOT NULL,
  `CategoryNameAbrv` VARBINARY(50) NULL,
  PRIMARY KEY (`idCategory`),
  INDEX `fk_Category_Classes1_idx` (`Classes_idClasses` ASC),
  CONSTRAINT `fk_Category_Classes1`
    FOREIGN KEY (`Classes_idClasses`)
    REFERENCES `spdev_scoreportalunity`.`classes` (`idClasses`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`assignments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`assignments` (
  `idAssignments` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `AssignmentName` VARBINARY(200) NULL,
  `AssignmentDate` DATETIME NULL,
  `Category_idCategory` INT UNSIGNED NOT NULL,
  `Classes_idClasses` INT UNSIGNED NOT NULL,
  `NewAlert` TINYINT(1) NULL,
  `AssignmentMultiplier` FLOAT NULL,
  `Population` SMALLINT UNSIGNED NULL,
  `AssignmentAverage` FLOAT NULL,
  `AssignmentStandardDeviation` FLOAT NULL,
  PRIMARY KEY (`idAssignments`),
  INDEX `fk_Assignments_Category1_idx` (`Category_idCategory` ASC),
  INDEX `fk_Assignments_Classes1_idx` (`Classes_idClasses` ASC),
  CONSTRAINT `fk_Assignments_Category1`
    FOREIGN KEY (`Category_idCategory`)
    REFERENCES `spdev_scoreportalunity`.`category` (`idCategory`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Assignments_Classes1`
    FOREIGN KEY (`Classes_idClasses`)
    REFERENCES `spdev_scoreportalunity`.`classes` (`idClasses`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`assignmentscore`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`assignmentscore` (
  `idAssignmentScore` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Assignments_idAssignments` INT UNSIGNED NOT NULL,
  `Student_idStudent` INT UNSIGNED NOT NULL,
  `AssignmentEarnedPoints` FLOAT NULL,
  `AssignmentPossiblePoints` FLOAT NULL,
  `AssignmentBookmarked` TINYINT(1) NULL,
  `AssignmentNewAlert` TINYINT(1) NULL,
  PRIMARY KEY (`idAssignmentScore`, `Assignments_idAssignments`),
  INDEX `fk_AssignmentScore_Student1_idx` (`Student_idStudent` ASC),
  INDEX `fk_AssignmentScore_Assignments1_idx` (`Assignments_idAssignments` ASC),
  UNIQUE INDEX `idAssignmentScore_UNIQUE` (`idAssignmentScore` ASC),
  CONSTRAINT `fk_AssignmentScore_Student1`
    FOREIGN KEY (`Student_idStudent`)
    REFERENCES `spdev_scoreportalunity`.`students` (`idStudents`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_AssignmentScore_Assignments1`
    FOREIGN KEY (`Assignments_idAssignments`)
    REFERENCES `spdev_scoreportalunity`.`assignments` (`idAssignments`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`modifiedassignmentscore`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`modifiedassignmentscore` (
  `idModifiedAssignmentScore` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `AssignmentScore_idAssignmentScore` INT UNSIGNED NOT NULL,
  `Students_idStudents` INT UNSIGNED NOT NULL,
  `AssignmentEarnedPoints` FLOAT NULL,
  `AssignmentPossiblePoints` FLOAT NULL,
  `AssignmentDisabled` TINYINT(1) NULL,
  `AssignmentMultiplier` FLOAT NULL,
  PRIMARY KEY (`idModifiedAssignmentScore`, `AssignmentScore_idAssignmentScore`, `Students_idStudents`),
  INDEX `fk_ModifiedAssignmentScore_AssignmentScore1_idx` (`AssignmentScore_idAssignmentScore` ASC),
  INDEX `fk_ModifiedAssignmentScore_Students1_idx` (`Students_idStudents` ASC),
  UNIQUE INDEX `idModifiedAssignmentScore_UNIQUE` (`idModifiedAssignmentScore` ASC),
  UNIQUE INDEX `AssignmentScore_idAssignmentScore_UNIQUE` (`AssignmentScore_idAssignmentScore` ASC),
  CONSTRAINT `fk_ModifiedAssignmentScore_AssignmentScore1`
    FOREIGN KEY (`AssignmentScore_idAssignmentScore`)
    REFERENCES `spdev_scoreportalunity`.`assignmentscore` (`idAssignmentScore`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ModifiedAssignmentScore_Students1`
    FOREIGN KEY (`Students_idStudents`)
    REFERENCES `spdev_scoreportalunity`.`students` (`idStudents`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`modifiedcategory`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`modifiedcategory` (
  `idModifiedCategory` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `CategoryName` VARCHAR(45) NULL,
  `CategoryWeight` FLOAT NULL,
  `Classes_idClasses` INT UNSIGNED NOT NULL,
  `CategoryNameAbrv` VARCHAR(15) NULL,
  `isUserCreated` TINYINT(1) NULL,
  `Students_idStudents` INT UNSIGNED NOT NULL,
  `isDisabled` TINYINT(1) NULL,
  PRIMARY KEY (`idModifiedCategory`),
  INDEX `fk_Category_Classes1_idx` (`Classes_idClasses` ASC),
  INDEX `fk_ModifiedCategory_Students1_idx` (`Students_idStudents` ASC),
  CONSTRAINT `fk_ModifiedCategory_Classes1`
    FOREIGN KEY (`Classes_idClasses`)
    REFERENCES `spdev_scoreportalunity`.`classes` (`idClasses`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ModifiedCategory_Students1`
    FOREIGN KEY (`Students_idStudents`)
    REFERENCES `spdev_scoreportalunity`.`students` (`idStudents`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`useraddedassignments`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`useraddedassignments` (
  `idUserAddedAssignments` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `UserAddedAssignmentName` VARCHAR(45) NOT NULL,
  `UserAddedAssignmentEarnedPoints` FLOAT NOT NULL,
  `UserAddedAssignmentPossiblePoints` FLOAT NOT NULL,
  `AssignmentDisabled` TINYINT(1) NULL,
  `AssignmentBookmarked` TINYINT(1) NULL,
  `Students_idStudents` INT UNSIGNED NOT NULL,
  `Classes_idClasses` INT UNSIGNED NOT NULL,
  `Category_idCategory` INT UNSIGNED NULL,
  `ModifiedCategory_idModifiedCategory` INT UNSIGNED NULL,
  PRIMARY KEY (`idUserAddedAssignments`),
  INDEX `fk_UserAddedAssignments_Students1_idx` (`Students_idStudents` ASC),
  INDEX `fk_UserAddedAssignments_Classes1_idx` (`Classes_idClasses` ASC),
  INDEX `fk_UserAddedAssignments_Category1_idx` (`Category_idCategory` ASC),
  INDEX `fk_UserAddedAssignments_ModifiedCategory1_idx` (`ModifiedCategory_idModifiedCategory` ASC),
  CONSTRAINT `fk_UserAddedAssignments_Students1`
    FOREIGN KEY (`Students_idStudents`)
    REFERENCES `spdev_scoreportalunity`.`students` (`idStudents`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_UserAddedAssignments_Classes1`
    FOREIGN KEY (`Classes_idClasses`)
    REFERENCES `spdev_scoreportalunity`.`classes` (`idClasses`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_UserAddedAssignments_Category1`
    FOREIGN KEY (`Category_idCategory`)
    REFERENCES `spdev_scoreportalunity`.`category` (`idCategory`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_UserAddedAssignments_ModifiedCategory1`
    FOREIGN KEY (`ModifiedCategory_idModifiedCategory`)
    REFERENCES `spdev_scoreportalunity`.`modifiedcategory` (`idModifiedCategory`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`userlevel`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`userlevel` (
  `idUserLevel` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `UserLevelDescription` VARCHAR(45) NULL COMMENT 'UserLevel helps determine if the user is VIP, Mod, Admin, etc.',
  PRIMARY KEY (`idUserLevel`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`users` (
  `idUsers` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `FirstName` VARBINARY(100) NULL,
  `LastName` VARBINARY(100) NULL,
  `Email` VARBINARY(300) NULL,
  `DateJoined` DATETIME NULL,
  `LastLogin` TIMESTAMP NULL,
  `UserLevel_idUserLevel` INT UNSIGNED NOT NULL,
  `LoginNumber` INT UNSIGNED NULL,
  PRIMARY KEY (`idUsers`),
  INDEX `fk_Users_UserLevel1_idx` (`UserLevel_idUserLevel` ASC),
  CONSTRAINT `fk_Users_UserLevel1`
    FOREIGN KEY (`UserLevel_idUserLevel`)
    REFERENCES `spdev_scoreportalunity`.`userlevel` (`idUserLevel`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`loginip`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`loginip` (
  `Users_idUsers` INT UNSIGNED NOT NULL,
  `IP` VARCHAR(45) NULL,
  `Time` TIMESTAMP NULL,
  INDEX `fk_LoginIP_Users1_idx` (`Users_idUsers` ASC),
  CONSTRAINT `fk_LoginIP_Users1`
    FOREIGN KEY (`Users_idUsers`)
    REFERENCES `spdev_scoreportalunity`.`users` (`idUsers`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`students_users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`students_users` (
  `Student_idStudent` INT UNSIGNED NOT NULL,
  `Users_idUsers` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`Student_idStudent`, `Users_idUsers`),
  INDEX `fk_Student_has_Users_Users1_idx` (`Users_idUsers` ASC),
  INDEX `fk_Student_has_Users_Student1_idx` (`Student_idStudent` ASC),
  CONSTRAINT `fk_Student_has_Users_Student1`
    FOREIGN KEY (`Student_idStudent`)
    REFERENCES `spdev_scoreportalunity`.`students` (`idStudents`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Student_has_Users_Users1`
    FOREIGN KEY (`Users_idUsers`)
    REFERENCES `spdev_scoreportalunity`.`users` (`idUsers`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`xmlarchives`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`xmlarchives` (
  `idXMLArchives` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Students_idStudents` INT UNSIGNED NOT NULL,
  `XML` MEDIUMBLOB NULL,
  `RecordedTime` TIMESTAMP NULL,
  PRIMARY KEY (`idXMLArchives`),
  INDEX `fk_XMLArchives_Students1_idx` (`Students_idStudents` ASC),
  CONSTRAINT `fk_XMLArchives_Students1`
    FOREIGN KEY (`Students_idStudents`)
    REFERENCES `spdev_scoreportalunity`.`students` (`idStudents`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
CHECKSUM = 1
ROW_FORMAT = COMPRESSED;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`loginservicetype`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`loginservicetype` (
  `idLoginServiceType` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `LoginServiceName` VARCHAR(45) NULL,
  `LoginServiceDomain` VARCHAR(45) NULL,
  `LoginTechnology` INT UNSIGNED NULL COMMENT 'OpenID, Oauth 2.0, etc.',
  PRIMARY KEY (`idLoginServiceType`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`loginservice`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`loginservice` (
  `idLoginService` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `LoginServiceType_idLoginServiceType` INT UNSIGNED NOT NULL,
  `LoginServiceID` VARCHAR(255) NULL,
  `Users_idUsers` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idLoginService`),
  INDEX `fk_LoginService_LoginServiceType1_idx` (`LoginServiceType_idLoginServiceType` ASC),
  INDEX `fk_LoginService_Users1_idx` (`Users_idUsers` ASC),
  CONSTRAINT `fk_LoginService_LoginServiceType1`
    FOREIGN KEY (`LoginServiceType_idLoginServiceType`)
    REFERENCES `spdev_scoreportalunity`.`loginservicetype` (`idLoginServiceType`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_LoginService_Users1`
    FOREIGN KEY (`Users_idUsers`)
    REFERENCES `spdev_scoreportalunity`.`users` (`idUsers`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`rapidupdatestudents`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`rapidupdatestudents` (
  `idRapidUpdateStudents` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Students_idStudents` INT UNSIGNED NOT NULL,
  `UpdateInterval` INT UNSIGNED NULL COMMENT 'In minuets',
  `LastUpdate` DATETIME NULL,
  PRIMARY KEY (`idRapidUpdateStudents`),
  INDEX `fk_RapidUpdateStudents_Students1_idx` (`Students_idStudents` ASC),
  CONSTRAINT `fk_RapidUpdateStudents_Students1`
    FOREIGN KEY (`Students_idStudents`)
    REFERENCES `spdev_scoreportalunity`.`students` (`idStudents`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`achievements`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`achievements` (
  `idAchievements` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `AchievementName` VARCHAR(45) NULL,
  `AchievementDescription` VARCHAR(45) NULL,
  `AchievementReward` INT NULL COMMENT 'Point worth.',
  PRIMARY KEY (`idAchievements`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`student_achievements`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`student_achievements` (
  `Achievements_idAchievements` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Students_idStudents` INT UNSIGNED NOT NULL,
  `TimeEarned` TIMESTAMP NULL,
  PRIMARY KEY (`Achievements_idAchievements`, `Students_idStudents`),
  INDEX `fk_Achievements_has_Students_Students1_idx` (`Students_idStudents` ASC),
  INDEX `fk_Achievements_has_Students_Achievements1_idx` (`Achievements_idAchievements` ASC),
  CONSTRAINT `fk_Achievements_has_Students_Achievements1`
    FOREIGN KEY (`Achievements_idAchievements`)
    REFERENCES `spdev_scoreportalunity`.`achievements` (`idAchievements`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Achievements_has_Students_Students1`
    FOREIGN KEY (`Students_idStudents`)
    REFERENCES `spdev_scoreportalunity`.`students` (`idStudents`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`notificationtype`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`notificationtype` (
  `idNotificationTypes` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ImagePath` VARCHAR(45) NULL,
  PRIMARY KEY (`idNotificationTypes`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`notifications`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`notifications` (
  `idNotifications` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `Students_idStudents` INT UNSIGNED NOT NULL,
  `NotificationURL` VARCHAR(45) NULL,
  `Notification` VARBINARY(350) NULL,
  `NotificationTime` TIMESTAMP NULL,
  `NotificationType_idNotificationType` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idNotifications`),
  INDEX `fk_Notifications_Students1_idx` (`Students_idStudents` ASC),
  INDEX `fk_Notifications_NotificationType1_idx` (`NotificationType_idNotificationType` ASC),
  CONSTRAINT `fk_Notifications_Students1`
    FOREIGN KEY (`Students_idStudents`)
    REFERENCES `spdev_scoreportalunity`.`students` (`idStudents`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Notifications_NotificationType1`
    FOREIGN KEY (`NotificationType_idNotificationType`)
    REFERENCES `spdev_scoreportalunity`.`notificationtype` (`idNotificationTypes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`student_classes_grade`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`student_classes_grade` (
  `idStudent_Classes_Grade` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `NumericGrade` FLOAT NULL,
  `RecordedTime` TIMESTAMP NULL,
  `LetterGrade` VARCHAR(10) NULL,
  `TotalClassPoints` FLOAT NULL COMMENT 'Use this for weighing the significance of this point at time X',
  `Student_Classes_Student_idStudent` INT UNSIGNED NOT NULL,
  `Student_Classes_Classes_idClasses` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idStudent_Classes_Grade`),
  INDEX `fk_Student_Classes_Grade_Student_Classes1_idx` (`Student_Classes_Student_idStudent` ASC, `Student_Classes_Classes_idClasses` ASC),
  CONSTRAINT `fk_Student_Classes_Grade_Student_Classes1`
    FOREIGN KEY (`Student_Classes_Student_idStudent` , `Student_Classes_Classes_idClasses`)
    REFERENCES `spdev_scoreportalunity`.`student_classes` (`Student_idStudent` , `Classes_idClasses`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`classaverage`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`classaverage` (
  `idClassAverageGrade` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `NumericGrade` FLOAT NULL,
  `StandardDeviation` FLOAT NULL,
  `Population` SMALLINT NULL,
  `RecordedTime` TIMESTAMP NULL,
  `Classes_idClasses` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idClassAverageGrade`),
  INDEX `fk_ClassAverage_Classes1_idx` (`Classes_idClasses` ASC),
  INDEX `RecordedTime` (`RecordedTime` ASC),
  CONSTRAINT `fk_ClassAverage_Classes1`
    FOREIGN KEY (`Classes_idClasses`)
    REFERENCES `spdev_scoreportalunity`.`classes` (`idClasses`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`courses_courseattributes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`courses_courseattributes` (
  `Courses_idCourses` INT UNSIGNED NOT NULL,
  `CourseAttributes_idCourseAttributes` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`Courses_idCourses`, `CourseAttributes_idCourseAttributes`),
  INDEX `fk_Courses_has_CourseAttributes_CourseAttributes1_idx` (`CourseAttributes_idCourseAttributes` ASC),
  INDEX `fk_Courses_has_CourseAttributes_Courses1_idx` (`Courses_idCourses` ASC),
  CONSTRAINT `fk_Courses_has_CourseAttributes_Courses1`
    FOREIGN KEY (`Courses_idCourses`)
    REFERENCES `spdev_scoreportalunity`.`courses` (`idCourses`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Courses_has_CourseAttributes_CourseAttributes1`
    FOREIGN KEY (`CourseAttributes_idCourseAttributes`)
    REFERENCES `spdev_scoreportalunity`.`courseattributes` (`idCourseAttributes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`globalcourses_courseattributes`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`globalcourses_courseattributes` (
  `CourseAttributes_idCourseAttributes` INT UNSIGNED NOT NULL,
  `GlobalCourses_idGlobalCourses` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`CourseAttributes_idCourseAttributes`, `GlobalCourses_idGlobalCourses`),
  INDEX `fk_CourseAttributes_has_GlobalCourses_GlobalCourses1_idx` (`GlobalCourses_idGlobalCourses` ASC),
  INDEX `fk_CourseAttributes_has_GlobalCourses_CourseAttributes1_idx` (`CourseAttributes_idCourseAttributes` ASC),
  CONSTRAINT `fk_CourseAttributes_has_GlobalCourses_CourseAttributes1`
    FOREIGN KEY (`CourseAttributes_idCourseAttributes`)
    REFERENCES `spdev_scoreportalunity`.`courseattributes` (`idCourseAttributes`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_CourseAttributes_has_GlobalCourses_GlobalCourses1`
    FOREIGN KEY (`GlobalCourses_idGlobalCourses`)
    REFERENCES `spdev_scoreportalunity`.`globalcourses` (`idGlobalCourses`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`classreviews`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`classreviews` (
  `idClassReviews` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `AvgTimeConsumed` TINYINT UNSIGNED NULL,
  `TestQuizFrequency` TINYINT UNSIGNED NULL,
  `ProjectPresentationImportance` TINYINT UNSIGNED NULL,
  `HowWellLearnFromClass` TINYINT UNSIGNED NULL,
  `ExtraCreditImportance` TINYINT UNSIGNED NULL,
  `OutsideResourceImportance` TINYINT UNSIGNED NULL,
  `GeneralClassRating` TINYINT UNSIGNED NULL,
  `DifficultyIndex` TINYINT UNSIGNED NULL,
  `TextReview` LONGTEXT NULL,
  `isAnonymous` TINYINT(1) NULL,
  `Students_idStudents` INT UNSIGNED NOT NULL,
  `Classes_idClasses` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idClassReviews`),
  INDEX `fk_ClassReview_Classes_idx` (`Classes_idClasses` ASC),
  INDEX `fk_ClassReview_Students_idx` (`Students_idStudents` ASC),
  CONSTRAINT `fk_ClassReview_Classes`
    FOREIGN KEY (`Classes_idClasses`)
    REFERENCES `spdev_scoreportalunity`.`classes` (`idClasses`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_ClassReview_Students`
    FOREIGN KEY (`Students_idStudents`)
    REFERENCES `spdev_scoreportalunity`.`students` (`idStudents`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`categoryreviews`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`categoryreviews` (
  `idCategoryReviews` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `ClassReviews_idClassReviews` INT UNSIGNED NOT NULL,
  `WorkLoadRanking` SMALLINT UNSIGNED NULL,
  `Category_idCategory` INT UNSIGNED NOT NULL,
  PRIMARY KEY (`idCategoryReviews`),
  INDEX `fk_CategoryReviews_idClassReviews_idx` (`ClassReviews_idClassReviews` ASC),
  INDEX `fk_Category_idCategory_idx` (`Category_idCategory` ASC),
  CONSTRAINT `fk_CategoryReviews_idClassReviews`
    FOREIGN KEY (`ClassReviews_idClassReviews`)
    REFERENCES `spdev_scoreportalunity`.`classreviews` (`idClassReviews`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_Category_idCategory`
    FOREIGN KEY (`Category_idCategory`)
    REFERENCES `spdev_scoreportalunity`.`category` (`idCategory`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`SettingType`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`SettingType` (
  `idSettingType` INT NOT NULL,
  `SettingDescription` VARCHAR(45) NULL,
  PRIMARY KEY (`idSettingType`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `spdev_scoreportalunity`.`StudentSetting`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`StudentSetting` (
  `idStudentSetting` INT NOT NULL,
  `students_idStudents` INT UNSIGNED NOT NULL,
  `SettingValue` VARCHAR(45) NULL,
  `SettingType_idSettingType` INT NOT NULL,
  PRIMARY KEY (`idStudentSetting`),
  INDEX `fk_StudentSetting_students1_idx` (`students_idStudents` ASC),
  INDEX `fk_StudentSetting_SettingType1_idx` (`SettingType_idSettingType` ASC),
  CONSTRAINT `fk_StudentSetting_students1`
    FOREIGN KEY (`students_idStudents`)
    REFERENCES `spdev_scoreportalunity`.`students` (`idStudents`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_StudentSetting_SettingType1`
    FOREIGN KEY (`SettingType_idSettingType`)
    REFERENCES `spdev_scoreportalunity`.`SettingType` (`idSettingType`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;

USE `spdev_scoreportalunity` ;

-- -----------------------------------------------------
-- Placeholder table for view `spdev_scoreportalunity`.`overview`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `spdev_scoreportalunity`.`overview` (`TeacherName` INT, `CourseName` INT, `idClasses` INT, `Courses_idCourses` INT, `Teachers_idTeachers` INT, `RoomNumber` INT, `Population` INT, `isWeighted` INT, `TermNumbers_idTermNumbers` INT);

-- -----------------------------------------------------
-- View `spdev_scoreportalunity`.`overview`
-- -----------------------------------------------------
DROP TABLE IF EXISTS `spdev_scoreportalunity`.`overview`;
USE `spdev_scoreportalunity`;
CREATE  OR REPLACE VIEW `spdev_scoreportalunity`.`overview` AS
SELECT t.TeacherName as TeacherName,
co.CourseName as CourseName,
cl.* 
FROM `classes` cl
JOIN  `teachers` t ON ( t.idTeachers = cl.Teachers_idTeachers ) 
JOIN  `courses` co ON ( co.idCourses = cl.Courses_idCourses ) ;
CREATE USER 'spdev' IDENTIFIED BY 'Nl-IT,?uZ,b=';

GRANT ALL ON `spdev_scoreportalunity`.* TO 'spdev';

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
