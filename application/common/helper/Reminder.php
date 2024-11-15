<?php

namespace app\common\helper;

use app\common\model\Reminder as ReminderModel;


class Reminder
{ 
    // $type 0: 转发 1: 评论 2: 回复 3: 好友 4: 私信  5: 群聊 【群聊提醒待定】
    public static function saveReminder($msgId, $fromuid, $touid, $type)
    {
        if (ReminderModel::where(['msg_id'=>$msgId, 'fromuid'=>$fromuid, 'touid'=>$touid, 'type'=>$type])->find()) {
            return ReminderModel::where(['msg_id'=>$msgId, 'fromuid'=>$fromuid, 'touid'=>$touid, 'type'=>$type])->update([
                'status' => 0
            ]);
        }
        return ReminderModel::create(['msg_id'=>$msgId, 'fromuid'=>$fromuid, 'touid'=>$touid, 'type'=>$type]);
    }
}
