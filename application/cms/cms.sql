
DROP TABLE IF EXISTS `wb_cms_category`;
CREATE TABLE `wb_cms_category` (
  `category_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `category_name` varchar(255) NOT NULL COMMENT '分类名称',
  `style` varchar(255) NOT NULL COMMENT '风格',
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4;


INSERT INTO `wb_cms_category` VALUES ('1', '分类一', 'list');
INSERT INTO `wb_cms_category` VALUES ('2', '分类二', 'flex');
INSERT INTO `wb_cms_category` VALUES ('3', '分类三', 'flex');
INSERT INTO `wb_cms_category` VALUES ('4', '分类四', 'list');


DROP TABLE IF EXISTS `wb_cms_content`;
CREATE TABLE `wb_cms_content` (
  `content_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `category_id` int(11) NOT NULL COMMENT '分类id',
  `title` varchar(255) NOT NULL COMMENT '标题',
  `content` longtext NOT NULL COMMENT '内容',
  `content_filtered` longtext NOT NULL COMMENT '内容过滤',
  `content_extra` text NOT NULL COMMENT '扩展内容',
  `comment_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '评论数',
  `like_num` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '点赞数',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `update_time` datetime NOT NULL COMMENT '更新时间',
  PRIMARY KEY (`content_id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4;


DROP TABLE IF EXISTS `wb_email`;
CREATE TABLE `wb_email` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `event` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '事件',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '邮箱',
  `code` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT '验证码',
  `times` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '验证次数',
  `ip` varchar(30) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'IP',
  `createtime` int(10) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='邮箱验证码表';


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
  `create_time` datetime NOT NULL COMMENT '创建时间',
  PRIMARY KEY (`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;