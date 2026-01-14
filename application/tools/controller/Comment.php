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
        $sql3 = "ALTER TABLE `wb_comment` ADD COLUMN `reply_count` int(11) NOT NULL DEFAULT 0 COMMENT '回复数' AFTER `touid`;";
        $sql4 = "ALTER TABLE `wb_channel_comment` ADD COLUMN `reply_count` int(11) NOT NULL DEFAULT 0 COMMENT '回复数' AFTER `touid`;";
        // 修改评论表增加用于直接查询的relation_reply_id varchar(255)
        $sql5 = "ALTER TABLE `wb_comment` ADD COLUMN `relation_reply_id` varchar(255) NULL DEFAULT '' COMMENT '关联回复ID,多个用逗号分隔' AFTER `touid`;";
        $sql6 = "ALTER TABLE `wb_channel_comment` ADD COLUMN `relation_reply_id` varchar(255) NULL DEFAULT '' COMMENT '关联回复ID,多个用逗号分隔' AFTER `touid`;";
        Db::query($sql1);
        Db::query($sql2);
        Db::query($sql3);
        Db::query($sql4);
        Db::query($sql5);
        Db::query($sql6);
    }
    public function index()
    {
        $type = input('type');
        $this->assign('type', $type);
        return $this->fetch('index');
    }

    public function getCommentList()
    {
        // 获取评论 每条评论获取10个回复
        $msgId = input('msg_id');
        $type = input('type');
        $limit = input('limit', 10);
        $page = input('page', 1);
        $order = input('order', 'desc'); // 按热度，按时间降序
        
        // 参数验证
        // if (empty($msgId)) {
        //     return json(['code' => 400, 'msg' => 'msg_id不能为空']);
        // }
        
        // 确定排序方式
        $orderField = $order == 'hot' ? 'reply_count' : 'ctime';
        $orderDirection = $order == 'asc' ? 'asc' : 'desc';
        
        // 计算偏移量
        $offset = ($page - 1) * $limit;
        
        // 根据类型获取评论
        if ($type == 'channel') {
            $commentTable = 'channel_comment';
            $replyTable = 'channel_comment_reply';
        } else {
            $commentTable = 'comment';
            $replyTable = 'comment_reply';
        }
        
        // 获取评论列表
        $list = Db::name($commentTable)
            // ->where('msg_id', $msgId)
            ->order($orderField, $orderDirection)
            ->limit($offset, $limit)
            ->select();
        if (empty($list)) {
            return json()->data([
                'code' => 200,
                'msg' => '获取成功',
                'data' => [
                    'list' => [],
                    'total' => 0,
                ],
            ]);
        }

        $uidArr = array_column($list, 'fromuid');
            
        // 获取所有评论的ID
        $commentIds = array_column($list, 'relation_reply_id');
        $commentIdArr = [];
        if ($commentIds) {
            foreach ($commentIds as $commentId) {
                $commentIdArr = array_merge($commentIdArr, explode(',', $commentId));
            }
        }
        $commentIdArr = array_filter($commentIdArr);
        // 初始化回复列表数组
        $replyList = [];
        if ($commentIdArr) {
            $uidArr = array_merge($uidArr, array_column($commentIdArr, 'fromuid'));
            // 获取每条评论的回复（最多10条）
            $replies = Db::name($replyTable)
                ->whereIn('cid', $commentIdArr)
                ->order('ctime', 'desc')
                ->select();
            // 按评论ID分组回复
            foreach ($replies as $reply) {
                $commentId = $reply['msg_id'];
                
                // 如果该评论的回复数量未达到限制，则添加到回复列表
                if (!isset($replyList[$commentId]) || count($replyList[$commentId]) < 10) {
                    $replyList[$commentId][] = $reply;
                }
            }

        }
        
        foreach ($list as $key => $comment) {
            $commentId = $comment['cid'];
            $list[$key]['replies'] = $replyList[$commentId] ?? [];
        }
        $userList = Db::name('user')->field('uid, nickname, blog, head_image')->whereIn('uid', $uidArr)->select();
        $userList = array_column($userList, null, 'uid');
        // 获取总数
        $total = Db::name($commentTable)->where('msg_id', $msgId)->count();
        
        // 返回结果
        return json([
            'code' => 200,
            'msg' => '获取成功',
            'data' => [
                'list' => $list,
                'userList' => $userList,
                'total' => $total,
                'page' => $page,
                'limit' => $limit
            ]
        ]);
    }

    public function getReplyList()
    {
        // 获取回复列表
        $msgId = input('msg_id');
        $type = input('type');
        $limit = input('limit', 10);
        $page = input('page', 1);
        $order = input('order', 'desc'); // 按热度，按时间降序
        // 验证参数
        if (empty($msgId)) {
            return json(['code' => 400, 'msg' => 'msg_id不能为空']);
        }
        // 确定排序方式
        $orderField = $order == 'hot' ? 'reply_count' : 'ctime';
        $orderDirection = $order == 'asc' ? 'asc' : 'desc';
        // 计算偏移量
        $offset = ($page - 1) * $limit;
        // 根据类型获取回复
        if ($type == 'channel') {
            $replyTable = 'channel_comment_reply';
        } else {
            $replyTable = 'comment_reply';
        }
        
        // 获取回复列表
        $list = Db::name($replyTable)
            ->where('msg_id', $msgId)
            ->order($orderField, $orderDirection)
            ->limit($offset, $limit)
            ->select();
        if (empty($list)) {
            return json()->data([
                'code' => 200,
                'msg' => '获取成功',
                'data' => [
                    'list' => [],
                    'total' => 0,
                ],
            ]);
        }
        $uidArr = array_column($list, 'fromuid');   
        $userList = Db::name('user')->field('uid, nickname, blog, head_image')->whereIn('uid', $uidArr)->select();
        $userList = array_column($userList, null, 'uid');
        return json([
            'code' => 200,
            'msg' => '获取成功',
            'data' => [
                'list' => $list,
                'userList' => $userList,
                'total' => Db::name($replyTable)->where('msg_id', $msgId)->count(),
                'page' => $page,
                'limit' => $limit
            ]
        ]);
        
    }

    public function addComment() {}

    public function addReply() {}
}