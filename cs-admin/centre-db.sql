-- Server version: 5.5.16
-- PHP Version: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- --------------------------------------------------------

--
-- Table structure for table `address`
--

DROP TABLE IF EXISTS `address`;
CREATE TABLE IF NOT EXISTS `address` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `house_no` double(5,0) DEFAULT NULL,
  `fraction` char(3) DEFAULT NULL,
  `letter` char(2) DEFAULT NULL,
  `direction` char(2) DEFAULT NULL,
  `street` char(30) DEFAULT NULL,
  `apt` char(5) DEFAULT NULL,
  `zipcode` char(10) DEFAULT NULL,
  `plus4` char(4) DEFAULT NULL,
  `city` char(60) DEFAULT NULL,
  `state` char(10) DEFAULT NULL,
  `mail_street` char(30) DEFAULT NULL,
  `mail_city` char(60) DEFAULT NULL,
  `mail_state` char(10) DEFAULT NULL,
  `mail_zipcode` char(10) DEFAULT NULL,
  `address` char(255) DEFAULT NULL,
  `mail_address` char(255) DEFAULT NULL,
  `phone` char(30) DEFAULT NULL,
  PRIMARY KEY (`address_id`),
  KEY `address_3` (`plus4`,`zipcode`),
  KEY `address_4` (`street`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `address`
--

INSERT INTO `address` (`address_id`, `house_no`, `fraction`, `letter`, `direction`, `street`, `apt`, `zipcode`, `plus4`, `city`, `state`, `mail_street`, `mail_city`, `mail_state`, `mail_zipcode`, `address`, `mail_address`, `phone`) VALUES
(0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'No Address', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `address_fields`
--

DROP TABLE IF EXISTS `address_fields`;
CREATE TABLE IF NOT EXISTS `address_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` char(10) DEFAULT NULL,
  `search` char(1) DEFAULT NULL,
  `title` longtext,
  `sort_order` double DEFAULT NULL,
  `select_options` longtext,
  `category_id` double DEFAULT NULL,
  `system_field` char(1) DEFAULT NULL,
  `required` char(1) DEFAULT NULL,
  `default_selection` char(255) DEFAULT NULL,
  `description` longtext,
  PRIMARY KEY (`id`),
  KEY `address_desc_ind` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `address_field_categories`
--

DROP TABLE IF EXISTS `address_field_categories`;
CREATE TABLE IF NOT EXISTS `address_field_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` longtext,
  `sort_order` double DEFAULT NULL,
  `residence` char(1) DEFAULT NULL,
  `mailing` char(1) DEFAULT NULL,
  `bus` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_exams`
--

DROP TABLE IF EXISTS `admin_notes`;
CREATE TABLE IF NOT EXISTS `admin_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `note` varchar(250) DEFAULT NULL,
  `url` varchar(250) DEFAULT NULL,
  `staff_id` int(11) DEFAULT NULL,
  `username` varchar(100) DEFAULT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_exams`
--

DROP TABLE IF EXISTS `assessment_exams`;
CREATE TABLE IF NOT EXISTS `assessment_exams` (
  `id` double DEFAULT NULL,
  `syear` double(4,0) DEFAULT NULL,
  `title` char(100) DEFAULT NULL,
  `short_name` char(25) DEFAULT NULL,
  `max_score` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_scores`
--

DROP TABLE IF EXISTS `assessment_scores`;
CREATE TABLE IF NOT EXISTS `assessment_scores` (
  `syear` double(4,0) DEFAULT NULL,
  `student_id` double DEFAULT NULL,
  `score_id` double DEFAULT NULL,
  `type` char(9) DEFAULT NULL,
  `score` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `assessment_sections`
--

DROP TABLE IF EXISTS `assessment_sections`;
CREATE TABLE IF NOT EXISTS `assessment_sections` (
  `id` double DEFAULT NULL,
  `syear` double(4,0) DEFAULT NULL,
  `exam_id` double DEFAULT NULL,
  `title` char(100) DEFAULT NULL,
  `short_name` char(25) DEFAULT NULL,
  `max_score` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `attendance_calendar`
--

DROP TABLE IF EXISTS `attendance_calendar`;
CREATE TABLE IF NOT EXISTS `attendance_calendar` (
  `syear` double(4,0) NOT NULL,
  `school_id` double NOT NULL,
  `school_date` date NOT NULL,
  `minutes` double DEFAULT NULL,
  `block` char(10) DEFAULT NULL,
  `calendar_id` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `attendance_calendar`
--

INSERT INTO `attendance_calendar` (`syear`, `school_id`, `school_date`, `minutes`, `block`, `calendar_id`) VALUES
(2014, 1, '2014-09-04', 999, NULL, 1),
(2014, 1, '2014-09-05', 999, NULL, 1),
(2014, 1, '2014-09-06', 999, NULL, 1),
(2014, 1, '2014-09-07', 999, NULL, 1),
(2014, 1, '2014-09-10', 999, NULL, 1),
(2014, 1, '2014-09-11', 999, NULL, 1),
(2014, 1, '2014-09-12', 999, NULL, 1),
(2014, 1, '2014-09-13', 999, NULL, 1),
(2014, 1, '2014-09-14', 999, NULL, 1),
(2014, 1, '2014-09-17', 999, NULL, 1),
(2014, 1, '2014-09-18', 999, NULL, 1),
(2014, 1, '2014-09-19', 999, NULL, 1),
(2014, 1, '2014-09-20', 999, NULL, 1),
(2014, 1, '2014-09-21', 999, NULL, 1),
(2014, 1, '2014-09-24', 999, NULL, 1),
(2014, 1, '2014-09-25', 999, NULL, 1),
(2014, 1, '2014-09-26', 999, NULL, 1),
(2014, 1, '2014-09-27', 999, NULL, 1),
(2014, 1, '2014-09-28', 999, NULL, 1),
(2014, 1, '2014-10-01', 999, NULL, 1),
(2014, 1, '2014-10-02', 999, NULL, 1),
(2014, 1, '2014-10-03', 999, NULL, 1),
(2014, 1, '2014-10-04', 999, NULL, 1),
(2014, 1, '2014-10-05', 999, NULL, 1),
(2014, 1, '2014-10-08', 999, NULL, 1),
(2014, 1, '2014-10-09', 999, NULL, 1),
(2014, 1, '2014-10-10', 999, NULL, 1),
(2014, 1, '2014-10-11', 999, NULL, 1),
(2014, 1, '2014-10-12', 999, NULL, 1),
(2014, 1, '2014-10-15', 999, NULL, 1),
(2014, 1, '2014-10-16', 999, NULL, 1),
(2014, 1, '2014-10-17', 999, NULL, 1),
(2014, 1, '2014-10-18', 999, NULL, 1),
(2014, 1, '2014-10-19', 999, NULL, 1),
(2014, 1, '2014-10-22', 999, NULL, 1),
(2014, 1, '2014-10-23', 999, NULL, 1),
(2014, 1, '2014-10-24', 999, NULL, 1),
(2014, 1, '2014-10-25', 999, NULL, 1),
(2014, 1, '2014-10-26', 999, NULL, 1),
(2014, 1, '2014-10-29', 999, NULL, 1),
(2014, 1, '2014-10-30', 999, NULL, 1),
(2014, 1, '2014-10-31', 999, NULL, 1),
(2014, 1, '2014-11-01', 999, NULL, 1),
(2014, 1, '2014-11-02', 999, NULL, 1),
(2014, 1, '2014-11-05', 999, NULL, 1),
(2014, 1, '2014-11-06', 999, NULL, 1),
(2014, 1, '2014-11-07', 999, NULL, 1),
(2014, 1, '2014-11-08', 999, NULL, 1),
(2014, 1, '2014-11-09', 999, NULL, 1),
(2014, 1, '2014-11-12', 999, NULL, 1),
(2014, 1, '2014-11-13', 999, NULL, 1),
(2014, 1, '2014-11-14', 999, NULL, 1),
(2014, 1, '2014-11-15', 999, NULL, 1),
(2014, 1, '2014-11-16', 999, NULL, 1),
(2014, 1, '2014-11-19', 999, NULL, 1),
(2014, 1, '2014-11-20', 999, NULL, 1),
(2014, 1, '2014-11-21', 999, NULL, 1),
(2014, 1, '2014-11-22', 999, NULL, 1),
(2014, 1, '2014-11-23', 999, NULL, 1),
(2014, 1, '2014-11-26', 999, NULL, 1),
(2014, 1, '2014-11-27', 999, NULL, 1),
(2014, 1, '2014-11-28', 999, NULL, 1),
(2014, 1, '2014-11-29', 999, NULL, 1),
(2014, 1, '2014-11-30', 999, NULL, 1),
(2014, 1, '2014-12-03', 999, NULL, 1),
(2014, 1, '2014-12-04', 999, NULL, 1),
(2014, 1, '2014-12-05', 999, NULL, 1),
(2014, 1, '2014-12-06', 999, NULL, 1),
(2014, 1, '2014-12-07', 999, NULL, 1),
(2014, 1, '2014-12-10', 999, NULL, 1),
(2014, 1, '2014-12-11', 999, NULL, 1),
(2014, 1, '2014-12-12', 999, NULL, 1),
(2014, 1, '2014-12-13', 999, NULL, 1),
(2014, 1, '2014-12-14', 999, NULL, 1),
(2014, 1, '2014-12-17', 999, NULL, 1),
(2014, 1, '2014-12-18', 999, NULL, 1),
(2014, 1, '2014-12-19', 999, NULL, 1),
(2014, 1, '2014-12-20', 999, NULL, 1),
(2014, 1, '2014-12-21', 999, NULL, 1),
(2014, 1, '2014-12-24', 999, NULL, 1),
(2014, 1, '2014-12-25', 999, NULL, 1),
(2014, 1, '2014-12-26', 999, NULL, 1),
(2014, 1, '2014-12-27', 999, NULL, 1),
(2014, 1, '2014-12-28', 999, NULL, 1),
(2014, 1, '2014-12-31', 999, NULL, 1),
(2014, 1, '2015-01-01', 999, NULL, 1),
(2014, 1, '2015-01-02', 999, NULL, 1),
(2014, 1, '2015-01-03', 999, NULL, 1),
(2014, 1, '2015-01-04', 999, NULL, 1),
(2014, 1, '2015-01-07', 999, NULL, 1),
(2014, 1, '2015-01-08', 999, NULL, 1),
(2014, 1, '2015-01-09', 999, NULL, 1),
(2014, 1, '2015-01-10', 999, NULL, 1),
(2014, 1, '2015-01-11', 999, NULL, 1),
(2014, 1, '2015-01-14', 999, NULL, 1),
(2014, 1, '2015-01-15', 999, NULL, 1),
(2014, 1, '2015-01-16', 999, NULL, 1),
(2014, 1, '2015-01-17', 999, NULL, 1),
(2014, 1, '2015-01-18', 999, NULL, 1),
(2014, 1, '2015-01-21', 999, NULL, 1),
(2014, 1, '2015-01-22', 999, NULL, 1),
(2014, 1, '2015-01-23', 999, NULL, 1),
(2014, 1, '2015-01-24', 999, NULL, 1),
(2014, 1, '2015-01-25', 999, NULL, 1),
(2014, 1, '2015-01-28', 999, NULL, 1),
(2014, 1, '2015-01-29', 999, NULL, 1),
(2014, 1, '2015-01-30', 999, NULL, 1),
(2014, 1, '2015-01-31', 999, NULL, 1),
(2014, 1, '2015-02-01', 999, NULL, 1),
(2014, 1, '2015-02-04', 999, NULL, 1),
(2014, 1, '2015-02-05', 999, NULL, 1),
(2014, 1, '2015-02-06', 999, NULL, 1),
(2014, 1, '2015-02-07', 999, NULL, 1),
(2014, 1, '2015-02-08', 999, NULL, 1),
(2014, 1, '2015-02-11', 999, NULL, 1),
(2014, 1, '2015-02-12', 999, NULL, 1),
(2014, 1, '2015-02-13', 999, NULL, 1),
(2014, 1, '2015-02-14', 999, NULL, 1),
(2014, 1, '2015-02-15', 999, NULL, 1),
(2014, 1, '2015-02-18', 999, NULL, 1),
(2014, 1, '2015-02-19', 999, NULL, 1),
(2014, 1, '2015-02-20', 999, NULL, 1),
(2014, 1, '2015-02-21', 999, NULL, 1),
(2014, 1, '2015-02-22', 999, NULL, 1),
(2014, 1, '2015-02-25', 999, NULL, 1),
(2014, 1, '2015-02-26', 999, NULL, 1),
(2014, 1, '2015-02-27', 999, NULL, 1),
(2014, 1, '2015-02-28', 999, NULL, 1),
(2014, 1, '2015-03-01', 999, NULL, 1),
(2014, 1, '2015-03-04', 999, NULL, 1),
(2014, 1, '2015-03-05', 999, NULL, 1),
(2014, 1, '2015-03-06', 999, NULL, 1),
(2014, 1, '2015-03-07', 999, NULL, 1),
(2014, 1, '2015-03-08', 999, NULL, 1),
(2014, 1, '2015-03-11', 999, NULL, 1),
(2014, 1, '2015-03-12', 999, NULL, 1),
(2014, 1, '2015-03-13', 999, NULL, 1),
(2014, 1, '2015-03-14', 999, NULL, 1),
(2014, 1, '2015-03-15', 999, NULL, 1),
(2014, 1, '2015-03-18', 999, NULL, 1),
(2014, 1, '2015-03-19', 999, NULL, 1),
(2014, 1, '2015-03-20', 999, NULL, 1),
(2014, 1, '2015-03-21', 999, NULL, 1),
(2014, 1, '2015-03-22', 999, NULL, 1),
(2014, 1, '2015-03-25', 999, NULL, 1),
(2014, 1, '2015-03-26', 999, NULL, 1),
(2014, 1, '2015-03-27', 999, NULL, 1),
(2014, 1, '2015-03-28', 999, NULL, 1),
(2014, 1, '2015-03-29', 999, NULL, 1),
(2014, 1, '2015-04-01', 999, NULL, 1),
(2014, 1, '2015-04-02', 999, NULL, 1),
(2014, 1, '2015-04-03', 999, NULL, 1),
(2014, 1, '2015-04-04', 999, NULL, 1),
(2014, 1, '2015-04-05', 999, NULL, 1),
(2014, 1, '2015-04-08', 999, NULL, 1),
(2014, 1, '2015-04-09', 999, NULL, 1),
(2014, 1, '2015-04-10', 999, NULL, 1),
(2014, 1, '2015-04-11', 999, NULL, 1),
(2014, 1, '2015-04-12', 999, NULL, 1),
(2014, 1, '2015-04-15', 999, NULL, 1),
(2014, 1, '2015-04-16', 999, NULL, 1),
(2014, 1, '2015-04-17', 999, NULL, 1),
(2014, 1, '2015-04-18', 999, NULL, 1),
(2014, 1, '2015-04-19', 999, NULL, 1),
(2014, 1, '2015-04-22', 999, NULL, 1),
(2014, 1, '2015-04-23', 999, NULL, 1),
(2014, 1, '2015-04-24', 999, NULL, 1),
(2014, 1, '2015-04-25', 999, NULL, 1),
(2014, 1, '2015-04-26', 999, NULL, 1),
(2014, 1, '2015-04-29', 999, NULL, 1),
(2014, 1, '2015-04-30', 999, NULL, 1),
(2014, 1, '2015-05-01', 999, NULL, 1),
(2014, 1, '2015-05-02', 999, NULL, 1),
(2014, 1, '2015-05-03', 999, NULL, 1),
(2014, 1, '2015-05-06', 999, NULL, 1),
(2014, 1, '2015-05-07', 999, NULL, 1),
(2014, 1, '2015-05-08', 999, NULL, 1),
(2014, 1, '2015-05-09', 999, NULL, 1),
(2014, 1, '2015-05-10', 999, NULL, 1),
(2014, 1, '2015-05-13', 999, NULL, 1),
(2014, 1, '2015-05-14', 999, NULL, 1),
(2014, 1, '2015-05-15', 999, NULL, 1),
(2014, 1, '2015-05-16', 999, NULL, 1),
(2014, 1, '2015-05-17', 999, NULL, 1),
(2014, 1, '2015-05-20', 999, NULL, 1),
(2014, 1, '2015-05-21', 999, NULL, 1),
(2014, 1, '2015-05-22', 999, NULL, 1),
(2014, 1, '2015-05-23', 999, NULL, 1),
(2014, 1, '2015-05-24', 999, NULL, 1),
(2014, 1, '2015-05-27', 999, NULL, 1),
(2014, 1, '2015-05-28', 999, NULL, 1),
(2014, 1, '2015-05-29', 999, NULL, 1),
(2014, 1, '2015-05-30', 999, NULL, 1),
(2014, 1, '2015-05-31', 999, NULL, 1),
(2014, 1, '2015-06-03', 999, NULL, 1),
(2014, 1, '2015-06-04', 999, NULL, 1),
(2014, 1, '2015-06-05', 999, NULL, 1),
(2014, 1, '2015-06-06', 999, NULL, 1),
(2014, 1, '2015-06-07', 999, NULL, 1),
(2014, 1, '2015-06-10', 999, NULL, 1),
(2014, 1, '2015-06-11', 999, NULL, 1),
(2014, 1, '2015-06-12', 999, NULL, 1),
(2014, 1, '2015-06-13', 999, NULL, 1),
(2014, 1, '2015-06-14', 999, NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `attendance_calendars`
--

DROP TABLE IF EXISTS `attendance_calendars`;
CREATE TABLE IF NOT EXISTS `attendance_calendars` (
  `school_id` double DEFAULT NULL,
  `title` char(100) DEFAULT NULL,
  `syear` double(4,0) DEFAULT NULL,
  `calendar_id` int(11) NOT NULL AUTO_INCREMENT,
  `default_calendar` char(1) DEFAULT NULL,
  `rollover_id` double DEFAULT NULL,
  PRIMARY KEY (`calendar_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `attendance_calendars`
--

INSERT INTO `attendance_calendars` (`school_id`, `title`, `syear`, `calendar_id`, `default_calendar`, `rollover_id`) VALUES
(1, 'Main Calendar', 2014, 1, 'Y', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `attendance_codes`
--


DROP TABLE IF EXISTS `attendance_codes`;
CREATE TABLE IF NOT EXISTS `attendance_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `syear` double(4,0) DEFAULT NULL,
  `school_id` double DEFAULT NULL,
  `title` char(100) DEFAULT NULL,
  `short_name` char(10) DEFAULT NULL,
  `type` char(10) DEFAULT NULL,
  `state_code` char(1) DEFAULT NULL,
  `default_code` char(1) DEFAULT NULL,
  `table_name` char(30) DEFAULT NULL,
  `sort_order` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_codes_ind2` (`school_id`,`syear`),
  KEY `attendance_codes_ind3` (`short_name`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=25 ;

--
-- Dumping data for table `attendance_codes`
--

INSERT INTO `attendance_codes` (`id`, `syear`, `school_id`, `title`, `short_name`, `type`, `state_code`, `default_code`, `table_name`, `sort_order`) VALUES
(21, 2014, 1, 'Present', 'P', 'teacher', 'P', 'Y', '0', 1),
(22, 2014, 1, 'Unexcused Absence', 'A', 'teacher', 'A', NULL, '0', 2),
(23, 2014, 1, 'Excused Absence', 'E', 'official', 'A', NULL, '0', 3),
(24, 2014, 1, 'Tardy', 'T', 'official', 'P', NULL, '0', 4);

-- --------------------------------------------------------

--
-- Table structure for table `attendance_code_categories`
--

DROP TABLE IF EXISTS `attendance_code_categories`;
CREATE TABLE IF NOT EXISTS `attendance_code_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `syear` double(4,0) DEFAULT NULL,
  `school_id` double DEFAULT NULL,
  `title` char(255) DEFAULT NULL,
  `rollover_id` double DEFAULT NULL,
  `sort_order` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `attendance_code_categories_ind1` (`id`),
  KEY `attendance_code_categories_ind2` (`school_id`,`syear`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_completed`
--

DROP TABLE IF EXISTS `attendance_completed`;
CREATE TABLE IF NOT EXISTS `attendance_completed` (
  `staff_id` double NOT NULL,
  `school_date` date NOT NULL,
  `period_id` double NOT NULL,
  `table_name` double NOT NULL,
  PRIMARY KEY (`table_name`,`period_id`,`school_date`,`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_day`
--

DROP TABLE IF EXISTS `attendance_day`;
CREATE TABLE IF NOT EXISTS `attendance_day` (
  `student_id` double NOT NULL,
  `school_date` date NOT NULL,
  `minutes_present` double DEFAULT NULL,
  `state_value` double(2,1) DEFAULT NULL,
  `syear` double(4,0) DEFAULT NULL,
  `marking_period_id` double DEFAULT NULL,
  `comment` char(255) DEFAULT NULL,
  PRIMARY KEY (`school_date`,`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `attendance_period`
--

DROP TABLE IF EXISTS `attendance_period`;
CREATE TABLE IF NOT EXISTS `attendance_period` (
  `student_id` double NOT NULL,
  `school_date` date NOT NULL,
  `period_id` double NOT NULL,
  `attendance_code` double DEFAULT NULL,
  `attendance_teacher_code` double DEFAULT NULL,
  `attendance_reason` char(100) DEFAULT NULL,
  `admin` char(1) DEFAULT NULL,
  `course_period_id` double DEFAULT NULL,
  `marking_period_id` double DEFAULT NULL,
  `comment` char(100) DEFAULT NULL,
  PRIMARY KEY (`period_id`,`school_date`,`student_id`),
  KEY `attendance_period_ind1` (`student_id`),
  KEY `attendance_period_ind2` (`period_id`),
  KEY `attendance_period_ind3` (`attendance_code`),
  KEY `attendance_period_ind4` (`school_date`),
  KEY `attendance_period_ind5` (`attendance_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `billing_accounts_join_students`
--

DROP TABLE IF EXISTS `billing_accounts_join_students`;
CREATE TABLE IF NOT EXISTS `billing_accounts_join_students` (
  `syear` int(32) DEFAULT NULL,
  `account_id` int(32) DEFAULT NULL,
  `student_id` int(32) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `billing_fee_categories`
--

DROP TABLE IF EXISTS `billing_fee_categories`;
CREATE TABLE IF NOT EXISTS `billing_fee_categories` (
  `fee_category_id` double NOT NULL,
  `syear` double(4,0) NOT NULL,
  `type` char(10) DEFAULT NULL,
  `title` char(255) DEFAULT NULL,
  PRIMARY KEY (`fee_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `billing_fee_categories`
--

INSERT INTO `billing_fee_categories` (`fee_category_id`, `syear`, `type`, `title`) VALUES
(1, 2010, 'payment', 'Payment'),
(2, 2010, 'payment', 'Refund'),
(3, 2010, 'billing', 'Tuition'),
(4, 2010, 'billing', 'Financial Aid'),
(5, 2010, 'billing', 'Other Fees'),
(6, 2010, 'billing', 'ASC');


-- --------------------------------------------------------

--
-- Table structure for table `calendar_events`
--

DROP TABLE IF EXISTS `calendar_events`;
CREATE TABLE IF NOT EXISTS `calendar_events` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `syear` double(4,0) DEFAULT NULL,
  `school_id` double DEFAULT NULL,
  `school_date` date DEFAULT NULL,
  `title` char(50) DEFAULT NULL,
  `description` longtext,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `config`
--

DROP TABLE IF EXISTS `config`;
CREATE TABLE IF NOT EXISTS `config` (
  `title` char(100) DEFAULT NULL,
  `syear` double(4,0) DEFAULT NULL,
  `login` char(3) DEFAULT NULL,
  `description` char(175) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `config`
--

INSERT INTO `config` (`title`, `syear`, `login`) VALUES
('core.dbversion', NULL, '5'),
('student_billing.dbversion', NULL, '1'),
('Centre School Software', 2004, 'Yes'),
('Centre School Software', 2004, 'Yes'),
('Centre School Software', 2014, 'No');

-- --------------------------------------------------------

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
CREATE TABLE IF NOT EXISTS `courses` (
  `syear` double(4,0) NOT NULL,
  `course_id` int(11) NOT NULL AUTO_INCREMENT,
  `subject_id` double NOT NULL,
  `school_id` double NOT NULL,
  `grade_level` double DEFAULT NULL,
  `title` char(100) DEFAULT NULL,
  `short_name` char(25) DEFAULT NULL,
  `rollover_id` double DEFAULT NULL,
  `custom_100380004` char(1) DEFAULT NULL,
  `custom_100380002` char(255) DEFAULT NULL,
  `custom_100380001` char(255) DEFAULT NULL,
  `custom_100380005` char(1) DEFAULT NULL,
  `custom_100380006` char(1) DEFAULT NULL,
  `custom_100380007` char(1) DEFAULT NULL,
  `custom_100380008` char(1) DEFAULT NULL,
  `custom_100380009` char(1) DEFAULT NULL,
  `custom_100380010` char(1) DEFAULT NULL,
  PRIMARY KEY (`course_id`),
  KEY `courses_ind1` (`syear`,`course_id`),
  KEY `courses_ind2` (`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `course_details`
--
DROP TABLE IF EXISTS `course_details`;
CREATE TABLE IF NOT EXISTS `course_details` (
`school_id` double
,`syear` double(4,0)
,`marking_period_id` double
,`period_id` double
,`subject_id` double
,`course_id` double
,`course_period_id` int(11)
,`teacher_id` double
,`course_title` char(100)
,`cp_title` char(100)
,`grade_scale_id` double
,`mp` char(3)
,`credits` double
);
-- --------------------------------------------------------

--
-- Table structure for table `course_periods`
--

DROP TABLE IF EXISTS `course_periods`;
CREATE TABLE IF NOT EXISTS `course_periods` (
  `syear` double(4,0) NOT NULL,
  `school_id` double NOT NULL,
  `course_period_id` int(11) NOT NULL AUTO_INCREMENT,
  `course_id` double NOT NULL,
  `title` char(100) DEFAULT NULL,
  `short_name` char(25) DEFAULT NULL,
  `period_id` double DEFAULT NULL,
  `mp` char(3) DEFAULT NULL,
  `marking_period_id` double DEFAULT NULL,
  `teacher_id` double DEFAULT NULL,
  `room` char(10) DEFAULT NULL,
  `total_seats` double DEFAULT NULL,
  `filled_seats` double DEFAULT NULL,
  `does_honor_roll` char(1) DEFAULT NULL,
  `does_class_rank` char(1) DEFAULT NULL,
  `gender_restriction` char(1) DEFAULT NULL,
  `house_restriction` char(1) DEFAULT NULL,
  `availability` double DEFAULT NULL,
  `rollover_id` double DEFAULT NULL,
  `parent_id` double DEFAULT NULL,
  `days` char(7) DEFAULT NULL,
  `calendar_id` double DEFAULT NULL,
  `half_day` char(1) DEFAULT NULL,
  `does_breakoff` char(1) DEFAULT NULL,
  `grade_scale_id` double DEFAULT NULL,
  `does_attendance` char(255) DEFAULT NULL,
  `credits` double DEFAULT NULL,
  PRIMARY KEY (`course_period_id`),
  KEY `course_periods_ind1` (`syear`),
  KEY `course_periods_ind3` (`course_period_id`),
  KEY `course_periods_ind4` (`period_id`),
  KEY `course_periods_ind5` (`parent_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `course_subjects`
--

DROP TABLE IF EXISTS `course_subjects`;
CREATE TABLE IF NOT EXISTS `course_subjects` (
  `syear` double(4,0) DEFAULT NULL,
  `school_id` double DEFAULT NULL,
  `subject_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` char(100) DEFAULT NULL,
  `short_name` char(25) DEFAULT NULL,
  `rollover_id` double DEFAULT NULL,
  `sort_order` double DEFAULT NULL,
  PRIMARY KEY (`subject_id`),
  KEY `course_subjects_ind1` (`subject_id`,`school_id`,`syear`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `custom`
--

DROP TABLE IF EXISTS `custom`;
CREATE TABLE IF NOT EXISTS `custom` (
  `student_id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`student_id`),
  KEY `custom_ind` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `custom_fields`
--

DROP TABLE IF EXISTS `custom_fields`;
CREATE TABLE IF NOT EXISTS `custom_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` char(10) DEFAULT NULL,
  `search` char(1) DEFAULT NULL,
  `title` longtext,
  `select_options` longtext,
  `category_id` double DEFAULT NULL,
  `system_field` char(1) DEFAULT NULL,
  `default_selection` char(255) DEFAULT NULL,
  `sort_order` double DEFAULT NULL,
  `required` char(1) DEFAULT NULL,
  `table` char(25) NOT NULL,
  `description` longtext,
  PRIMARY KEY (`id`,`table`),
  KEY `address_desc_ind2` (`type`),
  KEY `address_fields_ind3` (`category_id`),
  KEY `custom_desc_ind` (`id`),
  KEY `custom_desc_ind2` (`type`),
  KEY `custom_fields_ind3` (`category_id`),
  KEY `people_desc_ind2` (`type`),
  KEY `people_fields_ind3` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=200000012 ;

--
-- Dumping data for table `custom_fields`
--

INSERT INTO `custom_fields` (`id`, `type`, `search`, `title`, `select_options`, `category_id`, `system_field`, `default_selection`, `sort_order`, `required`, `table`, `description`) VALUES
(200000000, 'select', NULL, 'Gender', 'Male\r\nFemale', 1, 'Y', NULL, 0, 'Y', '', NULL),
(200000001, 'select', NULL, 'Ethnicity', 'White, Non-Hispanic\r\nBlack, Non-Hispanic\r\nAmer. Indian or Alaskan Native\r\nAsian or Pacific Islander\r\nHispanic\r\nOther', 1, 'Y', NULL, 1, 'Y', '', NULL),
(200000002, 'text', NULL, 'Common Name', NULL, 1, 'Y', NULL, 2, NULL, '', NULL),
(200000003, 'text', NULL, 'Social Security', NULL, 1, 'Y', NULL, 3, NULL, '', NULL),
(200000004, 'date', NULL, 'Birthdate', NULL, 1, 'Y', NULL, 4, NULL, '', NULL),
(200000005, 'select', NULL, 'Language', 'English\r\nSpanish', 1, 'Y', NULL, 5, NULL, '', NULL),
(200000006, 'text', NULL, 'Physician', NULL, 2, 'Y', NULL, 6, NULL, '', NULL),
(200000007, 'text', NULL, 'Physician Phone', NULL, 2, 'Y', NULL, 7, NULL, '', NULL),
(200000008, 'text', NULL, 'Preferred Hospital', NULL, 2, 'Y', NULL, 8, NULL, '', NULL),
(200000009, 'textarea', NULL, 'Comments', NULL, 2, 'Y', NULL, 9, NULL, '', NULL),
(200000010, 'radio', NULL, 'Has Doctor''s Note', NULL, 2, 'Y', NULL, 10, NULL, '', NULL),
(200000011, 'textarea', NULL, 'Doctor''s Note Comments', NULL, 2, 'Y', NULL, 11, NULL, '', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `eligibility`
--

DROP TABLE IF EXISTS `eligibility`;
CREATE TABLE IF NOT EXISTS `eligibility` (
  `student_id` double DEFAULT NULL,
  `syear` double(4,0) DEFAULT NULL,
  `school_date` date DEFAULT NULL,
  `period_id` double DEFAULT NULL,
  `eligibility_code` char(20) DEFAULT NULL,
  `course_period_id` double DEFAULT NULL,
  KEY `eligibility_ind1` (`school_date`,`course_period_id`,`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `eligibility_activities`
--

DROP TABLE IF EXISTS `eligibility_activities`;
CREATE TABLE IF NOT EXISTS `eligibility_activities` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `syear` double(4,0) DEFAULT NULL,
  `school_id` double DEFAULT NULL,
  `title` char(100) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `eligibility_activities_ind1` (`syear`,`school_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `eligibility_activities`
--

INSERT INTO `eligibility_activities` (`id`, `syear`, `school_id`, `title`, `start_date`, `end_date`) VALUES
(1, 2015, 1, 'Boys Basketball', '2015-10-15', '2015-03-29'),
(2, 2015, 1, 'Chess Team', '2015-09-04', '2015-06-13'),
(8, 2015, 1, 'Girls Basketball', '2015-10-15', '2015-03-07');

-- --------------------------------------------------------

--
-- Table structure for table `eligibility_completed`
--

DROP TABLE IF EXISTS `eligibility_completed`;
CREATE TABLE IF NOT EXISTS `eligibility_completed` (
  `staff_id` double NOT NULL,
  `school_date` date NOT NULL,
  `period_id` double NOT NULL,
  PRIMARY KEY (`period_id`,`school_date`,`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stand-in structure for view `enroll_grade`
--
DROP TABLE IF EXISTS `enroll_grade`;
CREATE TABLE IF NOT EXISTS `enroll_grade` (
`id` int(11)
,`syear` double(4,0)
,`school_id` double
,`student_id` double
,`start_date` date
,`end_date` date
,`short_name` char(5)
,`title` char(50)
);
-- --------------------------------------------------------

--
-- Table structure for table `food_service_accounts`
--

DROP TABLE IF EXISTS `food_service_accounts`;
CREATE TABLE IF NOT EXISTS `food_service_accounts` (
  `account_id` double NOT NULL,
  `balance` double(9,2) NOT NULL,
  `transaction_id` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `food_service_categories`
--

DROP TABLE IF EXISTS `food_service_categories`;
CREATE TABLE IF NOT EXISTS `food_service_categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` double NOT NULL,
  `menu_id` double NOT NULL,
  `title` char(25) DEFAULT NULL,
  `sort_order` double DEFAULT NULL,
  PRIMARY KEY (`category_id`),
  KEY `food_service_categories_title` (`title`,`menu_id`,`school_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `food_service_categories`
--

INSERT INTO `food_service_categories` (`category_id`, `school_id`, `menu_id`, `title`, `sort_order`) VALUES
(1, 1, 1, 'Lunch', 1);

-- --------------------------------------------------------

--
-- Table structure for table `food_service_items`
--

DROP TABLE IF EXISTS `food_service_items`;
CREATE TABLE IF NOT EXISTS `food_service_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` double NOT NULL,
  `short_name` char(25) DEFAULT NULL,
  `sort_order` double DEFAULT NULL,
  `description` char(25) DEFAULT NULL,
  `icon` char(50) DEFAULT NULL,
  `price` double(9,2) NOT NULL,
  `price_reduced` double(9,2) DEFAULT NULL,
  `price_free` double(9,2) DEFAULT NULL,
  `price_staff` double(9,2) NOT NULL,
  PRIMARY KEY (`item_id`),
  KEY `food_service_items_short_name` (`short_name`,`school_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `food_service_items`
--

INSERT INTO `food_service_items` (`item_id`, `school_id`, `short_name`, `sort_order`, `description`, `icon`, `price`, `price_reduced`, `price_free`, `price_staff`) VALUES
(1, 1, 'Lunch', 1, 'Student Lunch', 'Lunch.jpg', 1.65, 0.85, NULL, 2.00),
(2, 1, 'Milk', 2, 'Milk', 'Milk.jpg', 0.25, NULL, NULL, 0.50),
(3, 1, 'Extra', 3, 'Xtra', 'Fries.jpg', 0.50, NULL, NULL, 0.50),
(4, 1, 'Pizza', 4, 'Pizza', 'Pizza.jpg', 1.00, NULL, NULL, 1.00);

-- --------------------------------------------------------

--
-- Table structure for table `food_service_menus`
--

DROP TABLE IF EXISTS `food_service_menus`;
CREATE TABLE IF NOT EXISTS `food_service_menus` (
  `menu_id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` double NOT NULL,
  `title` char(25) NOT NULL,
  `sort_order` double DEFAULT NULL,
  PRIMARY KEY (`menu_id`),
  KEY `food_service_menus_title` (`title`,`school_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `food_service_menus`
--

INSERT INTO `food_service_menus` (`menu_id`, `school_id`, `title`, `sort_order`) VALUES
(1, 1, 'Lunch', 1);

-- --------------------------------------------------------

--
-- Table structure for table `food_service_menu_items`
--

DROP TABLE IF EXISTS `food_service_menu_items`;
CREATE TABLE IF NOT EXISTS `food_service_menu_items` (
  `menu_item_id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` double NOT NULL,
  `menu_id` double NOT NULL,
  `item_id` double NOT NULL,
  `category_id` double DEFAULT NULL,
  `sort_order` double DEFAULT NULL,
  `does_count` char(1) DEFAULT NULL,
  PRIMARY KEY (`menu_item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `food_service_menu_items`
--

INSERT INTO `food_service_menu_items` (`menu_item_id`, `school_id`, `menu_id`, `item_id`, `category_id`, `sort_order`, `does_count`) VALUES
(1, 1, 1, 1, 1, 1, 'Y'),
(2, 1, 1, 2, 1, 2, 'Y'),
(3, 1, 1, 3, 1, 3, 'Y'),
(4, 1, 1, 4, 1, 4, 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `food_service_staff_accounts`
--

DROP TABLE IF EXISTS `food_service_staff_accounts`;
CREATE TABLE IF NOT EXISTS `food_service_staff_accounts` (
  `staff_id` double NOT NULL,
  `status` char(25) DEFAULT NULL,
  `barcode` char(50) DEFAULT NULL,
  `balance` double(9,2) NOT NULL,
  `transaction_id` double DEFAULT NULL,
  KEY `staff_barcode` (`barcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `food_service_staff_accounts`
--

INSERT INTO `food_service_staff_accounts` (`staff_id`, `status`, `barcode`, `balance`, `transaction_id`) VALUES
(1, NULL, NULL, 0.00, 0);

-- --------------------------------------------------------

--
-- Table structure for table `food_service_staff_transaction_items`
--

DROP TABLE IF EXISTS `food_service_staff_transaction_items`;
CREATE TABLE IF NOT EXISTS `food_service_staff_transaction_items` (
  `item_id` double NOT NULL,
  `transaction_id` double NOT NULL,
  `amount` double(9,2) DEFAULT NULL,
  `short_name` char(25) DEFAULT NULL,
  `description` char(50) DEFAULT NULL,
  PRIMARY KEY (`transaction_id`,`item_id`),
  KEY `food_service_staff_transaction_items_ind1` (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `food_service_staff_transactions`
--

DROP TABLE IF EXISTS `food_service_staff_transactions`;
CREATE TABLE IF NOT EXISTS `food_service_staff_transactions` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` int(11) NOT NULL,
  `school_id` int(11) DEFAULT NULL,
  `syear` double(4,0) DEFAULT NULL,
  `balance` double(9,2) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `short_name` char(25) DEFAULT NULL,
  `description` char(50) DEFAULT NULL,
  `seller_id` double DEFAULT NULL,
  PRIMARY KEY (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `food_service_student_accounts`
--

DROP TABLE IF EXISTS `food_service_student_accounts`;
CREATE TABLE IF NOT EXISTS `food_service_student_accounts` (
  `student_id` double NOT NULL,
  `account_id` double NOT NULL,
  `discount` char(25) DEFAULT NULL,
  `status` char(25) DEFAULT NULL,
  `barcode` char(50) DEFAULT NULL,
  KEY `students_barcode` (`barcode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `food_service_student_accounts`
--

INSERT INTO `food_service_student_accounts` (`student_id`, `account_id`, `discount`, `status`, `barcode`) VALUES
(1, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `food_service_transactions`
--

DROP TABLE IF EXISTS `food_service_transactions`;
CREATE TABLE IF NOT EXISTS `food_service_transactions` (
  `transaction_id` int(11) NOT NULL AUTO_INCREMENT,
  `account_id` double NOT NULL,
  `student_id` double DEFAULT NULL,
  `syear` double(4,0) DEFAULT NULL,
  `discount` char(25) DEFAULT NULL,
  `balance` double(9,2) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT NULL,
  `short_name` char(25) DEFAULT NULL,
  `description` char(50) DEFAULT NULL,
  `seller_id` double DEFAULT NULL,
  `school_id` double DEFAULT NULL,
  PRIMARY KEY (`transaction_id`),
  KEY `   fst_balance_timestamp_index` (`balance`,`timestamp`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `food_service_transaction_items`
--

DROP TABLE IF EXISTS `food_service_transaction_items`;
CREATE TABLE IF NOT EXISTS `food_service_transaction_items` (
  `item_id` int(11) NOT NULL AUTO_INCREMENT,
  `transaction_id` double NOT NULL,
  `amount` double(9,2) DEFAULT NULL,
  `discount` char(25) DEFAULT NULL,
  `short_name` char(25) DEFAULT NULL,
  `description` char(50) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  KEY `food_service_transaction_items_ind1` (`transaction_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `gradebook_assignments`
--

DROP TABLE IF EXISTS `gradebook_assignments`;
CREATE TABLE IF NOT EXISTS `gradebook_assignments` (
  `assignment_id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` double DEFAULT NULL,
  `marking_period_id` double DEFAULT NULL,
  `course_period_id` double DEFAULT NULL,
  `course_id` double DEFAULT NULL,
  `assignment_type_id` double NOT NULL,
  `title` char(100) DEFAULT NULL,
  `assigned_date` date DEFAULT NULL,
  `due_date` date DEFAULT NULL,
  `points` double DEFAULT NULL,
  `description` longtext,
  PRIMARY KEY (`assignment_id`),
  KEY `gradebook_assignment_types_ind1` (`course_id`,`staff_id`),
  KEY `gradebook_assignments_ind1` (`marking_period_id`,`staff_id`),
  KEY `gradebook_assignments_ind2` (`course_period_id`,`course_id`),
  KEY `gradebook_assignments_ind3` (`assignment_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `gradebook_assignment_types`
--

DROP TABLE IF EXISTS `gradebook_assignment_types`;
CREATE TABLE IF NOT EXISTS `gradebook_assignment_types` (
  `assignment_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `staff_id` double DEFAULT NULL,
  `course_id` double DEFAULT NULL,
  `title` char(100) DEFAULT NULL,
  `final_grade_percent` double(6,5) DEFAULT NULL,
  `sort_order` double DEFAULT NULL,
  `color` char(30) DEFAULT NULL,
  PRIMARY KEY (`assignment_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `gradebook_grades`
--

DROP TABLE IF EXISTS `gradebook_grades`;
CREATE TABLE IF NOT EXISTS `gradebook_grades` (
  `student_id` double NOT NULL,
  `period_id` double DEFAULT NULL,
  `course_period_id` double NOT NULL,
  `assignment_id` double NOT NULL,
  `points` double(6,2) DEFAULT NULL,
  `comment` char(100) DEFAULT NULL,
  PRIMARY KEY (`course_period_id`,`assignment_id`,`student_id`),
  KEY `gradebook_grades_ind1` (`assignment_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `grades_completed`
--

DROP TABLE IF EXISTS `grades_completed`;
CREATE TABLE IF NOT EXISTS `grades_completed` (
  `staff_id` double NOT NULL,
  `marking_period_id` char(10) NOT NULL,
  `course_period_id` double NOT NULL,
  PRIMARY KEY (`course_period_id`,`marking_period_id`,`staff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `history_marking_periods`
--

DROP TABLE IF EXISTS `history_marking_periods`;
CREATE TABLE IF NOT EXISTS `history_marking_periods` (
  `parent_id` int(32) NOT NULL,
  `mp_type` char(20) DEFAULT NULL,
  `name` char(30) DEFAULT NULL,
  `short_name` char(10) DEFAULT NULL,
  `post_end_date` date DEFAULT NULL,
  `school_id` int(32) DEFAULT NULL,
  `syear` int(32) DEFAULT NULL,
  `marking_period_id` int(32) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`marking_period_id`),
  KEY `history_marking_period_ind1` (`school_id`),
  KEY `history_marking_period_ind2` (`syear`),
  KEY `history_marking_period_ind3` (`mp_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `homework_period`
--

DROP TABLE IF EXISTS `homework_period`;
CREATE TABLE IF NOT EXISTS `homework_period` (
  `student_id` double DEFAULT NULL,
  `school_date` date DEFAULT NULL,
  `period_id` double DEFAULT NULL,
  `attendance_code` double DEFAULT NULL,
  `attendance_teacher_code` double DEFAULT NULL,
  `attendance_reason` char(100) DEFAULT NULL,
  `admin` char(1) DEFAULT NULL,
  `course_period_id` double DEFAULT NULL,
  `marking_period_id` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lunch_config`
--

DROP TABLE IF EXISTS `lunch_config`;
CREATE TABLE IF NOT EXISTS `lunch_config` (
  `school_id` double DEFAULT NULL,
  `negative_balance` double(6,2) DEFAULT NULL,
  `warning_balance` double(6,2) DEFAULT NULL,
  `allow_override` char(1) DEFAULT NULL,
  `syear` double(4,0) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lunch_menu`
--

DROP TABLE IF EXISTS `lunch_menu`;
CREATE TABLE IF NOT EXISTS `lunch_menu` (
  `id` double DEFAULT NULL,
  `category_id` double DEFAULT NULL,
  `syear` double(4,0) DEFAULT NULL,
  `school_id` double DEFAULT NULL,
  `title` char(255) DEFAULT NULL,
  `sort_order` double DEFAULT NULL,
  `price` double(5,2) DEFAULT NULL,
  `reduced_price` double(5,2) DEFAULT NULL,
  `free_price` double(5,2) DEFAULT NULL,
  `key` char(3) DEFAULT NULL,
  `color` char(6) DEFAULT NULL,
  `icon` char(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lunch_menu_categories`
--

DROP TABLE IF EXISTS `lunch_menu_categories`;
CREATE TABLE IF NOT EXISTS `lunch_menu_categories` (
  `id` double DEFAULT NULL,
  `syear` double(4,0) DEFAULT NULL,
  `school_id` double DEFAULT NULL,
  `title` char(255) DEFAULT NULL,
  `sort_order` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lunch_period`
--

DROP TABLE IF EXISTS `lunch_period`;
CREATE TABLE IF NOT EXISTS `lunch_period` (
  `student_id` double NOT NULL,
  `school_date` date NOT NULL,
  `period_id` double NOT NULL,
  `attendance_code` double DEFAULT NULL,
  `attendance_teacher_code` double DEFAULT NULL,
  `attendance_reason` char(100) DEFAULT NULL,
  `admin` char(1) DEFAULT NULL,
  `course_period_id` double DEFAULT NULL,
  `marking_period_id` double DEFAULT NULL,
  `table_name` double DEFAULT NULL,
  `comment` char(100) DEFAULT NULL,
  PRIMARY KEY (`period_id`,`school_date`,`student_id`),
  KEY `lunch_period_ind1` (`student_id`),
  KEY `lunch_period_ind2` (`period_id`),
  KEY `lunch_period_ind3` (`attendance_code`),
  KEY `lunch_period_ind4` (`school_date`),
  KEY `lunch_period_ind5` (`attendance_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lunch_transactions`
--

DROP TABLE IF EXISTS `lunch_transactions`;
CREATE TABLE IF NOT EXISTS `lunch_transactions` (
  `id` double DEFAULT NULL,
  `syear` double(4,0) DEFAULT NULL,
  `school_id` double DEFAULT NULL,
  `run_id` double DEFAULT NULL,
  `transaction_date` date DEFAULT NULL,
  `student_id` double DEFAULT NULL,
  `menu_id` double DEFAULT NULL,
  `title` char(255) DEFAULT NULL,
  `amount` double(6,2) DEFAULT NULL,
  `count` double DEFAULT NULL,
  `fsc` char(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lunch_users`
--

DROP TABLE IF EXISTS `lunch_users`;
CREATE TABLE IF NOT EXISTS `lunch_users` (
  `user_id` double DEFAULT NULL,
  `school_id` double DEFAULT NULL,
  `name` char(255) DEFAULT NULL,
  `password` char(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Stand-in structure for view `marking_periods`
--
DROP TABLE IF EXISTS `marking_periods`;
CREATE TABLE IF NOT EXISTS `marking_periods` (
`marking_period_id` int(11)
,`mp_source` varchar(7)
,`syear` double(11,0)
,`school_id` double
,`mp_type` varchar(20)
,`title` char(50)
,`short_name` char(10)
,`sort_order` double
,`parent_id` double
,`grandparent_id` double
,`start_date` date
,`end_date` date
,`post_start_date` date
,`post_end_date` date
,`does_grades` varchar(1)
,`does_exam` char(1)
,`does_comments` char(1)
);
-- --------------------------------------------------------

--
-- Table structure for table `people`
--


DROP TABLE IF EXISTS `people`;
CREATE TABLE IF NOT EXISTS `people` (
  `person_id` int(11) NOT NULL AUTO_INCREMENT,
  `last_name` char(50) NOT NULL,
  `first_name` char(50) NOT NULL,
  `middle_name` char(50) DEFAULT NULL,
  `username` char(100) DEFAULT NULL,
  `password` char(100) DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `failed_login` double DEFAULT NULL,
  `profile_id` double DEFAULT NULL,
  `phone_1` char(100) DEFAULT NULL,
  `phone_1_flags` char(10) DEFAULT NULL,
  `email_1` char(100) DEFAULT NULL,
  `email_1_flags` char(10) DEFAULT NULL,
  `phone_2` char(100) DEFAULT NULL,
  `phone_2_flags` char(10) DEFAULT NULL,
  `email_2` char(100) DEFAULT NULL,
  `email_2_flags` char(10) DEFAULT NULL,
  `phone_3` char(100) DEFAULT NULL,
  `phone_3_flags` char(10) DEFAULT NULL,
  `email_3` char(100) DEFAULT NULL,
  `email_3_flags` char(10) DEFAULT NULL,
  `phone_4` char(100) DEFAULT NULL,
  `phone_4_flags` char(10) DEFAULT NULL,
  `email_4` char(100) DEFAULT NULL,
  `email_4_flags` char(10) DEFAULT NULL,
  PRIMARY KEY (`person_id`),
  KEY `people_1` (`first_name`,`last_name`),
  KEY `people_3` (`middle_name`,`first_name`,`last_name`,`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE `people` ADD `custom_3` CHAR( 1 ) NOT NULL ,
ADD `custom_4` CHAR( 1 ) NOT NULL ,
ADD `custom_5` CHAR( 1 ) NOT NULL ,
ADD `custom_6` CHAR( 1 ) NOT NULL ,
ADD `custom_7` CHAR( 1 ) NOT NULL ,
ADD `custom_8` CHAR( 1 ) NOT NULL ,
ADD `custom_9` CHAR( 1 ) NOT NULL ,
ADD `custom_10` CHAR( 1 ) NOT NULL ,
ADD `custom_11` CHAR( 1 ) NOT NULL ,
ADD `custom_12` CHAR( 1 ) NOT NULL ,
ADD `custom_13` CHAR( 1 ) NOT NULL ,
ADD `custom_14` CHAR( 1 ) NOT NULL ,
ADD `custom_15` CHAR( 1 ) NOT NULL ,
ADD `custom_16` CHAR( 1 ) NOT NULL ,
ADD `custom_17` CHAR( 1 ) NOT NULL ,
ADD `custom_18` CHAR( 1 ) NOT NULL ,
ADD `custom_19` CHAR( 1 ) NOT NULL ,
ADD `custom_20` CHAR( 1 ) NOT NULL ,
ADD `custom_21` CHAR( 1 ) NOT NULL ,
ADD `custom_22` CHAR( 1 ) NOT NULL ;

-- --------------------------------------------------------

--
-- Table structure for table `people_fields`
--

DROP TABLE IF EXISTS `people_fields`;
CREATE TABLE IF NOT EXISTS `people_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` char(10) DEFAULT NULL,
  `search` char(1) DEFAULT NULL,
  `title` longtext,
  `sort_order` double DEFAULT NULL,
  `select_options` longtext,
  `category_id` double DEFAULT NULL,
  `system_field` char(1) DEFAULT NULL,
  `required` char(1) DEFAULT NULL,
  `default_selection` char(255) DEFAULT NULL,
  `description` longtext,
  PRIMARY KEY (`id`),
  KEY `people_desc_ind` (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


INSERT INTO `people_fields` (`id`, `type`, `search`, `title`, `sort_order`, `select_options`, `category_id`, `system_field`, `required`, `default_selection`) VALUES
(3, 'radio', NULL, '1st', NULL, '1st', 4, NULL, NULL, NULL),
(4, 'radio', NULL, '2nd', NULL, '2nd', 4, NULL, NULL, NULL),
(5, 'radio', NULL, '3rd', NULL, '3rd', 4, NULL, NULL, NULL),
(6, 'radio', NULL, '4th', NULL, '4th', 4, NULL, NULL, NULL),
(7, 'radio', NULL, '5th', NULL, '5th', 4, NULL, NULL, NULL),
(8, 'radio', NULL, '6th', NULL, '6th', 4, NULL, NULL, NULL),
(9, 'radio', NULL, '7th', NULL, '7th', 4, NULL, NULL, NULL),
(10, 'radio', NULL, '8th', NULL, '8th', 4, NULL, NULL, NULL),
(11, 'radio', NULL, 'Donor', NULL, 'Donor', 4, NULL, NULL, NULL),
(12, 'radio', NULL, 'TK', NULL, 'TK', 4, NULL, NULL, NULL),
(13, 'radio', NULL, 'KDG', NULL, 'KDG', 4, NULL, NULL, NULL),
(14, 'radio', NULL, 'alum', NULL, NULL, 4, NULL, NULL, NULL),
(15, 'radio', NULL, 'Board Member', NULL, NULL, 4, NULL, NULL, NULL),
(16, 'radio', NULL, 'Federation/Clergy', NULL, NULL, 4, NULL, NULL, NULL),
(17, 'radio', NULL, 'Grandparents', NULL, NULL, 4, NULL, NULL, NULL),
(18, 'radio', NULL, 'Inactive', NULL, NULL, 4, NULL, NULL, NULL),
(19, 'radio', NULL, 'Misc. Supporter', NULL, NULL, 4, NULL, NULL, NULL),
(20, 'radio', NULL, 'Parent', NULL, NULL, 4, NULL, NULL, NULL),
(21, 'radio', NULL, 'Parent Alum', NULL, NULL, 4, NULL, NULL, NULL),
(22, 'radio', NULL, 'PT Officer', NULL, NULL, 4, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `people_field_categories`
--

DROP TABLE IF EXISTS `people_field_categories`;
CREATE TABLE IF NOT EXISTS `people_field_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` longtext,
  `sort_order` double DEFAULT NULL,
  `custody` char(1) DEFAULT NULL,
  `emergency` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

INSERT INTO `people_field_categories` (`id`, `title`, `sort_order`, `custody`, `emergency`) VALUES
(4, 'Parent Info', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `people_join_contacts`
--

DROP TABLE IF EXISTS `people_join_contacts`;
CREATE TABLE IF NOT EXISTS `people_join_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `person_id` double DEFAULT NULL,
  `title` char(100) DEFAULT NULL,
  `value` char(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `people_join_contacts_ind1` (`person_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `portal_notes`
--

DROP TABLE IF EXISTS `portal_notes`;
CREATE TABLE IF NOT EXISTS `portal_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` double DEFAULT NULL,
  `syear` double(4,0) DEFAULT NULL,
  `title` char(255) DEFAULT NULL,
  `content` longtext,
  `sort_order` double DEFAULT NULL,
  `published_user` double DEFAULT NULL,
  `published_date` timestamp NULL DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `published_profiles` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `profile_exceptions`
--


DROP TABLE IF EXISTS `profile_exceptions`;
CREATE TABLE IF NOT EXISTS `profile_exceptions` (
  `profile_id` decimal(10,0) DEFAULT NULL,
  `modname` varchar(255) DEFAULT NULL,
  `can_use` varchar(1) DEFAULT NULL,
  `can_edit` varchar(1) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `profile_exceptions`
--

INSERT INTO `profile_exceptions` (`profile_id`, `modname`, `can_use`, `can_edit`) VALUES
('0', 'Attendance/DailySummary.php', 'Y', NULL),
('0', 'Attendance/StudentSummary.php', 'Y', NULL),
('0', 'Eligibility/Student.php', 'Y', NULL),
('0', 'Eligibility/StudentList.php', 'Y', NULL),
('0', 'Food_Service/Accounts.php', 'Y', NULL),
('0', 'Food_Service/DailyMenus.php', 'Y', NULL),
('0', 'Food_Service/MenuItems.php', 'Y', NULL),
('0', 'Food_Service/Statements.php', 'Y', NULL),
('0', 'Grades/FinalGrades.php', 'Y', NULL),
('0', 'Grades/GPARankList.php', 'Y', NULL),
('0', 'Grades/ReportCards.php', 'Y', NULL),
('0', 'Grades/StudentGrades.php', 'Y', NULL),
('0', 'Grades/Transcripts.php', 'Y', NULL),
('0', 'Resources/Redirect.php?to=doc', 'Y', 'Y'),
('0', 'Resources/Redirect.php?to=forums', 'Y', 'Y'),
('0', 'Resources/Redirect.php?to=translate', 'Y', 'Y'),
('0', 'Resources/Redirect.php?to=videohelp', 'Y', 'Y'),
('0', 'Scheduling/PrintClassPictures.php', 'Y', NULL),
('0', 'Scheduling/Requests.php', 'Y', NULL),
('0', 'Scheduling/Schedule.php', 'Y', NULL),
('0', 'School_Setup/Calendar.php', 'Y', NULL),
('0', 'School_Setup/Schools.php', 'Y', NULL),
('0', 'Students/Student.php', 'Y', NULL),
('0', 'Students/Student.php&category_id=1', 'Y', NULL),
('0', 'Students/Student.php&category_id=3', 'Y', NULL),
('0', 'Students/Student.php&category_id=5', 'Y', NULL),
('1', 'Admin/AddressFields.php', 'Y', 'Y'),
('1', 'Admin/AttendanceCodes.php', 'Y', 'Y'),
('1', 'Admin/CalcGPA.php', 'Y', 'Y'),
('1', 'Admin/CopySchool.php', 'Y', 'Y'),
('1', 'Admin/DailyMenus.php', 'Y', 'Y'),
('1', 'Admin/DuplicateAttendance.php', 'Y', 'Y'),
('1', 'Admin/EditHistoryMarkingPeriods.php', 'Y', 'Y'),
('1', 'Admin/Ethnicity.php', 'Y', 'Y'),
('1', 'Admin/EnrollmentCodes.php', 'Y', 'Y'),
('1', 'Admin/EntryTimes.php', 'Y', 'Y'),
('1', 'Admin/Exceptions.php', 'Y', 'Y'),
('1', 'Admin/FixDailyAttendance.php', 'Y', 'Y'),
('1', 'Admin/ImportDataToCoodle.php', 'Y', 'Y'),
('1', 'Admin/Kiosk.php', 'Y', 'Y'),
('1', 'Admin/MenuItems.php', 'Y', 'Y'),
('1', 'Admin/Menus.php', 'Y', 'Y'),
('1', 'Admin/PeopleFields.php', 'Y', 'Y'),
('1', 'Admin/Profiles.php', 'Y', 'Y'),
('1', 'Admin/ReferralForm.php', 'Y', 'Y'),
('1', 'Admin/ReportCardCommentCodes.php', 'Y', 'Y'),
('1', 'Admin/ReportCardComments.php', 'Y', 'Y'),
('1', 'Admin/ReportCardGrades.php', 'Y', 'Y'),
('1', 'Admin/Rollover.php', 'Y', 'Y'),
('1', 'Admin/Scheduler.php', 'Y', 'Y'),
('1', 'Admin/Schools.php?new_school=true', 'Y', 'Y'),
('1', 'Admin/Settings.php', 'Y', 'Y'),
('1', 'Admin/StudentFields.php', 'Y', 'Y'),
('1', 'Admin/UserFields.php', 'Y', 'Y'),
('1', 'Attendance/AddAbsences.php', 'Y', 'Y'),
('1', 'Attendance/Administration.php', 'Y', 'Y'),
('1', 'Attendance/AttendanceCodes.php', 'Y', 'Y'),
('1', 'Attendance/DailySummary.php', 'Y', 'Y'),
('1', 'Attendance/DuplicateAttendance.php', 'Y', 'Y'),
('1', 'Attendance/FixDailyAttendance.php', 'Y', 'Y'),
('1', 'Attendance/Percent.php', 'Y', 'Y'),
('1', 'Attendance/Percent.php?list_by_day=true', 'Y', 'Y'),
('1', 'Attendance/StudentSummary.php', 'Y', 'Y'),
('1', 'Attendance/TeacherCompletion.php', 'Y', 'Y'),
('1', 'Custom/CreateParents.php', 'Y', 'Y'),
('1', 'Custom/MyReport.php', 'Y', 'Y'),
('1', 'Eligibility/Activities.php', 'Y', 'Y'),
('1', 'Eligibility/AddActivity.php', 'Y', 'Y'),
('1', 'Eligibility/EntryTimes.php', 'Y', 'Y'),
('1', 'Eligibility/Student.php', 'Y', 'Y'),
('1', 'Eligibility/StudentList.php', 'Y', 'Y'),
('1', 'Eligibility/TeacherCompletion.php', 'Y', 'Y'),
('1', 'Food_Service/Accounts.php', 'Y', 'Y'),
('1', 'Food_Service/ActivityReport.php', 'Y', 'Y'),
('1', 'Food_Service/BalanceReport.php', 'Y', 'Y'),
('1', 'Food_Service/DailyMenus.php', 'Y', 'Y'),
('1', 'Food_Service/Kiosk.php', 'Y', 'Y'),
('1', 'Food_Service/MenuItems.php', 'Y', 'Y'),
('1', 'Food_Service/MenuReports.php', 'Y', 'Y'),
('1', 'Food_Service/Menus.php', 'Y', 'Y'),
('1', 'Food_Service/Reminders.php', 'Y', 'Y'),
('1', 'Food_Service/ServeMenus.php', 'Y', 'Y'),
('1', 'Food_Service/Statements.php', 'Y', 'Y'),
('1', 'Food_Service/Transactions.php', 'Y', 'Y'),
('1', 'Food_Service/TransactionsReport.php', 'Y', 'Y'),
('1', 'Grades/CalcGPA.php', 'Y', 'Y'),
('1', 'Grades/EditHistoryMarkingPeriods.php', 'Y', 'Y'),
('1', 'Grades/EditReportCardGrades.php', 'Y', 'Y'),
('1', 'Grades/FinalGrades.php', 'Y', 'Y'),
('1', 'Grades/FixGPA.php', 'Y', 'Y'),
('1', 'Grades/GPAMPList.php', 'Y', 'Y'),
('1', 'Grades/GPARankList.php', 'Y', 'Y'),
('1', 'Grades/GradeBreakdown.php', 'Y', 'Y'),
('1', 'Grades/HonorRoll.php', 'Y', 'Y'),
('1', 'Grades/ReportCardCommentCodes.php', 'Y', 'Y'),
('1', 'Grades/ReportCardComments.php', 'Y', 'Y'),
('1', 'Grades/ReportCardGrades.php', 'Y', 'Y'),
('1', 'Grades/ReportCards.php', 'Y', 'Y'),
('1', 'Grades/StudentGrades.php', 'Y', 'Y'),
('1', 'Grades/TeacherCompletion.php', 'Y', 'Y'),
('1', 'Grades/Transcripts.php', 'Y', 'Y'),
('1', 'Resources/Redirect.php?to=doc', 'Y', 'Y'),
('1', 'Resources/Redirect.php?to=forums', 'Y', 'Y'),
('1', 'Resources/Redirect.php?to=translate', 'Y', 'Y'),
('1', 'Resources/Redirect.php?to=videohelp', 'Y', 'Y'),
('1', 'Scheduling/AddDrop.php', 'Y', 'Y'),
('1', 'Scheduling/Courses.php', 'Y', 'Y'),
('1', 'Scheduling/IncompleteSchedules.php', 'Y', 'Y'),
('1', 'Scheduling/MassDrops.php', 'Y', 'Y'),
('1', 'Scheduling/MassRequests.php', 'Y', 'Y'),
('1', 'Scheduling/MassSchedule.php', 'Y', 'Y'),
('1', 'Scheduling/PrintClassLists.php', 'Y', 'Y'),
('1', 'Scheduling/PrintClassPictures.php', 'Y', 'Y'),
('1', 'Scheduling/PrintRequests.php', 'Y', 'Y'),
('1', 'Scheduling/PrintSchedules.php', 'Y', 'Y'),
('1', 'Scheduling/Requests.php', 'Y', 'Y'),
('1', 'Scheduling/RequestsReport.php', 'Y', 'Y'),
('1', 'Scheduling/Schedule.php', 'Y', 'Y'),
('1', 'Scheduling/Scheduler.php', 'Y', 'Y'),
('1', 'Scheduling/ScheduleReport.php', 'Y', 'Y'),
('1', 'Scheduling/UnfilledRequests.php', 'Y', 'Y'),
('1', 'School_Setup/Calendar.php', 'Y', 'Y'),
('1', 'School_Setup/CopySchool.php', 'Y', 'Y'),
('1', 'School_Setup/GradeLevels.php', 'Y', 'Y'),
('1', 'School_Setup/MarkingPeriods.php', 'Y', 'Y'),
('1', 'School_Setup/Periods.php', 'Y', 'Y'),
('1', 'School_Setup/PortalNotes.php', 'Y', 'Y'),
('1', 'School_Setup/Schools.php', 'Y', 'Y'),
('1', 'School_Setup/Schools.php?new_school=true', 'Y', 'Y'),
('1', 'Students/AddDrop.php', 'Y', 'Y'),
('1', 'Students/AddressFields.php', 'Y', 'Y'),
('1', 'Students/AddUsers.php', 'Y', 'Y'),
('1', 'Students/AdvancedReport.php', 'Y', 'Y'),
('1', 'Students/AssignOtherInfo.php', 'Y', 'Y'),
('1', 'Students/EnrollmentCodes.php', 'Y', 'Y'),
('1', 'Students/Letters.php', 'Y', 'Y'),
('1', 'Students/MailingLabels.php', 'Y', 'Y'),
('1', 'Students/PeopleFields.php', 'Y', 'Y'),
('1', 'Students/PrintStudentInfo.php', 'Y', 'Y'),
('1', 'Students/Student.php', 'Y', 'Y'),
('1', 'Students/Student.php&category_id=1', 'Y', 'Y'),
('1', 'Students/Student.php&category_id=2', 'Y', 'Y'),
('1', 'Students/Student.php&category_id=3', 'Y', 'Y'),
('1', 'Students/Student.php&category_id=5', 'Y', 'Y'),
('1', 'Students/Student.php&include=General_Info&student_id=new', 'Y', 'Y'),
('1', 'Students/StudentFields.php', 'Y', 'Y'),
('1', 'Students/StudentLabels.php', 'Y', 'Y'),
('1', 'Users/AddStudents.php', 'Y', 'Y'),
('1', 'Users/Exceptions.php', 'Y', 'Y'),
('1', 'Users/Preferences.php', 'Y', 'Y'),
('1', 'Users/Profiles.php', 'Y', 'Y'),
('1', 'Users/TeacherPrograms.php?include=Attendance/TakeAttendance.php', 'Y', 'Y'),
('1', 'Users/TeacherPrograms.php?include=Eligibility/EnterEligibility.php', 'Y', 'Y'),
('1', 'Users/TeacherPrograms.php?include=Grades/AnomalousGrades.php', 'Y', 'Y'),
('1', 'Users/TeacherPrograms.php?include=Grades/AnomalousGrades.php', 'Y', 'Y'),
('1', 'Users/TeacherPrograms.php?include=Grades/Grades.php', 'Y', 'Y'),
('1', 'Users/TeacherPrograms.php?include=Grades/InputFinalGrades.php', 'Y', 'Y'),
('1', 'Users/User.php', 'Y', 'Y'),
('1', 'Users/User.php&category_id=1', 'Y', 'Y'),
('1', 'Users/User.php&category_id=2', 'Y', 'Y'),
('1', 'Users/User.php&category_id=905', 'Y', 'Y'),
('1', 'Users/User.php&category_id=938', 'Y', 'Y'),
('1', 'Users/User.php&staff_id=new', 'Y', 'Y'),
('1', 'Users/UserFields.php', 'Y', 'Y'),
('2', 'Attendance/DailySummary.php', 'Y', NULL),
('2', 'Attendance/StudentSummary.php', 'Y', NULL),
('2', 'Attendance/TakeAttendance.php', 'Y', NULL),
('2', 'Eligibility/EnterEligibility.php', 'Y', NULL),
('2', 'Food_Service/Accounts.php', 'Y', NULL),
('2', 'Food_Service/DailyMenus.php', 'Y', NULL),
('2', 'Food_Service/MenuItems.php', 'Y', NULL),
('2', 'Food_Service/Statements.php', 'Y', NULL),
('2', 'Grades/AnomalousGrades.php', 'Y', NULL),
('2', 'Grades/Assignments-new.php', 'Y', NULL),
('2', 'Grades/Assignments.php', 'Y', NULL),
('2', 'Grades/Configuration.php', 'Y', NULL),
('2', 'Grades/FinalGrades.php', 'Y', NULL),
('2', 'Grades/Grades.php', 'Y', NULL),
('2', 'Grades/InputFinalGrades.php', 'Y', NULL),
('2', 'Grades/ProgressReports.php', 'Y', NULL),
('2', 'Grades/ReportCardCommentCodes.php', 'Y', NULL),
('2', 'Grades/ReportCardComments.php', 'Y', NULL),
('2', 'Grades/ReportCardGrades.php', 'Y', NULL),
('2', 'Grades/ReportCards.php', 'Y', NULL),
('2', 'Grades/StudentGrades.php', 'Y', NULL),
('2', 'Resources/Redirect.php?to=doc', 'Y', 'Y'),
('2', 'Resources/Redirect.php?to=forums', 'Y', 'Y'),
('2', 'Resources/Redirect.php?to=translate', 'Y', 'Y'),
('2', 'Resources/Redirect.php?to=videohelp', 'Y', 'Y'),
('2', 'Scheduling/PrintClassLists.php', 'Y', NULL),
('2', 'Scheduling/PrintClassPictures.php', 'Y', NULL),
('2', 'Scheduling/PrintSchedules.php', 'Y', NULL),
('2', 'Scheduling/Schedule.php', 'Y', NULL),
('2', 'School_Setup/Calendar.php', 'Y', NULL),
('2', 'School_Setup/MarkingPeriods.php', 'Y', NULL),
('2', 'School_Setup/Schools.php', 'Y', NULL),
('2', 'Students/AddUsers.php', 'Y', NULL),
('2', 'Students/AdvancedReport.php', 'Y', NULL),
('2', 'Students/Letters.php', 'Y', NULL),
('2', 'Students/Student.php', 'Y', NULL),
('2', 'Students/Student.php&category_id=1', 'Y', NULL),
('2', 'Students/Student.php&category_id=3', 'Y', NULL),
('2', 'Students/Student.php&category_id=4', 'Y', 'Y'),
('2', 'Students/Student.php&category_id=5', 'Y', NULL),
('2', 'Students/StudentLabels.php', 'Y', NULL),
('2', 'Users/Preferences.php', 'Y', NULL),
('2', 'Users/User.php', 'Y', NULL),
('2', 'Users/User.php&category_id=1', 'Y', NULL),
('2', 'Users/User.php&category_id=2', 'Y', NULL),
('2', 'Users/User.php&category_id=3', 'Y', NULL),
('3', 'Attendance/DailySummary.php', 'Y', NULL),
('3', 'Attendance/StudentSummary.php', 'Y', NULL),
('3', 'Eligibility/Student.php', 'Y', NULL),
('3', 'Eligibility/StudentList.php', 'Y', NULL),
('3', 'Food_Service/Accounts.php', 'Y', NULL),
('3', 'Food_Service/DailyMenus.php', 'Y', NULL),
('3', 'Food_Service/MenuItems.php', 'Y', NULL),
('3', 'Food_Service/Statements.php', 'Y', NULL),
('3', 'Grades/FinalGrades.php', 'Y', NULL),
('3', 'Grades/GPARankList.php', 'Y', NULL),
('3', 'Grades/ReportCards.php', 'Y', NULL),
('3', 'Grades/StudentGrades.php', 'Y', NULL),
('3', 'Grades/Transcripts.php', 'Y', NULL),
('3', 'Resources/Redirect.php?to=doc', 'Y', 'Y'),
('3', 'Resources/Redirect.php?to=forums', 'Y', 'Y'),
('3', 'Resources/Redirect.php?to=translate', 'Y', 'Y'),
('3', 'Resources/Redirect.php?to=videohelp', 'Y', 'Y'),
('3', 'Scheduling/PrintClassPictures.php', 'Y', NULL),
('3', 'Scheduling/Requests.php', 'Y', NULL),
('3', 'Scheduling/Schedule.php', 'Y', NULL),
('3', 'School_Setup/Calendar.php', 'Y', NULL),
('3', 'School_Setup/Schools.php', 'Y', NULL),
('3', 'Students/Student.php', 'Y', NULL),
('3', 'Students/Student.php&category_id=1', 'Y', NULL),
('3', 'Students/Student.php&category_id=3', 'Y', NULL),
('3', 'Students/Student.php&category_id=5', 'Y', NULL),
('3', 'Users/Preferences.php', 'Y', NULL),
('3', 'Users/User.php', 'Y', NULL),
('3', 'Users/User.php&category_id=1', 'Y', NULL),
('3', 'Users/User.php&category_id=2', 'Y', NULL),
('3', 'Users/User.php&category_id=3', 'Y', NULL),
('6', 'Attendance/AddAbsences.php', 'Y', NULL),
('6', 'Attendance/Administration.php', 'Y', NULL),
('6', 'Attendance/DailySummary.php', 'Y', NULL),
('6', 'Attendance/Percent.php', 'Y', NULL),
('6', 'Attendance/Percent.php?list_by_day=true', 'Y', NULL),
('6', 'Attendance/StudentSummary.php', 'Y', NULL),
('6', 'Attendance/TeacherCompletion.php', 'Y', NULL),
('6', 'Custom/AttendanceSummary.php', 'Y', NULL),
('6', 'Custom/MyReport.php', 'Y', 'Y'),
('6', 'Eligibility/AddActivity.php', 'Y', NULL),
('6', 'Eligibility/Student.php', 'Y', NULL),
('6', 'Eligibility/StudentList.php', 'Y', NULL),
('6', 'Eligibility/TeacherCompletion.php', 'Y', NULL),
('6', 'Grades/CalcGPA.php', 'Y', NULL),
('6', 'Grades/EditHistoryMarkingPeriods.php', 'Y', NULL),
('6', 'Grades/EditReportCardGrades.php', 'Y', NULL),
('6', 'Grades/FinalGrades.php', 'Y', NULL),
('6', 'Grades/GPAMPList.php', 'Y', NULL),
('6', 'Grades/GPARankList.php', 'Y', NULL),
('6', 'Grades/GradeBreakdown.php', 'Y', NULL),
('6', 'Grades/HonorRoll.php', 'Y', NULL),
('6', 'Grades/ReportCardCommentCodes.php', 'Y', NULL),
('6', 'Grades/ReportCardComments.php', 'Y', NULL),
('6', 'Grades/ReportCardGrades.php', 'Y', NULL),
('6', 'Grades/ReportCards.php', 'Y', NULL),
('6', 'Grades/StudentGrades.php', 'Y', NULL),
('6', 'Grades/TeacherCompletion.php', 'Y', NULL),
('6', 'Grades/Transcripts.php', 'Y', NULL),
('6', 'Resources/Redirect.php?to=doc', 'Y', 'Y'),
('6', 'Resources/Redirect.php?to=forums', 'Y', 'Y'),
('6', 'Resources/Redirect.php?to=translate', 'Y', 'Y'),
('6', 'Resources/Redirect.php?to=videohelp', 'Y', 'Y'),
('6', 'Scheduling/AddDrop.php', 'Y', 'Y'),
('6', 'Scheduling/Courses.php', 'Y', 'Y'),
('6', 'Scheduling/IncompleteSchedules.php', 'Y', 'Y'),
('6', 'Scheduling/MassDrops.php', 'Y', 'Y'),
('6', 'Scheduling/MassRequests.php', 'Y', 'Y'),
('6', 'Scheduling/MassSchedule.php', 'Y', 'Y'),
('6', 'Scheduling/PrintClassLists.php', 'Y', 'Y'),
('6', 'Scheduling/PrintClassPictures.php', 'Y', 'Y'),
('6', 'Scheduling/PrintRequests.php', 'Y', 'Y'),
('6', 'Scheduling/PrintSchedules.php', 'Y', 'Y'),
('6', 'Scheduling/Requests.php', 'Y', 'Y'),
('6', 'Scheduling/RequestsReport.php', 'Y', 'Y'),
('6', 'Scheduling/Schedule.php', 'Y', 'Y'),
('6', 'Scheduling/Scheduler.php', 'Y', 'Y'),
('6', 'Scheduling/ScheduleReport.php', 'Y', 'Y'),
('6', 'Scheduling/UnfilledRequests.php', 'Y', 'Y'),
('6', 'School_Setup/Calendar.php', 'Y', NULL),
('6', 'School_Setup/GradeLevels.php', 'Y', NULL),
('6', 'School_Setup/MarkingPeriods.php', 'Y', NULL),
('6', 'School_Setup/Periods.php', 'Y', NULL),
('6', 'School_Setup/PortalNotes.php', 'Y', NULL),
('6', 'School_Setup/Schools.php', 'Y', NULL),
('6', 'Students/AddDrop.php', 'Y', NULL),
('6', 'Students/AddUsers.php', 'Y', NULL),
('6', 'Students/AdvancedReport.php', 'Y', NULL),
('6', 'Students/AssignOtherInfo.php', 'Y', NULL),
('6', 'Students/Letters.php', 'Y', NULL),
('6', 'Students/MailingLabels.php', 'Y', NULL),
('6', 'Students/PrintStudentInfo.php', 'Y', NULL),
('6', 'Students/Student.php', 'Y', NULL),
('6', 'Students/Student.php&category_id=1', 'Y', NULL),
('6', 'Students/Student.php&category_id=2', 'Y', NULL),
('6', 'Students/Student.php&category_id=3', 'Y', NULL),
('6', 'Students/Student.php&category_id=4', 'Y', NULL),
('6', 'Students/Student.php&category_id=5', 'Y', NULL),
('6', 'Students/Student.php&include=General_Info&student_id=new', 'Y', NULL),
('6', 'Students/StudentLabels.php', 'Y', NULL),
('6', 'Users/Preferences.php', 'Y', 'Y'),
('6', 'Users/User.php', 'Y', NULL),
('6', 'Users/User.php&category_id=1', 'Y', NULL),
('6', 'Users/User.php&category_id=2', 'Y', NULL),
('7', 'Attendance/AddAbsences.php', 'Y', 'Y'),
('7', 'Attendance/Administration.php', 'Y', 'Y'),
('7', 'Attendance/AttendanceCodes.php', 'Y', 'Y'),
('7', 'Attendance/DailySummary.php', 'Y', 'Y'),
('7', 'Attendance/DuplicateAttendance.php', 'Y', 'Y'),
('7', 'Attendance/FixDailyAttendance.php', 'Y', 'Y'),
('7', 'Attendance/Percent.php', 'Y', 'Y'),
('7', 'Attendance/Percent.php?list_by_day=true', 'Y', 'Y'),
('7', 'Attendance/StudentSummary.php', 'Y', 'Y'),
('7', 'Attendance/TeacherCompletion.php', 'Y', 'Y'),
('7', 'Custom/AttendanceSummary.php', 'Y', 'Y'),
('7', 'Custom/CreateParents.php', 'Y', 'Y'),
('7', 'Custom/MyReport.php', 'Y', 'Y'),
('7', 'Eligibility/Activities.php', 'Y', NULL),
('7', 'Eligibility/AddActivity.php', 'Y', 'Y'),
('7', 'Eligibility/EntryTimes.php', 'Y', NULL),
('7', 'Eligibility/Student.php', 'Y', 'Y'),
('7', 'Eligibility/StudentList.php', 'Y', 'Y'),
('7', 'Eligibility/TeacherCompletion.php', 'Y', 'Y'),
('7', 'Food_Service/Accounts.php', 'Y', 'Y'),
('7', 'Food_Service/ActivityReport.php', 'Y', 'Y'),
('7', 'Food_Service/BalanceReport.php', 'Y', 'Y'),
('7', 'Food_Service/DailyMenus.php', 'Y', 'Y'),
('7', 'Food_Service/Kiosk.php', 'Y', 'Y'),
('7', 'Food_Service/MenuItems.php', 'Y', 'Y'),
('7', 'Food_Service/MenuReports.php', 'Y', 'Y'),
('7', 'Food_Service/Menus.php', 'Y', 'Y'),
('7', 'Food_Service/Reminders.php', 'Y', 'Y'),
('7', 'Food_Service/ServeMenus.php', 'Y', 'Y'),
('7', 'Food_Service/Statements.php', 'Y', 'Y'),
('7', 'Food_Service/Transactions.php', 'Y', 'Y'),
('7', 'Food_Service/TransactionsReport.php', 'Y', 'Y'),
('7', 'Grades/CalcGPA.php', 'Y', NULL),
('7', 'Grades/EditHistoryMarkingPeriods.php', 'Y', 'Y'),
('7', 'Grades/EditReportCardGrades.php', 'Y', 'Y'),
('7', 'Grades/FinalGrades.php', 'Y', NULL),
('7', 'Grades/GPAMPList.php', 'Y', NULL),
('7', 'Grades/GPARankList.php', 'Y', NULL),
('7', 'Grades/GradeBreakdown.php', 'Y', NULL),
('7', 'Grades/HonorRoll.php', 'Y', NULL),
('7', 'Grades/ReportCardCommentCodes.php', 'Y', NULL),
('7', 'Grades/ReportCardComments.php', 'Y', NULL),
('7', 'Grades/ReportCardGrades.php', 'Y', NULL),
('7', 'Grades/ReportCards.php', 'Y', NULL),
('7', 'Grades/StudentGrades.php', 'Y', NULL),
('7', 'Grades/TeacherCompletion.php', 'Y', NULL),
('7', 'Grades/Transcripts.php', 'Y', NULL),
('7', 'Resources/Redirect.php?to=doc', 'Y', 'Y'),
('7', 'Resources/Redirect.php?to=forums', 'Y', 'Y'),
('7', 'Resources/Redirect.php?to=getkey', 'Y', 'Y'),
('7', 'Resources/Redirect.php?to=translate', 'Y', 'Y'),
('7', 'Resources/Redirect.php?to=videohelp', 'Y', 'Y'),
('7', 'Scheduling/AddDrop.php', 'Y', NULL),
('7', 'Scheduling/Courses.php', 'Y', NULL),
('7', 'Scheduling/IncompleteSchedules.php', 'Y', NULL),
('7', 'Scheduling/MassDrops.php', 'Y', NULL),
('7', 'Scheduling/MassRequests.php', 'Y', NULL),
('7', 'Scheduling/MassSchedule.php', 'Y', NULL),
('7', 'Scheduling/PrintClassLists.php', 'Y', NULL),
('7', 'Scheduling/PrintClassPictures.php', 'Y', NULL),
('7', 'Scheduling/PrintRequests.php', 'Y', NULL),
('7', 'Scheduling/PrintSchedules.php', 'Y', NULL),
('7', 'Scheduling/Requests.php', 'Y', NULL),
('7', 'Scheduling/RequestsReport.php', 'Y', NULL),
('7', 'Scheduling/Schedule.php', 'Y', NULL),
('7', 'Scheduling/ScheduleReport.php', 'Y', NULL),
('7', 'Scheduling/UnfilledRequests.php', 'Y', NULL),
('7', 'School_Setup/Calendar.php', 'Y', 'Y'),
('7', 'School_Setup/CopySchool.php', 'Y', NULL),
('7', 'School_Setup/GradeLevels.php', 'Y', NULL),
('7', 'School_Setup/MarkingPeriods.php', 'Y', NULL),
('7', 'School_Setup/Periods.php', 'Y', NULL),
('7', 'School_Setup/PortalNotes.php', 'Y', 'Y'),
('7', 'School_Setup/Schools.php', 'Y', 'Y'),
('7', 'School_Setup/Schools.php?new_school=true', 'Y', NULL),
('7', 'Students/AddDrop.php', 'Y', 'Y'),
('7', 'Students/AddressFields.php', 'Y', NULL),
('7', 'Students/AddUsers.php', 'Y', 'Y'),
('7', 'Students/AdvancedReport.php', 'Y', 'Y'),
('7', 'Students/AssignOtherInfo.php', 'Y', 'Y'),
('7', 'Students/EnrollmentCodes.php', 'Y', NULL),
('7', 'Students/Letters.php', 'Y', 'Y'),
('7', 'Students/MailingLabels.php', 'Y', 'Y'),
('7', 'Students/PeopleFields.php', 'Y', NULL),
('7', 'Students/PrintStudentInfo.php', 'Y', 'Y'),
('7', 'Students/Student.php', 'Y', 'Y'),
('7', 'Students/Student.php&category_id=1', 'Y', 'Y'),
('7', 'Students/Student.php&category_id=2', 'Y', 'Y'),
('7', 'Students/Student.php&category_id=3', 'Y', 'Y'),
('7', 'Students/Student.php&category_id=4', 'Y', 'Y'),
('7', 'Students/Student.php&category_id=5', 'Y', 'Y'),
('7', 'Students/Student.php&include=General_Info&student_id=new', 'Y', 'Y'),
('7', 'Students/StudentFields.php', 'Y', NULL),
('7', 'Students/StudentLabels.php', 'Y', 'Y'),
('7', 'Users/AddStudents.php', 'Y', 'Y'),
('7', 'Users/Preferences.php', 'Y', 'Y'),
('7', 'Users/TeacherPrograms.php?include=Attendance/TakeAttendance.php', 'Y', 'Y'),
('7', 'Users/TeacherPrograms.php?include=Attendance/TakeAttendance.php', 'Y', 'Y'),
('7', 'Users/TeacherPrograms.php?include=Eligibility/EnterEligibility.php', 'Y', 'Y'),
('7', 'Users/TeacherPrograms.php?include=Grades/AnomalousGrades.php', 'Y', 'Y'),
('7', 'Users/TeacherPrograms.php?include=Grades/AnomalousGrades.php', 'Y', 'Y'),
('7', 'Users/TeacherPrograms.php?include=Grades/Grades.php', 'Y', 'Y'),
('7', 'Users/TeacherPrograms.php?include=Grades/Grades.php', 'Y', 'Y'),
('7', 'Users/TeacherPrograms.php?include=Grades/InputFinalGrades.php', 'Y', 'Y'),
('7', 'Users/TeacherPrograms.php?include=Grades/InputFinalGrades.php', 'Y', 'Y'),
('7', 'Users/User.php', 'Y', 'Y'),
('7', 'Users/User.php&category_id=1', 'Y', 'Y'),
('7', 'Users/User.php&category_id=2', 'Y', 'Y'),
('7', 'Users/User.php&staff_id=new', 'Y', 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `program_config`
--

DROP TABLE IF EXISTS `program_config`;
CREATE TABLE IF NOT EXISTS `program_config` (
  `syear` double(4,0) DEFAULT NULL,
  `school_id` double DEFAULT NULL,
  `program` char(255) DEFAULT NULL,
  `title` char(100) DEFAULT NULL,
  `value` char(100) DEFAULT NULL,
  KEY `program_config_ind1` (`syear`,`school_id`,`program`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `program_config`
--

INSERT INTO `program_config` (`syear`, `school_id`, `program`, `title`, `value`) VALUES
(2014, 1, 'eligibility', 'START_DAY', '1'),
(2014, 1, 'eligibility', 'START_HOUR', '1'),
(2014, 1, 'eligibility', 'START_MINUTE', '0'),
(2014, 1, 'eligibility', 'START_M', 'AM'),
(2014, 1, 'eligibility', 'END_DAY', '6'),
(2014, 1, 'eligibility', 'END_HOUR', '1'),
(2014, 1, 'eligibility', 'END_MINUTE', '0'),
(2014, 1, 'eligibility', 'END_M', 'AM');

-- --------------------------------------------------------

--
-- Table structure for table `program_user_config`
--

DROP TABLE IF EXISTS `program_user_config`;
CREATE TABLE IF NOT EXISTS `program_user_config` (
  `program` char(255) DEFAULT NULL,
  `title` char(100) DEFAULT NULL,
  `value` char(100) DEFAULT NULL,
  `user_id` double NOT NULL,
  KEY `program_user_config_ind1` (`program`,`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `program_user_config`
--

INSERT INTO `program_user_config` (`program`, `title`, `value`, `user_id`) VALUES
('Preferences', 'THEME', 'Modern', 1),
('Preferences', 'COLOR', '#FFFFCC', 1),
('Preferences', 'HIGHLIGHT', '#3366FF', 1),
('Preferences', 'TITLES', 'gray', 1),
('Preferences', 'HIDDEN', 'Y', 1),
('Preferences', 'HIDE_ALERTS', 'N', 1);

-- --------------------------------------------------------

--
-- Table structure for table `report_card_comments`
--

DROP TABLE IF EXISTS `report_card_comments`;
CREATE TABLE IF NOT EXISTS `report_card_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `syear` double(4,0) DEFAULT NULL,
  `school_id` double DEFAULT NULL,
  `course_id` double DEFAULT NULL,
  `sort_order` double DEFAULT NULL,
  `category_id` double DEFAULT NULL,
  `title` longtext,
  `scale_id` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `report_card_comments_ind1` (`school_id`,`syear`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `report_card_comments`
--

INSERT INTO `report_card_comments` (`id`, `syear`, `school_id`, `course_id`, `sort_order`, `category_id`, `title`, `scale_id`) VALUES
(1, 2014, 1, NULL, 1, NULL, '^n Fails to Meet Course Requirements', NULL),
(2, 2014, 1, NULL, 2, NULL, '^n Comes to ^s Class Unprepared', NULL),
(3, 2014, 1, NULL, 3, NULL, '^n Exerts Positive Influence in Class', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `report_card_comment_categories`
--

DROP TABLE IF EXISTS `report_card_comment_categories`;
CREATE TABLE IF NOT EXISTS `report_card_comment_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `syear` double(4,0) DEFAULT NULL,
  `school_id` double DEFAULT NULL,
  `course_id` double DEFAULT NULL,
  `sort_order` double DEFAULT NULL,
  `title` longtext,
  `rollover_id` double DEFAULT NULL,
  `color` char(30) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `report_card_comment_categories_ind1` (`school_id`,`syear`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `report_card_comment_codes`
--

DROP TABLE IF EXISTS `report_card_comment_codes`;
CREATE TABLE IF NOT EXISTS `report_card_comment_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` double NOT NULL,
  `scale_id` double NOT NULL,
  `title` char(5) NOT NULL,
  `short_name` char(100) DEFAULT NULL,
  `comment` char(100) DEFAULT NULL,
  `sort_order` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `report_card_comment_codes_ind1` (`school_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `report_card_comment_codes`
--

INSERT INTO `report_card_comment_codes` (`id`, `school_id`, `scale_id`, `title`, `short_name`, `comment`, `sort_order`) VALUES
(1, 1, 1, '4', 'Outstanding Achievement', 'Outstanding Achievement', 1),
(2, 1, 1, '3', 'Good Achievement', 'Good Achievement', 2),
(3, 1, 1, '2', 'Needs Improvement', 'Needs Improvement', 3),
(4, 1, 1, '1', 'Unsatisfactory', 'Unsatisfactory', 4);

-- --------------------------------------------------------

--
-- Table structure for table `report_card_comment_code_scales`
--

DROP TABLE IF EXISTS `report_card_comment_code_scales`;
CREATE TABLE IF NOT EXISTS `report_card_comment_code_scales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` double NOT NULL,
  `title` char(25) DEFAULT NULL,
  `comment` char(100) DEFAULT NULL,
  `sort_order` double DEFAULT NULL,
  `rollover_id` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `report_card_grades`
--

DROP TABLE IF EXISTS `report_card_grades`;
CREATE TABLE IF NOT EXISTS `report_card_grades` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `syear` double(4,0) DEFAULT NULL,
  `school_id` double DEFAULT NULL,
  `title` char(100) DEFAULT NULL,
  `sort_order` double DEFAULT NULL,
  `gpa_value` double(4,2) DEFAULT NULL,
  `break_off` double DEFAULT NULL,
  `comment` longtext,
  `grade_scale_id` double DEFAULT NULL,
  `unweighted_gp` double(4,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `report_card_grades_ind1` (`school_id`,`syear`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Dumping data for table `report_card_grades`
--

INSERT INTO `report_card_grades` (`id`, `syear`, `school_id`, `title`, `sort_order`, `gpa_value`, `break_off`, `comment`, `grade_scale_id`, `unweighted_gp`) VALUES
(1, 2014, 1, 'A+', 1, 4.25, 98, NULL, 1, 4.25),
(2, 2014, 1, 'A+', 2, 4.00, 96, NULL, 1, 4.00),
(3, 2014, 1, 'A-', 3, 3.75, 93, NULL, 1, 3.75),
(4, 2014, 1, 'B+', 4, 3.50, 91, NULL, 1, 3.50),
(5, 2014, 1, 'B+', NULL, 3.00, 88, NULL, 1, 3.00),
(6, 2014, 1, 'B-', NULL, 2.75, 85, NULL, 1, 2.75),
(7, 2014, 1, 'C+', NULL, 2.50, 83, NULL, 1, 2.50),
(8, 2014, 1, 'C', NULL, 2.00, 80, NULL, 1, 2.00),
(9, 2014, 1, 'C-', NULL, 1.75, 78, NULL, 1, 1.75),
(10, 2014, 1, 'D+', NULL, 1.50, 75, NULL, 1, 1.50),
(11, 2014, 1, 'D', NULL, 1.00, 72, NULL, 1, 1.00),
(12, 2014, 1, 'D-', NULL, 0.75, 70, NULL, 1, 0.75),
(13, 2014, 1, 'F', NULL, NULL, NULL, NULL, 1, NULL),
(14, 2014, 1, 'I', NULL, NULL, NULL, NULL, 1, NULL),
(15, 2014, 1, 'P', 1, NULL, 70, NULL, 2, NULL),
(16, 2014, 1, 'F', NULL, NULL, NULL, NULL, 2, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `report_card_grade_scales`
--

DROP TABLE IF EXISTS `report_card_grade_scales`;
CREATE TABLE IF NOT EXISTS `report_card_grade_scales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `syear` double(4,0) DEFAULT NULL,
  `school_id` double NOT NULL,
  `title` longtext,
  `comment` longtext,
  `sort_order` double DEFAULT NULL,
  `rollover_id` double DEFAULT NULL,
  `hhr_gpa_value` double(4,2) DEFAULT NULL,
  `hr_gpa_value` double(4,2) DEFAULT NULL,
  `gp_scale` double(10,3) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Dumping data for table `report_card_grade_scales`
--

INSERT INTO `report_card_grade_scales` (`id`, `syear`, `school_id`, `title`, `comment`, `sort_order`, `rollover_id`, `hhr_gpa_value`, `hr_gpa_value`, `gp_scale`) VALUES
(1, 2014, 1, 'Main', NULL, 1, NULL, 3.85, 3.50, 4.000),
(2, 2014, 1, 'Pass/Fail', NULL, 2, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `schedule`
--

DROP TABLE IF EXISTS `schedule`;
CREATE TABLE IF NOT EXISTS `schedule` (
  `syear` double(4,0) NOT NULL,
  `school_id` double DEFAULT NULL,
  `student_id` double NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `modified_date` date DEFAULT NULL,
  `modified_by` char(255) DEFAULT NULL,
  `course_id` double NOT NULL,
  `course_weight` char(10) DEFAULT NULL,
  `course_period_id` double NOT NULL,
  `mp` char(3) DEFAULT NULL,
  `marking_period_id` double DEFAULT NULL,
  `scheduler_lock` char(1) DEFAULT NULL,
  `id` double DEFAULT NULL,
  KEY `schedule_ind1` (`course_weight`,`course_id`),
  KEY `schedule_ind2` (`course_period_id`),
  KEY `schedule_ind3` (`end_date`,`start_date`,`marking_period_id`,`student_id`),
  KEY `schedule_ind4` (`school_id`,`syear`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `schedule_requests`
--

DROP TABLE IF EXISTS `schedule_requests`;
CREATE TABLE IF NOT EXISTS `schedule_requests` (
  `syear` double(4,0) DEFAULT NULL,
  `school_id` double DEFAULT NULL,
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` double DEFAULT NULL,
  `subject_id` double DEFAULT NULL,
  `course_id` double DEFAULT NULL,
  `course_weight` char(10) DEFAULT NULL,
  `marking_period_id` double DEFAULT NULL,
  `priority` double DEFAULT NULL,
  `with_teacher_id` double DEFAULT NULL,
  `not_teacher_id` double DEFAULT NULL,
  `with_period_id` double DEFAULT NULL,
  `not_period_id` double DEFAULT NULL,
  PRIMARY KEY (`request_id`),
  KEY `schedule_requests_ind1` (`school_id`,`syear`,`course_weight`,`course_id`,`student_id`),
  KEY `schedule_requests_ind2` (`school_id`,`syear`),
  KEY `schedule_requests_ind3` (`school_id`,`syear`,`course_weight`,`course_id`),
  KEY `schedule_requests_ind4` (`with_teacher_id`),
  KEY `schedule_requests_ind5` (`not_teacher_id`),
  KEY `schedule_requests_ind6` (`with_period_id`),
  KEY `schedule_requests_ind7` (`not_period_id`),
  KEY `schedule_requests_ind8` (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `schools`
--

DROP TABLE IF EXISTS `schools`;
CREATE TABLE IF NOT EXISTS `schools` (
  `syear` double(4,0) NOT NULL,
  `id` int(11) NOT NULL,
  `title` char(100) DEFAULT NULL,
  `address` char(100) DEFAULT NULL,
  `city` char(100) DEFAULT NULL,
  `zipcode` char(10) DEFAULT NULL,
  `principal` char(100) DEFAULT NULL,
  `district` char(255) DEFAULT NULL,
  `phone` char(30) DEFAULT NULL,
  `state` char(10) DEFAULT NULL,
  `www_address` char(100) DEFAULT NULL,
  `school_number` char(50) DEFAULT NULL,
  `sau_number` char(3) DEFAULT NULL,
  `district_number` char(3) DEFAULT NULL,
  `reporting_gp_scale` double(10,3) DEFAULT NULL,
  `short_name` char(25) DEFAULT NULL,
  KEY `schools_ind1` (`syear`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `schools`
--

INSERT INTO `schools` (`syear`, `id`, `title`, `address`, `city`, `zipcode`, `principal`, `district`, `phone`, `state`, `www_address`, `school_number`, `sau_number`, `district_number`, `reporting_gp_scale`, `short_name`) VALUES
(2014, 1, 'Default School', '500 North St.', 'Springfield', '62704', 'Mr. James Principal', NULL, '217 525-1212', 'IL', 'http://centresis.org', NULL, NULL, NULL, 4.000, 'Default');

-- --------------------------------------------------------

--
-- Table structure for table `school_gradelevels`
--

DROP TABLE IF EXISTS `school_gradelevels`;
CREATE TABLE IF NOT EXISTS `school_gradelevels` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `school_id` double NOT NULL,
  `short_name` char(5) DEFAULT NULL,
  `title` char(50) DEFAULT NULL,
  `next_grade_id` double DEFAULT NULL,
  `credits` double(6,3) DEFAULT NULL,
  `display` char(5) DEFAULT NULL,
  `sort_order` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `school_gradelevels_ind1` (`school_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

--
-- Dumping data for table `school_gradelevels`
--

INSERT INTO `school_gradelevels` (`id`, `school_id`, `short_name`, `title`, `next_grade_id`, `credits`, `display`, `sort_order`) VALUES
(1, 1, 'K', 'Kindergarten', 2, NULL, NULL, 1),
(2, 1, '1', 'First Grade', 3, NULL, NULL, 2),
(3, 1, '2', 'Second Grade', 4, NULL, NULL, 3),
(4, 1, '3', 'Third Grade', 5, NULL, NULL, 4),
(5, 1, '4', 'Fourth Grade', 6, NULL, NULL, 5),
(6, 1, '5', 'Fifth Grade', 7, NULL, NULL, 6),
(7, 1, '6', 'Sixth Grade', 8, NULL, NULL, 7),
(8, 1, '7', 'Seventh Grade', 9, NULL, NULL, 8),
(9, 1, '8', 'Eighth Grade', 10, NULL, NULL, 9),
(10, 1, '9', 'Freshman', 11, NULL, NULL, 10),
(11, 1, '10', 'Sophomore', 12, NULL, NULL, 11),
(12, 1, '11', 'Junior', 13, NULL, NULL, 12),
(13, 1, '12', 'Senior', NULL, NULL, NULL, 13);

-- --------------------------------------------------------

--
-- Table structure for table `school_marking_periods`
--

DROP TABLE IF EXISTS `school_marking_periods`;
CREATE TABLE IF NOT EXISTS `school_marking_periods` (
  `marking_period_id` int(11) NOT NULL AUTO_INCREMENT,
  `syear` double(4,0) DEFAULT NULL,
  `mp` char(3) NOT NULL,
  `school_id` double DEFAULT NULL,
  `parent_id` double DEFAULT NULL,
  `title` char(50) DEFAULT NULL,
  `short_name` char(10) DEFAULT NULL,
  `sort_order` double DEFAULT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `post_start_date` date DEFAULT NULL,
  `post_end_date` date DEFAULT NULL,
  `does_grades` char(1) DEFAULT NULL,
  `does_exam` char(1) DEFAULT NULL,
  `does_comments` char(1) DEFAULT NULL,
  `rollover_id` double DEFAULT NULL,
  PRIMARY KEY (`marking_period_id`),
  KEY `school_marking_periods_ind1` (`parent_id`),
  KEY `school_marking_periods_ind2` (`end_date`,`start_date`,`school_id`,`syear`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=12 ;

--
-- Dumping data for table `school_marking_periods`
--

INSERT INTO `school_marking_periods` (`marking_period_id`, `syear`, `mp`, `school_id`, `parent_id`, `title`, `short_name`, `sort_order`, `start_date`, `end_date`, `post_start_date`, `post_end_date`, `does_grades`, `does_exam`, `does_comments`, `rollover_id`) VALUES
(1, 2014, 'FY', 1, NULL, 'Full Year', 'FY', 1, '2014-09-04', '2015-06-14', NULL, NULL, NULL, NULL, NULL, NULL),
(2, 2014, 'SEM', 1, 1, 'Semester 1', 'S1', 1, '2014-09-04', '2014-12-31', '2014-12-17', '2014-12-21', 'Y', 'Y', 'Y', NULL),
(3, 2014, 'SEM', 1, 1, 'Semester 2', 'S2', 2, '2015-01-01', '2015-06-14', '2015-06-09', '2015-06-14', 'Y', 'Y', 'Y', NULL),
(4, 2014, 'QTR', 1, 2, 'Quarter 1', 'Q1', 1, '2014-09-04', '2014-10-30', '2014-10-25', '2014-11-01', 'Y', NULL, 'Y', NULL),
(5, 2014, 'QTR', 1, 2, 'Quarter 2', 'Q2', 2, '2014-11-01', '2014-12-31', '2014-12-18', '2014-12-21', 'Y', NULL, 'Y', NULL),
(6, 2014, 'PRO', 1, 4, 'Progress 1', 'P1', 1, '2014-09-04', '2014-10-02', '2014-10-01', '2014-10-02', 'Y', NULL, 'Y', NULL),
(7, 2014, 'PRO', 1, 5, 'Progress 2', 'P2', 2, '2014-11-01', '2014-11-27', '2015-11-25', '2014-11-27', 'Y', NULL, 'Y', NULL),
(8, 2014, 'QTR', 1, 3, 'Quarter 3', 'Q3', 3, '2015-01-01', '2015-03-05', '2015-03-04', '2015-03-07', 'Y', NULL, 'Y', NULL),
(9, 2014, 'PRO', 1, 8, 'Progress 3', 'P3', 3, '2015-01-01', '2015-02-04', '2015-02-01', '2015-02-04', 'Y', NULL, 'Y', NULL),
(10, 2014, 'QTR', 1, 3, 'Quarter 4', 'Q4', 4, '2015-03-06', '2015-06-14', '2015-06-10', '2015-06-17', 'Y', NULL, 'Y', NULL),
(11, 2014, 'PRO', 1, 10, 'Progress 4', 'P4', 4, '2015-03-06', '2015-04-08', '2015-04-05', '2015-04-08', 'Y', NULL, 'Y', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `school_periods`
--

DROP TABLE IF EXISTS `school_periods`;
CREATE TABLE IF NOT EXISTS `school_periods` (
  `period_id` int(11) NOT NULL AUTO_INCREMENT,
  `syear` double(4,0) DEFAULT NULL,
  `school_id` double DEFAULT NULL,
  `sort_order` double DEFAULT NULL,
  `title` char(100) DEFAULT NULL,
  `short_name` char(10) DEFAULT NULL,
  `length` double DEFAULT NULL,
  `rollover_id` double DEFAULT NULL,
  `start_time` char(10) DEFAULT NULL,
  `end_time` char(10) DEFAULT NULL,
  `block` char(10) DEFAULT NULL,
  `attendance` char(1) DEFAULT NULL,
  PRIMARY KEY (`period_id`),
  KEY `school_periods_ind1` (`syear`,`period_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

--
-- Dumping data for table `school_periods`
--

INSERT INTO `school_periods` (`period_id`, `syear`, `school_id`, `sort_order`, `title`, `short_name`, `length`, `rollover_id`, `start_time`, `end_time`, `block`, `attendance`) VALUES
(1, 2014, 1, 1, 'Homeroom', 'HR', 300, NULL, NULL, NULL, NULL, 'Y'),
(2, 2014, 1, 2, 'Period 1', '1', 55, NULL, NULL, NULL, NULL, 'Y'),
(3, 2014, 1, 3, 'Period 2', '2', 55, NULL, NULL, NULL, NULL, 'Y'),
(4, 2014, 1, 4, 'Period 3', '3', 50, NULL, NULL, NULL, NULL, 'Y'),
(5, 2014, 1, 5, 'Period 4', '4', 55, NULL, NULL, NULL, NULL, 'Y'),
(6, 2014, 1, 6, 'Period 5', '5', 55, NULL, NULL, NULL, NULL, 'Y'),
(7, 2014, 1, 7, 'Period 6', '6', 50, NULL, NULL, NULL, NULL, 'Y'),
(8, 2014, 1, 8, 'Period 7', '7', 50, NULL, NULL, NULL, NULL, 'Y'),
(9, 2014, 1, 9, 'HR K - AM', 'KAM', 150, NULL, NULL, NULL, NULL, 'Y'),
(10, 2014, 1, 10, 'HR K - PM', 'KPM', 150, NULL, NULL, NULL, NULL, 'Y');

-- --------------------------------------------------------

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
CREATE TABLE IF NOT EXISTS `staff` (
  `syear` double(4,0) DEFAULT NULL,
  `staff_id` int(11) NOT NULL AUTO_INCREMENT,
  `current_school_id` double DEFAULT NULL,
  `first_name` char(100) NOT NULL,
  `last_name` char(100) NOT NULL,
  `middle_name` char(100) DEFAULT NULL,
  `username` char(100) DEFAULT NULL,
  `password` char(100) DEFAULT NULL,
  `phone` char(100) DEFAULT NULL,
  `email` char(100) DEFAULT NULL,
  `profile` char(30) DEFAULT NULL,
  `homeroom` char(5) DEFAULT NULL,
  `rollover_id` double DEFAULT NULL,
  `schools` char(255) DEFAULT NULL,
  `profile_id` double DEFAULT NULL,
  `last_login` timestamp NULL DEFAULT NULL,
  `failed_login` double DEFAULT NULL,
  `title` char(5) DEFAULT NULL,
  `name_suffix` char(3) DEFAULT NULL,
  PRIMARY KEY (`staff_id`),
  KEY `staff_ind1` (`syear`,`staff_id`),
  KEY `staff_ind2` (`first_name`,`last_name`),
  KEY `staff_ind3` (`schools`),
  KEY `staff_ind4` (`syear`,`username`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `staff`
--

INSERT INTO `staff` (`syear`, `staff_id`, `current_school_id`, `first_name`, `last_name`, `middle_name`, `username`, `password`, `phone`, `email`, `profile`, `homeroom`, `rollover_id`, `schools`, `profile_id`, `last_login`, `failed_login`, `title`, `name_suffix`) VALUES
(2014, 1, 1, 'Admin', 'Administrator', 'A', 'admin', 'a5uPUl+hDVH3VVwC3pBT3z2WuDLGwMIEX0taCx8gXzY=', NULL, NULL, 'admin', NULL, NULL, ',1,', 1, '2014-10-01 03:07:29', NULL, NULL, NULL),
(2014, 2, 1, 'Teach', 'Teacher', NULL, 'teacher', 'BHV4PIMx82XTvxXkuMtV13KwTCPCFszr2BNfRYo/Kr0=', NULL, NULL, 'teacher', NULL, NULL, ',1,', 2, '2014-09-30 21:34:34', NULL, NULL, NULL),
(2014, 3, NULL, 'Parent', 'Parent', 'P', 'parent', 'wz20QfC4cqId8BIY97JrfLkecnswEwz49j1OQRAXr7A=', NULL, NULL, 'parent', NULL, NULL, NULL, 3, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `staff_exceptions`
--

DROP TABLE IF EXISTS `staff_exceptions`;
CREATE TABLE IF NOT EXISTS `staff_exceptions` (
  `modname` char(255) DEFAULT NULL,
  `can_use` char(1) DEFAULT NULL,
  `can_edit` char(1) DEFAULT NULL,
  `user_id` double NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `staff_exceptions`
--

INSERT INTO `staff_exceptions` (`modname`, `can_use`, `can_edit`, `user_id`) VALUES
('School_Setup/PortalNotes.php', 'Y', 'Y', 1),
('School_Setup/Schools.php', 'Y', 'Y', 1),
('School_Setup/Schools.php?new_school=true', 'Y', 'Y', 1),
('School_Setup/CopySchool.php', 'Y', 'Y', 1),
('School_Setup/MarkingPeriods.php', 'Y', 'Y', 1),
('School_Setup/Periods.php', 'Y', 'Y', 1),
('School_Setup/GradeLevels.php', 'Y', 'Y', 1),
('School_Setup/Rollover.php', 'Y', 'Y', 1),
('Students/Student.php', 'Y', 'Y', 1),
('Students/Student.php&include=General_Info&student_id=new', 'Y', 'Y', 1),
('Students/AssignOtherInfo.php', 'Y', 'Y', 1),
('Students/AddUsers.php', 'Y', 'Y', 1),
('Students/AddDrop.php', 'Y', 'Y', 1),
('Students/Letters.php', 'Y', 'Y', 1),
('Students/MailingLabels.php', 'Y', 'Y', 1),
('Students/PrintStudentInfo.php', 'Y', 'Y', 1),
('Students/StudentFields.php', 'Y', 'Y', 1),
('Students/EnrollmentCodes.php', 'Y', 'Y', 1),
('Students/Student.php&category_id=1', 'Y', 'Y', 1),
('Students/Student.php&category_id=3', 'Y', 'Y', 1),
('Students/Student.php&category_id=2', 'Y', 'Y', 1),
('Users/User.php', 'Y', 'Y', 1),
('Users/AddStudents.php', 'Y', 'Y', 1),
('Users/Preferences.php', 'Y', 'Y', 1),
('Users/Profiles.php', 'Y', 'Y', 1),
('Users/Exceptions.php', 'Y', 'Y', 1),
('Users/TeacherPrograms.php?include=Grades/InputFinalGrades.php', 'Y', 'Y', 1),
('Users/TeacherPrograms.php?include=Grades/Grades.php', 'Y', 'Y', 1),
('Users/TeacherPrograms.php?include=Attendance/TakeAttendance.php', 'Y', 'Y', 1),
('Users/TeacherPrograms.php?include=Eligibility/EnterEligibility.php', 'Y', 'Y', 1),
('Scheduling/Schedule.php', 'Y', 'Y', 1),
('Scheduling/Requests.php', 'Y', 'Y', 1),
('Scheduling/MassSchedule.php', 'Y', 'Y', 1),
('Scheduling/MassRequests.php', 'Y', 'Y', 1),
('Scheduling/MassDrops.php', 'Y', 'Y', 1),
('Scheduling/ScheduleReport.php', 'Y', 'Y', 1),
('Scheduling/RequestsReport.php', 'Y', 'Y', 1),
('Scheduling/UnfilledRequests.php', 'Y', 'Y', 1),
('Scheduling/IncompleteSchedules.php', 'Y', 'Y', 1),
('Scheduling/AddDrop.php', 'Y', 'Y', 1),
('Scheduling/PrintSchedules.php', 'Y', 'Y', 1),
('Scheduling/PrintRequests.php', 'Y', 'Y', 1),
('Scheduling/PrintClassLists.php', 'Y', 'Y', 1),
('Scheduling/Courses.php', 'Y', 'Y', 1),
('Scheduling/Scheduler.php', 'Y', 'Y', 1),
('Grades/ReportCards.php', 'Y', 'Y', 1),
('Grades/CalcGPA.php', 'Y', 'Y', 1),
('Grades/Transcripts.php', 'Y', 'Y', 1),
('Grades/TeacherCompletion.php', 'Y', 'Y', 1),
('Users/User.php&staff_id=new', 'Y', 'Y', 1),
('Eligibility/Student.php', 'Y', 'Y', 1),
('Eligibility/AddActivity.php', 'Y', 'Y', 1),
('Eligibility/StudentList.php', 'Y', 'Y', 1),
('Eligibility/TeacherCompletion.php', 'Y', 'Y', 1),
('Eligibility/Activities.php', 'Y', 'Y', 1),
('Eligibility/EntryTimes.php', 'Y', 'Y', 1),
('Food_Service/Accounts.php', 'Y', 'Y', 1),
('Food_Service/Statements.php', 'Y', 'Y', 1),
('Food_Service/Transactions.php', 'Y', 'Y', 1),
('Food_Service/ServeMenus.php', 'Y', 'Y', 1),
('Food_Service/ActivityReport.php', 'Y', 'Y', 1),
('Food_Service/TransactionsReport.php', 'Y', 'Y', 1),
('Food_Service/MenuReports.php', 'Y', 'Y', 1),
('Food_Service/BalanceReport.php', 'Y', 'Y', 1),
('Food_Service/Reminders.php', 'Y', 'Y', 1),
('Food_Service/DailyMenus.php', 'Y', 'Y', 1),
('Food_Service/MenuItems.php', 'Y', 'Y', 1),
('Food_Service/Menus.php', 'Y', 'Y', 1),
('Food_Service/Kiosk.php', 'Y', 'Y', 1),
('Resources/Redirect.php?to=doc', 'Y', 'Y', 1),
('Resources/Redirect.php?to=videohelp', 'Y', 'Y', 1),
('Resources/Redirect.php?to=forums', 'Y', 'Y', 1),
('Resources/Redirect.php?to=translate', 'Y', 'Y', 1),
('Resources/Redirect.php?to=getkey', 'Y', 'Y', 1),
('School_Setup/Calendar.php', 'Y', 'Y', 1),
('Students/AdvancedReport.php', 'Y', 'Y', 1),
('Students/StudentLabels.php', 'Y', 'Y', 1),
('Students/AddressFields.php', 'Y', 'Y', 1),
('Students/PeopleFields.php', 'Y', 'Y', 1),
('Students/Student.php&category_id=4', 'Y', 'Y', 1),
('Students/Student.php&category_id=5', 'Y', 'Y', 1),
('Users/UserFields.php', 'Y', 'Y', 1),
('Users/TeacherPrograms.php?include=Grades/AnomalousGrades.php', 'Y', 'Y', 1),
('Users/User.php&category_id=1', 'Y', 'Y', 1),
('Users/User.php&category_id=2', 'Y', 'Y', 1),
('Users/User.php&category_id=3', 'Y', 'Y', 1),
('Users/User.php&category_id=4', 'Y', 'Y', 1),
('Users/User.php&category_id=905', 'Y', 'Y', 1),
('Users/User.php&category_id=938', 'Y', 'Y', 1),
('Scheduling/PrintClassPictures.php', 'Y', 'Y', 1),
('Grades/HonorRoll.php', 'Y', 'Y', 1),
('Grades/StudentGrades.php', 'Y', 'Y', 1),
('Grades/GradeBreakdown.php', 'Y', 'Y', 1),
('Grades/FinalGrades.php', 'Y', 'Y', 1),
('Grades/GPARankList.php', 'Y', 'Y', 1),
('Grades/GPAMPList.php', 'Y', 'Y', 1),
('Grades/ReportCardGrades.php', 'Y', 'Y', 1),
('Grades/ReportCardComments.php', 'Y', 'Y', 1),
('Grades/ReportCardCommentCodes.php', 'Y', 'Y', 1),
('Grades/EditHistoryMarkingPeriods.php', 'Y', 'Y', 1),
('Grades/EditReportCardGrades.php', 'Y', 'Y', 1),
('Users/TeacherPrograms.php?include=Grades/AnomalousGrades.php', 'Y', 'Y', 1),
('Attendance/Administration.php', 'Y', 'Y', 1),
('Attendance/AddAbsences.php', 'Y', 'Y', 1),
('Attendance/TeacherCompletion.php', 'Y', 'Y', 1),
('Attendance/Percent.php', 'Y', 'Y', 1),
('Attendance/Percent.php?list_by_day=true', 'Y', 'Y', 1),
('Attendance/DailySummary.php', 'Y', 'Y', 1),
('Attendance/StudentSummary.php', 'Y', 'Y', 1),
('Attendance/FixDailyAttendance.php', 'Y', 'Y', 1),
('Attendance/DuplicateAttendance.php', 'Y', 'Y', 1),
('Attendance/AttendanceCodes.php', 'Y', 'Y', 1);

-- --------------------------------------------------------

--
-- Table structure for table `staff_fields`
--

DROP TABLE IF EXISTS `staff_fields`;
CREATE TABLE IF NOT EXISTS `staff_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` char(10) DEFAULT NULL,
  `search` char(1) DEFAULT NULL,
  `title` longtext,
  `sort_order` double DEFAULT NULL,
  `select_options` longtext,
  `category_id` double DEFAULT NULL,
  `system_field` char(1) DEFAULT NULL,
  `required` char(1) DEFAULT NULL,
  `default_selection` char(255) DEFAULT NULL,
  `description` longtext,
  PRIMARY KEY (`id`),
  KEY `staff_desc_ind1` (`id`),
  KEY `staff_desc_ind2` (`type`),
  KEY `staff_fields_ind3` (`category_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=100380037 ;

-- --------------------------------------------------------

--
-- Table structure for table `staff_field_categories`
--

DROP TABLE IF EXISTS `staff_field_categories`;
CREATE TABLE IF NOT EXISTS `staff_field_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` longtext,
  `sort_order` double DEFAULT NULL,
  `include` char(100) DEFAULT NULL,
  `admin` char(1) DEFAULT NULL,
  `teacher` char(1) DEFAULT NULL,
  `parent` char(1) DEFAULT NULL,
  `none` char(1) DEFAULT NULL,
  `columns` double(4,0) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `staff_field_categories`
--

INSERT INTO `staff_field_categories` (`id`, `title`, `sort_order`, `include`, `admin`, `teacher`, `parent`, `none`, `columns`) VALUES
(1, 'General Info', 1, NULL, 'Y', 'Y', 'Y', 'Y', NULL),
(2, 'Schedule', 2, NULL, NULL, 'Y', NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
CREATE TABLE IF NOT EXISTS `students` (
  `student_id` int(11) NOT NULL AUTO_INCREMENT,
  `last_name` char(50) NOT NULL,
  `first_name` char(50) NOT NULL,
  `middle_name` char(50) DEFAULT NULL,
  `name_suffix` char(3) DEFAULT NULL,
  `nickname` char(50) DEFAULT NULL,
  `soc_sec_no` char(9) DEFAULT NULL,
  `gender` char(1) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `birth_place` char(25) DEFAULT NULL,
  `birth_city` char(25) DEFAULT NULL,
  `birth_state` char(25) DEFAULT NULL,
  `birth_country` char(25) DEFAULT NULL,
  `language` char(100) DEFAULT NULL,
  `ethnicity` char(100) DEFAULT NULL,
  `physician` char(100) DEFAULT NULL,
  `physician_phone` char(20) DEFAULT NULL,
  `hospital` char(100) DEFAULT NULL,
  `medical_comments` longtext,
  `doctors_note` char(1) DEFAULT NULL,
  `doctors_note_comments` longtext,
  `username` char(100) DEFAULT NULL,
  `password` char(100) DEFAULT NULL,
  `custom_200000000` char(255) DEFAULT NULL,
  `custom_200000001` char(255) DEFAULT NULL,
  `custom_200000002` char(255) DEFAULT NULL,
  `custom_200000003` char(255) DEFAULT NULL,
  `custom_200000005` char(255) DEFAULT NULL,
  `custom_200000006` char(255) DEFAULT NULL,
  `custom_200000007` char(255) DEFAULT NULL,
  `custom_200000008` char(255) DEFAULT NULL,
  `custom_200000009` longtext,
  `custom_200000010` char(1) DEFAULT NULL,
  `custom_200000011` longtext,
  `last_login` timestamp NULL DEFAULT NULL,
  `failed_login` double DEFAULT NULL,
  `custom_200000004` date DEFAULT NULL,
  PRIMARY KEY (`student_id`),
  KEY `ethnic` (`ethnicity`),
  KEY `name` (`middle_name`,`first_name`,`last_name`),
  KEY `sex` (`gender`),
  KEY `ssn` (`soc_sec_no`),
  KEY `students_ind4` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `student_delete_records`
--

CREATE TABLE IF NOT EXISTS `student_delete_records` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `deleted_by_id` int(1) NOT NULL,
  `deleted_by_user` varchar(150) NOT NULL,
  `student_id` int(11) NOT NULL,
  `student_fullname` varchar(250) NOT NULL,
  `datetime` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Table structure for table `students_join_address`
--

DROP TABLE IF EXISTS `students_join_address`;
CREATE TABLE IF NOT EXISTS `students_join_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` double NOT NULL,
  `address_id` double(10,0) NOT NULL,
  `contact_seq` double(10,0) DEFAULT NULL,
  `gets_mail` char(1) DEFAULT NULL,
  `primary_residence` char(1) DEFAULT NULL,
  `legal_residence` char(1) DEFAULT NULL,
  `am_bus` char(1) DEFAULT NULL,
  `pm_bus` char(1) DEFAULT NULL,
  `mailing` char(1) DEFAULT NULL,
  `residence` char(1) DEFAULT NULL,
  `bus` char(1) DEFAULT NULL,
  `bus_pickup` char(1) DEFAULT NULL,
  `bus_dropoff` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stu_addr_meets_2` (`address_id`),
  KEY `stu_addr_meets_3` (`primary_residence`),
  KEY `stu_addr_meets_4` (`legal_residence`),
  KEY `students_join_address_ind1` (`student_id`),
  KEY `sys_c007322` (`address_id`,`student_id`,`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `students_join_people`
--

DROP TABLE IF EXISTS `students_join_people`;
CREATE TABLE IF NOT EXISTS `students_join_people` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` double NOT NULL,
  `person_id` double(10,0) NOT NULL,
  `address_id` double DEFAULT NULL,
  `custody` char(1) DEFAULT NULL,
  `emergency` char(1) DEFAULT NULL,
  `student_relation` char(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `relations_meets_2` (`person_id`),
  KEY `relations_meets_5` (`id`),
  KEY `relations_meets_6` (`emergency`,`custody`),
  KEY `students_join_people_ind1` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `students_join_users`
--

DROP TABLE IF EXISTS `students_join_users`;
CREATE TABLE IF NOT EXISTS `students_join_users` (
  `student_id` double NOT NULL,
  `staff_id` int(11) NOT NULL,
  PRIMARY KEY (`staff_id`,`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student_eligibility_activities`
--

DROP TABLE IF EXISTS `student_eligibility_activities`;
CREATE TABLE IF NOT EXISTS `student_eligibility_activities` (
  `syear` double(4,0) DEFAULT NULL,
  `student_id` double DEFAULT NULL,
  `activity_id` double DEFAULT NULL,
  KEY `student_eligibility_activities_ind1` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student_enrollment`
--

DROP TABLE IF EXISTS `student_enrollment`;
CREATE TABLE IF NOT EXISTS `student_enrollment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `syear` double(4,0) DEFAULT NULL,
  `school_id` double DEFAULT NULL,
  `student_id` double DEFAULT NULL,
  `grade_id` double DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `enrollment_code` double DEFAULT NULL,
  `drop_code` double DEFAULT NULL,
  `next_school` double DEFAULT NULL,
  `calendar_id` double DEFAULT NULL,
  `last_school` double DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_enrollment_1` (`enrollment_code`,`student_id`),
  KEY `student_enrollment_2` (`grade_id`),
  KEY `student_enrollment_3` (`grade_id`,`school_id`,`student_id`,`syear`),
  KEY `student_enrollment_6` (`end_date`,`start_date`),
  KEY `student_enrollment_7` (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `student_enrollment_codes`
--

DROP TABLE IF EXISTS `student_enrollment_codes`;
CREATE TABLE IF NOT EXISTS `student_enrollment_codes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `syear` double(4,0) DEFAULT NULL,
  `title` char(100) DEFAULT NULL,
  `short_name` char(10) DEFAULT NULL,
  `type` char(4) DEFAULT NULL,
  `default_code` char(1) DEFAULT NULL,
  `sort_order` double DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `student_enrollment_codes`
--

INSERT INTO `student_enrollment_codes` (`id`, `syear`, `title`, `short_name`, `type`, `default_code`, `sort_order`) VALUES
(1, 2014, 'Moved from District', 'MOVE', 'Drop', NULL, 1),
(2, 2014, 'Expelled', 'EXP', 'Drop', NULL, 2),
(3, 2014, 'Beginning of Year', 'EBY', 'Add', 'Y', 3),
(4, 2014, 'From Other District', 'OTHER', 'Add', NULL, 4),
(5, 2014, 'Transferred in District', 'TRAN', 'Drop', NULL, 5),
(6, 2014, 'Transferred in District', 'EMY', 'Add', NULL, 6);

-- --------------------------------------------------------

--
-- Table structure for table `student_field_categories`
--

DROP TABLE IF EXISTS `student_field_categories`;
CREATE TABLE IF NOT EXISTS `student_field_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text,
  `sort_order` decimal(10,0) DEFAULT NULL,
  `columns` decimal(4,0) DEFAULT NULL,
  `include` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `student_field_categories`
--

INSERT INTO `student_field_categories` (`id`, `title`, `sort_order`, `columns`, `include`) VALUES
(1, 'General Info', 1, NULL, NULL),
(2, 'Medical', 3, NULL, NULL),
(3, 'Addresses & Contacts', 2, NULL, NULL),
(4, 'Comments', 4, NULL, NULL),
(5, 'Food Service', 5, NULL, 'Food_Service/Student');

-- --------------------------------------------------------

--
-- Table structure for table `student_gpa_calculated`
--

DROP TABLE IF EXISTS `student_gpa_calculated`;
CREATE TABLE IF NOT EXISTS `student_gpa_calculated` (
  `student_id` double DEFAULT NULL,
  `marking_period_id` double DEFAULT NULL,
  `mp` char(4) DEFAULT NULL,
  `gpa` double DEFAULT NULL,
  `weighted_gpa` double DEFAULT NULL,
  `class_rank` double DEFAULT NULL,
  KEY `student_gpa_calculated_ind1` (`student_id`,`marking_period_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student_gpa_running`
--

DROP TABLE IF EXISTS `student_gpa_running`;
CREATE TABLE IF NOT EXISTS `student_gpa_running` (
  `student_id` double DEFAULT NULL,
  `marking_period_id` double DEFAULT NULL,
  `gpa_points` double DEFAULT NULL,
  `gpa_points_weighted` double DEFAULT NULL,
  `divisor` double DEFAULT NULL,
  KEY `student_gpa_running_ind1` (`student_id`,`marking_period_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student_medical`
--

DROP TABLE IF EXISTS `student_medical`;
CREATE TABLE IF NOT EXISTS `student_medical` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` double DEFAULT NULL,
  `type` char(25) DEFAULT NULL,
  `medical_date` date DEFAULT NULL,
  `comments` char(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_medical_ind1` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `student_medical_alerts`
--

DROP TABLE IF EXISTS `student_medical_alerts`;
CREATE TABLE IF NOT EXISTS `student_medical_alerts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` double DEFAULT NULL,
  `title` char(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_medical_alerts_ind1` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `student_medical_visits`
--

DROP TABLE IF EXISTS `student_medical_visits`;
CREATE TABLE IF NOT EXISTS `student_medical_visits` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` double DEFAULT NULL,
  `school_date` date DEFAULT NULL,
  `time_in` char(20) DEFAULT NULL,
  `time_out` char(20) DEFAULT NULL,
  `reason` char(100) DEFAULT NULL,
  `result` char(100) DEFAULT NULL,
  `comments` char(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `student_medical_visits_ind1` (`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `student_mp_comments`
--

DROP TABLE IF EXISTS `student_mp_comments`;
CREATE TABLE IF NOT EXISTS `student_mp_comments` (
  `student_id` double NOT NULL,
  `syear` double(4,0) NOT NULL,
  `marking_period_id` double NOT NULL,
  `comment` text,
  PRIMARY KEY (`marking_period_id`,`syear`,`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student_mp_stats`
--

DROP TABLE IF EXISTS `student_mp_stats`;
CREATE TABLE IF NOT EXISTS `student_mp_stats` (
  `student_id` int(32) NOT NULL,
  `marking_period_id` int(32) NOT NULL,
  `cum_weighted_factor` double DEFAULT NULL,
  `cum_unweighted_factor` double DEFAULT NULL,
  `cum_rank` int(32) DEFAULT NULL,
  `mp_rank` int(32) DEFAULT NULL,
  `class_size` int(32) DEFAULT NULL,
  `sum_weighted_factors` double DEFAULT NULL,
  `sum_unweighted_factors` double DEFAULT NULL,
  `count_weighted_factors` double DEFAULT NULL,
  `count_unweighted_factors` double DEFAULT NULL,
  `grade_level_short` char(3) DEFAULT NULL,
  `cr_weighted_factors` double DEFAULT NULL,
  `cr_unweighted_factors` double DEFAULT NULL,
  `count_cr_factors` int(32) DEFAULT NULL,
  `cum_cr_weighted_factor` double DEFAULT NULL,
  `cum_cr_unweighted_factor` double DEFAULT NULL,
  `credit_attempted` double DEFAULT NULL,
  `credit_earned` double DEFAULT NULL,
  `gp_credits` double DEFAULT NULL,
  `cr_credits` double DEFAULT NULL,
  `comments` char(75) DEFAULT NULL,
  PRIMARY KEY (`marking_period_id`,`student_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student_report_card_comments`
--

DROP TABLE IF EXISTS `student_report_card_comments`;
CREATE TABLE IF NOT EXISTS `student_report_card_comments` (
  `syear` double(4,0) NOT NULL,
  `school_id` double DEFAULT NULL,
  `student_id` double NOT NULL,
  `course_period_id` double NOT NULL,
  `report_card_comment_id` double NOT NULL,
  `marking_period_id` char(10) NOT NULL,
  `comment` char(5) DEFAULT NULL,
  PRIMARY KEY (`report_card_comment_id`,`marking_period_id`,`course_period_id`,`student_id`,`syear`),
  KEY `student_report_card_comments_ind1` (`school_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `student_report_card_grades`
--

DROP TABLE IF EXISTS `student_report_card_grades`;
CREATE TABLE IF NOT EXISTS `student_report_card_grades` (
  `syear` double(4,0) DEFAULT NULL,
  `school_id` double DEFAULT NULL,
  `student_id` double NOT NULL,
  `course_period_id` double DEFAULT NULL,
  `report_card_grade_id` double DEFAULT NULL,
  `report_card_comment_id` double DEFAULT NULL,
  `comment` char(255) DEFAULT NULL,
  `marking_period_id` char(10) NOT NULL,
  `grade_percent` double(4,1) DEFAULT NULL,
  `grade_letter` char(5) DEFAULT NULL,
  `weighted_gp` double NOT NULL DEFAULT '0',
  `unweighted_gp` double NOT NULL DEFAULT '0',
  `gp_scale` double NOT NULL DEFAULT '0',
  `credit_attempted` double NOT NULL DEFAULT '0',
  `credit_earned` double NOT NULL DEFAULT '0',
  `credit_category` char(10) DEFAULT NULL,
  `course_title` char(100) DEFAULT NULL,
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `school` char(255) DEFAULT NULL,
  `class_rank` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `student_report_card_grades_id_key` (`id`),
  KEY `student_report_card_grades_ind1` (`school_id`),
  KEY `student_report_card_grades_ind2` (`student_id`),
  KEY `student_report_card_grades_ind3` (`course_period_id`),
  KEY `student_report_card_grades_ind4` (`marking_period_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;


-- --------------------------------------------------------

--
-- Table structure for table `student_test_categories`
--

DROP TABLE IF EXISTS `student_test_categories`;
CREATE TABLE IF NOT EXISTS `student_test_categories` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `test` char(25) DEFAULT NULL,
  `category` char(40) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `student_test_scores`
--

DROP TABLE IF EXISTS `student_test_scores`;
CREATE TABLE IF NOT EXISTS `student_test_scores` (
  `id` int(32) NOT NULL AUTO_INCREMENT,
  `student_id` int(32) DEFAULT NULL,
  `test_category_id` int(32) DEFAULT NULL,
  `score` char(25) DEFAULT NULL,
  `test_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

DROP TABLE IF EXISTS `user_profiles`;
CREATE TABLE IF NOT EXISTS `user_profiles` (
  `id` double NOT NULL DEFAULT '0',
  `profile` char(30) DEFAULT NULL,
  `title` char(100) DEFAULT NULL,
  `upid` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`upid`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `profile`, `title`, `upid`) VALUES
(0, 'student', 'Student', 1),
(1, 'admin', 'Administrator', 2),
(2, 'teacher', 'Teacher', 3),
(3, 'parent', 'Parent', 4),
(6, 'admin', 'Guidance', 5),
(7, 'admin', 'Admin', 6);

-- --------------------------------------------------------

--
-- Table structure for table `volunteer_log`
--

DROP TABLE IF EXISTS `volunteer_log`;
CREATE TABLE IF NOT EXISTS `volunteer_log` (
  `id` double NOT NULL,
  `syear` double(4,0) NOT NULL,
  `school_id` double NOT NULL,
  `student_id` double NOT NULL,
  `category_id` double DEFAULT NULL,
  `event_date` date DEFAULT NULL,
  `event_title` char(50) DEFAULT NULL,
  `hours` double(4,2) DEFAULT NULL,
  `comment` char(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `workbook_period`
--

DROP TABLE IF EXISTS `workbook_period`;
CREATE TABLE IF NOT EXISTS `workbook_period` (
  `student_id` double DEFAULT NULL,
  `school_date` date DEFAULT NULL,
  `period_id` double DEFAULT NULL,
  `attendance_code` double DEFAULT NULL,
  `attendance_teacher_code` double DEFAULT NULL,
  `attendance_reason` char(100) DEFAULT NULL,
  `admin` char(1) DEFAULT NULL,
  `course_period_id` double DEFAULT NULL,
  `marking_period_id` double DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure for view `course_details`
--
DROP VIEW IF EXISTS `course_details`;

CREATE VIEW `course_details` AS select `cp`.`school_id` AS `school_id`,`cp`.`syear` AS `syear`,`cp`.`marking_period_id` AS `marking_period_id`,`cp`.`period_id` AS `period_id`,`c`.`subject_id` AS `subject_id`,`cp`.`course_id` AS `course_id`,`cp`.`course_period_id` AS `course_period_id`,`cp`.`teacher_id` AS `teacher_id`,`c`.`title` AS `course_title`,`cp`.`title` AS `cp_title`,`cp`.`grade_scale_id` AS `grade_scale_id`,`cp`.`mp` AS `mp`,`cp`.`credits` AS `credits` from (`course_periods` `cp` join `courses` `c`) where (`cp`.`course_id` = `c`.`course_id`);

-- --------------------------------------------------------

--
-- Structure for view `enroll_grade`
--
DROP VIEW IF EXISTS `enroll_grade`;

CREATE VIEW `enroll_grade` AS select `e`.`id` AS `id`,`e`.`syear` AS `syear`,`e`.`school_id` AS `school_id`,`e`.`student_id` AS `student_id`,`e`.`start_date` AS `start_date`,`e`.`end_date` AS `end_date`,`sg`.`short_name` AS `short_name`,`sg`.`title` AS `title` from (`student_enrollment` `e` join `school_gradelevels` `sg`) where (`e`.`grade_id` = `sg`.`id`);

-- --------------------------------------------------------

--
-- Structure for view `marking_periods`
--
DROP VIEW IF EXISTS `marking_periods`;

CREATE VIEW `marking_periods` AS select `school_marking_periods`.`marking_period_id` AS `marking_period_id`,'Centre' AS `mp_source`,`school_marking_periods`.`syear` AS `syear`,`school_marking_periods`.`school_id` AS `school_id`,(case when (`school_marking_periods`.`mp` = 'FY') then 'year' when (`school_marking_periods`.`mp` = 'SEM') then 'semester' when (`school_marking_periods`.`mp` = 'QTR') then 'quarter' else NULL end) AS `mp_type`,`school_marking_periods`.`title` AS `title`,`school_marking_periods`.`short_name` AS `short_name`,`school_marking_periods`.`sort_order` AS `sort_order`,(case when (`school_marking_periods`.`parent_id` > 0) then `school_marking_periods`.`parent_id` else -(1) end) AS `parent_id`,(case when ((select `smp`.`parent_id` from `school_marking_periods` `smp` where (`smp`.`marking_period_id` = `school_marking_periods`.`parent_id`)) > 0) then (select `smp`.`parent_id` from `school_marking_periods` `smp` where (`smp`.`marking_period_id` = `school_marking_periods`.`parent_id`)) else -(1) end) AS `grandparent_id`,`school_marking_periods`.`start_date` AS `start_date`,`school_marking_periods`.`end_date` AS `end_date`,`school_marking_periods`.`post_start_date` AS `post_start_date`,`school_marking_periods`.`post_end_date` AS `post_end_date`,`school_marking_periods`.`does_grades` AS `does_grades`,`school_marking_periods`.`does_exam` AS `does_exam`,`school_marking_periods`.`does_comments` AS `does_comments` from `school_marking_periods` union select `history_marking_periods`.`marking_period_id` AS `marking_period_id`,'History' AS `mp_source`,`history_marking_periods`.`syear` AS `syear`,`history_marking_periods`.`school_id` AS `school_id`,`history_marking_periods`.`mp_type` AS `mp_type`,`history_marking_periods`.`name` AS `title`,`history_marking_periods`.`short_name` AS `short_name`,NULL AS `sort_order`,`history_marking_periods`.`parent_id` AS `parent_id`,-(1) AS `grandparent_id`,NULL AS `start_date`,`history_marking_periods`.`post_end_date` AS `end_date`,NULL AS `post_start_date`,`history_marking_periods`.`post_end_date` AS `post_end_date`,'Y' AS `does_grades`,NULL AS `does_exam`,NULL AS `does_comments` from `history_marking_periods`;

-- --------------------------------------------------------

--
-- Structure for view `transcript_grades`
--
DROP VIEW IF EXISTS `transcript_grades`;

CREATE VIEW `transcript_grades` AS
  select `mp`.`syear` AS `syear`,
    `mp`.`school_id` AS `school_id`,
    `mp`.`marking_period_id` AS `marking_period_id`,
    `mp`.`mp_type` AS `mp_type`,
    `mp`.`short_name` AS `short_name`,
    `mp`.`parent_id` AS `parent_id`,
    `mp`.`grandparent_id` AS `grandparent_id`,
    `mp`.`end_date` AS `end_date`,
    `sms`.`student_id` AS `student_id`,
    (`sms`.`cum_weighted_factor` * `schools`.`reporting_gp_scale`) AS `cum_weighted_gpa`,
    (`sms`.`cum_unweighted_factor` * `schools`.`reporting_gp_scale`) AS `cum_unweighted_gpa`,
    `sms`.`cum_rank` AS `cum_rank`,
    `sms`.`mp_rank` AS `mp_rank`,
    `sms`.`class_size` AS `class_size`,
    ((`sms`.`sum_weighted_factors` / `sms`.`count_weighted_factors`) * `schools`.`reporting_gp_scale`) AS `weighted_gpa`,
    ((`sms`.`sum_unweighted_factors` / `sms`.`count_unweighted_factors`) * `schools`.`reporting_gp_scale`) AS `unweighted_gpa`,
    `sms`.`grade_level_short` AS `grade_level_short`,
    `srcg`.`comment` AS `comment`,
    `srcg`.`grade_percent` AS `grade_percent`,
    `srcg`.`grade_letter` AS `grade_letter`,
    `srcg`.`weighted_gp` AS `weighted_gp`,
    `srcg`.`unweighted_gp` AS `unweighted_gp`,
    `srcg`.`gp_scale` AS `gp_scale`,
    `srcg`.`credit_attempted` AS `credit_attempted`,
    `srcg`.`credit_earned` AS `credit_earned`,
    `srcg`.`course_title` AS `course_title`,
    `srcg`.`school` AS `school_name`,
    `schools`.`reporting_gp_scale` AS `school_scale`,
    ((`sms`.`cr_weighted_factors` / `sms`.`count_cr_factors`) * `schools`.`reporting_gp_scale`) AS `cr_weighted_gpa`,
    ((`sms`.`cr_unweighted_factors` / `sms`.`count_cr_factors`) * `schools`.`reporting_gp_scale`) AS `cr_unweighted_gpa`,
    (`sms`.`cum_cr_weighted_factor` * `schools`.`reporting_gp_scale`) AS `cum_cr_weighted_gpa`,
    (`sms`.`cum_cr_unweighted_factor` * `schools`.`reporting_gp_scale`) AS `cum_cr_unweighted_gpa`,
    `srcg`.`class_rank` AS `class_rank`
  from (((`student_report_card_grades` `srcg` join `marking_periods` `mp` on(((`srcg`.`marking_period_id` = `mp`.`marking_period_id`) and (`srcg`.`school_id` = `mp`.`school_id`) ))) join `student_mp_stats` `sms` on(((`srcg`.`student_id` = `sms`.`student_id`) and (`sms`.`marking_period_id` = `srcg`.`marking_period_id`)))) join `schools` on(((`mp`.`school_id` = `schools`.`id`) )));


-- UPDATES
ALTER TABLE `staff` ADD `moodle_id` INT( 11 ) NULL;
ALTER TABLE `students` ADD `moodle_id` INT( 11 ) NULL;
ALTER TABLE `courses` ADD `moodle_id` INT( 11 ) NULL;
ALTER TABLE `course_periods` ADD `moodle_id` INT( 11 ) NULL;
ALTER TABLE `course_subjects` ADD `moodle_id` INT( 11 ) NULL;

--


DELIMITER $$
--
-- Procedures
--
DROP PROCEDURE IF EXISTS `calc_cum_cr_gpa`$$
CREATE PROCEDURE `calc_cum_cr_gpa`(mp_id CHAR(20), s_id INT)
BEGIN
  DECLARE mpinfo_enddate VARCHAR(170);
    SELECT end_date INTO mpinfo_enddate FROM marking_periods WHERE marking_period_id = mp_id;
      UPDATE student_mp_stats, (
        SELECT (sum((weighted_gp/gp_scale)*credit_attempted)/sum(credit_attempted)) as weighted_gpa,
          (sum((unweighted_gp/gp_scale)*credit_attempted)/sum(credit_attempted)) as unweighted_gpa
        FROM (
          SELECT weighted_gp, unweighted_gp, gp_scale, credit_attempted, credit_earned, school_scale
          FROM transcript_grades WHERE student_id = s_id
          AND (end_date <= mpinfo_enddate OR marking_period_id = mp_id)
          AND gp_scale > 0
          AND credit_attempted > 0 and class_rank = 'Y' ) as x group by school_scale) as sms1
      SET cum_cr_weighted_factor = sms1.weighted_gpa,
      cum_cr_unweighted_factor = sms1.unweighted_gpa
    WHERE student_mp_stats.student_id = s_id and student_mp_stats.marking_period_id = mp_id;
END$$

DROP PROCEDURE IF EXISTS `calc_cum_gpa`$$
CREATE PROCEDURE `calc_cum_gpa`(mp_id CHAR(20), s_id INT)
BEGIN
  DECLARE mpinfo_enddate VARCHAR(170);
    SELECT end_date INTO mpinfo_enddate FROM marking_periods WHERE marking_period_id = mp_id;
      UPDATE student_mp_stats, (
        SELECT (sum((weighted_gp/gp_scale)*credit_attempted)/sum(credit_attempted)) as weighted_gpa,
          (sum((unweighted_gp/gp_scale)*credit_attempted)/sum(credit_attempted)) as unweighted_gpa
        FROM (
          SELECT weighted_gp, unweighted_gp, gp_scale, credit_attempted, credit_earned, school_scale
          FROM transcript_grades WHERE student_id = s_id
          AND (end_date <= mpinfo_enddate OR marking_period_id = mp_id)
          AND gp_scale > 0
          AND credit_attempted > 0 ) as x group by school_scale) as sms1
      SET cum_weighted_factor = sms1.weighted_gpa,
      cum_unweighted_factor = sms1.unweighted_gpa
    WHERE student_mp_stats.student_id = s_id and student_mp_stats.marking_period_id = mp_id;
END$$

DROP PROCEDURE IF EXISTS `calc_gpa_mp`$$
CREATE PROCEDURE `calc_gpa_mp`(s_id INT, mp_id CHAR(20))
BEGIN
  DECLARE count INT;
  SELECT COUNT(*) INTO count FROM student_mp_stats AS oldrec WHERE student_id = s_id and marking_period_id = mp_id;
  IF count > 0 THEN
    UPDATE student_mp_stats smstarget, (
      SELECT
      student_id, marking_period_id,
        SUM(weighted_gp*credit_attempted/gp_scale) as sum_weighted_factors,
        SUM(unweighted_gp*credit_attempted/gp_scale) as sum_unweighted_factors,
        SUM(credit_attempted) as gp_credits,
        SUM( case when class_rank = 'Y' THEN weighted_gp*credit_attempted/gp_scale END ) as cr_weighted,
        SUM( case when class_rank = 'Y' THEN unweighted_gp*credit_attempted/gp_scale END ) as cr_unweighted,
        SUM( case when class_rank = 'Y' THEN credit_attempted END) as cr_credits

        FROM student_report_card_grades WHERE student_id = s_id
            AND marking_period_id = mp_id
            AND NOT gp_scale = 0 AND NOT marking_period_id LIKE 'E%' group by student_id, marking_period_id
  ) as rcg
    SET
        smstarget.sum_weighted_factors = rcg.sum_weighted_factors,
        smstarget.sum_unweighted_factors = rcg.sum_unweighted_factors,
        smstarget.cr_weighted_factors = rcg.cr_weighted,
        smstarget.cr_unweighted_factors = rcg.cr_unweighted,
        smstarget.gp_credits = rcg.gp_credits,
        smstarget.cr_credits = rcg.cr_credits
    WHERE smstarget.student_id = s_id and smstarget.marking_period_id = mp_id;
  ELSE
    INSERT INTO student_mp_stats (student_id, marking_period_id, sum_weighted_factors, sum_unweighted_factors, grade_level_short, cr_weighted_factors, cr_unweighted_factors, gp_credits, cr_credits)
    SELECT
        srcg.student_id, srcg.marking_period_id,
        SUM(weighted_gp*credit_attempted/gp_scale) as sum_weighted_factors,
        SUM(unweighted_gp*credit_attempted/gp_scale) as sum_unweighted_factors,
        eg.short_name,
        SUM( case when class_rank = 'Y' THEN weighted_gp*credit_attempted/gp_scale END ) as cr_weighted,
        SUM( case when class_rank = 'Y' THEN unweighted_gp*credit_attempted/gp_scale END ) as cr_unweighted,
        SUM(credit_attempted) as gp_credits,
        SUM(case when class_rank = 'Y' THEN credit_attempted END) as cr_credits
    FROM student_report_card_grades AS srcg join marking_periods mp on (mp.marking_period_id = srcg.marking_period_id) left outer join enroll_grade AS eg on (eg.student_id = srcg.student_id and eg.syear = mp.syear and eg.school_id = mp.school_id)
    WHERE srcg.student_id = s_id and srcg.marking_period_id = mp_id and not srcg.gp_scale = 0
    AND NOT srcg.marking_period_id LIKE 'E%' group by srcg.student_id, srcg.marking_period_id, eg.short_name;
  END IF;
END$$

DROP PROCEDURE IF EXISTS `test`$$
CREATE PROCEDURE `test`()
BEGIN
  DECLARE myvar varchar(170);
  SELECT end_date INTO myvar FROM marking_periods WHERE marking_period_id =46;
  SELECT myvar;
  END$$

--
-- Functions
--
DROP FUNCTION IF EXISTS `calc_cum_gpa_mp`$$
CREATE FUNCTION `calc_cum_gpa_mp`(mp_id CHAR(20)) RETURNS int(11)
BEGIN
    DECLARE done INT DEFAULT FALSE;
    DECLARE s_id VARCHAR(20);
    DECLARE my_cur CURSOR FOR SELECT student_id from student_mp_stats where marking_period_id = mp_id;
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    OPEN my_cur;
        FETCH my_cur INTO s_id;
          CALL calc_cum_gpa(mp_id, s_id);
          CALL calc_cum_cr_gpa(mp_id, s_id);
    CLOSE my_cur;
  RETURN 1;
END$$

DROP FUNCTION IF EXISTS `credit`$$
CREATE FUNCTION `credit`(cp_id INT, mp_id INT) RETURNS decimal(10,2)
    DETERMINISTIC
BEGIN
  DECLARE course_detail_mp VARCHAR(170);
  DECLARE course_detail_cr decimal(10);
  DECLARE course_detail_m VARCHAR(170);
  DECLARE mp_detail_mp VARCHAR(170);
  DECLARE mp_detail_type VARCHAR(170);
  #DECLARE mp_count decimal(10);
  DECLARE vals DECIMAL(10,2); DECLARE rate_result DECIMAL(10,2);

  SELECT marking_period_id INTO course_detail_mp from course_periods where course_period_id = cp_id;
  SELECT credits INTO course_detail_cr from course_periods where course_period_id = cp_id;
  SELECT mp INTO course_detail_m from course_periods where course_period_id = cp_id;

  SELECT marking_period_id INTO mp_detail_mp from marking_periods where marking_period_id = mp_id;
  SELECT mp_type INTO mp_detail_type from marking_periods where marking_period_id = mp_id;

  IF course_detail_mp = mp_detail_mp THEN
    return course_detail_cr;
  ELSEIF course_detail_m = 'FY' AND mp_detail_type = 'semester' THEN
    SELECT count(*) AS mp_count INTO vals from marking_periods where parent_id = course_detail_mp group by parent_id;
  ELSEIF course_detail_m = 'FY' and mp_detail_type = 'quarter' THEN
    SELECT count(*) AS mp_count INTO vals from marking_periods where grandparent_id = course_detail_mp group by grandparent_id;
  ELSEIF course_detail_m = 'SEM' and mp_detail_type = 'quarter' THEN
    SELECT count(*) AS mp_count INTO vals from marking_periods where parent_id = course_detail_mp group by parent_id;
  ELSE
    return 0;
  END IF;

  IF vals > 0 THEN
    return course_detail_cr/vals;
  ELSE
    return 0;
  END IF;
END$$

DROP FUNCTION IF EXISTS `set_class_rank_mp`$$
CREATE FUNCTION `set_class_rank_mp`(mp_id CHAR(20)) RETURNS decimal(10,2)
    DETERMINISTIC
BEGIN
    UPDATE student_mp_stats, (
      SELECT
      mp.syear, mp.marking_period_id, sgm.student_id, se.grade_id, sgm.cum_cr_weighted_factor,
        (SELECT count(*)+1
           FROM student_mp_stats sgm3
           WHERE sgm3.cum_cr_weighted_factor > sgm.cum_cr_weighted_factor
             AND sgm3.marking_period_id = mp.marking_period_id
             AND sgm3.student_id in (select distinct sgm2.student_id
                    FROM student_mp_stats sgm2, student_enrollment se2
                    WHERE sgm2.student_id = se2.student_id
                        AND sgm2.marking_period_id = mp.marking_period_id
                        AND se2.grade_id = se.grade_id
                        AND se2.syear = se.syear)
        ) AS netrank,

        (SELECT count(*)
           FROM student_mp_stats sgm4
           WHERE
             sgm4.marking_period_id = mp.marking_period_id
             AND sgm4.student_id in (select distinct sgm5.student_id
                    FROM student_mp_stats sgm5, student_enrollment se3
                    WHERE sgm5.student_id = se3.student_id
                      AND sgm5.marking_period_id = mp.marking_period_id
                        AND se3.grade_id = se.grade_id
                        AND se3.syear = se.syear)
        ) AS netclass_size
          FROM student_enrollment se, student_mp_stats sgm, marking_periods mp
          WHERE
          se.student_id = sgm.student_id
          AND sgm.marking_period_id = mp.marking_period_id
          AND mp.marking_period_id = mp_id
          AND se.syear = mp.syear
          AND NOT sgm.cum_cr_weighted_factor is null
      ORDER BY grade_id, netrank
      ) AS rank
    SET cum_rank = rank.netrank, class_size = rank.netclass_size
    WHERE student_mp_stats.marking_period_id = rank.marking_period_id AND student_mp_stats.student_id = rank.student_id;
RETURN 1;
END$$

DROP TRIGGER IF EXISTS srcg_mp_stats_insert $$
CREATE TRIGGER `srcg_mp_stats_insert` AFTER INSERT ON `student_report_card_grades`
 FOR EACH ROW BEGIN
            CALL calc_gpa_mp(NEW.student_id, NEW.marking_period_id);
END$$

DROP TRIGGER IF EXISTS srcg_mp_stats_update $$
CREATE TRIGGER `srcg_mp_stats_update` AFTER UPDATE ON `student_report_card_grades`
 FOR EACH ROW BEGIN
            CALL calc_gpa_mp(OLD.student_id, OLD.marking_period_id);
END$$

DROP TRIGGER IF EXISTS srcg_mp_stats_delete $$
CREATE TRIGGER `srcg_mp_stats_delete` AFTER DELETE ON `student_report_card_grades`
 FOR EACH ROW BEGIN
        CALL calc_gpa_mp(OLD.student_id, OLD.marking_period_id);
END$$

DELIMITER ;
