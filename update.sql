ALTER TABLE `wb_message` 
CHANGE COLUMN `image` `media` varchar(255) NULL DEFAULT NULL AFTER `refrom`,
CHANGE COLUMN `image_info` `media_info` text NULL AFTER `media`;


------------chat-----------

CREATE TABLE `wb_chat_friends` (
  `fansid` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fromuid` bigint NOT NULL,
  `touid` bigint NOT NULL,
  `mutual_concern` tinyint NOT NULL DEFAULT '0',
  `message_count` int unsigned NOT NULL DEFAULT '0',
  `ctime` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`fansid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Table structure for wb_chat_friends_history
-- ----------------------------

CREATE TABLE `wb_chat_friends_history` (
  `chat_id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `fromuid` int NOT NULL COMMENT '来源id',
  `touid` int NOT NULL COMMENT '目标id',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '内容',
  `content_type` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '内容类型',
  `send_status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '发送状态',
  `create_time` datetime NOT NULL COMMENT '消息时间',
  PRIMARY KEY (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Table structure for wb_chat_group
-- ----------------------------

CREATE TABLE `wb_chat_group` (
  `groupid` bigint unsigned NOT NULL AUTO_INCREMENT,
  `groupname` char(11) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT '',
  `head_image` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT '',
  `head_image_info` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT '',
  `city` char(25) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT '',
  `intro` varchar(210) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT '',
  `ctime` bigint DEFAULT NULL,
  `usernum` int NOT NULL DEFAULT '0' COMMENT '粉丝数',
  PRIMARY KEY (`groupid`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Table structure for wb_chat_group_history
-- ----------------------------

CREATE TABLE `wb_chat_group_history` (
  `chat_id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `fromuid` int NOT NULL COMMENT '来源id',
  `groupid` int NOT NULL COMMENT '目标id',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '内容',
  `content_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '内容类型',
  `create_time` datetime NOT NULL COMMENT '消息时间',
  PRIMARY KEY (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Table structure for wb_chat_group_user
-- ----------------------------

CREATE TABLE `wb_chat_group_user` (
  `fansid` bigint unsigned NOT NULL AUTO_INCREMENT,
  `groupid` bigint NOT NULL,
  `uid` bigint NOT NULL,
  `message_count` int unsigned NOT NULL DEFAULT '0',
  `ctime` bigint NOT NULL DEFAULT '0' COMMENT '加入时间',
  `dtime` bigint unsigned NOT NULL DEFAULT '0' COMMENT '删除时间',
  PRIMARY KEY (`fansid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Table structure for wb_chat_online
-- ----------------------------

CREATE TABLE `wb_chat_online` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint unsigned NOT NULL,
  `fd` bigint unsigned NOT NULL,
  `online_time` bigint unsigned NOT NULL,
  `offline_time` bigint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Table structure for wb_chat_private_letter
-- ----------------------------

CREATE TABLE `wb_chat_private_letter` (
  `fansid` bigint unsigned NOT NULL AUTO_INCREMENT,
  `fromuid` bigint NOT NULL,
  `touid` bigint NOT NULL,
  `mutual_concern` tinyint NOT NULL DEFAULT '0',
  `message_count` int unsigned NOT NULL DEFAULT '0',
  `ctime` bigint NOT NULL DEFAULT '0',
  PRIMARY KEY (`fansid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

-- ----------------------------
-- Table structure for wb_chat_private_letter_history
-- ----------------------------

CREATE TABLE `wb_chat_private_letter_history` (
  `chat_id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `fromuid` int NOT NULL COMMENT '来源id',
  `touid` int NOT NULL COMMENT '目标id',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '内容',
  `content_type` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '内容类型',
  `send_status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '发送状态',
  `create_time` datetime NOT NULL COMMENT '消息时间',
  PRIMARY KEY (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

ALTER TABLE `wb_chat_friends` 
ADD COLUMN `utime` bigint UNSIGNED NOT NULL DEFAULT 0 AFTER `ctime`;
ALTER TABLE `wb_chat_group_user` 
ADD COLUMN `utime` bigint UNSIGNED NOT NULL DEFAULT 0 COMMENT '更新时间' AFTER `dtime`;
ALTER TABLE `wb_chat_private_letter` 
ADD COLUMN `utime` bigint UNSIGNED NOT NULL DEFAULT 0 AFTER `ctime`;

------------chat-----------