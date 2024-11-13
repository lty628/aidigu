-- pake https://aidigu.cn --name Aidigu --icon /Users/mac/Desktop/tmp/digu/aidigu.icns --hide-title-bar
INSERT INTO `wb_app` (`app_name`, `app_url`, `app_status`, `app_type`, `app_image`, `remind_key`, `app_config`, `open_type`, `create_time`, `update_time`) VALUES ('素材', '/tools/sourcematerial/list', 1, 1, '/static/tools/common/images/sourcematerial.jpeg', '', '{\"title\":\"素材\",\"shade\":0.8,\"closeBtn\":true,\"shadeClose\":false,\"area\":[\"70%\",\"80%\"],\"resize\":true,\"maxmin\":true,\"skin\":\"layui-layer-win10\",\"id\":\"app_18\",\"hideOnClose\":false,\"scrollbar\":false}', 0, '2024-01-30 11:16:10', '2024-02-06 11:29:54');

ALTER TABLE `aidigu_cn`.`wb_reminder` 
ADD COLUMN `status` tinyint UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态：0未读，1已读' AFTER `type`;
INSERT INTO wb_app
(app_name, app_url, app_status, app_type, app_image, remind_key, app_config, open_type, create_time, update_time)
VALUES('互动提醒', '/tools/notice/list', 1, 1, '/static/tools/common/images/notice.jpg', '', '{"title":"互动提醒","shade":0.8,"closeBtn":true,"shadeClose":true,"area":["80%","90%"],"resize":true,"maxmin":true,"skin":"layui-layer-win10","id":"app_47","hideOnClose":true,"scrollbar":false}', 0, '2024-01-30 11:16:10', '2024-10-10 10:17:48');
INSERT INTO wb_app
(app_name, app_url, app_status, app_type, app_image, remind_key, app_config, open_type, create_time, update_time)
VALUES('互动提醒', '/tools/notice/list', 1, 2, '/static/tools/common/images/notice.jpg', '', '{"title":"互动提醒","shade":0.8,"closeBtn":true,"shadeClose":false,"area":["100%","100%"],"resize":false,"maxmin":false,"skin":"layui-layer-win10","id":"app_48","hideOnClose":true,"scrollbar":false}', 0, '2024-01-30 11:16:10', '2024-10-10 10:17:48');