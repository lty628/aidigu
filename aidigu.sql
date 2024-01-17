
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for wb_app
-- ----------------------------
DROP TABLE IF EXISTS `wb_app`;
CREATE TABLE `wb_app` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `app_name` varchar(255) CHARACTER SET utf8mb4 NOT NULL COMMENT '应用名称',
  `app_url` varchar(255) CHARACTER SET utf8mb4 NOT NULL COMMENT '链接',
  `app_status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '0关闭，1开启（默认）',
  `app_type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0全部，1pc,2手机',
  `app_image` varchar(255) CHARACTER SET utf8mb4 NOT NULL COMMENT '图片地址',
  `remind_key` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '提醒key',
  `app_config` text CHARACTER SET utf8mb4 NOT NULL COMMENT '配置',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Records of wb_app
-- ----------------------------
BEGIN;
INSERT INTO `wb_app` VALUES (1, '我的云盘', '/cloud/show/', 1, 0, '/static/tools/common/images/cloud.jpg', '', '{\"title\":\"我的云盘\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"80%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_1\",\"hideOnClose\":false,\"scrollbar\":false}', '2024-01-16 11:31:53', '2024-01-17 19:39:40');
INSERT INTO `wb_app` VALUES (2, '嘀友聊天', '/chat', 1, 0, '/static/tools/common/images/chat.jpg', 'chat', '{\"title\":\"嘀友聊天\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"60%\",\"70%\"],\"resize\":true,\"maxmin\":true,\"id\":\"app_2\",\"hideOnClose\":true,\"scrollbar\":false}', '2024-01-16 11:31:53', '2024-01-17 19:43:36');
INSERT INTO `wb_app` VALUES (3, '嘀咕影院', '/tools/movie', 1, 0, '/static/tools/common/images/movie.jpg', '', '{\"title\":\"嘀咕影院\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"80%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_3\",\"hideOnClose\":false,\"scrollbar\":false}', '2024-01-16 11:31:53', '2024-01-17 19:41:07');
INSERT INTO `wb_app` VALUES (4, '开车啦', '/tools/onlinecar', 1, 0, '/static/tools/common/images/onlinecar.jpg', '', '{\"title\":\"开车啦\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"80%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_4\",\"hideOnClose\":false,\"scrollbar\":false}', '2024-01-16 11:31:53', '2024-01-17 19:41:20');
INSERT INTO `wb_app` VALUES (5, 'BMI体重计算', '/tools/bmi', 1, 0, '/static/tools/common/images/bmi.jpg', '', '{\"title\":\"BMI体重计算\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"80%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_5\",\"hideOnClose\":false,\"scrollbar\":false}', '2024-01-16 11:31:53', '2024-01-17 19:41:33');
COMMIT;

-- ----------------------------
-- Table structure for wb_badword
-- ----------------------------
DROP TABLE IF EXISTS `wb_badword`;
CREATE TABLE `wb_badword` (
  `bid` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  `ctype` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`bid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Records of wb_badword
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for wb_chat_friends
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_friends`;
CREATE TABLE `wb_chat_friends` (
  `fansid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fromuid` bigint(20) NOT NULL,
  `touid` bigint(20) NOT NULL,
  `mutual_concern` tinyint(4) NOT NULL DEFAULT '0',
  `message_count` int(10) unsigned NOT NULL DEFAULT '0',
  `ctime` bigint(20) NOT NULL DEFAULT '0',
  `utime` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fansid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Records of wb_chat_friends
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for wb_chat_friends_history
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_friends_history`;
CREATE TABLE `wb_chat_friends_history` (
  `chat_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `fromuid` int(11) NOT NULL COMMENT '来源id',
  `touid` int(11) NOT NULL COMMENT '目标id',
  `content` text COLLATE utf8mb4_bin NOT NULL COMMENT '内容',
  `content_type` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '内容类型',
  `send_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '发送状态',
  `create_time` datetime NOT NULL COMMENT '消息时间',
  PRIMARY KEY (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Records of wb_chat_friends_history
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for wb_chat_group
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_group`;
CREATE TABLE `wb_chat_group` (
  `groupid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `groupname` char(11) COLLATE utf8mb4_bin DEFAULT '',
  `head_image` varchar(255) COLLATE utf8mb4_bin DEFAULT '',
  `head_image_info` varchar(255) COLLATE utf8mb4_bin DEFAULT '',
  `city` char(25) COLLATE utf8mb4_bin DEFAULT '',
  `intro` varchar(210) COLLATE utf8mb4_bin DEFAULT '',
  `ctime` bigint(20) DEFAULT NULL,
  `usernum` int(11) NOT NULL DEFAULT '0' COMMENT '粉丝数',
  PRIMARY KEY (`groupid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Records of wb_chat_group
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for wb_chat_group_history
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_group_history`;
CREATE TABLE `wb_chat_group_history` (
  `chat_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `fromuid` int(11) NOT NULL COMMENT '来源id',
  `groupid` int(11) NOT NULL COMMENT '目标id',
  `content` text COLLATE utf8mb4_bin NOT NULL COMMENT '内容',
  `content_type` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '内容类型',
  `create_time` datetime NOT NULL COMMENT '消息时间',
  PRIMARY KEY (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Records of wb_chat_group_history
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for wb_chat_group_user
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_group_user`;
CREATE TABLE `wb_chat_group_user` (
  `fansid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `groupid` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `message_count` int(10) unsigned NOT NULL DEFAULT '0',
  `ctime` bigint(20) NOT NULL DEFAULT '0' COMMENT '加入时间',
  `dtime` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  `utime` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '更新时间',
  PRIMARY KEY (`fansid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Records of wb_chat_group_user
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for wb_chat_online
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_online`;
CREATE TABLE `wb_chat_online` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) unsigned NOT NULL,
  `fd` bigint(20) unsigned NOT NULL,
  `online_time` bigint(20) unsigned NOT NULL,
  `offline_time` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Records of wb_chat_online
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for wb_chat_private_letter
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_private_letter`;
CREATE TABLE `wb_chat_private_letter` (
  `fansid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `fromuid` bigint(20) NOT NULL,
  `touid` bigint(20) NOT NULL,
  `mutual_concern` tinyint(4) NOT NULL DEFAULT '0',
  `message_count` int(10) unsigned NOT NULL DEFAULT '0',
  `ctime` bigint(20) NOT NULL DEFAULT '0',
  `utime` bigint(20) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`fansid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Records of wb_chat_private_letter
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for wb_chat_private_letter_history
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_private_letter_history`;
CREATE TABLE `wb_chat_private_letter_history` (
  `chat_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `fromuid` int(11) NOT NULL COMMENT '来源id',
  `touid` int(11) NOT NULL COMMENT '目标id',
  `content` text COLLATE utf8mb4_bin NOT NULL COMMENT '内容',
  `content_type` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '内容类型',
  `send_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '发送状态',
  `create_time` datetime NOT NULL COMMENT '消息时间',
  PRIMARY KEY (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Records of wb_chat_private_letter_history
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for wb_comment
-- ----------------------------
DROP TABLE IF EXISTS `wb_comment`;
CREATE TABLE `wb_comment` (
  `cid` bigint(20) NOT NULL AUTO_INCREMENT,
  `fromuid` bigint(20) NOT NULL,
  `msg` text COLLATE utf8mb4_bin NOT NULL,
  `msg_id` bigint(20) NOT NULL,
  `touid` mediumint(9) DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`cid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Records of wb_comment
-- ----------------------------
BEGIN;
COMMIT;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Records of wb_fans
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for wb_file
-- ----------------------------
DROP TABLE IF EXISTS `wb_file`;
CREATE TABLE `wb_file` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `userid` int(10) unsigned NOT NULL COMMENT '用户id',
  `type` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '0File, 1S3File',
  `file_name` varchar(255) COLLATE utf8mb4_bin NOT NULL COMMENT '文件名',
  `file_type` varchar(255) COLLATE utf8mb4_bin NOT NULL,
  `file_size` double NOT NULL COMMENT '文件大小',
  `file_location` varchar(255) COLLATE utf8mb4_bin NOT NULL COMMENT '文件地址',
  `file_path` varchar(255) COLLATE utf8mb4_bin NOT NULL COMMENT '文件地址',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除',
  `share_msg_id` bigint(20) unsigned NOT NULL DEFAULT '0' COMMENT '是否站内分享0未分享',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Records of wb_file
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for wb_message
-- ----------------------------
DROP TABLE IF EXISTS `wb_message`;
CREATE TABLE `wb_message` (
  `msg_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(9) NOT NULL,
  `contents` text COLLATE utf8mb4_bin NOT NULL,
  `repost` mediumtext COLLATE utf8mb4_bin,
  `refrom` varchar(50) COLLATE utf8mb4_bin NOT NULL,
  `media` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `media_info` text COLLATE utf8mb4_bin,
  `repostsum` int(11) NOT NULL DEFAULT '0',
  `commentsum` int(11) NOT NULL DEFAULT '0',
  `topic_id` int(10) unsigned NOT NULL DEFAULT '0',
  `is_delete` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否删除0未删除，1已删除',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`msg_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Records of wb_message
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for wb_reminder
-- ----------------------------
DROP TABLE IF EXISTS `wb_reminder`;
CREATE TABLE `wb_reminder` (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `msg_id` bigint(20) DEFAULT NULL,
  `fromuid` bigint(20) NOT NULL,
  `touid` bigint(20) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `ctime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Records of wb_reminder
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for wb_topic
-- ----------------------------
DROP TABLE IF EXISTS `wb_topic`;
CREATE TABLE `wb_topic` (
  `topic_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_bin NOT NULL COMMENT '标题',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '话题数量',
  PRIMARY KEY (`topic_id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Records of wb_topic
-- ----------------------------
BEGIN;
COMMIT;

-- ----------------------------
-- Table structure for wb_user
-- ----------------------------
DROP TABLE IF EXISTS `wb_user`;
CREATE TABLE `wb_user` (
  `uid` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `nickname` char(11) COLLATE utf8mb4_bin NOT NULL,
  `username` char(10) COLLATE utf8mb4_bin DEFAULT NULL,
  `blog` char(20) COLLATE utf8mb4_bin DEFAULT NULL,
  `phone` bigint(20) DEFAULT NULL COMMENT '手机号',
  `password` char(32) COLLATE utf8mb4_bin NOT NULL,
  `head_image` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `head_image_info` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `sex` tinyint(4) NOT NULL DEFAULT '0',
  `province` char(10) COLLATE utf8mb4_bin DEFAULT NULL,
  `city` char(25) COLLATE utf8mb4_bin DEFAULT NULL,
  `email` char(32) COLLATE utf8mb4_bin DEFAULT NULL,
  `intro` varchar(210) COLLATE utf8mb4_bin DEFAULT NULL,
  `ctime` bigint(20) NOT NULL,
  `message_sum` mediumint(9) NOT NULL DEFAULT '0',
  `theme` varchar(255) COLLATE utf8mb4_bin DEFAULT NULL,
  `fansnum` bigint(20) NOT NULL DEFAULT '0' COMMENT '粉丝数',
  `follownum` bigint(20) NOT NULL DEFAULT '0' COMMENT '关注数',
  `invisible` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '广场隐身1，默认0不隐身',
  PRIMARY KEY (`uid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Records of wb_user
-- ----------------------------
BEGIN;
COMMIT;

SET FOREIGN_KEY_CHECKS = 1;
