<?php

namespace app\common\helper;
use think\Db;
use app\common\model\Reminder as ReminderModel;


// DROP TABLE IF EXISTS `wb_reminder`;
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

// alter table wb_reminder add column note text comment '提示语' after msg_id;
// alter table wb_reminder add column extra text comment '额外信息'  after status;
class Reminder
{ 
    // $type  1: 微博评论 2: 微博回复，3频道评论，4频道回复 5: 好友关注 6: 取消关注  7: 提到@了您 8收藏了微博， 9收藏了频道微博
    public static function saveReminder($msgId, $fromuid, $touid, $type, $extra = [])
    {
        if ($fromuid == $touid) {
            return false;
        }

        // $data = self::{'saveReminderType'.$type}($msgId, $fromuid, $touid, $extra);
        // if (!$data) {
        //     return false;
        // }
        // 传了msgId代表消息可能多条，应避免因此需要判断
        if ($msgId && ReminderModel::where(['msg_id'=>$msgId, 'fromuid'=>$fromuid, 'touid'=>$touid, 'type'=>$type])->find()) {
            return ReminderModel::where(['msg_id'=>$msgId, 'fromuid'=>$fromuid, 'touid'=>$touid, 'type'=>$type])->update([
                'status' => 0
            ]);
        }
        return ReminderModel::create([
            'msg_id'=>$msgId ,
            'fromuid'=>$fromuid,
            'touid'=>$touid,
            'type'=>$type,
            // 'extra' => json_encode($data['extra'], 320),
            'status' => 0,
            'ctime' => time(),
            // 'note' => $data['note']
        ]);
    }


    // public static function saveReminderType1($msgId, $fromuid, $touid, array $extra = [])
    // {
    //     $messageInfo = Db::name('message')->field('content, ctime')->where('msg_id', $msgId)->find();
    //     if (!$messageInfo) {
    //         return false;
    //     }
    //     $userInfo = self::getUserInfo($fromuid);
    //     $extra['content'] = $messageInfo['content'];
    //     $extra['content_time'] = $messageInfo['ctime'];
    //     $extra['from_nickname'] = $userInfo['nickname'];
    //     $extra['from_head_image'] = $userInfo['head_image'];
    //     $extra['from_blog'] = $userInfo['blog'];

    //     $data['msgId'] = $msgId;
    //     $data['note'] = '';
    //     return $data;
    // }

    // protected static function getUserInfo($userid)
    // {
    //     return Db::name('user')->field('uid, blog, nickname, head_image')->where('uid', $userid)->find();
    // }
}
