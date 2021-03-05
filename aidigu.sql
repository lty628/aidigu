/*
Navicat MySQL Data Transfer

Source Server         : 47.75.11.220
Source Server Version : 50648
Source Host           : localhost:3306
Source Database       : aidigu

Target Server Type    : MYSQL
Target Server Version : 50648
File Encoding         : 65001

Date: 2021-03-05 15:35:33
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for wb_badword
-- ----------------------------
DROP TABLE IF EXISTS `wb_badword`;
CREATE TABLE `wb_badword` (
  `bid` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(50) NOT NULL,
  `ctype` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of wb_badword
-- ----------------------------

-- ----------------------------
-- Table structure for wb_comment
-- ----------------------------
DROP TABLE IF EXISTS `wb_comment`;
CREATE TABLE `wb_comment` (
  `cid` bigint(20) NOT NULL AUTO_INCREMENT,
  `fromuid` mediumint(9) NOT NULL,
  `msg` varchar(200) NOT NULL,
  `msg_id` bigint(20) NOT NULL,
  `touid` mediumint(9) DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of wb_comment
-- ----------------------------

-- ----------------------------
-- Table structure for wb_fans
-- ----------------------------
DROP TABLE IF EXISTS `wb_fans`;
CREATE TABLE `wb_fans` (
  `fansid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fromuid` mediumint(9) NOT NULL,
  `touid` mediumint(9) NOT NULL,
  `mutual_concern` tinyint(4) NOT NULL DEFAULT '0',
  `ctime` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fansid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of wb_fans
-- ----------------------------

-- ----------------------------
-- Table structure for wb_message
-- ----------------------------
DROP TABLE IF EXISTS `wb_message`;
CREATE TABLE `wb_message` (
  `msg_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(9) NOT NULL,
  `contents` varchar(255) NOT NULL,
  `repost` mediumtext,
  `refrom` varchar(50) NOT NULL,
  `repostsum` int(11) NOT NULL DEFAULT '0',
  `commentsum` int(11) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`msg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of wb_message
-- ----------------------------

-- ----------------------------
-- Table structure for wb_reminder
-- ----------------------------
DROP TABLE IF EXISTS `wb_reminder`;
CREATE TABLE `wb_reminder` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `msg_id` bigint(20) DEFAULT NULL,
  `fromuid` bigint(20) NOT NULL,
  `touid` bigint(20) NOT NULL,
  `type` tinyint(11) NOT NULL,
  `ctime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of wb_reminder
-- ----------------------------

-- ----------------------------
-- Table structure for wb_user
-- ----------------------------
DROP TABLE IF EXISTS `wb_user`;
CREATE TABLE `wb_user` (
  `uid` mediumint(9) unsigned NOT NULL AUTO_INCREMENT,
  `username` char(10) DEFAULT NULL,
  `phone` bigint(20) DEFAULT NULL COMMENT '手机号',
  `password` char(32) NOT NULL,
  `head_image` varchar(255) DEFAULT NULL,
  `head_image_info` varchar(255) DEFAULT NULL,
  `nickname` char(10) NOT NULL,
  `sex` tinyint(4) NOT NULL DEFAULT '0',
  `province` char(10) DEFAULT NULL,
  `city` char(25) DEFAULT NULL,
  `blog` char(10) DEFAULT NULL,
  `email` char(32) DEFAULT NULL,
  `intro` varchar(210) DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  `message_sum` mediumint(9) NOT NULL DEFAULT '0',
  `themes` varchar(15) NOT NULL DEFAULT 'default',
  `fansnum` bigint(20) NOT NULL DEFAULT '0' COMMENT '粉丝数',
  `follownum` bigint(20) NOT NULL DEFAULT '0' COMMENT '关注数',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Records of wb_user
-- ----------------------------
