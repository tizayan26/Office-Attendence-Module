/*
Navicat MySQL Data Transfer

Source Server         : Local Sever
Source Server Version : 50711
Source Host           : localhost:3306
Source Database       : attendence_db

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2017-02-08 15:30:04
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `tbl_candidate_info`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_candidate_info`;
CREATE TABLE `tbl_candidate_info` (
  `F_Recruitment_Request_ID` mediumint(9) NOT NULL,
  `Candidate_ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Name` varchar(255) NOT NULL,
  `Phone` varchar(255) NOT NULL,
  `Resume` varchar(255) NOT NULL,
  `Is_Void` enum('1','0') NOT NULL,
  `Is_Processed` enum('1','0') NOT NULL,
  `Is_Exist` enum('1','0') NOT NULL,
  PRIMARY KEY (`Candidate_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_candidate_info
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_company_info`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_company_info`;
CREATE TABLE `tbl_company_info` (
  `Company_ID` tinyint(4) NOT NULL AUTO_INCREMENT,
  `Company_Name` varchar(100) NOT NULL,
  `Leave` tinyint(4) NOT NULL,
  `Is_Void` enum('1','0') NOT NULL DEFAULT '0',
  `Is_Processed` enum('1','0') NOT NULL DEFAULT '0',
  `Is_Exist` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Company_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_company_info
-- ----------------------------
INSERT INTO `tbl_company_info` VALUES ('1', 'Ryans IT Limited', '32', '0', '0', '1');
INSERT INTO `tbl_company_info` VALUES ('2', 'Ryans Archives', '30', '0', '0', '1');

-- ----------------------------
-- Table structure for `tbl_dept_info`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_dept_info`;
CREATE TABLE `tbl_dept_info` (
  `Dept_ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Dept_Name` varchar(200) NOT NULL,
  `Description` text NOT NULL,
  `Is_Void` enum('1','0') NOT NULL DEFAULT '0',
  `Is_Processed` enum('1','0') NOT NULL DEFAULT '0',
  `Is_Exist` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Dept_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_dept_info
-- ----------------------------
INSERT INTO `tbl_dept_info` VALUES ('1', 'Software', 'Development', '0', '0', '1');
INSERT INTO `tbl_dept_info` VALUES ('2', 'Business Development', 'Developing business', '0', '0', '1');
INSERT INTO `tbl_dept_info` VALUES ('3', 'Management', 'Managing decision', '0', '0', '1');
INSERT INTO `tbl_dept_info` VALUES ('4', 'Accounts', 'Accounting department', '0', '0', '1');

-- ----------------------------
-- Table structure for `tbl_employee_profile`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_employee_profile`;
CREATE TABLE `tbl_employee_profile` (
  `ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Employee_ID` varchar(9) NOT NULL,
  `Full_Name` varchar(80) NOT NULL,
  `DOB` date NOT NULL,
  `Father` varchar(255) NOT NULL,
  `Mother` varchar(255) NOT NULL,
  `Email` varchar(200) DEFAULT NULL,
  `Join_Date` date NOT NULL,
  `Emergency_Contact` varchar(255) NOT NULL,
  `Blood_Group` varchar(255) NOT NULL,
  `Height` varchar(255) NOT NULL,
  `Weight` varchar(255) NOT NULL,
  `Marital_Status` varchar(255) NOT NULL,
  `Identification_Mark` varchar(255) NOT NULL,
  `Training` text NOT NULL,
  `NID` text NOT NULL,
  `Passport` text,
  `Permanent_Address` text NOT NULL,
  `Address` text NOT NULL,
  `Previous_Organization` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  KEY `E_F_User_ID` (`ID`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_employee_profile
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_hierarchy_info`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_hierarchy_info`;
CREATE TABLE `tbl_hierarchy_info` (
  `F_User_ID` mediumint(9) NOT NULL,
  `H_ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Designation` varchar(100) NOT NULL,
  `Supervisor1_ID` mediumint(9) NOT NULL,
  `Supervisor2_ID` mediumint(9) NOT NULL,
  `Supervisor3_ID` mediumint(9) NOT NULL,
  `Is_Void` enum('1','0') NOT NULL DEFAULT '0',
  `Is_Processed` enum('1','0') NOT NULL DEFAULT '0',
  `Is_Exist` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`H_ID`),
  KEY `H_F_User_ID` (`F_User_ID`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_hierarchy_info
-- ----------------------------
INSERT INTO `tbl_hierarchy_info` VALUES ('230', '1', 'Supervisor', '517', '230', '230', '0', '0', '1');
INSERT INTO `tbl_hierarchy_info` VALUES ('3', '2', 'Software Engineer', '2', '4', '3', '0', '0', '1');

-- ----------------------------
-- Table structure for `tbl_holiday_info`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_holiday_info`;
CREATE TABLE `tbl_holiday_info` (
  `Holiday_ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Holiday_Name` varchar(100) NOT NULL,
  `Holiday_Type` varchar(100) NOT NULL,
  `From_Date` date NOT NULL,
  `To_Date` date NOT NULL,
  `Is_Void` enum('1','0') NOT NULL DEFAULT '0',
  `Is_Processed` enum('1','0') NOT NULL DEFAULT '0',
  `Is_Exist` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Holiday_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_holiday_info
-- ----------------------------
INSERT INTO `tbl_holiday_info` VALUES ('1', 'Victory Day', 'Government Holiday', '2015-12-16', '2015-12-16', '0', '0', '1');

-- ----------------------------
-- Table structure for `tbl_leave_info`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_leave_info`;
CREATE TABLE `tbl_leave_info` (
  `Leave_Type_ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Leave_Type` varchar(200) NOT NULL,
  `Is_Void` enum('1','0') NOT NULL DEFAULT '0',
  `Is_Processed` enum('1','0') NOT NULL DEFAULT '0',
  `Is_Exist` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Leave_Type_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_leave_info
-- ----------------------------
INSERT INTO `tbl_leave_info` VALUES ('1', 'Casual', '0', '0', '1');
INSERT INTO `tbl_leave_info` VALUES ('2', 'Medical', '0', '0', '1');
INSERT INTO `tbl_leave_info` VALUES ('3', 'Maternity', '0', '0', '1');

-- ----------------------------
-- Table structure for `tbl_leave_record`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_leave_record`;
CREATE TABLE `tbl_leave_record` (
  `F_User_ID` mediumint(9) NOT NULL,
  `Leave_ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Leave_Type_ID` mediumint(9) NOT NULL,
  `Leave_Reason` text NOT NULL,
  `From_Date` date NOT NULL,
  `To_Date` date NOT NULL,
  `Recommend` varchar(200) DEFAULT NULL,
  `Status` varchar(255) NOT NULL DEFAULT 'unread',
  `Is_Void` enum('1','0') NOT NULL DEFAULT '0',
  `Is_Processed` enum('1','0') NOT NULL DEFAULT '0',
  `Is_Exist` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Leave_ID`),
  KEY `L_F_User_ID` (`F_User_ID`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_leave_record
-- ----------------------------
INSERT INTO `tbl_leave_record` VALUES ('3', '1', '1', 'test', '2017-02-05', '2017-02-06', '2,', 'unread', '0', '0', '1');

-- ----------------------------
-- Table structure for `tbl_login_info`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_login_info`;
CREATE TABLE `tbl_login_info` (
  `F_User_ID` mediumint(9) NOT NULL,
  `F_User_Permission_ID` mediumint(9) NOT NULL,
  `Login_ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `User_Name` varchar(100) NOT NULL,
  `Password` varchar(150) NOT NULL,
  `Is_Void` enum('1','0') NOT NULL DEFAULT '0',
  `Is_Processed` enum('1','0') NOT NULL DEFAULT '0',
  `Is_Exist` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`Login_ID`),
  KEY `F_User_ID` (`F_User_ID`) USING BTREE,
  KEY `L_F_User_Permission_ID` (`F_User_Permission_ID`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_login_info
-- ----------------------------
INSERT INTO `tbl_login_info` VALUES ('1', '1', '2', 'upal', '2dbj1dHn', '0', '0', '1');
INSERT INTO `tbl_login_info` VALUES ('2', '4', '6', 'admin', 'naejlQ', '0', '0', '1');
INSERT INTO `tbl_login_info` VALUES ('3', '5', '7', 'khan', 'pa2nkZ6mow', '0', '0', '1');
INSERT INTO `tbl_login_info` VALUES ('4', '5', '8', 'shiplu', 'naejlQ', '0', '0', '1');

-- ----------------------------
-- Table structure for `tbl_login_record`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_login_record`;
CREATE TABLE `tbl_login_record` (
  `F_User_ID` mediumint(9) NOT NULL,
  `Login_Record_ID` int(11) NOT NULL AUTO_INCREMENT,
  `Date` date NOT NULL,
  `In_Time` time NOT NULL,
  `Out_Time` time DEFAULT '00:00:00',
  PRIMARY KEY (`Login_Record_ID`),
  KEY `LR_F_User_ID` (`F_User_ID`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=70 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_login_record
-- ----------------------------
INSERT INTO `tbl_login_record` VALUES ('1', '46', '2016-08-15', '15:45:18', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('2', '47', '2016-08-15', '15:55:48', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('1', '48', '2017-01-04', '13:59:48', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('1', '49', '2017-01-18', '12:54:44', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('1', '50', '2017-01-19', '09:26:02', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('1', '51', '2017-01-21', '10:11:07', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('2', '52', '2017-01-21', '16:37:22', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('1', '53', '2017-01-22', '10:12:53', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('3', '54', '2017-01-22', '13:06:39', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('1', '55', '2017-01-23', '10:29:08', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('1', '56', '2017-01-25', '09:38:33', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('1', '57', '2017-01-26', '10:34:14', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('2', '58', '2017-01-26', '14:34:54', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('3', '59', '2017-01-26', '14:48:13', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('1', '60', '2017-01-29', '09:16:06', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('2', '61', '2017-01-29', '15:16:21', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('3', '62', '2017-01-29', '15:21:41', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('1', '63', '2017-02-04', '14:42:21', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('1', '64', '2017-02-05', '09:13:06', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('3', '65', '2017-02-05', '09:13:32', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('1', '66', '2017-02-06', '10:15:28', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('1', '67', '2017-02-08', '11:52:09', '00:00:00');
INSERT INTO `tbl_login_record` VALUES ('3', '68', '2017-02-08', '12:24:17', '13:39:35');
INSERT INTO `tbl_login_record` VALUES ('2', '69', '2017-02-08', '12:48:39', '00:00:00');

-- ----------------------------
-- Table structure for `tbl_office_time`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_office_time`;
CREATE TABLE `tbl_office_time` (
  `F_User_ID` mediumint(9) NOT NULL,
  `In` time NOT NULL,
  `Out` time NOT NULL,
  PRIMARY KEY (`F_User_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_office_time
-- ----------------------------
INSERT INTO `tbl_office_time` VALUES ('1', '10:00:00', '18:00:00');
INSERT INTO `tbl_office_time` VALUES ('2', '09:00:00', '17:00:00');
INSERT INTO `tbl_office_time` VALUES ('3', '09:00:00', '17:00:00');
INSERT INTO `tbl_office_time` VALUES ('4', '10:00:00', '18:00:00');

-- ----------------------------
-- Table structure for `tbl_recruitment_record`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_recruitment_record`;
CREATE TABLE `tbl_recruitment_record` (
  `Recruitment_Request_ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `F_User_ID` mediumint(9) NOT NULL,
  `F_Company_ID` tinyint(4) NOT NULL,
  `F_Dept_ID` mediumint(9) NOT NULL,
  `Number` int(11) NOT NULL,
  `Remarks` text NOT NULL,
  `Comment` text NOT NULL,
  `Date` date NOT NULL,
  `Is_Void` enum('1','0') NOT NULL,
  `Is_Processed` enum('1','0') NOT NULL,
  `Is_Exist` enum('1','0') NOT NULL,
  PRIMARY KEY (`Recruitment_Request_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_recruitment_record
-- ----------------------------

-- ----------------------------
-- Table structure for `tbl_sessions_info`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_sessions_info`;
CREATE TABLE `tbl_sessions_info` (
  `session_id` varchar(40) NOT NULL DEFAULT '0',
  `ip_address` varchar(16) NOT NULL DEFAULT '0',
  `user_agent` varchar(120) NOT NULL,
  `last_activity` int(10) unsigned NOT NULL DEFAULT '0',
  `user_data` text,
  PRIMARY KEY (`session_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- ----------------------------
-- Records of tbl_sessions_info
-- ----------------------------
INSERT INTO `tbl_sessions_info` VALUES ('3ebeb5e384f9c92c4f2f2913f1bd4a88', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2547.0 Safari/537.36', '1446363397', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2015-11-01 19:36:55');
INSERT INTO `tbl_sessions_info` VALUES ('443cd9f44471e787f496a0a8eacdb094', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2552.0 Safari/537.36', '1446978976', 'Login ID:230|Name:Saiful Islam Sweet|Time:2015-11-08 22:36:27');
INSERT INTO `tbl_sessions_info` VALUES ('4c70e37e297144ad2dcba938c853d7d0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2552.0 Safari/537.36', '1446981961', 'Login ID:230|Name:Saiful Islam Sweet|Time:2015-11-08 23:26:02');
INSERT INTO `tbl_sessions_info` VALUES ('7a49fc74c8192ae4297faa56b989320d', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2552.0 Safari/537.36', '1446972791', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-08 20:53:16');
INSERT INTO `tbl_sessions_info` VALUES ('8198348268b4e1b97f9ae8e88a8464a7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2552.0 Safari/537.36', '1446967751', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2015-11-08 19:29:11');
INSERT INTO `tbl_sessions_info` VALUES ('8c05f77f8e4d1cd1abd4259f1536bfb5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2552.0 Safari/537.36', '1446973181', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-08 20:59:47');
INSERT INTO `tbl_sessions_info` VALUES ('a298126a1210f40dd1369f6b50d09ce0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2547.0 Safari/537.36', '1446363245', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2015-11-01 19:34:11');
INSERT INTO `tbl_sessions_info` VALUES ('b650618167eb14479aee92f33a5cacae', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2552.0 Safari/537.36', '1446973255', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-08 21:01:00');
INSERT INTO `tbl_sessions_info` VALUES ('eb7764cc534eac2f7c5a83a9538b454b', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2547.0 Safari/537.36', '1446363172', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2015-11-01 19:33:01');
INSERT INTO `tbl_sessions_info` VALUES ('d108555d7f20dfc0befe40dbb9987bdc', '192.168.110.211', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2552.0 Safari/537.36', '1446983005', 'Login ID:230|Name:Saiful Islam Sweet|Time:2015-11-08 23:44:26');
INSERT INTO `tbl_sessions_info` VALUES ('47a61760930b608318caa56a6305aef6', '192.168.110.216', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/46.0.2490.80 Safari/537.36', '1446983761', 'Login ID:230|Name:Saiful Islam Sweet|Time:2015-11-08 23:56:14');
INSERT INTO `tbl_sessions_info` VALUES ('be49ffc6bf97bf4a2dc4fb56470bb554', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447313019', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-12 19:24:02');
INSERT INTO `tbl_sessions_info` VALUES ('8ff14bf2549233174df89bc73fe2abe6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447488522', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-14 20:09:23');
INSERT INTO `tbl_sessions_info` VALUES ('078a09ba582944f17879cc1afd85a2d9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447488840', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-14 20:14:05');
INSERT INTO `tbl_sessions_info` VALUES ('59d7641062b7ced80c55ab96c18e5cd6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447488902', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-14 20:15:07');
INSERT INTO `tbl_sessions_info` VALUES ('132745b10020861d884d1aba894b145f', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447489079', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-14 20:18:10');
INSERT INTO `tbl_sessions_info` VALUES ('97b628e771d87834430561810e26e97b', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447489102', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-14 20:18:27');
INSERT INTO `tbl_sessions_info` VALUES ('fdc0df5b276d1bad266c15c3079c5fe4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447578812', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-15 21:13:40');
INSERT INTO `tbl_sessions_info` VALUES ('3f034d883b9777936fd6703dda163727', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447657531', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-16 19:05:40');
INSERT INTO `tbl_sessions_info` VALUES ('664d1209a077a73390e50427f2c20788', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447666827', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-16 21:40:27');
INSERT INTO `tbl_sessions_info` VALUES ('e8448ccef4dc379f8cfe24276ae76fa1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447673199', 'Login ID:230|Name:Saiful Islam Sweet|Time:2015-11-16 23:26:46');
INSERT INTO `tbl_sessions_info` VALUES ('2a9a7359126093441f685ed5dd38e1e7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447757704', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-17 22:55:17');
INSERT INTO `tbl_sessions_info` VALUES ('30d30f292a750cfffe54b8ad41b1c425', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447758221', 'Login ID:230|Name:Saiful Islam Sweet|Time:2015-11-17 23:03:57');
INSERT INTO `tbl_sessions_info` VALUES ('8a8747418fe28c520a5cf6bc4be707c1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447758248', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-17 23:04:13');
INSERT INTO `tbl_sessions_info` VALUES ('8161df66cd68da5db9ed2011875967e4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447758330', 'Login ID:230|Name:Saiful Islam Sweet|Time:2015-11-17 23:05:39');
INSERT INTO `tbl_sessions_info` VALUES ('2854e6dfda1db4cf066113d976f8ddf6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447758437', 'Login ID:517|Name:Nasirul Akbar Khan|Time:2015-11-17 23:07:25');
INSERT INTO `tbl_sessions_info` VALUES ('bd12103109c6405b0998c760feda9ed0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447758486', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-17 23:08:14');
INSERT INTO `tbl_sessions_info` VALUES ('58d6e1cee2a7e9e1d14a8400713dcab6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447758544', 'Login ID:517|Name:Nasirul Akbar Khan|Time:2015-11-17 23:09:10');
INSERT INTO `tbl_sessions_info` VALUES ('e52a8d0a9c39bd3e7a707b699961ad3e', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447837471', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-18 21:04:31');
INSERT INTO `tbl_sessions_info` VALUES ('58dffd6173baeca2ec9ee13e42342cdc', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447844705', 'Login ID:230|Name:Saiful Islam Sweet|Time:2015-11-18 23:05:14');
INSERT INTO `tbl_sessions_info` VALUES ('b77e8de24d31467a5712731544d271ce', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447845521', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-18 23:18:50');
INSERT INTO `tbl_sessions_info` VALUES ('1fcedc4b63d5dc552efa84a18f267dc5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447846165', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-18 23:29:35');
INSERT INTO `tbl_sessions_info` VALUES ('6d38381e6ec5fe3f3bda65c7190fb283', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447846203', 'Login ID:517|Name:Nasirul Akbar Khan|Time:2015-11-18 23:30:12');
INSERT INTO `tbl_sessions_info` VALUES ('de0c2cc328059dbb60f912aa0b2b4eee', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/48.0.2560.0 Safari/537.36', '1447846220', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-18 23:30:27');
INSERT INTO `tbl_sessions_info` VALUES ('80169e48454f560a23d31dc971287ed5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:41.0) Gecko/20100101 Firefox/41.0', '1448430424', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-25 17:50:45');
INSERT INTO `tbl_sessions_info` VALUES ('7f5aa365e1d8653b506acbd262b8e488', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:41.0) Gecko/20100101 Firefox/41.0', '1448430772', 'Login ID:517|Name:Nasirul Akbar Khan|Time:2015-11-25 17:52:58');
INSERT INTO `tbl_sessions_info` VALUES ('81a16a9f496a64e67f043817c985a214', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:41.0) Gecko/20100101 Firefox/41.0', '1448431037', 'Login ID:230|Name:Saiful Islam Sweet|Time:2015-11-25 17:57:23');
INSERT INTO `tbl_sessions_info` VALUES ('e74761c9f6807f6837a450b213816ea1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:41.0) Gecko/20100101 Firefox/41.0', '1448431053', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-25 17:57:51');
INSERT INTO `tbl_sessions_info` VALUES ('6ef55e4ef1c91801aaa6cc8367784b13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:41.0) Gecko/20100101 Firefox/41.0', '1448431080', 'Login ID:517|Name:Nasirul Akbar Khan|Time:2015-11-25 17:58:07');
INSERT INTO `tbl_sessions_info` VALUES ('10c419e997575cdee7475267dfb99877', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:41.0) Gecko/20100101 Firefox/41.0', '1448431187', 'Login ID:230|Name:Saiful Islam Sweet|Time:2015-11-25 17:59:54');
INSERT INTO `tbl_sessions_info` VALUES ('59360db0e252ec503023fde7ee280d13', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:41.0) Gecko/20100101 Firefox/41.0', '1448433213', 'Login ID:517|Name:Nasirul Akbar Khan|Time:2015-11-25 18:33:56');
INSERT INTO `tbl_sessions_info` VALUES ('0579dfe20201f15226184f92fe4d4eff', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:41.0) Gecko/20100101 Firefox/41.0', '1448433308', 'Login ID:230|Name:Saiful Islam Sweet|Time:2015-11-25 18:35:14');
INSERT INTO `tbl_sessions_info` VALUES ('87737ad90e6f7ebccde3eeac1dacca31', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:41.0) Gecko/20100101 Firefox/41.0', '1448433322', 'Login ID:517|Name:Nasirul Akbar Khan|Time:2015-11-25 18:35:41');
INSERT INTO `tbl_sessions_info` VALUES ('de17aaff590346f1ecaa115a53d940c8', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:41.0) Gecko/20100101 Firefox/41.0', '1448446075', 'Login ID:230|Name:Saiful Islam Sweet|Time:2015-11-25 22:08:11');
INSERT INTO `tbl_sessions_info` VALUES ('601d903b63d4c5de81f222638f55ba71', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:42.0) Gecko/20100101 Firefox/42.0', '1448530078', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-26 21:27:59');
INSERT INTO `tbl_sessions_info` VALUES ('58d25e8e75b55b115bfd50a5801d5b60', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:42.0) Gecko/20100101 Firefox/42.0', '1448538069', 'Login ID:230|Name:Saiful Islam Sweet|Time:2015-11-26 23:41:19');
INSERT INTO `tbl_sessions_info` VALUES ('bad8031bd969da709eb903a83d430666', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:42.0) Gecko/20100101 Firefox/42.0', '1448538097', 'Login ID:517|Name:Nasirul Akbar Khan|Time:2015-11-26 23:41:52');
INSERT INTO `tbl_sessions_info` VALUES ('75ab10fc7fab48be2784b7322490dcb2', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:42.0) Gecko/20100101 Firefox/42.0', '1448538143', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-26 23:42:41');
INSERT INTO `tbl_sessions_info` VALUES ('e70e53c3ea6f86ba2515754b15059da3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:42.0) Gecko/20100101 Firefox/42.0', '1448875794', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-11-30 21:30:35');
INSERT INTO `tbl_sessions_info` VALUES ('02b42a49600de5590157ea4f1cd3ea22', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:42.0) Gecko/20100101 Firefox/42.0', '1448964815', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-12-01 22:13:47');
INSERT INTO `tbl_sessions_info` VALUES ('2c2a193718014120b551853dd9582eaa', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1; rv:42.0) Gecko/20100101 Firefox/42.0', '1448970622', 'Login ID:517|Name:Nasirul Akbar Khan|Time:2015-12-01 23:50:35');
INSERT INTO `tbl_sessions_info` VALUES ('fe630675dad0bb9050b570187d2c4351', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1451377640', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-12-29 20:27:50');
INSERT INTO `tbl_sessions_info` VALUES ('d6d6a87956f8d60c0209e6add85a1e37', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1451387582', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-12-29 23:14:29');
INSERT INTO `tbl_sessions_info` VALUES ('c028e79a89d97d47462a29d4151b834f', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1451387726', 'Login ID:230|Name:Saiful Islam Sweet|Time:2015-12-29 23:15:33');
INSERT INTO `tbl_sessions_info` VALUES ('cc1e16776f8f3ea30770e13ba458bd21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1451453679', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-12-30 17:34:41');
INSERT INTO `tbl_sessions_info` VALUES ('553cf158034622c22bf240e967129e58', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1451457135', 'Login ID:517|Name:Nasirul Akbar Khan|Time:2015-12-30 18:32:21');
INSERT INTO `tbl_sessions_info` VALUES ('2c71a3b26db1fdbe05da116757f71611', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1451457187', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-12-30 18:33:15');
INSERT INTO `tbl_sessions_info` VALUES ('e9ba450324ffb997565431b463b73dae', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1451466917', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-12-30 21:15:28');
INSERT INTO `tbl_sessions_info` VALUES ('adccbda1e9855c221c85d9fe148dcafb', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1451551960', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2015-12-31 20:52:54');
INSERT INTO `tbl_sessions_info` VALUES ('ae1ec7ea94055038f0ed783808955ab1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1451722306', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-01-02 20:12:13');
INSERT INTO `tbl_sessions_info` VALUES ('b8f92b5ac7f340e209befbb8270408f0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1451990027', 'Login ID:517|Name:Nasirul Akbar Khan|Time:2016-01-05 22:33:58');
INSERT INTO `tbl_sessions_info` VALUES ('4bd23644988febbf53183544778ec4ca', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1452070985', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-01-06 21:03:14');
INSERT INTO `tbl_sessions_info` VALUES ('b6d95a92be67aed712257c4d495560de', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1452071001', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-01-06 21:03:30');
INSERT INTO `tbl_sessions_info` VALUES ('7720e950103b81ec2e664f789034817f', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1452074772', 'Login ID:517|Name:Nasirul Akbar Khan|Time:2016-01-06 22:06:20');
INSERT INTO `tbl_sessions_info` VALUES ('e16f369973f3e27add2e0272ef30034b', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1452075343', 'Login ID:230|Name:Saiful Islam Sweet|Time:2016-01-06 22:15:49');
INSERT INTO `tbl_sessions_info` VALUES ('553d9d268b2c48fb3cb5f2ff02caed02', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1452075382', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-01-06 22:16:34');
INSERT INTO `tbl_sessions_info` VALUES ('3521ca226a5f89de84a4c8d9eaa1768d', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1452075577', 'Login ID:517|Name:Nasirul Akbar Khan|Time:2016-01-06 22:19:48');
INSERT INTO `tbl_sessions_info` VALUES ('b6198a1371628e3b0c717cedf19b02fc', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1452075671', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-01-06 22:21:21');
INSERT INTO `tbl_sessions_info` VALUES ('9ad42282496023869132370f3898a2c9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1452076182', 'Login ID:517|Name:Nasirul Akbar Khan|Time:2016-01-06 22:29:49');
INSERT INTO `tbl_sessions_info` VALUES ('80b25b2ec4af9ed09d3fb2b5db9b2759', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1452077206', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-01-06 22:46:47');
INSERT INTO `tbl_sessions_info` VALUES ('051152c8957121cb22a5dd9d90bec1c0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1452077457', 'Login ID:517|Name:Nasirul Akbar Khan|Time:2016-01-06 22:51:04');
INSERT INTO `tbl_sessions_info` VALUES ('ec9fc94f18cffb2c452beec2bd1b3610', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1452077811', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-01-06 22:56:56');
INSERT INTO `tbl_sessions_info` VALUES ('8e939b323d3f241e25a9238fdde8cf56', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1452077828', 'Login ID:517|Name:Nasirul Akbar Khan|Time:2016-01-06 22:57:17');
INSERT INTO `tbl_sessions_info` VALUES ('40a2f4b2877a6e80e693eb70447eb503', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1452077990', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-01-06 22:59:55');
INSERT INTO `tbl_sessions_info` VALUES ('185c6884df581d6af5bfea695bbb8dde', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1452078007', 'Login ID:230|Name:Saiful Islam Sweet|Time:2016-01-06 23:00:14');
INSERT INTO `tbl_sessions_info` VALUES ('22003689adac541abcfd683fb2837ff0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1452078074', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-01-06 23:01:21');
INSERT INTO `tbl_sessions_info` VALUES ('e3f5c1f870da9f48affbc26c2d5eec87', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1452078091', 'Login ID:517|Name:Nasirul Akbar Khan|Time:2016-01-06 23:01:38');
INSERT INTO `tbl_sessions_info` VALUES ('5078ebdd25f56c5a2a627895004663a5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1452078171', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-01-06 23:03:07');
INSERT INTO `tbl_sessions_info` VALUES ('4b329e146dbdbfd4319e4c76bf08067d', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2593.0 Safari/537.36', '1452078224', 'Login ID:230|Name:Saiful Islam Sweet|Time:2016-01-06 23:03:51');
INSERT INTO `tbl_sessions_info` VALUES ('4e1e18414cfe5f98650677cc0bb9f407', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2612.0 Safari/537.36', '1452151211', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-01-07 19:20:13');
INSERT INTO `tbl_sessions_info` VALUES ('01ab3bb0c340576a39fffabf06e851e5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2612.0 Safari/537.36', '1452156526', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-01-07 20:48:48');
INSERT INTO `tbl_sessions_info` VALUES ('d804aa150779ce34fb1af74538f6344d', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2612.0 Safari/537.36', '1452680062', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-01-13 22:15:04');
INSERT INTO `tbl_sessions_info` VALUES ('b7d3c9f4a1abc023decb49fe8791abf3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2618.8 Safari/537.36', '1452751137', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-01-14 17:59:04');
INSERT INTO `tbl_sessions_info` VALUES ('b5d787cd55c350b625e176a484fe82f3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/49.0.2618.8 Safari/537.36', '1453267989', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-01-20 17:34:07');
INSERT INTO `tbl_sessions_info` VALUES ('afbab4c84b4618b5c620ab6bfcbd433a', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:45.0) Gecko/20100101 Firefox/45.0', '1459405565', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-03-31 18:26:10');
INSERT INTO `tbl_sessions_info` VALUES ('8f549c6f2299bb5bb082909a05b7b820', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:45.0) Gecko/20100101 Firefox/45.0', '1461044865', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-04-19 17:48:07');
INSERT INTO `tbl_sessions_info` VALUES ('27d9a97d2f272c88c34753972969e0d3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0', '1465213461', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-06-06 23:44:33');
INSERT INTO `tbl_sessions_info` VALUES ('6d858007e9f74572f6d5c68f99f201cd', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0', '1465213484', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-06-06 23:44:46');
INSERT INTO `tbl_sessions_info` VALUES ('381012d1bf694b3c0e2d7cb30310e571', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0', '1465213658', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-06-06 23:49:11');
INSERT INTO `tbl_sessions_info` VALUES ('d942bfd91a854d56d0f67ebd88c8169b', '127.0.0.1', 'Mozilla/5.0 (Windows NT 6.3; WOW64; rv:46.0) Gecko/20100101 Firefox/46.0', '1465213756', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-06-06 23:49:19');
INSERT INTO `tbl_sessions_info` VALUES ('ca679f01afa4232ef76daa8c2ee3d0f7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:47.0) Gecko/20100101 Firefox/47.0', '1471240166', 'Login ID:216|Name:Nasirul Akbar Khan|Time:2016-08-15 17:49:29');
INSERT INTO `tbl_sessions_info` VALUES ('93975a3ac55c72a0e949eeb7e255bee9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:47.0) Gecko/20100101 Firefox/47.0', '1471254317', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2016-08-15 21:45:18');
INSERT INTO `tbl_sessions_info` VALUES ('b37d7e805c82b78c54ced37c38206c1f', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:47.0) Gecko/20100101 Firefox/47.0', '1471254943', 'Login ID:2|Name:Mostafizur Rahman|Time:2016-08-15 21:55:48');
INSERT INTO `tbl_sessions_info` VALUES ('8d3bf2715f18b711f69c6d69928c41d0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:47.0) Gecko/20100101 Firefox/47.0', '1471254960', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2016-08-15 21:56:03');
INSERT INTO `tbl_sessions_info` VALUES ('2f4c6af3199f0d0bcec23d76c4eb56ec', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:47.0) Gecko/20100101 Firefox/47.0', '1471254984', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2016-08-15 21:56:26');
INSERT INTO `tbl_sessions_info` VALUES ('d6cb01396d9243679a0c80daf26481c7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:47.0) Gecko/20100101 Firefox/47.0', '1471254987', 'Login ID:2|Name:Mostafizur Rahman|Time:2016-08-15 21:56:33');
INSERT INTO `tbl_sessions_info` VALUES ('19541d61266e850eadb979612249b2d9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1483516778', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-04 19:59:47');
INSERT INTO `tbl_sessions_info` VALUES ('6bc6a0ddcf6ddec9755fb09966920886', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1484722462', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-18 18:54:43');
INSERT INTO `tbl_sessions_info` VALUES ('b00e6e7141fd319e0dd8f079c270c1f4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1484723499', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-18 19:11:43');
INSERT INTO `tbl_sessions_info` VALUES ('56013d0a55dbe895947d9181a421caa7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1484727775', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-18 20:24:49');
INSERT INTO `tbl_sessions_info` VALUES ('799a597b215d9669dc9598cf4efef2a1', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1484728029', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-18 20:30:08');
INSERT INTO `tbl_sessions_info` VALUES ('7083e67b80ab67ab2d124604f7d8f397', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1484736676', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-18 22:51:20');
INSERT INTO `tbl_sessions_info` VALUES ('17d0359798723fb302dce8bf83104ab8', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1484736695', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-18 22:51:39');
INSERT INTO `tbl_sessions_info` VALUES ('dc029f050980b5de4f470a0e4ebdd156', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1484796355', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-19 15:26:02');
INSERT INTO `tbl_sessions_info` VALUES ('9783e64ae3ccb99a6af30638032db973', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1484816297', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-19 20:58:18');
INSERT INTO `tbl_sessions_info` VALUES ('39342d754af513d2bbc8a6f1ab7c768f', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1484971723', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-21 16:11:07');
INSERT INTO `tbl_sessions_info` VALUES ('daf5543a73a993473a70e232307ef861', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1484971876', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-21 16:11:20');
INSERT INTO `tbl_sessions_info` VALUES ('5ecda971fee9d23497ca8a821b4ec5fe', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1484981045', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-21 18:44:05');
INSERT INTO `tbl_sessions_info` VALUES ('1f6a9891aa95e386701e990cf20e5a51', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1484995036', 'Login ID:2|Name:Mostafizur Rahman|Time:2017-01-21 22:37:22');
INSERT INTO `tbl_sessions_info` VALUES ('cdf163f35011e3041e1a4efe7307db91', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1484995047', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-21 22:37:30');
INSERT INTO `tbl_sessions_info` VALUES ('f97fbed77f51b79eb755bb3c3c154aea', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1484995065', 'Login ID:2|Name:Mostafizur Rahman|Time:2017-01-21 22:37:55');
INSERT INTO `tbl_sessions_info` VALUES ('eb36bc73959f9427d765ed6c269c9712', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1484995229', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-21 22:40:35');
INSERT INTO `tbl_sessions_info` VALUES ('74d42a287fc0ae28cf7dc80480c986a0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1484995241', 'Login ID:2|Name:Mostafizur Rahman|Time:2017-01-21 22:40:48');
INSERT INTO `tbl_sessions_info` VALUES ('18ceec29bc9767230ba1fe512ff41d02', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485058364', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-22 16:12:53');
INSERT INTO `tbl_sessions_info` VALUES ('b879d6001c19db4412b6c2847ed8de21', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485064398', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-22 17:53:43');
INSERT INTO `tbl_sessions_info` VALUES ('93b81554e34011593af8fa2ee3fcab0f', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485068793', 'Login ID:3|Name:Nasirul Akbar Khan|Time:2017-01-22 19:06:39');
INSERT INTO `tbl_sessions_info` VALUES ('dc47e08c4ccd5d4236a0601a196b8257', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485068809', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-22 19:06:54');
INSERT INTO `tbl_sessions_info` VALUES ('35c363ebae6368b2a0f123c1f884d593', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485068838', 'Login ID:3|Name:Nasirul Akbar Khan|Time:2017-01-22 19:07:27');
INSERT INTO `tbl_sessions_info` VALUES ('0946e7169aa92c24f98a50398aa5db7e', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485068868', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-22 19:08:21');
INSERT INTO `tbl_sessions_info` VALUES ('198f368e1501b9b1ece88d430beaae39', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485069206', 'Login ID:3|Name:Nasirul Akbar Khan|Time:2017-01-22 19:13:34');
INSERT INTO `tbl_sessions_info` VALUES ('11b08666482e29a27b2edbdbc3b18a93', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485069237', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-22 19:14:01');
INSERT INTO `tbl_sessions_info` VALUES ('ad871c2796f2be64a76418947a1f890e', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485069256', 'Login ID:3|Name:Nasirul Akbar Khan|Time:2017-01-22 19:14:25');
INSERT INTO `tbl_sessions_info` VALUES ('8120520136f39f62d946a5d7a5ede725', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485069279', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-22 19:15:11');
INSERT INTO `tbl_sessions_info` VALUES ('cf2df93e261bd6af7aa0ff2d075e065f', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485069574', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-22 19:20:29');
INSERT INTO `tbl_sessions_info` VALUES ('0e3b6fea1bb7de83cd997f5fc04f6627', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36', '1485071501', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-22 19:51:45');
INSERT INTO `tbl_sessions_info` VALUES ('877d595209355ab7f72e51eac0f3a0fe', '127.0.0.1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Mobile', '1485073418', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-22 20:23:43');
INSERT INTO `tbl_sessions_info` VALUES ('6ba739679eed32c09c5d93a4c28d2eb1', '127.0.0.1', 'Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Mobile', '1485074714', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-22 20:45:27');
INSERT INTO `tbl_sessions_info` VALUES ('0aab6a069dd9d6f095fd1258b296e4d5', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485145748', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-23 16:29:08');
INSERT INTO `tbl_sessions_info` VALUES ('9f18a451c1927a8544c1ccdc1e554a25', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485315505', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-25 15:38:32');
INSERT INTO `tbl_sessions_info` VALUES ('d77f52828f4ee985b2e50687e1db0d25', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36', '1485317013', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-25 16:03:38');
INSERT INTO `tbl_sessions_info` VALUES ('f00b9d5b32e509be5f96ad3a77dd1599', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485405249', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-26 16:34:14');
INSERT INTO `tbl_sessions_info` VALUES ('e3203285c8dcacb1f2195b38be816061', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485419685', 'Login ID:2|Name:Mostafizur Rahman|Time:2017-01-26 20:34:54');
INSERT INTO `tbl_sessions_info` VALUES ('fcd0caab3d583e9b6411ef81fbb827ed', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485419710', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-26 20:35:17');
INSERT INTO `tbl_sessions_info` VALUES ('7a13f654cebb33a63638b9b60bc3a931', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485420441', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-26 20:47:45');
INSERT INTO `tbl_sessions_info` VALUES ('b0acd34756a426b3f7a18d6afe7e233e', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485420483', 'Login ID:3|Name:Nasirul Akbar Khan|Time:2017-01-26 20:48:13');
INSERT INTO `tbl_sessions_info` VALUES ('bb8af95b78dba85dc64ffdfb7cdf4af3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485420569', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-26 20:49:40');
INSERT INTO `tbl_sessions_info` VALUES ('fe08e5483541b931ff2fe7d07171afe9', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485420609', 'Login ID:2|Name:Mostafizur Rahman|Time:2017-01-26 20:50:30');
INSERT INTO `tbl_sessions_info` VALUES ('ea2afce694dace050ede51f55a96d2a4', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485420663', 'Login ID:2|Name:Mostafizur Rahman|Time:2017-01-26 20:51:08');
INSERT INTO `tbl_sessions_info` VALUES ('b11086b673bb6247e749cee5fb851e6b', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485420701', 'Login ID:3|Name:Nasirul Akbar Khan|Time:2017-01-26 20:51:51');
INSERT INTO `tbl_sessions_info` VALUES ('92003d921807e1e8003d5092bb49ca3e', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485420732', 'Login ID:2|Name:Mostafizur Rahman|Time:2017-01-26 20:52:19');
INSERT INTO `tbl_sessions_info` VALUES ('2c26ddd644ae2f789a1c289742b6e1ff', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485421082', 'Login ID:3|Name:Nasirul Akbar Khan|Time:2017-01-26 20:58:10');
INSERT INTO `tbl_sessions_info` VALUES ('1f4b4d30b5b12aec5d39407374c8e927', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485421744', 'Login ID:2|Name:Mostafizur Rahman|Time:2017-01-26 21:09:16');
INSERT INTO `tbl_sessions_info` VALUES ('4e423a6306929960425a0fbc31bffeaf', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485421895', 'Login ID:3|Name:Nasirul Akbar Khan|Time:2017-01-26 21:11:41');
INSERT INTO `tbl_sessions_info` VALUES ('0067ace7cd92f322c1e1cf51ee487a1e', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485422028', 'Login ID:2|Name:Mostafizur Rahman|Time:2017-01-26 21:13:57');
INSERT INTO `tbl_sessions_info` VALUES ('8d69d295aa66f7f7eedb8cf131093cfe', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485425430', 'Login ID:2|Name:Mostafizur Rahman|Time:2017-01-26 22:12:02');
INSERT INTO `tbl_sessions_info` VALUES ('92477ba084f2db203b75400e45c75be0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485428906', 'Login ID:3|Name:Nasirul Akbar Khan|Time:2017-01-26 23:08:32');
INSERT INTO `tbl_sessions_info` VALUES ('5a276abede74270b4d673063f84331a0', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485428919', 'Login ID:2|Name:Mostafizur Rahman|Time:2017-01-26 23:08:44');
INSERT INTO `tbl_sessions_info` VALUES ('85a16ebe35aedebe2c365c65426dbc88', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485659493', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-29 15:16:06');
INSERT INTO `tbl_sessions_info` VALUES ('5fbec314533de11351494e1e4bbfc645', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36', '1485680604', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-01-29 21:03:33');
INSERT INTO `tbl_sessions_info` VALUES ('5ca9a148d742cd5952fa66aec5e54a6d', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485681345', 'Login ID:2|Name:Mostafizur Rahman|Time:2017-01-29 21:16:21');
INSERT INTO `tbl_sessions_info` VALUES ('99953e1761dad1016f3fb3eac6cde9d6', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485681650', 'Login ID:3|Name:Nasirul Akbar Khan|Time:2017-01-29 21:21:41');
INSERT INTO `tbl_sessions_info` VALUES ('1d30c0b10586665a72cb6bd965e3574e', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485681821', 'Login ID:2|Name:Mostafizur Rahman|Time:2017-01-29 21:23:47');
INSERT INTO `tbl_sessions_info` VALUES ('ead7846007d52c4299b2334569167af7', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/55.0.2883.87 Safari/537.36', '1485683330', 'Login ID:3|Name:Nasirul Akbar Khan|Time:2017-01-29 21:49:04');
INSERT INTO `tbl_sessions_info` VALUES ('2b54460b09583bdc94cc8df41bc3d484', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485683171', 'Login ID:2|Name:Mostafizur Rahman|Time:2017-01-29 21:49:15');
INSERT INTO `tbl_sessions_info` VALUES ('407ae3b642a3ca48311da0c73209b651', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:50.0) Gecko/20100101 Firefox/50.0', '1485683798', 'Login ID:2|Name:Mostafizur Rahman|Time:2017-01-29 21:56:38');
INSERT INTO `tbl_sessions_info` VALUES ('8c9268719ae8d082a69f8b9cb7b2da42', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:51.0) Gecko/20100101 Firefox/51.0', '1486197732', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-02-04 20:42:21');
INSERT INTO `tbl_sessions_info` VALUES ('d238ad8bae98aaba7e9433499745698b', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:51.0) Gecko/20100101 Firefox/51.0', '1486264376', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-02-05 15:13:06');
INSERT INTO `tbl_sessions_info` VALUES ('7ab4fedf0c9671a971469450c39f4d2a', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:51.0) Gecko/20100101 Firefox/51.0', '1486264405', 'Login ID:3|Name:Nasirul Akbar Khan|Time:2017-02-05 15:13:32');
INSERT INTO `tbl_sessions_info` VALUES ('9a70dae8e0f735f09a447fb7a9d5fb52', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:51.0) Gecko/20100101 Firefox/51.0', '1486264430', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-02-05 15:13:54');
INSERT INTO `tbl_sessions_info` VALUES ('a29bb193366e254529afb2924d6b02a3', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:51.0) Gecko/20100101 Firefox/51.0', '1486354523', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-02-06 16:15:28');
INSERT INTO `tbl_sessions_info` VALUES ('24c0417ad9a9c477111580155b4ecdf8', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:51.0) Gecko/20100101 Firefox/51.0', '1486533124', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-02-08 17:52:08');
INSERT INTO `tbl_sessions_info` VALUES ('6ad68b904e0e3307cef446bec2e40619', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:51.0) Gecko/20100101 Firefox/51.0', '1486535049', 'Login ID:3|Name:Nasirul Akbar Khan|Time:2017-02-08 18:24:17');
INSERT INTO `tbl_sessions_info` VALUES ('39776c97ebdab9a8b40d960b326d3669', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:51.0) Gecko/20100101 Firefox/51.0', '1486535570', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-02-08 18:32:50');
INSERT INTO `tbl_sessions_info` VALUES ('3f5184c2e6107a8a3571e06f15f82591', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:51.0) Gecko/20100101 Firefox/51.0', '1486536422', 'Login ID:2|Name:Mostafizur Rahman|Time:2017-02-08 18:48:39');
INSERT INTO `tbl_sessions_info` VALUES ('547ab37335a884545ba096a031b67864', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:51.0) Gecko/20100101 Firefox/51.0', '1486536900', 'Login ID:3|Name:Nasirul Akbar Khan|Time:2017-02-08 18:55:07');
INSERT INTO `tbl_sessions_info` VALUES ('a78bb5db8318e729eeb6f61eab4a123a', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:51.0) Gecko/20100101 Firefox/51.0', '1486536937', 'Login ID:2|Name:Mostafizur Rahman|Time:2017-02-08 18:55:47');
INSERT INTO `tbl_sessions_info` VALUES ('7bd4c1a0cd21c7c68f61a8a109fcaf2c', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:51.0) Gecko/20100101 Firefox/51.0', '1486538804', 'Login ID:3|Name:Nasirul Akbar Khan|Time:2017-02-08 19:26:57');
INSERT INTO `tbl_sessions_info` VALUES ('497ac81e913e6168e184a7e7bbd3746a', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:51.0) Gecko/20100101 Firefox/51.0', '1486539725', 'Login ID:2|Name:Mostafizur Rahman|Time:2017-02-08 19:42:11');
INSERT INTO `tbl_sessions_info` VALUES ('6d5bdd2b146ba12c6866b1daad223c1d', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; WOW64; rv:51.0) Gecko/20100101 Firefox/51.0', '1486541073', 'Login ID:1|Name:Nasirul Akbar Khan|Time:2017-02-08 20:04:33');

-- ----------------------------
-- Table structure for `tbl_user_info`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_user_info`;
CREATE TABLE `tbl_user_info` (
  `F_Company_ID` tinyint(9) NOT NULL,
  `F_Dept_ID` mediumint(9) NOT NULL,
  `F_User_Permission_ID` mediumint(9) NOT NULL,
  `User_ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Full_Name` varchar(80) NOT NULL,
  `Address` text NOT NULL,
  `Email` varchar(200) NOT NULL,
  `Join_Date` date NOT NULL,
  `Is_Void` enum('1','0') NOT NULL DEFAULT '0',
  `Is_Processed` enum('1','0') NOT NULL DEFAULT '0',
  `Is_Exist` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`User_ID`),
  KEY `U_F_Company_ID` (`F_Company_ID`) USING BTREE,
  KEY `U_F_Dept_ID` (`F_Dept_ID`) USING BTREE,
  KEY `U_F_User_Permission_ID` (`F_User_Permission_ID`) USING BTREE,
  KEY `User_ID` (`User_ID`) USING BTREE
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_user_info
-- ----------------------------
INSERT INTO `tbl_user_info` VALUES ('1', '1', '1', '1', 'Nasirul Akbar Khan', '281/2 Ibrahimpur, Kafrul', 'nasir@ryansplus.com', '2015-11-08', '0', '0', '1');
INSERT INTO `tbl_user_info` VALUES ('1', '3', '4', '2', 'Mostafizur Rahman', 'Dhaka', 'mostafizur@ryansplus.com', '2016-08-15', '0', '0', '1');
INSERT INTO `tbl_user_info` VALUES ('1', '1', '5', '3', 'Nasirul Akbar Khan', '', 'nasir@ryansplus.com', '2017-01-22', '0', '0', '1');
INSERT INTO `tbl_user_info` VALUES ('1', '1', '5', '4', 'Rezwanul Haq', 'Uttara Dhaka', 'shiplu@ryansplus.com', '2013-10-01', '0', '0', '1');

-- ----------------------------
-- Table structure for `tbl_user_type`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_user_type`;
CREATE TABLE `tbl_user_type` (
  `User_Permission_ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `User_Permission_Type` varchar(100) NOT NULL,
  `User_Manager` enum('1','0') NOT NULL DEFAULT '0',
  `Holiday` enum('1','0') NOT NULL DEFAULT '0',
  `Company` enum('1','0') NOT NULL DEFAULT '0',
  `Department` enum('1','0') NOT NULL DEFAULT '0',
  `Report` enum('1','0') NOT NULL DEFAULT '0',
  `All_Report` enum('1','0') NOT NULL DEFAULT '0',
  `Leave_Preapproval` enum('1','0') NOT NULL DEFAULT '0',
  `Leave_Manager` enum('1','0') NOT NULL DEFAULT '0',
  `Hierarchy` enum('0','1') NOT NULL DEFAULT '0',
  `Recruitment_Manager` enum('1','0') NOT NULL DEFAULT '0',
  `Recruitment_Preapproval` enum('1','0') NOT NULL DEFAULT '0',
  `Is_Void` enum('1','0') NOT NULL DEFAULT '0',
  `Is_Processed` enum('1','0') NOT NULL DEFAULT '0',
  `Is_Exist` enum('1','0') NOT NULL DEFAULT '1',
  PRIMARY KEY (`User_Permission_ID`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_user_type
-- ----------------------------
INSERT INTO `tbl_user_type` VALUES ('1', 'Super Administrator', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '0', '1');
INSERT INTO `tbl_user_type` VALUES ('2', 'Administrator', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '0', '1');
INSERT INTO `tbl_user_type` VALUES ('3', 'Admin Operator', '0', '0', '0', '0', '1', '1', '1', '1', '0', '1', '1', '0', '0', '1');
INSERT INTO `tbl_user_type` VALUES ('4', 'Manager', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '1', '0', '0', '1');
INSERT INTO `tbl_user_type` VALUES ('5', 'User', '0', '0', '0', '0', '1', '0', '0', '0', '0', '0', '0', '0', '0', '1');

-- ----------------------------
-- Table structure for `tbl_work_days`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_work_days`;
CREATE TABLE `tbl_work_days` (
  `F_User_ID` mediumint(9) NOT NULL,
  `Sun` enum('1','0') NOT NULL DEFAULT '0',
  `Mon` enum('1','0') NOT NULL DEFAULT '0',
  `Tue` enum('1','0') NOT NULL DEFAULT '0',
  `Wed` enum('1','0') NOT NULL DEFAULT '0',
  `Thu` enum('1','0') NOT NULL DEFAULT '0',
  `Fri` enum('1','0') NOT NULL DEFAULT '0',
  `Sat` enum('1','0') NOT NULL DEFAULT '0',
  PRIMARY KEY (`F_User_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_work_days
-- ----------------------------
INSERT INTO `tbl_work_days` VALUES ('1', '1', '1', '1', '1', '1', '0', '1');
INSERT INTO `tbl_work_days` VALUES ('2', '1', '1', '1', '1', '1', '0', '1');
INSERT INTO `tbl_work_days` VALUES ('3', '1', '1', '1', '1', '1', '0', '1');
INSERT INTO `tbl_work_days` VALUES ('4', '1', '1', '1', '1', '1', '0', '1');
