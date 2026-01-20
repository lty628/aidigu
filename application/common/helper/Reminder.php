<?php

namespace app\common\helper;
use think\Db;
use app\common\model\Reminder as ReminderModel;


// $type  1: 微博评论 2: 微博回复，3频道评论，4频道回复 5: 好友关注 6: 取消关注  7: 提到@了您 8收藏了微博， 9收藏了频道微博
// DROP TABLE IF EXISTS `wb_reminder`;
// CREATE TABLE `wb_reminder` (
//   `reminder_id` bigint(20) NOT NULL AUTO_INCREMENT COMMENT '提醒记录ID，主键',
//   `uk_id` bigint(20) NULL DEFAULT NULL COMMENT '唯一键ID，根据类型不同含义不同：评论类型=评论ID(cid)，回复类型=回复ID(rid)，收藏类型=消息ID(msg_id)，关注/取消关注=0',
//   `fromuid` bigint(20) NOT NULL COMMENT '发送提醒的用户ID',
//   `touid` bigint(20) NOT NULL COMMENT '接收提醒的用户ID',
//   `type` tinyint(4) NOT NULL COMMENT '提醒类型：1微博评论，2微博回复，3频道评论，4频道回复，5好友关注，6取消关注，7@提及，8收藏微博，9收藏频道微博',
//   `extra` text NULL DEFAULT NULL COMMENT '额外信息，JSON格式存储详细内容',
//   `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态：0未读，1已读',
//   `ctime` bigint(20) NOT NULL COMMENT '创建时间戳',
//   `uptime` bigint(20) NULL DEFAULT NULL COMMENT '更新时间戳',
//   PRIMARY KEY (`reminder_id`),
//   INDEX `idx_uk_from_touid_type` (`uk_id`, `fromuid`, `touid`, `type`),
//   INDEX `idx_touid` (`touid`),
//   INDEX `idx_touid_status_ctime` (`touid`, `status`, `ctime`),
//   INDEX `idx_status` (`status`),
//   INDEX `idx_type` (`type`),
//   INDEX `idx_ctime` (`ctime`)
// ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC COMMENT='消息提醒表，存储各类消息提醒记录';

class Reminder
{ 
    public static function getReminderMsg($userid, $count = 20, $page = 1, $type = null, $isMobile = 0)
    {
        $where = [];
        if ($isMobile) {
             $where['type'] = ['not in', [3,4,9]];
        }
        $query = ReminderModel::alias('reminder')
            ->join('user u', 'u.uid = reminder.fromuid', 'LEFT')
            ->field('reminder.*, u.nickname as from_nickname, u.head_image as from_head_image, u.blog as from_blog')
            ->where('reminder.touid', $userid)
            // ->where('reminder.status', 0)  // 只获取未读提醒
            ->where($where)
            ->order('reminder.status asc, reminder.ctime desc');
        
        // 如果指定了类型，则添加类型筛选
        if ($type !== null && $type !== '') {
            $query->where('reminder.type', (int)$type);
        }
        
        $reminders = $query->page($page, $count)->select();
        
        // 解析额外信息
        // foreach ($reminders as &$reminder) {
        //     $extra = json_decode($reminder['extra'], true);
        //     if ($extra) {
        //         $reminder = array_merge($reminder, $extra);
        //     }
        // }
        
        return [
            'data' => $reminders,
            'page' => $page,
            'count' => count($reminders)
        ];
    }
    /**
     * 保存提醒信息
     * @param int|null $ukId 关联的内容ID
     * @param int $fromuid 发送提醒的用户ID
     * @param int $touid 接收提醒的用户ID
     * @param int $type 提醒类型：1评论，2回复，3@提及等
     * @param array $extra 额外参数 
     * [
     * 'msg_id'=>消息ID, 
     * 'cid'=>评论ID, 
     * 'rid'=>回复ID, 
     * ]
     * @return bool|object
     */
    public static function saveReminder($ukId, $fromuid, $touid, $type, $extra = [])
    {
        if ($fromuid == $touid) {
            return false;
        }
        
        // [
        // 'msg_id'=>消息ID
        // 'cid'=>评论ID
        // 'rid'=>回复ID
        // 'msg_contents'=>'主内容','comment_contents'=>'评论内容','reply_contents'=>'回复内容'
        // 'msg_timestamp'=>'主内容时间戳'
        // 'comment_timestamp'=>'评论时间戳'
        // 'reply_timestamp'=>'回复时间戳'
        // 'from_nickname'=>'来源昵称'
        // 'from_head_image'=>'来源头像',
        // 'from_blog'=>'来源博客',
        // ]
        // ukId ： 评论=cid，回复=rid，收藏=msg_id, 好友关注 0， 取消关注 0 ...
        $extraData = self::generateReminderData($ukId, $fromuid, $type, $extra);

        // 检查是否已存在相同的提醒记录
        $condition = [
            'uk_id' => $ukId,
            'fromuid' => $fromuid,
            'touid' => $touid,
            'type' => $type
        ];
        
        // 过滤掉空值，避免影响查询条件
        $condition = array_filter($condition, function($value) {
            return $value !== null;
        });

        // 查找是否存在相同的提醒记录
        $existingRecord = ReminderModel::where($condition)->find();
        if ($existingRecord) {
            // 如果存在，则更新状态为未读
            return ReminderModel::where($condition)->update([
                'status' => 0,
                'extra' => json_encode($extraData, JSON_UNESCAPED_UNICODE),
                'uptime' => time()
            ]);
        }
        
        // 创建新的提醒记录
        return ReminderModel::create([
            'uk_id' => $ukId,
            'fromuid' => $fromuid,
            'touid' => $touid,
            'type' => $type,
            'extra' => json_encode($extraData, JSON_UNESCAPED_UNICODE),
            'status' => 0,
            'ctime' => time(),
            'uptime' => time()
        ]);
    }

    /**
     * 根据不同提醒类型生成特定的提醒数据
     * @param int $ukId 关联的内容ID（根据类型不同代表不同的ID）
     * @param int $fromuid 发送提醒的用户ID
     * @param int $type 提醒类型：1评论，2回复，3@提及等
     * @param array $extra 包含上下文信息的数组
     * @return array 完整的提醒数据
     */
    public static function generateReminderData($ukId, $fromuid, $type, array $extra = [])
    {   
        // 获取用户信息
        $userInfo = self::getUserInfo($fromuid);
        
        switch ($type) {
            case 1: // 微博评论 - 主题内容，评论内容
                return self::generateCommentReminderData($ukId, $userInfo, $extra);
            case 2: // 微博回复 - 主题，回复，评论信息
                return self::generateReplyReminderData($ukId, $userInfo, $extra);
            case 3: // 频道评论 - 主题内容，评论内容
                return self::generateChannelCommentReminderData($ukId, $userInfo, $extra);
            case 4: // 频道回复 - 主题，回复，评论信息
                return self::generateChannelReplyReminderData($ukId, $userInfo, $extra);
            case 5: // 好友关注
            case 6: // 取消关注
                return self::generateFollowReminderData($ukId, $userInfo, $extra);
            case 7: // @提及
                return self::generateAtReminderData($ukId, $userInfo, $extra);
            case 8: // 收藏了微博 - 收藏主体内容
                return self::generateFavoriteWeiboReminderData($ukId, $userInfo, $extra);
            case 9: // 收藏了频道微博 - 收藏主体内容
                return self::generateFavoriteChannelWeiboReminderData($ukId, $userInfo, $extra);
            default:
                // 默认情况下也包含用户信息
                return [
                    'from_nickname' => $userInfo['nickname'] ?? '',
                    'from_head_image' => $userInfo['head_image'] ?? '',
                    'from_blog' => $userInfo['blog'] ?? ''
                ] + $extra;
        }
    }

    /**
     * 获取默认提醒文本
     */
    private static function getDefaultNote($type)
    {
        $notes = [
            1 => '有人评论了你的内容',
            2 => '有人回复了你的评论',
            3 => '有人评论了你的频道内容',
            4 => '有人回复了你的频道评论',
            5 => '有人关注了你',
            6 => '有人取消关注了你',
            7 => '有人在内容中提到了你',
            8 => '有人收藏了你的内容',
            9 => '有人收藏了你的频道内容'
        ];
        
        return $notes[$type] ?? '你有一条新提醒';
    }

    /**
     * 生成评论提醒数据
     */
    private static function generateCommentReminderData($commentId, $userInfo, $extra)
    {
        // 从评论ID获取评论信息
        $commentInfo = Db::name('comment')->field('msg, ctime, msg_id')->where('cid', $commentId)->find();
        
        // 从消息ID获取原始消息信息
        $messageInfo = null;
        if ($commentInfo && !empty($commentInfo['msg_id'])) {
            $messageInfo = Db::name('message')->field('contents, ctime')->where('msg_id', $commentInfo['msg_id'])->find();
        }
        
        $result = [
            'note' => $userInfo['nickname'] . ' 评论了你的内容',
            'from_nickname' => $userInfo['nickname'],
            'from_head_image' => $userInfo['head_image'],
            'from_blog' => $userInfo['blog'],
            'msg_contents' => $messageInfo ? mb_substr($messageInfo['contents'], 0, 100, 'utf-8') : '',
            'msg_timestamp' => $messageInfo ? $messageInfo['ctime'] : 0,
            'comment_contents' => $commentInfo ? mb_substr($commentInfo['msg'], 0, 50, 'utf-8') : '', // 评论内容
            'comment_timestamp' => $commentInfo ? $commentInfo['ctime'] : 0,
            'comment_id' => $commentId,
            'timestamp' => time()
        ];
        
        // 合并额外参数
        return $result + $extra;
    }

    /**
     * 生成微博回复提醒数据
     */
    private static function generateReplyReminderData($replyId, $userInfo, $extra)
    {
        // 从回复ID获取回复信息
        $replyInfo = Db::name('comment')->alias('c')
            ->join('comment_reply cr', 'c.cid = cr.cid')
            ->field('c.msg as comment_msg, c.ctime as comment_ctime, c.msg_id, cr.msg as reply_msg, cr.ctime as reply_ctime, cr.cid as comment_id')
            ->where('cr.rid', $replyId)
            ->find();
        
        // 从消息ID获取原始消息信息
        $messageInfo = null;
        if ($replyInfo && !empty($replyInfo['msg_id'])) {
            $messageInfo = Db::name('message')->field('contents, ctime')->where('msg_id', $replyInfo['msg_id'])->find();
        }
        
        $result = [
            'note' => $userInfo['nickname'] . ' 回复了你的评论',
            'from_nickname' => $userInfo['nickname'],
            'from_head_image' => $userInfo['head_image'],
            'from_blog' => $userInfo['blog'],
            'msg_contents' => $messageInfo ? mb_substr($messageInfo['contents'], 0, 100, 'utf-8') : '',
            'msg_timestamp' => $messageInfo ? $messageInfo['ctime'] : 0,
            'comment_contents' => $replyInfo ? mb_substr($replyInfo['comment_msg'], 0, 50, 'utf-8') : '', // 原评论内容
            'comment_timestamp' => $replyInfo ? $replyInfo['comment_ctime'] : 0,
            'reply_contents' => $replyInfo ? mb_substr($replyInfo['reply_msg'], 0, 50, 'utf-8') : '', // 回复内容
            'reply_timestamp' => $replyInfo ? $replyInfo['reply_ctime'] : 0,
            'reply_id' => $replyId,
            'comment_id' => $replyInfo ? $replyInfo['comment_id'] : null,
            'timestamp' => time()
        ];
        
        return $result + $extra;
    }

    /**
     * 生成频道评论提醒数据
     */
    private static function generateChannelCommentReminderData($commentId, $userInfo, $extra)
    {
        // 从评论ID获取评论信息
        $commentInfo = Db::name('channel_comment')->field('msg, ctime, msg_id')->where('cid', $commentId)->find();
        
        // 从消息ID获取原始消息信息
        $messageInfo = null;
        if ($commentInfo && !empty($commentInfo['msg_id'])) {
            $messageInfo = Db::name('channel_message')->field('channel_id, contents, ctime')->where('msg_id', $commentInfo['msg_id'])->find();
        }
        
        $result = [
            'note' => $userInfo['nickname'] . ' 评论了你的频道内容',
            'from_nickname' => $userInfo['nickname'],
            'from_head_image' => $userInfo['head_image'],
            'from_blog' => $userInfo['blog'],
            'msg_contents' => $messageInfo ? mb_substr($messageInfo['contents'], 0, 100, 'utf-8') : '',
            'msg_timestamp' => $messageInfo ? $messageInfo['ctime'] : 0,
            'comment_contents' => $commentInfo ? mb_substr($commentInfo['msg'], 0, 50, 'utf-8') : '', // 评论内容
            'comment_timestamp' => $commentInfo ? $commentInfo['ctime'] : 0,
            'comment_id' => $commentId,
            'channel_id' => $messageInfo['channel_id'] ?? 0,
            'timestamp' => time()
        ];
        
        return $result + $extra;
    }

    /**
     * 生成频道回复提醒数据
     */
    private static function generateChannelReplyReminderData($replyId, $userInfo, $extra)
    {
        // 从回复ID获取回复信息
        $replyInfo = Db::name('channel_comment')->alias('c')
            ->join('channel_comment_reply cr', 'c.cid = cr.cid')
            ->field('c.msg as comment_msg, c.ctime as comment_ctime, c.msg_id, cr.msg as reply_msg, cr.ctime as reply_ctime, cr.cid as comment_id')
            ->where('cr.rid', $replyId)
            ->find();
        
        // 从消息ID获取原始消息信息
        $messageInfo = null;
        if ($replyInfo && !empty($replyInfo['msg_id'])) {
            $messageInfo = Db::name('channel_message')->field('channel_id, contents, ctime')->where('msg_id', $replyInfo['msg_id'])->find();
        }
        
        $result = [
            'note' => $userInfo['nickname'] . ' 回复了你的频道评论',
            'from_nickname' => $userInfo['nickname'],
            'from_head_image' => $userInfo['head_image'],
            'from_blog' => $userInfo['blog'],
            'msg_contents' => $messageInfo ? mb_substr($messageInfo['contents'], 0, 100, 'utf-8') : '',
            'msg_timestamp' => $messageInfo ? $messageInfo['ctime'] : 0,
            'comment_contents' => $replyInfo ? mb_substr($replyInfo['comment_msg'], 0, 50, 'utf-8') : '', // 原评论内容
            'comment_timestamp' => $replyInfo ? $replyInfo['comment_ctime'] : 0,
            'reply_contents' => $replyInfo ? mb_substr($replyInfo['reply_msg'], 0, 50, 'utf-8') : '', // 回复内容
            'reply_timestamp' => $replyInfo ? $replyInfo['reply_ctime'] : 0,
            'reply_id' => $replyId,
            'comment_id' => $replyInfo ? $replyInfo['comment_id'] : null,
            'channel_id' => $messageInfo['channel_id'] ?? 0,
            'timestamp' => time()
        ];
        
        return $result + $extra;
    }

    /**
     * 生成好友关注/取消关注提醒数据
     */
    private static function generateFollowReminderData($ukId, $userInfo, $extra)
    {
        $actionText = $ukId === 0 ? '关注' : '取消关注'; // 实际上这里的ukId可能是0或特定标识
        
        $result = [
            'note' => $userInfo['nickname'] . ' ' . $actionText . '了你',
            'from_nickname' => $userInfo['nickname'],
            'from_head_image' => $userInfo['head_image'],
            'from_blog' => $userInfo['blog'],
            'timestamp' => time()
        ];
        
        return $result + $extra;
    }

    /**
     * 生成@提及提醒数据
     */
    private static function generateAtReminderData($msgId, $userInfo, $extra)
    {
        $messageInfo = Db::name('message')->field('contents, ctime')->where('msg_id', $msgId)->find();
        
        $result = [
            'note' => $userInfo['nickname'] . ' 在内容中提到了你',
            'from_nickname' => $userInfo['nickname'],
            'from_head_image' => $userInfo['head_image'],
            'from_blog' => $userInfo['blog'],
            'msg_contents' => $messageInfo ? mb_substr($messageInfo['contents'], 0, 100, 'utf-8') : '',
            'msg_timestamp' => $messageInfo ? $messageInfo['ctime'] : 0,
            'timestamp' => time()
        ];
        
        return $result + $extra;
    }

    /**
     * 生成收藏微博提醒数据
     */
    private static function generateFavoriteWeiboReminderData($msgId, $userInfo, $extra)
    {
        $messageInfo = Db::name('message')->field('contents, ctime, fromuid')->where('msg_id', $msgId)->find();
        
        // 获取原作者信息
        $originalAuthorInfo = null;
        if ($messageInfo && !empty($messageInfo['fromuid'])) {
            $originalAuthorInfo = self::getUserInfo($messageInfo['fromuid']);
        }
        
        $result = [
            'note' => $userInfo['nickname'] . ' 收藏了你的微博',
            'from_nickname' => $userInfo['nickname'],
            'from_head_image' => $userInfo['head_image'],
            'from_blog' => $userInfo['blog'],
            'msg_contents' => $messageInfo ? mb_substr($messageInfo['contents'], 0, 100, 'utf-8') : '',
            'msg_timestamp' => $messageInfo ? $messageInfo['ctime'] : 0,
            'original_author_nickname' => $originalAuthorInfo['nickname'] ?? '',
            'original_author_head_image' => $originalAuthorInfo['head_image'] ?? '',
            'timestamp' => time()
        ];
        
        return $result + $extra;
    }

    /**
     * 生成收藏频道微博提醒数据
     */
    private static function generateFavoriteChannelWeiboReminderData($msgId, $userInfo, $extra)
    {
        $messageInfo = Db::name('channel_message')->field('channel_id, contents, ctime, fromuid')->where('msg_id', $msgId)->find();
        
        // 获取原作者信息
        $originalAuthorInfo = null;
        if ($messageInfo && !empty($messageInfo['fromuid'])) {
            $originalAuthorInfo = self::getUserInfo($messageInfo['fromuid']);
        }
        
        $result = [
            'note' => $userInfo['nickname'] . ' 收藏了你的频道微博',
            'from_nickname' => $userInfo['nickname'],
            'from_head_image' => $userInfo['head_image'],
            'from_blog' => $userInfo['blog'],
            'msg_contents' => $messageInfo ? mb_substr($messageInfo['contents'], 0, 100, 'utf-8') : '',
            'msg_timestamp' => $messageInfo ? $messageInfo['ctime'] : 0,
            'original_author_nickname' => $originalAuthorInfo['nickname'] ?? '',
            'original_author_head_image' => $originalAuthorInfo['head_image'] ?? '',
            'channel_id' => $messageInfo['channel_id'] ?? 0,
            'timestamp' => time()
        ];
        
        return $result + $extra;
    }

    /**
     * 获取用户信息
     */
    protected static function getUserInfo($userid)
    {
        $user = Db::name('user')->field('uid, blog, nickname, head_image')->where('uid', $userid)->find();
        return $user ?: [
            'uid' => $userid,
            'blog' => '',
            'nickname' => '未知用户',
            'head_image' => ''
        ];
    }

    /**
     * 批量获取用户信息
     */
    protected static function getUserList($userIds)
    {
        if (empty($userIds)) {
            return [];
        }
        
        return Db::name('user')
            ->field('uid, blog, nickname, head_image')
            ->where('uid', 'in', $userIds)
            ->select();
    }

    /**
     * 获取用户的未读提醒数量
     */
    public static function getUnreadCount($userId)
    {
        $unreadTypeCountGroup = ReminderModel::where(['touid' => $userId, 'status' => 0])->field('count(*) as count, type')->group('type')->select();
        $unreadTypeCount = [];
        if ($unreadTypeCountGroup) {
            foreach ($unreadTypeCountGroup as $value) {
                $unreadTypeCount[$value['type']] = $value['count'];
            }
        }
        $unreadAllCount = array_sum(array_values($unreadTypeCount));
        // dump($unreadAllCount);die;
        return [
            'unreadAllCount' => $unreadAllCount,
            'unreadTypeCount' => $unreadTypeCount
        ];
    }

    /**
     * 标记提醒为已读
     */
    public static function markAsRead($reminderIds, $userId = null)
    {
        $condition = is_array($reminderIds) ? ['reminder_id' => ['in', $reminderIds]] : ['reminder_id' => $reminderIds];
        if ($userId) {
            $condition['touid'] = $userId;
        }
        
        return ReminderModel::where($condition)->update([
            'status' => 1,
            'uptime' => time()
        ]);
    }

    /**
     * 清理过期的提醒记录
     */
    public static function cleanupOldReminders($days = 90)
    {
        $timeThreshold = time() - ($days * 24 * 3600);
        return ReminderModel::where('ctime', '<', $timeThreshold)->delete();
    }

    /**
     * 获取用户提醒列表
     */
    public static function getUserReminders($userId, $page = 1, $limit = 20, $type = null)
    {
        $query = ReminderModel::where('touid', $userId);
        
        if ($type !== null) {
            $query->where('type', $type);
        }
        
        $reminders = $query
            ->order('ctime DESC')
            ->page($page, $limit)
            ->select();
            
        foreach ($reminders as &$reminder) {
            $extra = json_decode($reminder['extra'], true);
            $reminder['extra'] = $extra ?: [];
        }
        
        return $reminders;
    }
}