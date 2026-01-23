
DROP TABLE IF EXISTS `wb_cms_category`;
CREATE TABLE `wb_cms_category` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `category_name` varchar(255) NOT NULL COMMENT '分类名称',
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `wb_cms_category` VALUES ('1', '资讯');
INSERT INTO `wb_cms_category` VALUES ('2', '求助');
INSERT INTO `wb_cms_category` VALUES ('3', '日志');
INSERT INTO `wb_cms_category` VALUES ('4', '分享');


DROP TABLE IF EXISTS `wb_cms_content`;
CREATE TABLE `wb_cms_content` (
  `content_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `category_id` int(11) NOT NULL COMMENT '分类id',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` longtext NOT NULL COMMENT '内容',
  `content_filtered` longtext NOT NULL COMMENT '内容过滤',
  `content_extra` text NOT NULL COMMENT '扩展内容',
  `commentsum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `likesum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `viewsum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览数',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

