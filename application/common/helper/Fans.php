<?php

namespace app\common\helper;

use think\Db;

class Fans
{
    public static function getExtraWhere($userid, $loginUid)
    {
        if ($userid == $loginUid) {
            $extraWhere = [];
        } else {
            $extraWhere = [
                'user.invisible' => 0,
            ];
        }
        return $extraWhere;
    }

    public static function setMyFans($userid, $loginUid)
    {
        return Db::name('user')
            ->alias('user')
            ->join([getPrefix() . 'fans' => 'fans'], 'user.uid=fans.fromuid')->where('fans.touid', $userid)->where('fans.fromuid', '<>', $userid)
            ->field('user.uid,user.nickname,user.province,user.city,user.message_sum,user.head_image,user.blog,fans.touid,fans.fromuid,fans.mutual_concern')
            ->order('fans.ctime desc')
            ->where(self::getExtraWhere($userid, $loginUid))
            ->limit(9)
            ->select();
    }
    public static function setMyConcern($userid, $loginUid)
    {
        return Db::name('user')
            ->alias('user')
            ->join([getPrefix() . 'fans' => 'fans'], 'user.uid=fans.touid')->where('fans.fromuid', $userid)->where('fans.touid', '<>', $userid)
            ->field('user.uid,user.nickname,user.province,user.city,user.message_sum,user.head_image,user.blog,fans.touid,fans.fromuid,fans.mutual_concern')
            ->order('fans.ctime desc')
            ->where(self::getExtraWhere($userid, $loginUid))
            ->limit(9)
            ->select();
    }
    //关注我的
    public static function getMyFans($userid, $loginUid, $count = 9)
    {
        return Db::name('user')
            ->alias('user')
            ->join([getPrefix() . 'fans' => 'fans'], 'user.uid=fans.fromuid')->where('fans.touid', $userid)->where('fans.fromuid', '<>', $userid)
            ->field('user.uid,user.nickname,user.province,user.city,user.message_sum,user.head_image,user.blog,fans.touid,fans.fromuid,fans.mutual_concern')
            ->where(self::getExtraWhere($userid, $loginUid))
            ->order('fans.ctime desc')
            ->paginate($count, false, ['page' => request()->param('page/d', 1), 'path' => '[PAGE].html']);
    }
    //我关注的
    public static function getMyConcern($userid, $loginUid, $count = 9)
    {
        return Db::name('user')
            ->alias('user')
            ->join([getPrefix() . 'fans' => 'fans'], 'user.uid=fans.touid')->where('fans.fromuid', $userid)->where('fans.touid', '<>', $userid)
            ->field('user.uid,user.nickname,user.province,user.city,user.message_sum,user.head_image,user.blog,fans.touid,fans.fromuid,fans.mutual_concern')
            ->where(self::getExtraWhere($userid, $loginUid))
            ->order('fans.ctime desc')
            ->paginate($count, false, ['page' => request()->param('page/d', 1), 'path' => '[PAGE].html']);
    }


    public static function getReminderMsg($userid, $count = 20)
    {
        return Db::name('Reminder')
            ->alias('reminder')
            ->join([getPrefix() . 'message' => 'message'], 'reminder.msg_id=message.msg_id')
            ->join([getPrefix() . 'user' => 'user'], 'user.uid=reminder.fromuid')
            ->field('user.uid,user.nickname,message.media,message.media_info,message.contents,message.msg_id,message.repost,message.refrom,message.repostsum,message.commentsum,message.ctime,user.head_image,user.blog,reminder.type,reminder.touid,reminder.fromuid')
            ->order('reminder.ctime desc')
            ->where('touid', $userid)
            ->where('reminder.status', 0)
            ->paginate($count, false, ['page' => request()->param('page/d', 1), 'path' => '[PAGE].html']);
    }
}
