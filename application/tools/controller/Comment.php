<?php

namespace app\tools\controller;

use think\Controller;
use think\Db;
use app\common\helper\Reminder;

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
// 删除评论
// ALTER TABLE `wb_channel_comment` DROP `ctype`;
// ALTER TABLE `wb_comment` DROP `ctype`;
class Comment extends Controller
{
    // $type  1: 微博评论 2: 微博回复，3频道评论，4频道回复 5: 好友关注 6: 取消关注  7: 提到@了您 8收藏了微博， 9收藏了频道微博
    protected $typeRelationArr = [
        'default' => [
            'table' => 'comment',
            'reply_table' => 'comment_reply',
            'content_table' => 'message',
            'type' => 'default',
            'comment_type' => 1,
            'rely_type' => 2,
        ],
        'message' => [
            'table' => 'comment',
            'reply_table' => 'comment_reply',
            'content_table' => 'message',
            'type' => 'message',
            'comment_type' => 1,
            'rely_type' => 2,
        ],
        'channel' => [
            'table' => 'channel_comment',
            'reply_table' => 'channel_comment_reply',
            'content_table' => 'channel_message',
            'type' => 'channel',
            'comment_type' => 3,
            'rely_type' => 4,
        ]
    ];
    // 创建回复表
    public function createReplyTable()
    {
        $sql1 = "DROP TABLE IF EXISTS `wb_channel_comment_reply`; CREATE TABLE IF NOT EXISTS `wb_channel_comment_reply`  (
            `rid` bigint(20) NOT NULL AUTO_INCREMENT,
            `fromuid` bigint(20) NOT NULL,
            `msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL comment '回复内容',
            `msg_id` bigint(20) NOT NULL comment '关联内容msgid',
            `cid` bigint(20) NOT NULL,
            `touid` mediumint(9) NULL DEFAULT NULL,
            `ctime` int(11) NOT NULL,
            PRIMARY KEY (`rid`) USING BTREE
            ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;";
        $sql2 = "DROP TABLE IF EXISTS `wb_comment_reply`; CREATE TABLE IF NOT EXISTS `wb_comment_reply`  (
            `rid` bigint(20) NOT NULL AUTO_INCREMENT,
            `fromuid` bigint(20) NOT NULL,
            `msg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL comment '回复内容',
            `msg_id` bigint(20) NOT NULL comment '关联内容msgid',
            `cid` bigint(20) NOT NULL,
            `touid` mediumint(9) NULL DEFAULT NULL,
            `ctime` int(11) NOT NULL,  
            PRIMARY KEY (`rid`) USING BTREE
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
        // 分配类型变量
        $type = input('type', 'default');
        $msgId = input('msg_id');
        $commentId = input('cid');
        $replyId = input('rid');
        $currentUid = getLoginUid();
        $theme = input('theme', 'default');

        $content_table = $this->typeRelationArr[$type]['content_table'] ?? '';
        if (empty($content_table)) {
            return json(['code' => 400, 'msg' => '类型不存在']);
        }
        $msgUid = Db::name($content_table)->where('msg_id', $msgId)->value('uid');
        // dump($commentId);die;
        // $this->assign('title', $type == 'channel' ? '频道评论' : '文章评论');
        $this->assign('msgId', $msgId);
        $this->assign('commentId', $commentId);
        $this->assign('replyId', $replyId);
        $this->assign('msgUid', $msgUid);
        $this->assign('type', $type);
        $this->assign('currentUid', $currentUid);
        $this->assign('theme', $theme);
        // 渲染视图
        return $this->fetch();
    }

    public function getCommentList()
    {
        $msgId = input('msg_id');
        $commentId = input('comment_id');
        $replyId = input('reply_id');
        $type = input('type');
        $limit = input('limit', 5);
        $page = input('page', 1);
        $order = input('order', 'desc'); // 按热度，按时间降序

        // 参数验证
        if (empty($msgId) || empty($type)) {
            return json(['code' => 400, 'msg' => '参数不能为空']);
        }

        // 确定排序方式
        $orderField = $order == 'hot' ? 'reply_count' : 'ctime';
        $orderDirection = $order == 'asc' ? 'asc' : 'desc';

        // 计算偏移量
        $offset = ($page - 1) * $limit;

        $commentTable = $this->typeRelationArr[$type]['table'] ?? '';
        $replyTable = $this->typeRelationArr[$type]['reply_table'] ?? '';
        if (empty($commentTable) || empty($replyTable)) {
            return json(['code' => 400, 'msg' => '类型不存在']);
        }

        $where = [];
        if ($commentId) {
            $where[] = ['cid', '=', $commentId];
        }
        if ($msgId) {
            $where[] = ['msg_id', '=', $msgId];
        }


        // 获取评论列表
        $list = Db::name($commentTable)
            ->where($where)
            ->order($orderField, $orderDirection)
            ->limit($offset, $limit)
            ->select();
            
        if (empty($list)) {
            return json([
                'code' => 200,
                'msg' => '获取成功',
                'data' => [
                    'list' => [],
                    'userList' => [],
                    'total' => 0,
                    'page' => $page,
                    'limit' => $limit
                ],
            ]);
        }

        $uidArr = array_column($list, 'fromuid');

        if ($replyId) {
            $replyIdArr = [$replyId];
        } else {
            // 获取所有评论的relation_reply_id
            $replyIds = array_column($list, 'relation_reply_id');
            $replyIdArr = [];
            if ($replyIds) {
                foreach ($replyIds as $commentId) {
                    if (!empty($commentId)) {
                        $replyIdArr = array_merge($replyIdArr, explode(',', $commentId));
                    }
                }
            }
            $replyIdArr = array_filter($replyIdArr);
        }


        // 初始化回复列表数组
        $replyList = [];
        if ($replyIdArr) {
            // 获取每条评论的回复（最多10条）
            $replies = Db::name($replyTable)
                ->whereIn('rid', $replyIdArr) // 修正：使用rid而不是msg_id
                ->order('ctime', 'desc')
                ->select();

            // 按评论ID分组回复
            foreach ($replies as $reply) {
                $commentId = $reply['cid']; // 修正：使用cid而不是msg_id

                // 如果该评论的回复数量未达到限制，则添加到回复列表
                if (!isset($replyList[$commentId]) || count($replyList[$commentId]) < 3) {
                    $replyList[$commentId][] = $reply;
                }
            }
            $uidArr = array_merge($uidArr, array_column($replies, 'fromuid'));
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
        $commentId = input('comment_id');
        $type = input('type', 'default');
        $limit = input('limit', 10);
        $page = input('page', 1);
        $order = input('order', 'desc'); // 按热度，按时间降序

        // 参数验证 - 修正：使用$commentId而不是$msgId
        if (empty($commentId)) {
            return json(['code' => 400, 'msg' => 'comment_id不能为空']);
        }

        // 确定排序方式
        $orderField = $order == 'hot' ? 'reply_count' : 'ctime';
        $orderDirection = $order == 'asc' ? 'asc' : 'desc';

        // 计算偏移量
        $offset = ($page - 1) * $limit;

        $replyTable = $this->typeRelationArr[$type]['reply_table'] ?? '';
        if (empty($replyTable)) {
            return json(['code' => 400, 'msg' => '参数错误']);
        }

        // 获取回复列表
        $list = Db::name($replyTable)
            ->where('cid', $commentId)
            ->order($orderField, $orderDirection)
            ->limit($offset, $limit)
            ->select();

        if (empty($list)) {
            return json([
                'code' => 200,
                'msg' => '获取成功',
                'data' => [
                    'list' => [],
                    'total' => 0,
                ],
            ]);
        }

        $uidArr = array_column($list, 'fromuid');
        // $toUidArr = array_column($list, 'touid');
        // $uidArr = array_merge($uidArr, $toUidArr);
        $uidArr = array_filter($uidArr);
        $userList = Db::name('user')->field('uid, nickname, blog, head_image')->whereIn('uid', $uidArr)->select();
        $userList = array_column($userList, null, 'uid');

        // 修正：使用cid而不是msg_id查询总数
        return json([
            'code' => 200,
            'msg' => '获取成功',
            'data' => [
                'list' => $list,
                'userList' => $userList,
                'total' => Db::name($replyTable)->where('cid', $commentId)->count(),
                'page' => $page,
                'limit' => $limit
            ]
        ]);
    }

    public function addComment()
    {
        $msgId = input('msg_id');
        $type = input('type');
        $msg = input('msg');
        $msgUid = input('msg_uid', 0);

        // 参数验证
        if (empty($msgId) || empty($type) || empty($msg)) {
            return json(['code' => 400, 'msg' => '参数不能为空']);
        }

        $commentTable = $this->typeRelationArr[$type]['table'] ?? '';
        $contentTable = $this->typeRelationArr[$type]['content_table'] ?? '';
        if (empty($contentTable) || empty($commentTable)) {
            return json(['code' => 400, 'msg' => '类型不存在']);
        }

        // 获取用户ID
        $uid = getLoginUid() ?: 0;

        // 构建插入数据
        $data = [
            'fromuid' => $uid,
            'msg' => htmlspecialchars($msg),
            'msg_id' => $msgId,
            'touid' => $msgUid,
            'ctime' => time(),
            'reply_count' => 0,
            'relation_reply_id' => ''
        ];

        try {
            $result = Db::name($commentTable)->insertGetId($data);
            Db::name($contentTable)->where('msg_id', $msgId)->setInc('commentsum');
            if ($result) {
                Reminder::saveReminder(
                    $result,
                    $uid,
                    (int)$data['touid'],
                    $this->typeRelationArr[$type]['comment_type'],
                    [
                        'cid' => $result,
                        'msg_id' => $msgId
                    ]
                );
                return json([
                    'code' => 200,
                    'msg' => '评论成功',
                    'data' => [
                        'cid' => $result
                    ]
                ]);
            } else {
                return json(['code' => 500, 'msg' => '评论失败']);
            }
        } catch (\Exception $e) {
            return json(['code' => 500, 'msg' => '数据库错误: ' . $e->getMessage()]);
        }
    }

    public function addReply()
    {
        $commentId = input('comment_id');
        $type = input('type');
        $msg = input('msg');
        $touid = input('touid', 0);
        $msgId = input('msg_id');

        // 参数验证 - 修正：移除未定义的$msgId变量
        if (empty($commentId) || empty($type) || empty($msg) || empty($msgId)) {
            return json(['code' => 400, 'msg' => '参数不能为空']);
        }

        $commentTable = $this->typeRelationArr[$type]['table'] ?? '';
        $replyTable = $this->typeRelationArr[$type]['reply_table'] ?? '';
        if (empty($commentTable) || empty($replyTable)) {
            return json(['code' => 400, 'msg' => '类型不存在']);
        }

        // 获取用户ID
        $uid = getLoginUid() ?: 0;
        // dump($uid);
        // 构建插入数据
        $data = [
            'fromuid' => $uid,
            'msg' => htmlspecialchars($msg),
            'msg_id' => $msgId,
            'cid' => $commentId, // 回复对应的是评论ID
            'touid' => $touid,
            'ctime' => time()
        ];

        try {
            // 开始事务
            Db::startTrans();

            // 插入回复记录
            $result = Db::name($replyTable)->insertGetId($data);

            if ($result) {
                // 先查询当前的relation_reply_id
                $commentInfo = Db::name($commentTable)->field('relation_reply_id')->where('cid', $commentId)->find();
                $currentIds = isset($commentInfo['relation_reply_id']) ? $commentInfo['relation_reply_id'] : '';

                // 处理ID列表，确保不超过5个
                if (empty($currentIds)) {
                    $newIds = (string)$result;
                } else {
                    $idsArray = explode(',', $currentIds);
                    $idsArray[] = (string)$result; // 添加新ID

                    // 只保留最后3个ID
                    if (count($idsArray) > 3) {
                        $idsArray = array_slice($idsArray, -3);
                    }

                    $newIds = implode(',', $idsArray);
                }

                // 更新评论表中的回复数和相关回复ID
                Db::name($commentTable)
                    ->where('cid', $commentId)
                    ->update([
                        'reply_count' => Db::raw('reply_count + 1'),
                        'relation_reply_id' => $newIds
                    ]);

                // 提交事务
                Db::commit();
                // 保存提醒
                Reminder::saveReminder($result, $uid, (int)$data['touid'], $this->typeRelationArr[$type]['rely_type'], [
                    'rid' => $result,
                    'cid' => $commentId,
                    'msg_id' => $data['msg_id'],
                ]);
                return json([
                    'code' => 200,
                    'msg' => '回复成功',
                    'data' => [
                        'cid' => $result
                    ]
                ]);
            } else {
                Db::rollback();
                return json(['code' => 500, 'msg' => '回复失败']);
            }
        } catch (\Exception $e) {
            Db::rollback();
            return json(['code' => 500, 'msg' => '数据库错误: ' . $e->getMessage()]);
        }
    }

    public function deleteComment()
    {
        $commentId = input('comment_id');
        $type = input('type', 'default');

        // 参数验证
        if (empty($commentId)) {
            return json(['code' => 400, 'msg' => 'comment_id不能为空']);
        }

        $commentTable = $this->typeRelationArr[$type]['table'] ?? '';
        $replyTable = $this->typeRelationArr[$type]['reply_table'] ?? '';
        if (empty($commentTable) || empty($replyTable)) {
            return json(['code' => 400, 'msg' => '类型不存在']);
        }

        // 获取当前登录用户ID
        $currentUid = getLoginUid() ?: 0;
        if ($currentUid <= 0) {
            return json(['code' => 401, 'msg' => '请先登录']);
        }

        try {
            // 查询评论信息
            $commentInfo = Db::name($commentTable)->where('cid', $commentId)->find();
            if (!$commentInfo) {
                return json(['code' => 404, 'msg' => '评论不存在']);
            }

            // 检查权限：只能删除自己的评论
            if ($commentInfo['fromuid'] != $currentUid) {
                return json(['code' => 403, 'msg' => '您没有权限删除此评论']);
            }

            // 开始事务
            Db::startTrans();

            // 删除评论
            $result = Db::name($commentTable)->where('cid', $commentId)->delete();

            if ($result) {
                // 同时删除相关的回复
                Db::name($replyTable)->where('cid', $commentId)->delete();

                // 提交事务
                Db::commit();

                return json(['code' => 200, 'msg' => '删除成功']);
            } else {
                Db::rollback();
                return json(['code' => 500, 'msg' => '删除失败']);
            }
        } catch (\Exception $e) {
            Db::rollback();
            return json(['code' => 500, 'msg' => '数据库错误: ' . $e->getMessage()]);
        }
    }

    public function deleteReply()
    {
        $replyId = input('reply_id');
        $type = input('type', 'default');

        // 参数验证
        if (empty($replyId)) {
            return json(['code' => 400, 'msg' => 'reply_id不能为空']);
        }

        $commentTable = $this->typeRelationArr[$type]['table'] ?? '';
        $replyTable = $this->typeRelationArr[$type]['reply_table'] ?? '';
        if (empty($commentTable) || empty($replyTable)) {
            return json(['code' => 400, 'msg' => '类型不存在']);
        }

        // 获取当前登录用户ID
        $currentUid = getLoginUid() ?: 0;
        if ($currentUid <= 0) {
            return json(['code' => 401, 'msg' => '请先登录']);
        }

        try {
            // 查询回复信息
            $replyInfo = Db::name($replyTable)->where('rid', $replyId)->find();
            if (!$replyInfo) {
                return json(['code' => 404, 'msg' => '回复不存在']);
            }

            // 检查权限：只能删除自己的回复
            if ($replyInfo['fromuid'] != $currentUid) {
                return json(['code' => 403, 'msg' => '您没有权限删除此回复']);
            }

            // 开始事务
            Db::startTrans();

            // 获取回复所属的评论ID
            $commentId = $replyInfo['cid'];

            // 删除回复
            $result = Db::name($replyTable)->where('rid', $replyId)->delete();

            if ($result) {
                // 更新评论表中的回复数和相关回复ID
                // 查询该评论的所有回复
                $remainingReplies = Db::name($replyTable)->where('cid', $commentId)->select();
                $replyCount = count($remainingReplies);

                // 更新评论表中的回复数
                Db::name($commentTable)->where('cid', $commentId)->update([
                    'reply_count' => $replyCount
                ]);

                // 更新关联的回复ID列表
                $replyIds = array_column($remainingReplies, 'rid');
                $relationReplyIds = implode(',', array_slice($replyIds, 0, 3)); // 只保留前3个

                Db::name($commentTable)->where('cid', $commentId)->update([
                    'relation_reply_id' => $relationReplyIds
                ]);

                // 提交事务
                Db::commit();

                return json(['code' => 200, 'msg' => '删除成功']);
            } else {
                Db::rollback();
                return json(['code' => 500, 'msg' => '删除失败']);
            }
        } catch (\Exception $e) {
            Db::rollback();
            return json(['code' => 500, 'msg' => '数据库错误: ' . $e->getMessage()]);
        }
    }
}
