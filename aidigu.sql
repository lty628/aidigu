DROP TABLE IF EXISTS `wb_admin_behavior_log`;
CREATE TABLE `wb_admin_behavior_log`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `uid` int NOT NULL COMMENT '用户ID',
  `module` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '模块名',
  `controller` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '控制器名',
  `action` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NOT NULL COMMENT '方法名',
  `ip` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT 'IP地址',
  `content` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL COMMENT '操作内容描述',
  `params` text CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL COMMENT '操作参数JSON',
  `create_time` int NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_uid`(`uid` ASC) USING BTREE,
  INDEX `idx_create_time`(`create_time` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb3 COLLATE = utf8mb3_general_ci COMMENT = '用户行为日志表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_admin_behavior_log
-- ----------------------------

-- ----------------------------
-- Table structure for wb_admin_system_setting
-- ----------------------------
DROP TABLE IF EXISTS `wb_admin_system_setting`;
CREATE TABLE `wb_admin_system_setting`  (
  `id` int NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `section` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置分组/节名，如app、database等',
  `key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置键名',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '配置值',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '配置项描述',
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_section_key`(`section` ASC, `key` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统配置表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_admin_system_setting
-- ----------------------------

-- ----------------------------
-- Table structure for wb_app
-- ----------------------------
DROP TABLE IF EXISTS `wb_app`;
CREATE TABLE `wb_app`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `app_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '应用名称',
  `app_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '链接',
  `fromuid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建用户',
  `app_status` tinyint NOT NULL DEFAULT 1 COMMENT '0关闭，1站内，2站外',
  `app_type` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '0全部，1pc,2手机',
  `app_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '图片地址',
  `remind_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '提醒key',
  `app_config` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置',
  `open_type` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '打开方式，0frame,1直接打开，2新窗口打开',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_app
-- ----------------------------
INSERT INTO `wb_app` VALUES (1, '我的云盘', '/cloud/show/', 0, 1, 1, '/static/tools/common/images/cloud.jpg', '', '{\"title\":\"我的云盘\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"80%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_1\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-02-06 16:35:23');
INSERT INTO `wb_app` VALUES (2, '嘀友聊天', '/chat', 0, 1, 1, '/static/tools/common/images/chat.jpg', 'chat', '{\"title\":\"嘀友聊天\",\"shade\":0,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"60%\",\"70%\"],\"resize\":true,\"maxmin\":true,\"id\":\"app_2\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-07-03 15:16:00');
INSERT INTO `wb_app` VALUES (3, '嘀咕影院', '/tools/movie', 0, 1, 1, '/static/tools/common/images/movie.jpg', '', '{\"title\":\"嘀咕影院\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"80%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_3\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-01-22 18:33:56');
INSERT INTO `wb_app` VALUES (4, '开车啦', '/tools/onlinecar', 0, 0, 1, '/static/tools/common/images/onlinecar.jpg', '', '{\"title\":\"开车啦\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"80%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_4\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2025-03-03 20:51:00');
INSERT INTO `wb_app` VALUES (5, 'BMI体重计算', '/tools/bmi', 0, 1, 1, '/static/tools/common/images/bmi.jpg', '', '{\"title\":\"BMI体重计算\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"80%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_5\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-01-22 18:33:56');
INSERT INTO `wb_app` VALUES (9, '我的云盘', '/cloud/show/', 0, 1, 2, '/static/tools/common/images/cloud.jpg', '', '{\"title\":\"我的云盘\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_8\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-01-22 18:46:07');
INSERT INTO `wb_app` VALUES (10, '嘀友聊天', '/chat', 0, 1, 2, '/static/tools/common/images/chat.jpg', 'chat', '{\"title\":\"嘀友聊天\",\"shade\":0,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"id\":\"app_9\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-07-03 15:16:04');
INSERT INTO `wb_app` VALUES (11, '嘀咕影院', '/tools/movie', 0, 1, 2, '/static/tools/common/images/movie.jpg', '', '{\"title\":\"嘀咕影院\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_10\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-01-22 18:46:07');
INSERT INTO `wb_app` VALUES (12, '开车啦', '/tools/onlinecar', 0, 0, 2, '/static/tools/common/images/onlinecar.jpg', '', '{\"title\":\"开车啦\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_11\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2025-03-03 20:51:03');
INSERT INTO `wb_app` VALUES (13, 'BMI体重计算', '/tools/bmi', 0, 1, 2, '/static/tools/common/images/bmi.jpg', '', '{\"title\":\"BMI体重计算\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_12\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-01-22 18:46:07');
INSERT INTO `wb_app` VALUES (16, '话题', '/topic/', 0, 1, 2, '/static/tools/common/images/topic.jpeg', '', '{}', 1, '2024-01-16 11:31:53', '2024-01-23 16:15:21');
INSERT INTO `wb_app` VALUES (17, '群管理', '/tools/chat/list', 0, 1, 1, '/static/tools/common/images/chatgroup.jpeg', '', '{\"title\":\"群管理\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"70%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_11\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-06 11:28:44');
INSERT INTO `wb_app` VALUES (18, '群管理', '/tools/chat/list', 0, 1, 2, '/static/tools/common/images/chatgroup.jpeg', '', '{\"title\":\"群管理\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_12\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-05 23:35:29');
INSERT INTO `wb_app` VALUES (19, '邀请码', '/tools/userinvite/list', 0, 1, 1, '/static/tools/common/images/userinvite.jpeg', '', '{\"title\":\"邀请码\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"70%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_13\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-06 11:28:47');
INSERT INTO `wb_app` VALUES (20, '邀请码', '/tools/userinvite/list', 0, 1, 2, '/static/tools/common/images/userinvite.jpeg', '', '{\"title\":\"邀请码\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_14\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-05 23:35:41');
INSERT INTO `wb_app` VALUES (21, '收藏管理', '/tools/collect/list', 0, 1, 1, '/static/tools/common/images/collect.jpeg', '', '{\"title\":\"收藏管理\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"70%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_15\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-06 11:29:54');
INSERT INTO `wb_app` VALUES (22, '收藏管理', '/tools/collect/list', 0, 1, 2, '/static/tools/common/images/collect.jpeg', '', '{\"title\":\"收藏管理\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_16\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-06 11:29:54');
INSERT INTO `wb_app` VALUES (23, '素材管理', '/tools/sourcematerial/list', 0, 1, 1, '/static/tools/common/images/sourcematerial.jpeg', '', '{\"title\":\"素材管理\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"70%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_18\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-03-06 14:59:48');
INSERT INTO `wb_app` VALUES (24, '素材管理', '/tools/sourcematerial/list', 0, 1, 2, '/static/tools/common/images/sourcematerial.jpeg', '', '{\"title\":\"素材管理\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_19\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-06 11:29:54');
INSERT INTO `wb_app` VALUES (53, '密码工具', '/tools/password/list', 0, 1, 1, '/static/tools/common/images/password.jpg', '', '{\"title\":\"密码工具\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":true,\"area\":[\"60%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_53\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2025-03-26 21:05:36');
INSERT INTO `wb_app` VALUES (54, '密码工具', '/tools/password/list', 0, 1, 2, '/static/tools/common/images/password.jpg', '', '{\"title\":\"密码工具\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_54\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2025-03-26 21:05:36');
INSERT INTO `wb_app` VALUES (64, '时光邮局', '/tools/letter', 0, 1, 1, '/static/tools/common/images/letter.png', '', '{\"title\":\"时光邮局\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"70%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_64\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2026-01-19 10:25:36', '2026-01-19 11:12:49');
INSERT INTO `wb_app` VALUES (65, '表白墙', '/tools/lovewall', 0, 1, 1, '/static/tools/common/images/love.png', '', '{\"title\":\"表白墙\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"70%\",\"100%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_65\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2026-01-19 10:26:38', '2026-01-19 11:13:56');

-- ----------------------------
-- Table structure for wb_badword
-- ----------------------------
DROP TABLE IF EXISTS `wb_badword`;
CREATE TABLE `wb_badword`  (
  `bid` int NOT NULL AUTO_INCREMENT,
  `word` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ctype` tinyint NOT NULL DEFAULT 0,
  PRIMARY KEY (`bid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_badword
-- ----------------------------

-- ----------------------------
-- Table structure for wb_channel
-- ----------------------------
DROP TABLE IF EXISTS `wb_channel`;
CREATE TABLE `wb_channel`  (
  `channel_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `channel_name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '频道名称',
  `avatar` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '' COMMENT '频道头像',
  `avatar_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '' COMMENT '头像信息',
  `description` varchar(500) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '' COMMENT '频道介绍',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '' COMMENT '频道密码',
  `owner_uid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建者UID',
  `invite_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '邀请码',
  `invite_status` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '邀请状态 0:关闭 1:开启',
  `allow_speak` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否允许发言 0:不允许 1:允许',
  `allow_comment` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '是否允许评论 0:不允许 1:允许',
  `member_count` int NOT NULL DEFAULT 0 COMMENT '成员数',
  `message_count` int NOT NULL DEFAULT 0 COMMENT '消息数',
  `create_time` bigint NULL DEFAULT NULL COMMENT '创建时间',
  `update_time` bigint UNSIGNED NULL DEFAULT NULL COMMENT '更新时间',
  PRIMARY KEY (`channel_id`) USING BTREE,
  INDEX `idx_owner_uid`(`owner_uid` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_channel
-- ----------------------------

-- ----------------------------
-- Table structure for wb_channel_comment
-- ----------------------------
DROP TABLE IF EXISTS `wb_channel_comment`;
CREATE TABLE `wb_channel_comment`  (
  `cid` bigint NOT NULL AUTO_INCREMENT,
  `fromuid` bigint NOT NULL,
  `msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `msg_id` bigint NOT NULL,
  `touid` mediumint NULL DEFAULT NULL,
  `relation_reply_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '' COMMENT '关联回复ID,多个用逗号分隔',
  `reply_count` int NOT NULL DEFAULT 0 COMMENT '回复数',
  `ctime` int NOT NULL,
  `ctype` tinyint NOT NULL DEFAULT 0 COMMENT '回复类型',
  PRIMARY KEY (`cid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_channel_comment
-- ----------------------------

-- ----------------------------
-- Table structure for wb_channel_comment_reply
-- ----------------------------
DROP TABLE IF EXISTS `wb_channel_comment_reply`;
CREATE TABLE `wb_channel_comment_reply`  (
  `rid` bigint NOT NULL AUTO_INCREMENT,
  `fromuid` bigint NOT NULL,
  `msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '回复内容',
  `msg_id` bigint NOT NULL COMMENT '关联内容msgid',
  `cid` bigint NOT NULL,
  `touid` mediumint NULL DEFAULT NULL,
  `ctime` int NOT NULL,
  PRIMARY KEY (`rid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_channel_comment_reply
-- ----------------------------

-- ----------------------------
-- Table structure for wb_channel_message
-- ----------------------------
DROP TABLE IF EXISTS `wb_channel_message`;
CREATE TABLE `wb_channel_message`  (
  `msg_id` mediumint NOT NULL AUTO_INCREMENT,
  `uid` mediumint NOT NULL,
  `channel_id` int NOT NULL COMMENT '频道ID',
  `contents` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `repost` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `refrom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `media` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `media_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `repostsum` int NOT NULL DEFAULT 0,
  `commentsum` int NOT NULL DEFAULT 0,
  `collectsum` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '收藏点赞数',
  `topic_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '话题id',
  `is_delete` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除0未删除，1已删除',
  `ctime` int NOT NULL,
  PRIMARY KEY (`msg_id`) USING BTREE,
  INDEX `idx_channel_id`(`channel_id` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_channel_message
-- ----------------------------

-- ----------------------------
-- Table structure for wb_channel_user
-- ----------------------------
DROP TABLE IF EXISTS `wb_channel_user`;
CREATE TABLE `wb_channel_user`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `channel_id` bigint NOT NULL COMMENT '频道ID',
  `uid` bigint NOT NULL COMMENT '用户ID',
  `role` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '角色 0:普通成员 1:管理员 2:创建者',
  `message_count` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '消息计数',
  `join_time` bigint NOT NULL DEFAULT 0 COMMENT '加入时间',
  `leave_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '离开时间',
  `update_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_channel_id`(`channel_id` ASC) USING BTREE,
  INDEX `idx_uid`(`uid` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_channel_user
-- ----------------------------

-- ----------------------------
-- Table structure for wb_chat_friends
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_friends`;
CREATE TABLE `wb_chat_friends`  (
  `fansid` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fromuid` bigint NOT NULL,
  `touid` bigint NOT NULL,
  `mutual_concern` tinyint NOT NULL DEFAULT 0,
  `message_count` int UNSIGNED NOT NULL DEFAULT 0,
  `ctime` bigint NOT NULL DEFAULT 0,
  `utime` bigint UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`fansid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_chat_friends
-- ----------------------------

-- ----------------------------
-- Table structure for wb_chat_friends_history
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_friends_history`;
CREATE TABLE `wb_chat_friends_history`  (
  `chat_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `fromuid` int NOT NULL COMMENT '来源id',
  `touid` int NOT NULL COMMENT '目标id',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '内容',
  `content_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '内容类型',
  `send_status` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送状态',
  `create_time` datetime NOT NULL COMMENT '消息时间',
  PRIMARY KEY (`chat_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_chat_friends_history
-- ----------------------------

-- ----------------------------
-- Table structure for wb_chat_group
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_group`;
CREATE TABLE `wb_chat_group`  (
  `groupid` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `groupname` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
  `head_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
  `head_image_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
  `city` char(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
  `fromuid` int UNSIGNED NOT NULL DEFAULT 0,
  `invite_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `invite_status` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否开启加群',
  `intro` varchar(210) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '',
  `ctime` bigint NULL DEFAULT NULL,
  `utime` bigint UNSIGNED NULL DEFAULT NULL,
  `usernum` int NOT NULL DEFAULT 0 COMMENT '粉丝数',
  PRIMARY KEY (`groupid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_chat_group
-- ----------------------------

-- ----------------------------
-- Table structure for wb_chat_group_history
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_group_history`;
CREATE TABLE `wb_chat_group_history`  (
  `chat_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `fromuid` int NOT NULL COMMENT '来源id',
  `groupid` int NOT NULL COMMENT '目标id',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '内容',
  `content_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '内容类型',
  `create_time` datetime NOT NULL COMMENT '消息时间',
  PRIMARY KEY (`chat_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_chat_group_history
-- ----------------------------

-- ----------------------------
-- Table structure for wb_chat_group_user
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_group_user`;
CREATE TABLE `wb_chat_group_user`  (
  `fansid` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `groupid` bigint NOT NULL,
  `uid` bigint NOT NULL,
  `message_count` int UNSIGNED NOT NULL DEFAULT 0,
  `ctime` bigint NOT NULL DEFAULT 0 COMMENT '加入时间',
  `dtime` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '删除时间',
  `utime` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`fansid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_chat_group_user
-- ----------------------------

-- ----------------------------
-- Table structure for wb_chat_online
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_online`;
CREATE TABLE `wb_chat_online`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` bigint UNSIGNED NOT NULL,
  `fd` bigint UNSIGNED NOT NULL,
  `online_time` bigint UNSIGNED NOT NULL,
  `offline_time` bigint UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_chat_online
-- ----------------------------

-- ----------------------------
-- Table structure for wb_chat_private_letter
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_private_letter`;
CREATE TABLE `wb_chat_private_letter`  (
  `fansid` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fromuid` bigint NOT NULL,
  `touid` bigint NOT NULL,
  `mutual_concern` tinyint NOT NULL DEFAULT 0,
  `message_count` int UNSIGNED NOT NULL DEFAULT 0,
  `ctime` bigint NOT NULL DEFAULT 0,
  `utime` bigint UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`fansid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_chat_private_letter
-- ----------------------------

-- ----------------------------
-- Table structure for wb_chat_private_letter_history
-- ----------------------------
DROP TABLE IF EXISTS `wb_chat_private_letter_history`;
CREATE TABLE `wb_chat_private_letter_history`  (
  `chat_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `fromuid` int NOT NULL COMMENT '来源id',
  `touid` int NOT NULL COMMENT '目标id',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '内容',
  `content_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '内容类型',
  `send_status` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '发送状态',
  `create_time` datetime NOT NULL COMMENT '消息时间',
  PRIMARY KEY (`chat_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_chat_private_letter_history
-- ----------------------------

-- ----------------------------
-- Table structure for wb_comment
-- ----------------------------
DROP TABLE IF EXISTS `wb_comment`;
CREATE TABLE `wb_comment`  (
  `cid` bigint NOT NULL AUTO_INCREMENT,
  `fromuid` bigint NOT NULL,
  `msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `msg_id` bigint NOT NULL,
  `touid` mediumint NULL DEFAULT NULL,
  `relation_reply_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT '' COMMENT '关联回复ID,多个用逗号分隔',
  `reply_count` int NOT NULL DEFAULT 0 COMMENT '回复数',
  `ctime` int NOT NULL,
  `ctype` tinyint NOT NULL DEFAULT 0 COMMENT '回复类型',
  PRIMARY KEY (`cid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_comment
-- ----------------------------

-- ----------------------------
-- Table structure for wb_comment_reply
-- ----------------------------
DROP TABLE IF EXISTS `wb_comment_reply`;
CREATE TABLE `wb_comment_reply`  (
  `rid` bigint NOT NULL AUTO_INCREMENT,
  `fromuid` bigint NOT NULL,
  `msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '回复内容',
  `msg_id` bigint NOT NULL COMMENT '关联内容msgid',
  `cid` bigint NOT NULL,
  `touid` mediumint NULL DEFAULT NULL,
  `ctime` int NOT NULL,
  PRIMARY KEY (`rid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_comment_reply
-- ----------------------------

-- ----------------------------
-- Table structure for wb_fans
-- ----------------------------
DROP TABLE IF EXISTS `wb_fans`;
CREATE TABLE `wb_fans`  (
  `fansid` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `fromuid` bigint NOT NULL,
  `touid` bigint NOT NULL,
  `mutual_concern` tinyint NOT NULL DEFAULT 0,
  `ctime` bigint NOT NULL DEFAULT 0,
  PRIMARY KEY (`fansid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_fans
-- ----------------------------

-- ----------------------------
-- Table structure for wb_file
-- ----------------------------
DROP TABLE IF EXISTS `wb_file`;
CREATE TABLE `wb_file`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `userid` int UNSIGNED NOT NULL COMMENT '用户id',
  `type` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '0File, 1S3File',
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '文件名',
  `file_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `file_size` double NOT NULL COMMENT '文件大小',
  `file_location` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '文件地址',
  `file_path` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '文件地址',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `is_delete` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `share_msg_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否站内分享，0未分享',
  `dir_id` int NOT NULL DEFAULT 0 COMMENT '目录ID',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_file
-- ----------------------------

-- ----------------------------
-- Table structure for wb_file_dir
-- ----------------------------
DROP TABLE IF EXISTS `wb_file_dir`;
CREATE TABLE `wb_file_dir`  (
  `dir_id` int NOT NULL AUTO_INCREMENT,
  `parent_id` int NOT NULL DEFAULT 0 COMMENT '父级ID',
  `uid` int NOT NULL DEFAULT 0 COMMENT '用户ID',
  `dir_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '目录名称',
  `is_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`dir_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文件目录表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_file_dir
-- ----------------------------

-- ----------------------------
-- Table structure for wb_file_log
-- ----------------------------
DROP TABLE IF EXISTS `wb_file_log`;
CREATE TABLE `wb_file_log`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` bigint UNSIGNED NOT NULL DEFAULT 0,
  `type` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT 'type 1头像，2微博，3聊天，4素材，5主题, 6网盘',
  `media_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `media_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `media_size` double NOT NULL,
  `media_mime` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `media_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `is_del` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `create_time` bigint NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_file_log
-- ----------------------------

-- ----------------------------
-- Table structure for wb_game_config
-- ----------------------------
DROP TABLE IF EXISTS `wb_game_config`;
CREATE TABLE `wb_game_config`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `game_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '游戏名称',
  `game_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '游戏标识符',
  `game_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '游戏描述',
  `config_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置数据（JSON格式）',
  `config_type` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '配置类型：1-系统默认，2-用户自定义',
  `uid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID（系统默认配置为0）',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0-禁用，1-启用',
  `sort_order` int NOT NULL DEFAULT 0 COMMENT '排序',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `config_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '自定义配置名称',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_game_key`(`game_key` ASC) USING BTREE,
  INDEX `idx_uid`(`uid` ASC) USING BTREE,
  INDEX `idx_config_type`(`config_type` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_game_config
-- ----------------------------

-- ----------------------------
-- Table structure for wb_message
-- ----------------------------
DROP TABLE IF EXISTS `wb_message`;
CREATE TABLE `wb_message`  (
  `msg_id` mediumint NOT NULL AUTO_INCREMENT,
  `uid` mediumint NOT NULL,
  `contents` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `repost` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `refrom` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `media` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `media_info` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL,
  `repostsum` int NOT NULL DEFAULT 0,
  `commentsum` int NOT NULL DEFAULT 0,
  `collectsum` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '收藏点赞数',
  `topic_id` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '话题id',
  `is_delete` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除0未删除，1已删除',
  `ctime` int NOT NULL,
  PRIMARY KEY (`msg_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_message
-- ----------------------------

-- ----------------------------
-- Table structure for wb_password
-- ----------------------------
DROP TABLE IF EXISTS `wb_password`;
CREATE TABLE `wb_password`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建用户',
  `url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '网站地址',
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '网站名称',
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '用户名',
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '密码',
  `salt` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '盐',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '密码表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_password
-- ----------------------------

-- ----------------------------
-- Table structure for wb_reminder
-- ----------------------------
DROP TABLE IF EXISTS `wb_reminder`;
CREATE TABLE `wb_reminder`  (
  `reminder_id` bigint NOT NULL AUTO_INCREMENT COMMENT '提醒记录ID，主键',
  `uk_id` bigint NULL DEFAULT NULL COMMENT '唯一键ID，根据类型不同含义不同：评论类型=评论ID(cid)，回复类型=回复ID(rid)，收藏类型=消息ID(msg_id)，关注/取消关注=0',
  `fromuid` bigint NOT NULL COMMENT '发送提醒的用户ID',
  `touid` bigint NOT NULL COMMENT '接收提醒的用户ID',
  `type` tinyint NOT NULL COMMENT '提醒类型：1微博评论，2微博回复，3频道评论，4频道回复，5好友关注，6取消关注，7@提及，8收藏微博，9收藏频道微博',
  `extra` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL COMMENT '额外信息，JSON格式存储详细内容',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态：0未读，1已读',
  `ctime` bigint NOT NULL COMMENT '创建时间戳',
  `uptime` bigint NULL DEFAULT NULL COMMENT '更新时间戳',
  PRIMARY KEY (`reminder_id`) USING BTREE,
  INDEX `idx_uk_from_touid_type`(`uk_id` ASC, `fromuid` ASC, `touid` ASC, `type` ASC) USING BTREE,
  INDEX `idx_touid`(`touid` ASC) USING BTREE,
  INDEX `idx_touid_status_ctime`(`touid` ASC, `status` ASC, `ctime` ASC) USING BTREE,
  INDEX `idx_status`(`status` ASC) USING BTREE,
  INDEX `idx_type`(`type` ASC) USING BTREE,
  INDEX `idx_ctime`(`ctime` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin COMMENT = '消息提醒表，存储各类消息提醒记录' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_reminder
-- ----------------------------

-- ----------------------------
-- Table structure for wb_search
-- ----------------------------
DROP TABLE IF EXISTS `wb_search`;
CREATE TABLE `wb_search`  (
  `search_id` int NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '关键词',
  `uid` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人，默认0',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `count` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '搜索数量',
  PRIMARY KEY (`search_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_search
-- ----------------------------

-- ----------------------------
-- Table structure for wb_source_material
-- ----------------------------
DROP TABLE IF EXISTS `wb_source_material`;
CREATE TABLE `wb_source_material`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `uid` bigint UNSIGNED NOT NULL COMMENT '用户id',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '标题',
  `status` tinyint NOT NULL DEFAULT 1 COMMENT '状态0删除，1正常',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `push_time` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '投送时间',
  `share_msg_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '内容id',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_source_material
-- ----------------------------

-- ----------------------------
-- Table structure for wb_source_material_relation
-- ----------------------------
DROP TABLE IF EXISTS `wb_source_material_relation`;
CREATE TABLE `wb_source_material_relation`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `media_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件名',
  `media_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '类型',
  `file_size` double NOT NULL COMMENT '文件大小',
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件名',
  `source_material_id` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否站内分享0未分享',
  `is_delete` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_source_material_relation
-- ----------------------------

-- ----------------------------
-- Table structure for wb_tools_letters
-- ----------------------------
DROP TABLE IF EXISTS `wb_tools_letters`;
CREATE TABLE `wb_tools_letters`  (
  `id` int NOT NULL AUTO_INCREMENT,
  `sender_id` int NOT NULL COMMENT '发信人ID',
  `sender_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '发信人姓名',
  `receiver` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '收信人',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '信件标题',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '信件内容',
  `send_time` datetime NULL DEFAULT NULL COMMENT '发送时间',
  `receive_time` datetime NOT NULL COMMENT '接收时间',
  `status` tinyint(1) NULL DEFAULT 0 COMMENT '状态：0未读，1已读',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '信件表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_tools_letters
-- ----------------------------

-- ----------------------------
-- Table structure for wb_tools_love_wall
-- ----------------------------
DROP TABLE IF EXISTS `wb_tools_love_wall`;
CREATE TABLE `wb_tools_love_wall`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '发布用户ID',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '表白内容',
  `to_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '表白对象姓名',
  `is_anonymous` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否匿名 0:否 1:是',
  `status` tinyint UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态 0:删除 1:正常',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_uid`(`uid` ASC) USING BTREE,
  INDEX `idx_status`(`status` ASC) USING BTREE,
  INDEX `idx_create_time`(`create_time` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '表白墙表' ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_tools_love_wall
-- ----------------------------

-- ----------------------------
-- Table structure for wb_topic
-- ----------------------------
DROP TABLE IF EXISTS `wb_topic`;
CREATE TABLE `wb_topic`  (
  `topic_id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '标题',
  `uid` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人，默认0',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `count` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '话题数量',
  PRIMARY KEY (`topic_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_topic
-- ----------------------------
INSERT INTO `wb_topic` VALUES (1, '#你喜欢什么类型的电影、电视?#', 0, '2023-10-12 11:55:13', 1);
INSERT INTO `wb_topic` VALUES (2, '#你平时喜欢什么类型的穿着打扮#', 0, '2023-10-12 11:55:30', 0);
INSERT INTO `wb_topic` VALUES (3, '#你最喜欢的颜色#', 0, '2023-10-12 11:55:55', 0);
INSERT INTO `wb_topic` VALUES (4, '#你听过最好听的音乐#', 0, '2023-10-12 11:56:37', 0);
INSERT INTO `wb_topic` VALUES (5, '#你喜欢游泳吗#', 0, '2023-10-12 11:56:50', 0);
INSERT INTO `wb_topic` VALUES (6, '#你最喜欢什么牌子的衣服#', 0, '2023-10-12 11:57:04', 0);
INSERT INTO `wb_topic` VALUES (7, '#你喜欢养花吗？#', 0, '2023-10-12 11:58:43', 0);
INSERT INTO `wb_topic` VALUES (8, '#你周末一般都喜欢干嘛？#', 0, '2023-10-12 11:59:25', 2);
INSERT INTO `wb_topic` VALUES (9, '#你最喜欢的一项运动#', 0, '2023-10-12 12:00:22', 0);
INSERT INTO `wb_topic` VALUES (10, '#如果这个时候变成超人，你最想做什么？#', 0, '2023-10-12 12:11:09', 0);
INSERT INTO `wb_topic` VALUES (11, '#你最喜欢哪种食物，你最喜欢哪种口味？#', 0, '2023-10-12 12:11:29', 1);
INSERT INTO `wb_topic` VALUES (12, '#你玩过最好玩的游戏#', 0, '2023-10-12 12:11:43', 0);
INSERT INTO `wb_topic` VALUES (13, '#你知道今年的流行语有什么？#', 0, '2023-10-12 12:14:05', 1);
INSERT INTO `wb_topic` VALUES (14, '#你最近去过的景点或旅游城市#', 0, '2023-10-12 12:15:11', 0);
INSERT INTO `wb_topic` VALUES (15, '#你最近尝试过的美食或饮料#', 0, '2023-10-12 12:15:23', 0);
INSERT INTO `wb_topic` VALUES (16, '#你最近遇到的有趣的人或事#', 0, '2023-10-12 12:15:32', 0);
INSERT INTO `wb_topic` VALUES (17, '#你最近追的电视剧或综艺节目#', 0, '2023-10-12 12:15:58', 0);
INSERT INTO `wb_topic` VALUES (18, '#你最近参加过的文化活动或体育比赛#', 0, '2023-10-12 12:16:46', 0);
INSERT INTO `wb_topic` VALUES (19, '#你最喜欢的旅游目的地是哪里#', 0, '2023-10-12 12:17:26', 0);
INSERT INTO `wb_topic` VALUES (20, '#你最喜欢的明星是谁#', 0, '2023-10-12 12:18:26', 0);
INSERT INTO `wb_topic` VALUES (21, '#女生喜欢你的暗示有什么#', 0, '2023-10-12 16:46:19', 14);
INSERT INTO `wb_topic` VALUES (22, '#致回不去的青春#', 0, '2023-10-12 16:49:57', 1);
INSERT INTO `wb_topic` VALUES (23, '#你的头像有特殊含义吗？#', 0, '2023-11-01 17:51:50', 0);

-- ----------------------------
-- Table structure for wb_user
-- ----------------------------
DROP TABLE IF EXISTS `wb_user`;
CREATE TABLE `wb_user`  (
  `uid` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nickname` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `username` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `blog` char(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `phone` bigint NULL DEFAULT NULL COMMENT '手机号',
  `password` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `head_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `head_image_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `sex` tinyint NOT NULL DEFAULT 0,
  `province` char(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `city` char(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `email` char(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `intro` varchar(210) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `ctime` bigint NOT NULL,
  `uptime` bigint NOT NULL COMMENT '更新时间',
  `message_sum` mediumint NOT NULL DEFAULT 0,
  `theme` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `fansnum` bigint NOT NULL DEFAULT 0 COMMENT '粉丝数',
  `follownum` bigint NOT NULL DEFAULT 0 COMMENT '关注数',
  `invisible` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '广场隐身1，默认0不隐身',
  `status` tinyint NOT NULL DEFAULT 0 COMMENT '状态0正常，1以上不正常',
  PRIMARY KEY (`uid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_user
-- ----------------------------

-- ----------------------------
-- Table structure for wb_user_collect
-- ----------------------------
DROP TABLE IF EXISTS `wb_user_collect`;
CREATE TABLE `wb_user_collect`  (
  `collect_id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `msg_id` bigint UNSIGNED NOT NULL,
  `fromuid` int UNSIGNED NOT NULL,
  `collect_time` datetime NOT NULL,
  `delete_time` bigint UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (`collect_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_user_collect
-- ----------------------------

-- ----------------------------
-- Table structure for wb_user_invite
-- ----------------------------
DROP TABLE IF EXISTS `wb_user_invite`;
CREATE TABLE `wb_user_invite`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `fromuid` int UNSIGNED NOT NULL DEFAULT 0,
  `touid` int UNSIGNED NOT NULL DEFAULT 0,
  `topuid` int NOT NULL DEFAULT 0 COMMENT '父集uid',
  `invite_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `invite_status` tinyint UNSIGNED NOT NULL DEFAULT 0,
  `create_time` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_user_invite
-- ----------------------------
