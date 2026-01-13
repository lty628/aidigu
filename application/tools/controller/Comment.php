<?php

namespace app\tools\controller;

use think\Controller;
use think\Db;
// CREATE TABLE `wb_channel_comment`  (
//   `cid` bigint(20) NOT NULL AUTO_INCREMENT,
//   `fromuid` bigint(20) NOT NULL,
//   `msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
//   `msg_id` bigint(20) NOT NULL,
//   `touid` mediumint(9) NULL DEFAULT NULL,
//   `ctime` int(11) NOT NULL,
//   `ctype` tinyint(4) NOT NULL DEFAULT 0 COMMENT '回复类型',
//   PRIMARY KEY (`cid`) USING BTREE
// ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

// CREATE TABLE `wb_comment`  (
//   `cid` bigint(20) NOT NULL AUTO_INCREMENT,
//   `fromuid` bigint(20) NOT NULL,
//   `msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
//   `msg_id` bigint(20) NOT NULL,
//   `touid` mediumint(9) NULL DEFAULT NULL,
//   `ctime` int(11) NOT NULL,
//   `ctype` tinyint(4) NOT NULL DEFAULT 0 COMMENT '回复类型',
//   PRIMARY KEY (`cid`) USING BTREE
// ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;


class Comment extends Controller
{
    public function createReplyTable()
    {
        $sql1 = "CREATE TABLE IF NOT EXISTS `wb_channel_comment_reply`  (
            `cid` bigint(20) NOT NULL AUTO_INCREMENT,
            `fromuid` bigint(20) NOT NULL,
            `msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
            `msg_id` bigint(20) NOT NULL,
            `touid` mediumint(9) NULL DEFAULT NULL,
            `ctime` int(11) NOT NULL,
            PRIMARY KEY (`cid`) USING BTREE
            ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;";
        $sql2 = "CREATE TABLE IF NOT EXISTS `wb_comment_reply`  (
            `cid` bigint(20) NOT NULL AUTO_INCREMENT,
            `fromuid` bigint(20) NOT NULL,
            `msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
            `msg_id` bigint(20) NOT NULL,
            `touid` mediumint(9) NULL DEFAULT NULL,
            `ctime` int(11) NOT NULL,  
            PRIMARY KEY (`cid`) USING BTREE
            ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;";
        // 修改评论表增加回复数
        $sql3 = "ALTER TABLE `wb_comment` ADD COLUMN `reply_count` int(11) NOT NULL DEFAULT 0 COMMENT '回复数' AFTER `ctime`;";
        $sql4 = "ALTER TABLE `wb_channel_comment` ADD COLUMN `reply_count` int(11) NOT NULL DEFAULT 0 COMMENT '回复数' AFTER `ctime`;";
        Db::query($sql1);
        Db::query($sql2);
        Db::query($sql3);
        Db::query($sql4);
    }
    public function index()
    {
        return $this->fetch('index');
    }

    public function getCommentList()
    {
        // 获取评论 每条评论获取5个回复
        $msgId = input('msg_id');
        $type = input('type');
        $limit = input('limit', 10);
        $page = input('page', 1);
        $order = input('order', 'desc'); // 按热度，按时间降序
        $replyLimit = input('reply_limit', 10);
        if ($type == 'channel') {
            $list = Db::name('channel_comment')->where('msg_id', $msgId)->select();
            $replyList = Db::name('channel_comment_reply')->where('msg_id', $msgId)->select();
        } else {
            $list = Db::name('comment')->where('msg_id', $msgId)->select();
            $replyList = Db::name('comment_reply')->where('msg_id', $msgId)->select();
        }
    }

    public function getReplyList()
    { 
    }

    public function addComment() {}

    public function addReply() {}
}
