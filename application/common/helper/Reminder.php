<?php

namespace app\common\helper;
use think\Db;
use app\common\model\Reminder as ReminderModel;

// CREATE TABLE `wb_reminder`  (
//   `id` bigint(20) NOT NULL AUTO_INCREMENT,
//   `msg_id` bigint(20) NULL DEFAULT NULL,
//   `fromuid` bigint(20) NOT NULL,
//   `touid` bigint(20) NOT NULL,
//   `type` tinyint(4) NOT NULL,
//   `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '状态：0未读，1已读',
//   `ctime` bigint(20) NOT NULL,
//   PRIMARY KEY (`id`) USING BTREE
// ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_bin ROW_FORMAT = DYNAMIC;
// alter table wb_reminder change msg_id content text;
// alter table wb_reminder add column content_extra text after content;
class Reminder
{ 
    // $type  1: 微博评论 2: 微博回复，3频道评论，4频道回复 5: 好友关注 6: 取消关注  7: 提到@了您 8收藏了微博， 9收藏了频道微博
    public static function saveReminder($msgId, $fromuid, $touid, $type, $content_extra = [])
    {
        if ($fromuid == $touid) {
            return false;
        }


        // return self::{'saveReminderType'.$type}($msgId, $fromuid, $touid, $content_extra);
        
        if (ReminderModel::where(['msg_id'=>$msgId, 'fromuid'=>$fromuid, 'touid'=>$touid, 'type'=>$type])->find()) {
            return ReminderModel::where(['msg_id'=>$msgId, 'fromuid'=>$fromuid, 'touid'=>$touid, 'type'=>$type])->update([
                'status' => 0
            ]);
        }
        return ReminderModel::create(['msg_id'=>$msgId, 'fromuid'=>$fromuid, 'touid'=>$touid, 'type'=>$type]);
    }


    // public static function saveReminderType1($msgId, $fromuid, $touid, array $content_extra = [])
    // {
    //     $messageInfo = Db::name('message')->field('content, ctime')->where('msg_id', $msgId)->find();
    //     if (!$messageInfo) {
    //         return false;
    //     }
        
    //     $userInfo = self::getUserInfo($fromuid);

    //     $content_extra['content'] = $messageInfo['content'];
    //     $content_extra['content_time'] = $messageInfo['ctime'];
    //     $content_extra['from_nickname'] = $userInfo['nickname'];
    //     $content_extra['from_head_image'] = $userInfo['head_image'];
    //     $content_extra['from_blog'] = $userInfo['blog'];

    //     return ReminderModel::create(['content'=>$msg, 'content_extra'=>json_encode($content_extra, 320), 'fromuid'=>$fromuid, 'touid'=>$touid, 'type'=>1]);
    // }

    // protected static function getUserInfo($userid)
    // {
    //     return Db::name('user')->field('uid, blog, nickname, head_image')->where('uid', $userid)->find();
    // }
}
