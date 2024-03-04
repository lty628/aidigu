ALTER TABLE `wb_chat_group` 
ADD COLUMN `fromuid` int UNSIGNED NOT NULL DEFAULT 0 AFTER `city`,
ADD COLUMN `invite_code` varchar(255) NOT NULL DEFAULT '' AFTER `fromuid`;
ADD COLUMN `invite_status` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '是否开启加群' AFTER `invite_code`;
ADD COLUMN `utime` bigint UNSIGNED NULL AFTER `ctime`;

ALTER TABLE `wb_topic` 
ADD COLUMN `uid` bigint(0) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建人，默认0' AFTER `title`;

ALTER TABLE `wb_message` 
ADD COLUMN `collectsum` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '收藏点赞数' AFTER `commentsum`;

INSERT INTO `wb_app` (`app_name`, `app_url`, `app_status`, `app_type`, `app_image`, `remind_key`, `app_config`, `open_type`, `create_time`, `update_time`) VALUES ('收藏管理', '/tools/sourcematerial/list', 1, 1, '/static/tools/common/images/sourcematerial.jpeg', '', '{\"title\":\"素材\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"70%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_18\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-06 11:29:54');