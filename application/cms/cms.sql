

DROP TABLE IF EXISTS `wb_cms_category`;
CREATE TABLE `wb_cms_category` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `category_name` varchar(255) NOT NULL COMMENT '分类名称',
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


INSERT INTO `wb_cms_category` VALUES ('1', '1', '资讯');
INSERT INTO `wb_cms_category` VALUES ('2', '2', '求助');
INSERT INTO `wb_cms_category` VALUES ('3', '3', '日志');
INSERT INTO `wb_cms_category` VALUES ('4', '4', '分享');


DROP TABLE IF EXISTS `wb_cms_content`;
CREATE TABLE `wb_cms_content` (
  `content_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `category_id` int(11) NOT NULL COMMENT '分类id',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` longtext NOT NULL COMMENT '内容',
  `content_filtered` longtext NOT NULL COMMENT '内容过滤，简介',
  `content_extra` text NOT NULL COMMENT '扩展内容',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态 1:正常 0:禁用',
  `commentsum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `likesum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `viewsum` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '浏览数',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`content_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



DROP TABLE IF EXISTS `wb_cms_link_category`;
CREATE TABLE `wb_cms_link_category` (
  `link_category_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '分类id',
  `category_name` varchar(255) NOT NULL COMMENT '分类名称',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  PRIMARY KEY (`link_category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `wb_cms_link`;
CREATE TABLE `wb_cms_link` (
  `link_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '链接id',
  `link_category_id` int(10) unsigned NOT NULL COMMENT '分类id',
  `site_name` varchar(255) NOT NULL COMMENT '网站名称',
  `site_url` varchar(255) NOT NULL COMMENT '网站地址',
  `site_logo` varchar(255) DEFAULT NULL COMMENT '网站LOGO',
  `description` varchar(255) DEFAULT NULL COMMENT '网站描述',
  `sort_order` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '排序',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '状态 1:正常 0:禁用',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;