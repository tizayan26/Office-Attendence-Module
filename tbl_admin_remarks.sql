/*
Navicat MySQL Data Transfer

Source Server         : Local Sever
Source Server Version : 50711
Source Host           : localhost:3306
Source Database       : attendence_db

Target Server Type    : MYSQL
Target Server Version : 50711
File Encoding         : 65001

Date: 2017-03-08 14:57:27
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `tbl_admin_remarks`
-- ----------------------------
DROP TABLE IF EXISTS `tbl_admin_remarks`;
CREATE TABLE `tbl_admin_remarks` (
  `F_User_ID` mediumint(9) NOT NULL,
  `ID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `Date` date NOT NULL,
  `Remarks` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of tbl_admin_remarks
-- ----------------------------
