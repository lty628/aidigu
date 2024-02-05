ALTER TABLE `wb_chat_group` 
ADD COLUMN `fromuid` int UNSIGNED NOT NULL DEFAULT 0 AFTER `city`,
ADD COLUMN `invite_code` varchar(255) NOT NULL DEFAULT '' AFTER `fromuid`;
ADD COLUMN `invite_status` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否开启加群' AFTER `invite_code`;
ADD COLUMN `utime` bigint UNSIGNED NULL AFTER `ctime`;