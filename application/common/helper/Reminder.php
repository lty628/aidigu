<?php

namespace app\common\helper;
use think\Db;
use app\common\model\Reminder as ReminderModel;


// $type  1: 微博评论 2: 微博回复，3频道评论，4频道回复 5: 好友关注 6: 取消关注  7: 提到@了您 8收藏了微博， 9收藏了频道微博
// DROP TABLE IF EXISTS `wb_reminder`;
// CREATE TABLE `wb_reminder`  (
//   `reminder_id` bigint(20) NOT NULL AUTO_INCREMENT,
//   `uk_id` bigint(20) NULL DEFAULT NULL,
//   `fromuid` bigint(20) NOT NULL,
//   `touid` bigint(20) NOT NULL,
//   `type` tinyint(4) NOT NULL,
//   `extra` text NULL DEFAULT NULL COMMENT '额外信息',
//   `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态：0未读，1已读',
//   `ctime` bigint(20) NOT NULL,
//   `uptime` bigint(20) NULL DEFAULT NULL,
//   PRIMARY KEY (`reminder_id`) USING BTREE
// ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;

class Reminder
{ 
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
     * 'msg'=>'主内容',
     * 'comment_msg'=>'评论内容',
     * 'reply_msg'=>'回复内容', 
     * 'msg_timestamp'=>'主内容时间戳', 
     * 'comment_timestamp'=>'评论时间戳', 
     * 'reply_timestamp'=>'回复时间戳'
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
        // 'msg'=>'主内容','comment_msg'=>'评论内容','reply_msg'=>'回复内容'
        // 'msg_timestamp'=>'主内容时间戳'
        // 'comment_timestamp'=>'评论时间戳'
        // 'reply_timestamp'=>'回复时间戳'
        // 'from_nickname'=>'来源昵称'
        // 'from_head_image'=>'来源头像',
        // 'from_blog'=>'来源博客',
        // ]
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
     * @param int $ukId 关联的内容ID
     * @param int $fromuid 发送提醒的用户ID
     * @param int $type 提醒类型：1评论，2回复，3@提及等
     * @param array $context 包含上下文信息的数组
     * @return array 包含 note 和 extra 的数组
     */
    public static function generateReminderData($ukId, $fromuid, $type, array $extra = [])
    {   
        switch ($type) {
            case 1: // 微博评论
                return self::generateCommentReminderData($ukId, $fromuid, $extra);
            case 2: // 微博回复
                return self::generateReplyReminderData($ukId, $fromuid, $extra);
            case 3: // 频道评论
                return self::generateChannelCommentReminderData($ukId, $fromuid, $extra);
            case 4: // 频道回复
                return self::generateChannelReplyReminderData($ukId, $fromuid, $extra);
            case 7: // @提及
                return self::generateAtReminderData($ukId, $fromuid, $extra);
            default:
                return $extra;
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
    private static function generateCommentReminderData($msgId, $fromuid, $extra)
    {
        $messageInfo = Db::name('message')->field('content, ctime')->where('msg_id', $msgId)->find();
        if (!$messageInfo) {
            return $extra;
        }
        
        $userInfo = self::getUserInfo($fromuid);
        $commentContent = mb_substr($extra['msg'] ?? '', 0, 50, 'utf-8'); // 限制评论内容长度
        
        $result = [
            'note' => $userInfo['nickname'] . ' 评论了你的内容',
            'extra' => [
                'from_nickname' => $userInfo['nickname'],
                'from_head_image' => $userInfo['head_image'],
                'from_blog' => $userInfo['blog'],
                'content_preview' => mb_substr($messageInfo['content'], 0, 100, 'utf-8'),
                'comment_content' => $commentContent,
                'comment_id' => $extra['comment_id'] ?? null,
                'timestamp' => time()
            ] + $extra
        ];
        
        return $result;
    }

    /**
     * 生成回复提醒数据
     */
    private static function generateReplyReminderData($msgId, $fromuid, $extra)
    {
        $messageInfo = Db::name('message')->field('content, ctime')->where('msg_id', $msgId)->find();
        if (!$messageInfo) {
            return $extra;
        }
        
        $userInfo = self::getUserInfo($fromuid);
        $replyContent = mb_substr($extra['msg'] ?? '', 0, 50, 'utf-8'); // 限制回复内容长度
        
        return [
                'from_nickname' => $userInfo['nickname'],
                'from_head_image' => $userInfo['head_image'],
                'from_blog' => $userInfo['blog'],
                'content_preview' => mb_substr($messageInfo['content'], 0, 100, 'utf-8'),
                'reply_content' => $replyContent,
                'reply_id' => $extra['rid'] ?? null,
                'comment_id' => $extra['cid'] ?? null,
                'timestamp' => time()
            ] + $extra;
    }

    /**
     * 生成频道评论提醒数据
     */
    private static function generateChannelCommentReminderData($msgId, $fromuid, $extra)
    {
        $messageInfo = Db::name('channel_message')->field('content, ctime')->where('msg_id', $msgId)->find();
        if (!$messageInfo) {
            return $extra;
        }
        
        $userInfo = self::getUserInfo($fromuid);
        $commentContent = mb_substr($extra['msg'] ?? '', 0, 50, 'utf-8');
        
        return [
                'from_nickname' => $userInfo['nickname'],
                'from_head_image' => $userInfo['head_image'],
                'from_blog' => $userInfo['blog'],
                'content_preview' => mb_substr($messageInfo['content'], 0, 100, 'utf-8'),
                'comment_content' => $commentContent,
                'comment_id' => $extra['comment_id'] ?? null,
                'timestamp' => time()
            ] + $extra;
    }

    /**
     * 生成频道回复提醒数据
     */
    private static function generateChannelReplyReminderData($msgId, $fromuid, $extra)
    {
        $messageInfo = Db::name('channel_message')->field('content, ctime')->where('msg_id', $msgId)->find();
        if (!$messageInfo) {
            return $extra;
        }
        
        $userInfo = self::getUserInfo($fromuid);
        $replyContent = mb_substr($extra['msg'] ?? '', 0, 50, 'utf-8');
        
        return [
                'from_nickname' => $userInfo['nickname'],
                'from_head_image' => $userInfo['head_image'],
                'from_blog' => $userInfo['blog'],
                'content_preview' => mb_substr($messageInfo['content'], 0, 100, 'utf-8'),
                'reply_content' => $replyContent,
                'reply_id' => $extra['rid'] ?? null,
                'comment_id' => $extra['cid'] ?? null,
                'timestamp' => time()
            ] + $extra;
    }

    /**
     * 生成@提及提醒数据
     */
    private static function generateAtReminderData($msgId, $fromuid, $extra)
    {
        $messageInfo = Db::name('message')->field('content, ctime')->where('msg_id', $msgId)->find();
        if (!$messageInfo) {
            return $extra;
        }
        
        $userInfo = self::getUserInfo($fromuid);
        
        return [
                'from_nickname' => $userInfo['nickname'],
                'from_head_image' => $userInfo['head_image'],
                'from_blog' => $userInfo['blog'],
                'content_preview' => mb_substr($messageInfo['content'], 0, 100, 'utf-8'),
                'timestamp' => time()
            ] + $extra;
    }

    /**
     * 获取用户信息
     */
    protected static function getUserInfo($userid)
    {
        return Db::name('user')->field('uid, blog, nickname, head_image')->where('uid', $userid)->find();
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
        return ReminderModel::where(['touid' => $userId, 'status' => 0])->count();
    }

    /**
     * 标记提醒为已读
     */
    public static function markAsRead($reminderIds, $userId = null)
    {
        $condition = ['id' => ['in', $reminderIds]];
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
}