
DROP TABLE IF EXISTS `wb_cms_attachment`;
CREATE TABLE `wb_cms_attachment` (
  `attach_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `category_id` int(10) unsigned NOT NULL COMMENT '分类id',
  `attach_path` varchar(255) NOT NULL COMMENT '文件地址',
  `attach_title` varchar(255) NOT NULL COMMENT '标题',
  `attach_content` longtext COMMENT '附件内容',
  `attach_content_filtered` longtext,
  `attach_size` double unsigned NOT NULL DEFAULT '0' COMMENT '附件大小（字节）',
  `download_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '下载次数',
  `read_count` int(11) NOT NULL DEFAULT '0' COMMENT '阅读次数',
  `status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '状态（0：未审核，1:审核通过）',
  `finish_status` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT '是否完成转换 默认0 为转换， 1 已转换',
  `create_time` datetime NOT NULL COMMENT '上传时间',
  PRIMARY KEY (`attach_id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8mb4;


INSERT INTO `wb_cms_attachment` VALUES ('71', '2', '3', '/uploads/20211015/761627721fa07b55625ba4f9f4b3e12c.pdf', 'asdf', '<div class=\"markdown-body editormd-preview-container\" previewcontainer=\"true\" style=\"padding: 20px;\"><p>阿斯顿发顺丰</p>\n</div>', '阿斯顿发顺丰', '0', '0', '0', '0', '0', '2021-10-15 16:04:11');
INSERT INTO `wb_cms_attachment` VALUES ('72', '2', '5', '', '饿啊分噶士大夫', '<div class=\"markdown-body editormd-preview-container\" previewcontainer=\"true\" style=\"padding: 20px;\"><p><strong>sadf</strong></p>\n<h1 id=\"h1-asdfasdf\"><a name=\"asdfasdf\" class=\"reference-link\"></a><span class=\"header-link octicon octicon-link\"></span>asdfasdf</h1><pre class=\"prettyprint linenums prettyprinted\" style=\"\"><ol class=\"linenums\"><li class=\"L0\"><code class=\"lang-php\"><span class=\"pun\">&lt;?</span><span class=\"pln\">php</span></code></li><li class=\"L1\"><code class=\"lang-php\"><span class=\"pln\">echo </span><span class=\"str\">\"hellow world!\"</span></code></li></ol></pre>\n</div>', '**sadf**\n# asdfasdf\n```php\n<?php\necho \"hellow world!\"\n```', '0', '0', '0', '0', '0', '2021-10-15 16:21:18');
INSERT INTO `wb_cms_attachment` VALUES ('73', '2', '5', '', '饿啊分噶士大夫', '<div class=\"markdown-body editormd-preview-container\" previewcontainer=\"true\" style=\"padding: 20px;\"><p><strong>sadf</strong></p>\n<h1 id=\"h1-asdfasdf\"><a name=\"asdfasdf\" class=\"reference-link\"></a><span class=\"header-link octicon octicon-link\"></span>asdfasdf</h1><pre class=\"prettyprint linenums prettyprinted\" style=\"\"><ol class=\"linenums\"><li class=\"L0\"><code class=\"lang-php\"><span class=\"pun\">&lt;?</span><span class=\"pln\">php</span></code></li><li class=\"L1\"><code class=\"lang-php\"><span class=\"pln\">echo </span><span class=\"str\">\"hellow world!\"</span></code></li></ol></pre>\n<p><del>asdfasdsadfasdfasdfasdfasd阿斯蒂芬噶士大夫</del></p>\n<ul>\n<li>萨芬<br>阿斯蒂芬</li><li>阿斯顿发射点<br>| 阿斯蒂芬  |  阿斯蒂芬  |<br>| —————— | —————— |<br>|  aaaa | adsfdf   |<br>| asdfasf   |  asdf  |</li></ul>\n</div>', '**sadf**\n# asdfasdf\n```php\n<?php\necho \"hellow world!\"\n```\n\n~~asdfasdsadfasdfasdfasdfasd阿斯蒂芬噶士大夫~~\n\n- 萨芬\n阿斯蒂芬\n- 阿斯顿发射点\n| 阿斯蒂芬  |  阿斯蒂芬  |\n| ------------ | ------------ |\n|  aaaa | adsfdf   |\n| asdfasf   |  asdf  |\n', '0', '0', '0', '0', '0', '2021-10-15 16:22:20');
INSERT INTO `wb_cms_attachment` VALUES ('74', '2', '5', '', '饿啊分噶士大夫', '<div class=\"markdown-body editormd-preview-container\" previewcontainer=\"true\" style=\"padding: 20px;\"><p><strong>sadf</strong></p>\n<h1 id=\"h1-asdfasdf\"><a name=\"asdfasdf\" class=\"reference-link\"></a><span class=\"header-link octicon octicon-link\"></span>asdfasdf</h1><pre class=\"prettyprint linenums prettyprinted\" style=\"\"><ol class=\"linenums\"><li class=\"L0\"><code class=\"lang-php\"><span class=\"pun\">&lt;?</span><span class=\"pln\">php</span></code></li><li class=\"L1\"><code class=\"lang-php\"><span class=\"pln\">echo </span><span class=\"str\">\"hellow world!\"</span></code></li></ol></pre>\n<p><del>asdfasdsadfasdfasdfasdfasd阿斯蒂芬噶士大夫</del></p>\n<ul>\n<li>萨芬<br>阿斯蒂芬</li><li>阿斯顿发射点</li></ul>\n<table>\n<thead>\n<tr>\n<th>阿斯弗</th>\n<th>阿斯蒂芬</th>\n</tr>\n</thead>\n<tbody>\n<tr>\n<td>aaa</td>\n<td>aaaaa</td>\n</tr>\n<tr>\n<td>asdfasdf</td>\n<td>阿斯蒂芬阿斯蒂芬</td>\n</tr>\n</tbody>\n</table>\n</div>', '**sadf**\n# asdfasdf\n```php\n<?php\necho \"hellow world!\"\n```\n\n~~asdfasdsadfasdfasdfasdfasd阿斯蒂芬噶士大夫~~\n\n- 萨芬\n阿斯蒂芬\n- 阿斯顿发射点\n\n|  阿斯弗 |  阿斯蒂芬 |\n| ------------ | ------------ |\n|  aaa | aaaaa  |\n|  asdfasdf  |  阿斯蒂芬阿斯蒂芬 |\n', '0', '0', '0', '0', '0', '2021-10-15 16:23:12');


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


DROP TABLE IF EXISTS `wb_cms_comment`;
CREATE TABLE `wb_cms_comment` (
  `comment_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '评论id',
  `content_id` int(10) unsigned NOT NULL COMMENT '文章id',
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `comment` varchar(255) NOT NULL COMMENT '消息',
  `create_time` datetime NOT NULL,
  `delete_time` datetime NOT NULL COMMENT '删除时间',
  PRIMARY KEY (`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=436 DEFAULT CHARSET=utf8mb4;


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



DROP TABLE IF EXISTS `wb_cms_reply`;
CREATE TABLE `wb_cms_reply` (
  `reply_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '回复id',
  `comment_id` int(10) unsigned NOT NULL COMMENT '关联评论id',
  `uid` int(10) unsigned NOT NULL COMMENT '用户id',
  `reply` varchar(255) NOT NULL COMMENT '回复内容',
  `create_time` datetime NOT NULL COMMENT '回复时间',
  PRIMARY KEY (`reply_id`)
) ENGINE=InnoDB AUTO_INCREMENT=250 DEFAULT CHARSET=utf8mb4;

INSERT INTO `wb_cms_reply` VALUES ('1', '1', '1', '1dasdcfasdfasdf', '2021-09-23 16:22:25');


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



DROP TABLE IF EXISTS `wb_sms`;
CREATE TABLE `wb_sms` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `phone` bigint(20) unsigned NOT NULL COMMENT '手机号',
  `code` varchar(255) NOT NULL COMMENT '验证码',
  `create_time` datetime NOT NULL COMMENT '创建时间',
  `expire_time` datetime NOT NULL COMMENT '过期时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4;

