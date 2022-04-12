
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
-- Table structure for wb_comment
-- ----------------------------
DROP TABLE IF EXISTS `wb_comment`;
CREATE TABLE `wb_comment` (
  `cid` bigint(20) NOT NULL AUTO_INCREMENT,
  `fromuid` bigint(20) NOT NULL,
  `msg` text NOT NULL,
  `msg_id` bigint(20) NOT NULL,
  `touid` mediumint(9) DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for wb_fans
-- ----------------------------
DROP TABLE IF EXISTS `wb_fans`;
CREATE TABLE `wb_fans` (
  `fansid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fromuid` bigint(20) NOT NULL,
  `touid` bigint(20) NOT NULL,
  `mutual_concern` tinyint(4) NOT NULL DEFAULT '0',
  `ctime` bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`fansid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for wb_file
-- ----------------------------
DROP TABLE IF EXISTS `wb_file`;
CREATE TABLE `wb_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `userid` int(10) unsigned NOT NULL COMMENT '用户id',
  `type` tinyint(255) unsigned NOT NULL DEFAULT '0' COMMENT '0File, 1S3File',
  `file_name` varchar(255) NOT NULL COMMENT '文件名',
  `file_type` varchar(255) NOT NULL,
  `file_size` double NOT NULL COMMENT '文件大小',
  `file_location` varchar(255) NOT NULL COMMENT '文件地址',
  `file_path` varchar(255) NOT NULL COMMENT '文件地址',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `is_delete` tinyint(255) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `share_msg_id` bigint(255) unsigned NOT NULL DEFAULT '0' COMMENT '是否站内分享，0未分享',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for wb_message
-- ----------------------------
DROP TABLE IF EXISTS `wb_message`;
CREATE TABLE `wb_message` (
  `msg_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(9) NOT NULL,
  `contents` text NOT NULL,
  `repost` mediumtext,
  `refrom` varchar(50) NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `image_info` text,
  `repostsum` int(11) NOT NULL DEFAULT '0',
  `commentsum` int(11) NOT NULL DEFAULT '0',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`msg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

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
-- Table structure for wb_user
-- ----------------------------
DROP TABLE IF EXISTS `wb_user`;
CREATE TABLE `wb_user` (
  `uid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` char(11) NOT NULL,
  `username` char(10) DEFAULT NULL,
  `blog` char(20) DEFAULT NULL,
  `phone` bigint(20) DEFAULT NULL COMMENT '手机号',
  `password` char(32) NOT NULL,
  `head_image` varchar(255) DEFAULT NULL,
  `head_image_info` varchar(255) DEFAULT NULL,
  `sex` tinyint(4) NOT NULL DEFAULT '0',
  `province` char(10) DEFAULT NULL,
  `city` char(25) DEFAULT NULL,
  `email` char(32) DEFAULT NULL,
  `intro` varchar(210) DEFAULT NULL,
  `ctime` bigint(20) NOT NULL,
  `message_sum` mediumint(9) NOT NULL DEFAULT '0',
  `theme` varchar(255) DEFAULT NULL,
  `fansnum` bigint(20) NOT NULL DEFAULT '0' COMMENT '粉丝数',
  `follownum` bigint(20) NOT NULL DEFAULT '0' COMMENT '关注数',
  `invisible` tinyint(255) unsigned NOT NULL DEFAULT '0' COMMENT '广场隐身1，默认0不隐身',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
