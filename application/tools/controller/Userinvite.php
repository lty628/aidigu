<?php
namespace app\tools\controller;
use think\Controller;
use think\Db;


class Userinvite extends Controller
{	
	public function list()
    {
        return $this->fetch();
    }

    public function getList()
    {
        $uid = getLoginUid();
        $list = Db::name('user_invite')
            ->alias('user_invite')
            ->leftJoin([getPrefix() . 'user' => 'user'], 'user.uid=user_invite.touid')
            ->field('user.uid,user.head_image,user.nickname,user.blog,user_invite.invite_code,user_invite.invite_status,user_invite.create_time,user_invite.id')
            ->where('invite_status', '<>', 3)
            ->where('user_invite.fromuid', $uid)
            ->order('user_invite.id asc')
            ->select();
        return json(['code' => 0, 'data' => $list]);
    }

    public function createInviteCode()
    {
        $uid = getLoginUid();
        $time = time();

        $count = Db::name('user_invite')->where('fromuid', $uid)->where('invite_status', '<>', 3)->count();

        // 用户是否被邀请
        $topuid = Db::name('user_invite')->where('touid', $uid)->value('topuid');
        if (!$topuid) {
            // return json(['code' => 0, 'msg' => '您已被邀请，不能再邀请了']);
            $topuid = $uid;
        }
        
        if ($count >= 20) {
            return json(['code' => 0, 'msg' => '邀请人数超过20人不能再邀请了']);
        }

        Db::name('user_invite')->insert([
            'fromuid' => $uid,
            'invite_code' => md5('uid_'.$uid.'_time_'.$time),
            'fromuid' => $uid,
            'topuid' => $topuid,
            'create_time' => date('Y-m-d H:i:s', $time)
        ]);

        return json(['code' => 0, 'msg' => '生成成功']);
    }

    public static function checkInviteCode($inviteCode)
    {
        return Db::name('user_invite')->where('invite_status', 0)->where('invite_code', $inviteCode)->find();
    }

    public static function changeInviteInfo($uid, $inviteCode)
    {
        return Db::name('user_invite')->where('invite_code', $inviteCode)->update([
            'invite_status' => 2,
            'touid' => $uid,
        ]);
    }

    public function delInviteCode()
    {
        $id = input('post.id');

        $info = Db::name('user_invite')->where('id', $id)->find();
        if (!$info || $info['touid']) {
            return json(['code' => 1, 'msg' => '已邀请成功，无法删除']);
        }

        Db::name('user_invite')->where('id', $id)->update([
            'invite_status' => 3
        ]);

        return json(['code' => 0, 'msg' => '删除成功']);
    }

}
