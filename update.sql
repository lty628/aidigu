ALTER TABLE `wb_message` 
CHANGE COLUMN `image` `media` varchar(255) NULL DEFAULT NULL AFTER `refrom`,
CHANGE COLUMN `image_info` `media_info` text NULL AFTER `media`;


------------chat-----------

CREATE TABLE `wb_chat_private_letter` (
  `chat_id` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `fromuid` int NOT NULL COMMENT '来源id',
  `touid` int NOT NULL COMMENT '目标id',
  `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL COMMENT '内容',
  `content_type` varchar(255) COLLATE utf8mb4_bin NOT NULL DEFAULT '' COMMENT '内容类型',
  `send_status` tinyint unsigned NOT NULL DEFAULT '0' COMMENT '发送状态',
  `create_time` datetime NOT NULL COMMENT '消息时间',
  PRIMARY KEY (`chat_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

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

CREATE TABLE `wb_chat_online` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uid` bigint unsigned NOT NULL,
  `fd` bigint unsigned NOT NULL,
  `online_time` bigint unsigned NOT NULL,
  `offline_time` bigint unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

------------chat-----------