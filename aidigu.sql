
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for wb_app
-- ----------------------------
DROP TABLE IF EXISTS `wb_app`;
CREATE TABLE `wb_app`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `app_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '应用名称',
  `app_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '链接',
  `app_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '0关闭，1开启（默认）',
  `app_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0全部，1pc,2手机',
  `app_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '图片地址',
  `remind_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '提醒key',
  `app_config` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置',
  `open_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '打开方式，0frame,1直接打开，2新窗口打开',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 49 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_app
-- ----------------------------
INSERT INTO `wb_app` VALUES (1, '我的云盘', '/cloud/show/', 1, 1, '/static/tools/common/images/cloud.jpg', '', '{\"title\":\"我的云盘\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"80%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_1\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-02-06 16:35:23');
INSERT INTO `wb_app` VALUES (2, '嘀友聊天', '/chat', 1, 1, '/static/tools/common/images/chat.jpg', 'chat', '{\"title\":\"嘀友聊天\",\"shade\":0,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"60%\",\"70%\"],\"resize\":true,\"maxmin\":true,\"id\":\"app_2\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-07-03 15:16:00');
INSERT INTO `wb_app` VALUES (3, '嘀咕影院', '/tools/movie', 1, 1, '/static/tools/common/images/movie.jpg', '', '{\"title\":\"嘀咕影院\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"80%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_3\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-01-22 18:33:56');
INSERT INTO `wb_app` VALUES (5, 'BMI体重计算', '/tools/bmi', 1, 1, '/static/tools/common/images/bmi.jpg', '', '{\"title\":\"BMI体重计算\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"80%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_5\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-01-22 18:33:56');
INSERT INTO `wb_app` VALUES (9, '我的云盘', '/cloud/show/', 1, 2, '/static/tools/common/images/cloud.jpg', '', '{\"title\":\"我的云盘\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_8\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-01-22 18:46:07');
INSERT INTO `wb_app` VALUES (10, '嘀友聊天', '/chat', 1, 2, '/static/tools/common/images/chat.jpg', 'chat', '{\"title\":\"嘀友聊天\",\"shade\":0,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"id\":\"app_9\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-07-03 15:16:04');
INSERT INTO `wb_app` VALUES (11, '嘀咕影院', '/tools/movie', 1, 2, '/static/tools/common/images/movie.jpg', '', '{\"title\":\"嘀咕影院\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_10\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-01-22 18:46:07');
INSERT INTO `wb_app` VALUES (13, 'BMI体重计算', '/tools/bmi', 1, 2, '/static/tools/common/images/bmi.jpg', '', '{\"title\":\"BMI体重计算\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_12\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-01-22 18:46:07');
INSERT INTO `wb_app` VALUES (16, '话题', '/topic/', 1, 2, '/static/tools/common/images/topic.jpeg', '', '{}', 1, '2024-01-16 11:31:53', '2024-01-23 16:15:21');
INSERT INTO `wb_app` VALUES (17, '群管理', '/tools/chat/list', 1, 1, '/static/tools/common/images/chatgroup.jpeg', '', '{\"title\":\"群管理\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"70%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_11\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-06 11:28:44');
INSERT INTO `wb_app` VALUES (18, '群管理', '/tools/chat/list', 1, 2, '/static/tools/common/images/chatgroup.jpeg', '', '{\"title\":\"群管理\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_12\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-05 23:35:29');
INSERT INTO `wb_app` VALUES (19, '邀请码', '/tools/userinvite/list', 1, 1, '/static/tools/common/images/userinvite.jpeg', '', '{\"title\":\"邀请码\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"70%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_13\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-06 11:28:47');
INSERT INTO `wb_app` VALUES (20, '邀请码', '/tools/userinvite/list', 1, 2, '/static/tools/common/images/userinvite.jpeg', '', '{\"title\":\"邀请码\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_14\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-05 23:35:41');
INSERT INTO `wb_app` VALUES (21, '收藏管理', '/tools/collect/list', 1, 1, '/static/tools/common/images/collect.jpeg', '', '{\"title\":\"收藏管理\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"70%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_15\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-06 11:29:54');
INSERT INTO `wb_app` VALUES (22, '收藏管理', '/tools/collect/list', 1, 2, '/static/tools/common/images/collect.jpeg', '', '{\"title\":\"收藏管理\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_16\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-06 11:29:54');
INSERT INTO `wb_app` VALUES (23, '素材管理', '/tools/sourcematerial/list', 1, 1, '/static/tools/common/images/sourcematerial.jpeg', '', '{\"title\":\"素材管理\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"70%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_18\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-03-06 14:59:48');
INSERT INTO `wb_app` VALUES (24, '素材管理', '/tools/sourcematerial/list', 1, 2, '/static/tools/common/images/sourcematerial.jpeg', '', '{\"title\":\"素材管理\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_19\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-06 11:29:54');
INSERT INTO `wb_app` VALUES (25, '鉴宝', 'http://jb.96xy.cn/', 0, 0, '/static/tools/common/images/chat2.jpg', '', '{\"title\":\"鉴宝\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_25\",\"hideOnClose\":false,\"scrollbar\":false}', 2, '2024-01-16 11:31:53', '2024-07-01 19:58:48');
INSERT INTO `wb_app` VALUES (47, '互动提醒', '/tools/notice/list', 1, 1, '/static/tools/common/images/notice.jpg', 'message', '{\"title\":\"互动提醒\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":true,\"area\":[\"80%\",\"90%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_47\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-11-18 22:04:20');
INSERT INTO `wb_app` VALUES (48, '互动提醒', '/tools/notice/list', 1, 2, '/static/tools/common/images/notice.jpg', 'message', '{\"title\":\"互动提醒\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_48\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-11-18 22:04:21');

-- ----------------------------
-- Table structure for wb_badword
-- ----------------------------
DROP TABLE IF EXISTS `wb_badword`;
CREATE TABLE `wb_badword`  (
  `bid` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ctype` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`bid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_badword
-- ----------------------------

-- ----------------------------
-- Table structure for wb_chat_friends
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_friends`;
CREATE TABLE `wb_chat_friends`  (
  `fansid` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fromuid` bigint(20) NOT NULL,
  `touid` bigint(20) NOT NULL,
  `mutual_concern` tinyint(4) NOT NULL DEFAULT 0,
  `message_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `ctime` bigint(20) NOT NULL DEFAULT 0,
  `utime` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`fansid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_chat_friends
-- ----------------------------

-- ----------------------------
-- Table structure for wb_chat_friends_history
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_friends_history`;
CREATE TABLE `wb_chat_friends_history`  (
  `chat_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `fromuid` int(11) NOT NULL COMMENT '来源id',
  `touid` int(11) NOT NULL COMMENT '目标id',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '内容',
  `content_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '内容类型',
  `send_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送状态',
  `create_time` datetime NOT NULL COMMENT '消息时间',
  PRIMARY KEY (`chat_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_chat_friends_history
-- ----------------------------

-- ----------------------------
-- Table structure for wb_chat_group
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_group`;
CREATE TABLE `wb_chat_group`  (
  `groupid` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `groupname` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
  `head_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
  `head_image_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
  `city` char(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
  `fromuid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `invite_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `invite_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否开启加群',
  `intro` varchar(210) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
  `ctime` bigint(20) NULL DEFAULT NULL,
  `utime` bigint(20) UNSIGNED NULL DEFAULT NULL,
  `usernum` int(11) NOT NULL DEFAULT 0 COMMENT '粉丝数',
  PRIMARY KEY (`groupid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_chat_group
-- ----------------------------

-- ----------------------------
-- Table structure for wb_chat_group_history
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_group_history`;
CREATE TABLE `wb_chat_group_history`  (
  `chat_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `fromuid` int(11) NOT NULL COMMENT '来源id',
  `groupid` int(11) NOT NULL COMMENT '目标id',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '内容',
  `content_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '内容类型',
  `create_time` datetime NOT NULL COMMENT '消息时间',
  PRIMARY KEY (`chat_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_chat_group_history
-- ----------------------------

-- ----------------------------
-- Table structure for wb_chat_group_user
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_group_user`;
CREATE TABLE `wb_chat_group_user`  (
  `fansid` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `groupid` bigint(20) NOT NULL,
  `uid` bigint(20) NOT NULL,
  `message_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `ctime` bigint(20) NOT NULL DEFAULT 0 COMMENT '加入时间',
  `dtime` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  `utime` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`fansid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_chat_group_user
-- ----------------------------

-- ----------------------------
-- Table structure for wb_chat_online
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_online`;
CREATE TABLE `wb_chat_online`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) UNSIGNED NOT NULL,
  `fd` bigint(20) UNSIGNED NOT NULL,
  `online_time` bigint(20) UNSIGNED NOT NULL,
  `offline_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_chat_online
-- ----------------------------

-- ----------------------------
-- Table structure for wb_chat_private_letter
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_private_letter`;
CREATE TABLE `wb_chat_private_letter`  (
  `fansid` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fromuid` bigint(20) NOT NULL,
  `touid` bigint(20) NOT NULL,
  `mutual_concern` tinyint(4) NOT NULL DEFAULT 0,
  `message_count` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `ctime` bigint(20) NOT NULL DEFAULT 0,
  `utime` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`fansid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_chat_private_letter
-- ----------------------------

-- ----------------------------
-- Table structure for wb_chat_private_letter_history
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_private_letter_history`;
CREATE TABLE `wb_chat_private_letter_history`  (
  `chat_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `fromuid` int(11) NOT NULL COMMENT '来源id',
  `touid` int(11) NOT NULL COMMENT '目标id',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '内容',
  `content_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '内容类型',
  `send_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送状态',
  `create_time` datetime NOT NULL COMMENT '消息时间',
  PRIMARY KEY (`chat_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_chat_private_letter_history
-- ----------------------------

-- ----------------------------
-- Table structure for wb_comment
-- ----------------------------
DROP TABLE IF EXISTS `wb_comment`;
CREATE TABLE `wb_comment`  (
  `cid` bigint(20) NOT NULL AUTO_INCREMENT,
  `fromuid` bigint(20) NOT NULL,
  `msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `msg_id` bigint(20) NOT NULL,
  `touid` mediumint(9) NULL DEFAULT NULL,
  `ctime` int(11) NOT NULL,
  `ctype` tinyint(4) NOT NULL DEFAULT 0 COMMENT '回复类型',
  PRIMARY KEY (`cid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_comment
-- ----------------------------

-- ----------------------------
-- Table structure for wb_fans
-- ----------------------------
DROP TABLE IF EXISTS `wb_fans`;
CREATE TABLE `wb_fans`  (
  `fansid` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fromuid` bigint(20) NOT NULL,
  `touid` bigint(20) NOT NULL,
  `mutual_concern` tinyint(4) NOT NULL DEFAULT 0,
  `ctime` bigint(20) NOT NULL DEFAULT 0,
  PRIMARY KEY (`fansid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_fans
-- ----------------------------

-- ----------------------------
-- Table structure for wb_file
-- ----------------------------
DROP TABLE IF EXISTS `wb_file`;
CREATE TABLE `wb_file`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `userid` int(10) UNSIGNED NOT NULL COMMENT '用户id',
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0File, 1S3File',
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '文件名',
  `file_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `file_size` double NOT NULL COMMENT '文件大小',
  `file_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '文件地址',
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '文件地址',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `is_delete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `share_msg_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否站内分享，0未分享',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_file
-- ----------------------------

-- ----------------------------
-- Table structure for wb_file_log
-- ----------------------------
DROP TABLE IF EXISTS `wb_file_log`;
CREATE TABLE `wb_file_log`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  `type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT 'type 1头像，2微博，3聊天，4素材，5主题, 6网盘',
  `media_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `media_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `media_size` double NOT NULL,
  `media_mime` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `media_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_del` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `create_time` bigint(20) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_file_log
-- ----------------------------

-- ----------------------------
-- Table structure for wb_message
-- ----------------------------
DROP TABLE IF EXISTS `wb_message`;
CREATE TABLE `wb_message`  (
  `msg_id` mediumint(9) NOT NULL AUTO_INCREMENT,
  `uid` mediumint(9) NOT NULL,
  `contents` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `repost` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `refrom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `media` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `media_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `repostsum` int(11) NOT NULL DEFAULT 0,
  `commentsum` int(11) NOT NULL DEFAULT 0,
  `collectsum` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '收藏点赞数',
  `topic_id` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '话题id',
  `is_delete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除0未删除，1已删除',
  `ctime` int(11) NOT NULL,
  PRIMARY KEY (`msg_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_message
-- ----------------------------

-- ----------------------------
-- Table structure for wb_reminder
-- ----------------------------
DROP TABLE IF EXISTS `wb_reminder`;
CREATE TABLE `wb_reminder`  (
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `msg_id` bigint(20) NULL DEFAULT NULL,
  `fromuid` bigint(20) NOT NULL,
  `touid` bigint(20) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态：0未读，1已读',
  `ctime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_reminder
-- ----------------------------

-- ----------------------------
-- Table structure for wb_source_material
-- ----------------------------
DROP TABLE IF EXISTS `wb_source_material`;
CREATE TABLE `wb_source_material`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `uid` bigint(20) UNSIGNED NOT NULL COMMENT '用户id',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '标题',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态0删除，1正常',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `push_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '投送时间',
  `share_msg_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '内容id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_source_material
-- ----------------------------

-- ----------------------------
-- Table structure for wb_source_material_relation
-- ----------------------------
DROP TABLE IF EXISTS `wb_source_material_relation`;
CREATE TABLE `wb_source_material_relation`  (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `media_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件名',
  `media_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '类型',
  `file_size` double NOT NULL COMMENT '文件大小',
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件名',
  `source_material_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否站内分享0未分享',
  `is_delete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_source_material_relation
-- ----------------------------

-- ----------------------------
-- Table structure for wb_topic
-- ----------------------------
DROP TABLE IF EXISTS `wb_topic`;
CREATE TABLE `wb_topic`  (
  `topic_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '标题',
  `uid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人，默认0',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `count` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '话题数量',
  PRIMARY KEY (`topic_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_topic
-- ----------------------------

-- ----------------------------
-- Table structure for wb_user
-- ----------------------------
DROP TABLE IF EXISTS `wb_user`;
CREATE TABLE `wb_user`  (
  `uid` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `nickname` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `username` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `blog` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `phone` bigint(20) NULL DEFAULT NULL COMMENT '手机号',
  `password` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `head_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `head_image_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `sex` tinyint(4) NOT NULL DEFAULT 0,
  `province` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `city` char(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `email` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `intro` varchar(210) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `ctime` bigint(20) NOT NULL,
  `message_sum` mediumint(9) NOT NULL DEFAULT 0,
  `theme` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `fansnum` bigint(20) NOT NULL DEFAULT 0 COMMENT '粉丝数',
  `follownum` bigint(20) NOT NULL DEFAULT 0 COMMENT '关注数',
  `invisible` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '广场隐身1，默认0不隐身',
  PRIMARY KEY (`uid`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_user
-- ----------------------------

-- ----------------------------
-- Table structure for wb_user_collect
-- ----------------------------
DROP TABLE IF EXISTS `wb_user_collect`;
CREATE TABLE `wb_user_collect`  (
  `collect_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `msg_id` bigint(20) UNSIGNED NOT NULL,
  `fromuid` int(10) UNSIGNED NOT NULL,
  `collect_time` datetime NOT NULL,
  `delete_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`collect_id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_user_collect
-- ----------------------------

-- ----------------------------
-- Table structure for wb_user_invite
-- ----------------------------
DROP TABLE IF EXISTS `wb_user_invite`;
CREATE TABLE `wb_user_invite`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fromuid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `touid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `invite_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `invite_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `create_time` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 1 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_user_invite
-- ----------------------------

SET FOREIGN_KEY_CHECKS = 1;
