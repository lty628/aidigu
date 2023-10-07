/*
 Navicat Premium Data Transfer

 Source Server         : 127.0.0.1
 Source Server Type    : MySQL
 Source Server Version : 80100
 Source Host           : localhost:3306
 Source Schema         : aidigu_cn

 Target Server Type    : MySQL
 Target Server Version : 80100
 File Encoding         : 65001

 Date: 07/10/2023 12:44:25
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for wb_badword
-- ----------------------------
DROP TABLE IF EXISTS `wb_badword`;
CREATE TABLE `wb_badword` (
  `bid` int NOT NULL AUTO_INCREMENT,
  `word` varchar(50) NOT NULL,
  `ctype` tinyint NOT NULL DEFAULT '0',
  PRIMARY KEY (`bid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Table structure for wb_comment
-- ----------------------------
DROP TABLE IF EXISTS `wb_comment`;
CREATE TABLE `wb_comment` (
  `cid` bigint NOT NULL AUTO_INCREMENT,
  `fromuid` bigint NOT NULL,
  `msg` text NOT NULL,
  `msg_id` bigint NOT NULL,
  `touid` mediumint DEFAULT NULL,
  `ctime` int NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Table structure for wb_fans
-- ----------------------------
DROP TABLE IF EXISTS `wb_fans`;
CREATE TABLE `wb_fans` (
  `fansid` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fromuid` bigint NOT NULL,
  `touid` bigint NOT NULL,
  `mutual_concern` tinyint NOT NULL DEFAULT '0',
  `ctime` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`fansid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Table structure for wb_file
-- ----------------------------
DROP TABLE IF EXISTS `wb_file`;
CREATE TABLE `wb_file` (
  `id` int unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `userid` int unsigned NOT NULL COMMENT '用户id',
  `type` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '0File, 1S3File',
  `file_name` varchar(255) NOT NULL COMMENT '文件名',
  `file_type` varchar(255) NOT NULL,
  `file_size` double NOT NULL COMMENT '文件大小',
  `file_location` varchar(255) NOT NULL COMMENT '文件地址',
  `file_path` varchar(255) NOT NULL COMMENT '文件地址',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `is_delete` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `share_msg_id` bigint unsigned NOT NULL DEFAULT '0' COMMENT '是否站内分享0未分享',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Table structure for wb_message
-- ----------------------------
DROP TABLE IF EXISTS `wb_message`;
CREATE TABLE `wb_message` (
  `msg_id` mediumint NOT NULL AUTO_INCREMENT,
  `uid` mediumint NOT NULL,
  `contents` text NOT NULL,
  `repost` mediumtext,
  `refrom` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image_info` text,
  `repostsum` int NOT NULL DEFAULT '0',
  `commentsum` int NOT NULL DEFAULT '0',
  `topic_id` int unsigned NOT NULL DEFAULT '0' COMMENT '话题id',
  `is_delete` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '是否删除0未删除，1已删除',
  `ctime` int NOT NULL,
  PRIMARY KEY (`msg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Table structure for wb_reminder
-- ----------------------------
DROP TABLE IF EXISTS `wb_reminder`;
CREATE TABLE `wb_reminder` (
  `id` bigint NOT NULL AUTO_INCREMENT,
  `msg_id` bigint DEFAULT NULL,
  `fromuid` bigint NOT NULL,
  `touid` bigint NOT NULL,
  `type` tinyint NOT NULL,
  `ctime` bigint NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ----------------------------
-- Table structure for wb_topic
-- ----------------------------
DROP TABLE IF EXISTS `wb_topic`;
CREATE TABLE `wb_topic` (
  `topic_id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '标题',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `count` int unsigned NOT NULL DEFAULT '0' COMMENT '话题数量',
  PRIMARY KEY (`topic_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Table structure for wb_user
-- ----------------------------
DROP TABLE IF EXISTS `wb_user`;
CREATE TABLE `wb_user` (
  `uid` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nickname` char(11) NOT NULL,
  `username` char(10) DEFAULT NULL,
  `blog` char(20) DEFAULT NULL,
  `phone` bigint DEFAULT NULL COMMENT '手机号',
  `password` char(32) NOT NULL,
  `head_image` varchar(255) DEFAULT NULL,
  `head_image_info` varchar(255) DEFAULT NULL,
  `sex` tinyint NOT NULL DEFAULT '0',
  `province` char(10) DEFAULT NULL,
  `city` char(25) DEFAULT NULL,
  `email` char(32) DEFAULT NULL,
  `intro` varchar(210) DEFAULT NULL,
  `ctime` bigint NOT NULL,
  `message_sum` mediumint NOT NULL DEFAULT '0',
  `theme` varchar(255) DEFAULT NULL,
  `fansnum` bigint NOT NULL DEFAULT '0' COMMENT '粉丝数',
  `follownum` bigint NOT NULL DEFAULT '0' COMMENT '关注数',
  `invisible` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '广场隐身1，默认0不隐身',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

SET FOREIGN_KEY_CHECKS = 1;
