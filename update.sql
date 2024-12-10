-- pake https://aidigu.cn --name Aidigu --icon /Users/mac/Desktop/tmp/digu/aidigu.icns --hide-title-bar
INSERT INTO `wb_app` (`app_name`, `app_url`, `app_status`, `app_type`, `app_image`, `remind_key`, `app_config`, `open_type`, `create_time`, `update_time`) VALUES ('素材', '/tools/sourcematerial/list', 1, 1, '/static/tools/common/images/sourcematerial.jpeg', '', '{\"title\":\"素材\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"70%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_18\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-06 11:29:54');

ALTER TABLE `wb_reminder` 
ADD COLUMN `status` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态：0未读，1已读' AFTER `type`;
ALTER TABLE wb_comment ADD ctype TINYINT DEFAULT 0 NOT NULL COMMENT '回复类型';
INSERT INTO wb_app
(app_name, app_url, app_status, app_type, app_image, remind_key, app_config, open_type, create_time, update_time)
VALUES('互动提醒', '/tools/notice/list', 1, 1, '/static/tools/common/images/notice.jpg', '', '{"title":"互动提醒","shade":0.8,"closeBtn":true,"shadeClose":true,"area":["80%","90%"],"resize":true,"maxmin":true,"skin":"layui-layer-win10","id":"app_47","hideOnClose":true,"scrollbar":false}', 0, '2024-01-30 11:16:10', '2024-10-10 10:17:48');
INSERT INTO wb_app
(app_name, app_url, app_status, app_type, app_image, remind_key, app_config, open_type, create_time, update_time)
VALUES('互动提醒', '/tools/notice/list', 1, 2, '/static/tools/common/images/notice.jpg', '', '{"title":"互动提醒","shade":0.8,"closeBtn":true,"shadeClose":false,"area":["100%","100%"],"resize":false,"maxmin":false,"skin":"layui-layer-win10","id":"app_48","hideOnClose":true,"scrollbar":false}', 0, '2024-01-30 11:16:10', '2024-10-10 10:17:48');


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
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = Dynamic;