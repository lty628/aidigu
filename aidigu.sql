
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for wb_admin_behavior_log
-- ----------------------------
DROP TABLE IF EXISTS `wb_admin_behavior_log`;
CREATE TABLE `wb_admin_behavior_log`  (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL COMMENT '用户ID',
  `module` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '模块名',
  `controller` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '控制器名',
  `action` varchar(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '方法名',
  `ip` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT 'IP地址',
  `content` varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL COMMENT '操作内容描述',
  `params` text CHARACTER SET utf8 COLLATE utf8_general_ci NULL COMMENT '操作参数JSON',
  `create_time` int(11) NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE,
  INDEX `idx_uid`(`uid`) USING BTREE,
  INDEX `idx_create_time`(`create_time`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8 COLLATE = utf8_general_ci COMMENT = '用户行为日志表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wb_admin_behavior_log
-- ----------------------------

-- ----------------------------
-- Table structure for wb_admin_system_setting
-- ----------------------------
DROP TABLE IF EXISTS `wb_admin_system_setting`;
CREATE TABLE `wb_admin_system_setting`  (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT '主键ID',
  `section` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置分组/节名，如app、database等',
  `key` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置键名',
  `value` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL COMMENT '配置值',
  `description` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL COMMENT '配置项描述',
  `created_at` datetime NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `updated_at` datetime NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `uk_section_key`(`section`, `key`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '系统配置表' ROW_FORMAT = Dynamic;


-- ----------------------------
-- Table structure for wb_app
-- ----------------------------
DROP TABLE IF EXISTS `wb_app`;
CREATE TABLE `wb_app`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `app_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '应用名称',
  `app_url` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '链接',
  `fromuid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建用户',
  `app_status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '0关闭，1站内，2站外',
  `app_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0全部，1pc,2手机',
  `app_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '图片地址',
  `remind_key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '提醒key',
  `app_config` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置',
  `open_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '打开方式，0frame,1直接打开，2新窗口打开',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Table structure for wb_badword
-- ----------------------------
DROP TABLE IF EXISTS `wb_badword`;
CREATE TABLE `wb_badword`  (
  `bid` int(11) NOT NULL AUTO_INCREMENT,
  `word` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ctype` tinyint(4) NOT NULL DEFAULT 0,
  PRIMARY KEY (`bid`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

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
  `dir_id` int(11) NOT NULL DEFAULT 0 COMMENT '目录ID',
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
  `dir_id` int(11) NOT NULL AUTO_INCREMENT,
  `parent_id` int(11) NOT NULL DEFAULT 0 COMMENT '父级ID',
  `uid` int(11) NOT NULL DEFAULT 0 COMMENT '用户ID',
  `dir_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '目录名称',
  `is_delete` tinyint(1) NOT NULL DEFAULT 0 COMMENT '是否删除',
  `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
  `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  PRIMARY KEY (`dir_id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci COMMENT = '文件目录表' ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of wb_file_dir
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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_message
-- ----------------------------

-- ----------------------------
-- Table structure for wb_password
-- ----------------------------
DROP TABLE IF EXISTS `wb_password`;
CREATE TABLE `wb_password`  (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建用户',
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
  `id` bigint(20) NOT NULL AUTO_INCREMENT,
  `msg_id` bigint(20) NULL DEFAULT NULL,
  `fromuid` bigint(20) NOT NULL,
  `touid` bigint(20) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态：0未读，1已读',
  `ctime` bigint(20) NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_reminder
-- ----------------------------

-- ----------------------------
-- Table structure for wb_search
-- ----------------------------
DROP TABLE IF EXISTS `wb_search`;
CREATE TABLE `wb_search`  (
  `search_id` int(11) NOT NULL AUTO_INCREMENT,
  `keyword` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '关键词',
  `uid` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人，默认0',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `count` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '搜索数量',
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
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `uid` bigint(20) UNSIGNED NOT NULL COMMENT '用户id',
  `title` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0' COMMENT '标题',
  `status` tinyint(4) NOT NULL DEFAULT 1 COMMENT '状态0删除，1正常',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `push_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '投送时间',
  `share_msg_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '内容id',
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
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'id',
  `media_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件名',
  `media_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '类型',
  `file_size` double NOT NULL COMMENT '文件大小',
  `file_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '文件名',
  `source_material_id` bigint(20) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否站内分享0未分享',
  `is_delete` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否删除',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

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
  `uptime` bigint(20) NOT NULL COMMENT '更新时间',
  `message_sum` mediumint(9) NOT NULL DEFAULT 0,
  `theme` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NULL DEFAULT NULL,
  `fansnum` bigint(20) NOT NULL DEFAULT 0 COMMENT '粉丝数',
  `follownum` bigint(20) NOT NULL DEFAULT 0 COMMENT '关注数',
  `invisible` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '广场隐身1，默认0不隐身',
  `status` tinyint(4) NOT NULL DEFAULT 0 COMMENT '状态0正常，1以上不正常',
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
  `collect_id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `msg_id` bigint(20) UNSIGNED NOT NULL,
  `fromuid` int(10) UNSIGNED NOT NULL,
  `collect_time` datetime NOT NULL,
  `delete_time` bigint(20) UNSIGNED NOT NULL DEFAULT 0,
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
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `fromuid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `touid` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `topuid` int(11) NOT NULL DEFAULT 0 COMMENT '父集uid',
  `invite_code` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `invite_status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0,
  `create_time` datetime NOT NULL ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

-- ----------------------------
-- Records of wb_user_invite
-- ----------------------------
-- ----------------------------
-- Records of wb_admin_system_setting
-- ----------------------------
INSERT INTO `wb_admin_system_setting` VALUES (1, 'app', 'staticDomain', '', '配置CDN域名加速使用', '2025-12-04 20:15:33', '2025-12-04 20:15:33');
INSERT INTO `wb_admin_system_setting` VALUES (2, 'app', 'beian', '', '填入备案号', '2025-12-04 20:15:55', '2025-12-04 20:15:55');
INSERT INTO `wb_admin_system_setting` VALUES (3, 'app', 'noRegister', '0', '是否开放注册', '2025-12-04 20:16:22', '2025-12-04 20:16:22');
INSERT INTO `wb_admin_system_setting` VALUES (4, 'app', 'pubIndex', '0', '首页是否无需登录【默认0，必须登录，可改为1】', '2025-12-04 20:17:45', '2025-12-04 20:17:45');
INSERT INTO `wb_admin_system_setting` VALUES (5, 'app', 'defaultIndex', '/', '首页默认地址,如设置 app\\tools\\controller\\Nav@index 后首页为导航', '2025-12-04 20:18:53', '2025-12-04 20:18:53');
INSERT INTO `wb_admin_system_setting` VALUES (6, 'app', 'picUploadSize', '4194304', '图片上传限制大小，默认： 4194304 【4M】', '2025-12-04 20:19:21', '2025-12-04 20:20:44');
INSERT INTO `wb_admin_system_setting` VALUES (7, 'app', 'fileUploadSize', '62914561', '文件上传限制大小，默认：62914561 【60M】', '2025-12-04 20:20:12', '2025-12-04 20:20:12');
INSERT INTO `wb_admin_system_setting` VALUES (8, 'app', 'openToolType', '0', '打开工具类型', '2025-12-04 20:21:22', '2025-12-04 20:21:22');
INSERT INTO `wb_admin_system_setting` VALUES (9, 'storage', 'type', 'File', '网盘存储配置【File或S3File】', '2025-12-04 20:23:22', '2025-12-04 20:23:22');
INSERT INTO `wb_admin_system_setting` VALUES (10, 's3Config', 'awsAccessKey', 'AccessKeyAccessKeyAccessKey', '对象存储的AccessKey', '2025-12-04 20:23:59', '2025-12-04 20:24:24');
INSERT INTO `wb_admin_system_setting` VALUES (11, 's3Config', 'awsSecretKey', 'awsSecretKeyawsSecretKeyawsSecret', '对象存储的awsSecretKey', '2025-12-04 20:24:45', '2025-12-04 20:31:51');
INSERT INTO `wb_admin_system_setting` VALUES (12, 's3Config', 'bucket', 'aidigu', '对象存储的桶名字', '2025-12-04 20:25:19', '2025-12-04 20:25:19');
INSERT INTO `wb_admin_system_setting` VALUES (13, 's3Config', 'endpoint', 'http://192.168.1.11/', '对象存储地址', '2025-12-04 20:25:45', '2025-12-04 20:25:45');
INSERT INTO `wb_admin_system_setting` VALUES (14, 's3Config', 'region', '中国华北一区', '对象存储的地区', '2025-12-04 20:26:26', '2025-12-04 20:26:47');
INSERT INTO `wb_admin_system_setting` VALUES (15, 'wechat', 'app_id', 'app_id', '微信公众号app_id【暂时无效】', '2025-12-04 20:27:32', '2025-12-04 20:27:32');
INSERT INTO `wb_admin_system_setting` VALUES (16, 'wechat', 'secret', 'secret', '微信公众号secret【暂时无效】', '2025-12-04 20:28:12', '2025-12-04 20:28:37');
INSERT INTO `wb_admin_system_setting` VALUES (17, 'wechat', 'token', 'token', '微信公众号token【暂时无效】', '2025-12-04 20:29:36', '2025-12-04 20:29:36');
INSERT INTO `wb_admin_system_setting` VALUES (18, 'wechat', 'aes_key', 'aes_key', '微信公众号aes_key【暂时无效】', '2025-12-04 20:30:05', '2025-12-04 20:30:05');
INSERT INTO `wb_admin_system_setting` VALUES (19, 'wechat', 'callback', '/examples/oauth_callback.php', '微信公众号callback【暂时无效】', '2025-12-04 20:30:45', '2025-12-04 20:30:45');

-- ----------------------------
-- Records of wb_app
-- ----------------------------
INSERT INTO `wb_app` VALUES (1, '我的云盘', '/cloud/show/', 0, 1, 1, '/static/tools/common/images/cloud.jpg', '', '{\"title\":\"我的云盘\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"80%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_1\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-02-06 16:35:23');
INSERT INTO `wb_app` VALUES (2, '嘀友聊天', '/chat', 0, 1, 1, '/static/tools/common/images/chat.jpg', 'chat', '{\"title\":\"嘀友聊天\",\"shade\":0,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"60%\",\"70%\"],\"resize\":true,\"maxmin\":true,\"id\":\"app_2\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-07-03 15:16:00');
INSERT INTO `wb_app` VALUES (3, '嘀咕影院', '/tools/movie', 0, 1, 1, '/static/tools/common/images/movie.jpg', '', '{\"title\":\"嘀咕影院\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"80%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_3\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-01-22 18:33:56');
INSERT INTO `wb_app` VALUES (4, 'BMI体重计算', '/tools/bmi', 0, 1, 1, '/static/tools/common/images/bmi.jpg', '', '{\"title\":\"BMI体重计算\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"80%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_5\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-01-22 18:33:56');
INSERT INTO `wb_app` VALUES (5, '我的云盘', '/cloud/show/', 0, 1, 2, '/static/tools/common/images/cloud.jpg', '', '{\"title\":\"我的云盘\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_8\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-01-22 18:46:07');
INSERT INTO `wb_app` VALUES (6, '嘀友聊天', '/chat', 0, 1, 2, '/static/tools/common/images/chat.jpg', 'chat', '{\"title\":\"嘀友聊天\",\"shade\":0,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"id\":\"app_9\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-07-03 15:16:04');
INSERT INTO `wb_app` VALUES (7, '嘀咕影院', '/tools/movie', 0, 1, 2, '/static/tools/common/images/movie.jpg', '', '{\"title\":\"嘀咕影院\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_10\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-01-22 18:46:07');
INSERT INTO `wb_app` VALUES (8, 'BMI体重计算', '/tools/bmi', 0, 1, 2, '/static/tools/common/images/bmi.jpg', '', '{\"title\":\"BMI体重计算\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_12\",\"hideOnClose\":true,\"scrollbar\":false}', 0, '2024-01-16 11:31:53', '2024-01-22 18:46:07');
INSERT INTO `wb_app` VALUES (9, '话题', '/topic/', 0, 1, 2, '/static/tools/common/images/topic.jpeg', '', '{}', 1, '2024-01-16 11:31:53', '2024-01-23 16:15:21');
INSERT INTO `wb_app` VALUES (10, '群管理', '/tools/chat/list', 0, 1, 1, '/static/tools/common/images/chatgroup.jpeg', '', '{\"title\":\"群管理\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"70%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_11\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-06 11:28:44');
INSERT INTO `wb_app` VALUES (11, '群管理', '/tools/chat/list', 0, 1, 2, '/static/tools/common/images/chatgroup.jpeg', '', '{\"title\":\"群管理\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_12\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-05 23:35:29');
INSERT INTO `wb_app` VALUES (12, '邀请码', '/tools/userinvite/list', 0, 1, 1, '/static/tools/common/images/userinvite.jpeg', '', '{\"title\":\"邀请码\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"70%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_13\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-06 11:28:47');
INSERT INTO `wb_app` VALUES (13, '邀请码', '/tools/userinvite/list', 0, 1, 2, '/static/tools/common/images/userinvite.jpeg', '', '{\"title\":\"邀请码\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_14\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-05 23:35:41');
INSERT INTO `wb_app` VALUES (14, '收藏管理', '/tools/collect/list', 0, 1, 1, '/static/tools/common/images/collect.jpeg', '', '{\"title\":\"收藏管理\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"70%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_15\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-06 11:29:54');
INSERT INTO `wb_app` VALUES (15, '收藏管理', '/tools/collect/list', 0, 1, 2, '/static/tools/common/images/collect.jpeg', '', '{\"title\":\"收藏管理\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_16\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-06 11:29:54');
INSERT INTO `wb_app` VALUES (16, '素材管理', '/tools/sourcematerial/list', 0, 1, 1, '/static/tools/common/images/sourcematerial.jpeg', '', '{\"title\":\"素材管理\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"70%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_18\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-03-06 14:59:48');
INSERT INTO `wb_app` VALUES (17, '素材管理', '/tools/sourcematerial/list', 0, 1, 2, '/static/tools/common/images/sourcematerial.jpeg', '', '{\"title\":\"素材管理\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_19\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-06 11:29:54');
INSERT INTO `wb_app` VALUES (18, '互动提醒', '/tools/notice/list', 0, 1, 1, '/static/tools/common/images/notice.jpg', 'message', '{\"title\":\"互动提醒\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":true,\"area\":[\"80%\",\"90%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_47\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-11-18 22:04:20');
INSERT INTO `wb_app` VALUES (19, '互动提醒', '/tools/notice/list', 0, 1, 2, '/static/tools/common/images/notice.jpg', 'message', '{\"title\":\"互动提醒\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_48\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-11-18 22:04:21');
INSERT INTO `wb_app` VALUES (20, '密码工具', '/tools/password/list', 0, 1, 1, '/static/tools/common/images/password.jpg', '', '{\"title\":\"密码工具\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":true,\"area\":[\"60%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_53\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2025-03-26 21:05:36');
INSERT INTO `wb_app` VALUES (21, '密码工具', '/tools/password/list', 0, 1, 2, '/static/tools/common/images/password.jpg', '', '{\"title\":\"密码工具\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"100%\",\"100%\"],\"resize\":false,\"maxmin\":false,\"skin\":\"layui-layer-win10\",\"id\":\"app_54\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2025-03-26 21:05:36');


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
INSERT INTO `wb_topic` VALUES (8, '#你周末一般都喜欢干嘛？#', 0, '2023-10-12 11:59:25', 0);
INSERT INTO `wb_topic` VALUES (9, '#你最喜欢的一项运动#', 0, '2023-10-12 12:00:22', 0);
INSERT INTO `wb_topic` VALUES (10, '#如果这个时候变成超人，你最想做什么？#', 0, '2023-10-12 12:11:09', 0);
INSERT INTO `wb_topic` VALUES (11, '#你最喜欢哪种食物，你最喜欢哪种口味？#', 0, '2023-10-12 12:11:29', 0);
INSERT INTO `wb_topic` VALUES (12, '#你玩过最好玩的游戏#', 0, '2023-10-12 12:11:43', 0);
INSERT INTO `wb_topic` VALUES (13, '#你知道今年的流行语有什么？#', 0, '2023-10-12 12:14:05', 0);
INSERT INTO `wb_topic` VALUES (14, '#你最近去过的景点或旅游城市#', 0, '2023-10-12 12:15:11', 0);
INSERT INTO `wb_topic` VALUES (15, '#你最近尝试过的美食或饮料#', 0, '2023-10-12 12:15:23', 0);
INSERT INTO `wb_topic` VALUES (16, '#你最近遇到的有趣的人或事#', 0, '2023-10-12 12:15:32', 0);
INSERT INTO `wb_topic` VALUES (17, '#你最近追的电视剧或综艺节目#', 0, '2023-10-12 12:15:58', 0);
INSERT INTO `wb_topic` VALUES (18, '#你最近参加过的文化活动或体育比赛#', 0, '2023-10-12 12:16:46', 0);
INSERT INTO `wb_topic` VALUES (19, '#你最喜欢的旅游目的地是哪里#', 0, '2023-10-12 12:17:26', 0);
INSERT INTO `wb_topic` VALUES (20, '#你最喜欢的明星是谁#', 0, '2023-10-12 12:18:26', 0);
INSERT INTO `wb_topic` VALUES (21, '#女生喜欢你的暗示有什么#', 0, '2023-10-12 16:46:19', 0);
INSERT INTO `wb_topic` VALUES (22, '#致回不去的青春#', 0, '2023-10-12 16:49:57', 0);
INSERT INTO `wb_topic` VALUES (23, '#你的头像有特殊含义吗？#', 0, '2023-11-01 17:51:50', 0);


SET FOREIGN_KEY_CHECKS = 1;
