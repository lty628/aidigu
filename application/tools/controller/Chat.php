<?php
namespace app\tools\controller;
use think\Controller;
use think\Db;


class Chat extends Controller
{	
	public function list()
    {
        return $this->fetch();
    }

    public function getGroup()
    {
        $uid = getLoginUid();
        $list = Db::name('chat_group')->where('fromuid', $uid)->order('groupid', 'desc')->select();
        return json(['code' => 0, 'data' => $list]);
    }

    public function editGroup()
    {
        $uid = getLoginUid();
        $groupid = input('get.groupid');
        $data = Db::name('chat_group')->where('fromuid', $uid)->where('groupid', $groupid)->find();
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function addGroup()
    {
        $uid = getLoginUid();
        $count = Db::name('chat_group')->where('fromuid', $uid)->count();
        if ($count >= 10) $this->error('创建群数量不能多余10个');
        return $this->fetch();
    }

    public function groupFriends()
    {
        $uid = getLoginUid();
        $groupid = input('get.groupid');
        $isAdmin = Db::name('chat_group')->where('fromuid', $uid)->find();
        $isUser = Db::name('chat_group_user')->where('uid', $uid)->find();
        if (!$isUser) $this->error('您不再群里无法查看群信息');
        $this->assign('groupid', $groupid);
        $this->assign('isAdmin', $isAdmin);
        $this->assign('isUser', $isUser);
        return $this->fetch();
    }

    public function groupFriendsList()
    {
        $uid = getLoginUid();
        $groupid = input('get.groupid');
        $data = Db::name('user')
            ->alias('user')
            ->join([getPrefix() . 'chat_group_user' => 'chat_group_user'], 'user.uid=chat_group_user.uid')
            ->where('chat_group_user.groupid', $groupid)
            ->where('chat_group_user.dtime', 0)
            ->field('user.uid,user.head_image,user.nickname,user.blog,chat_group_user.groupid')
            ->order('chat_group_user.utime asc')
            ->select();

        return json(['code' => 0, 'data' => $data]);
    }

    public function joinGroup()
    {
        $uid = getLoginUid();
        $inviteCode = input('get.invite_code');
        if (!$uid) {
            return $this->error('未登录');
        }
        if (!$inviteCode) {
            return $this->error('加群码异常，请联系群管理员');
        }
        $inviteCode = input('get.invite_code');
        $info = Db::name('chat_group')->where('invite_code', $inviteCode)->find();
        if (!$info) return $this->error('加群码异常，请联系群管理员');

        $hasUser = Db::name('chat_group_user')->where('groupid', $info['groupid'])->where('uid', $uid)->find();

        if (!$hasUser) {
            Db::name('chat_group_user')->insert([
                'groupid' => $info['groupid'],
                'uid' => $uid,
                'ctime' => time(),
            ]);
            Db::name('chat_group')->where('groupid', $info['groupid'])->setInc('usernum');
            
        }

        return $this->success('已进入群聊, 即将进入应用页', '/tools/');

    }

    public function delGroupUser()
    {
        $fromuid = getLoginUid();
        $isAdmin = Db::name('chat_group')->where('fromuid', $fromuid)->find();
        if (!$isAdmin) return json(['code' => 1, 'msg' => '无法删除']);
        $uid = input('post.uid');
        $groupid = input('post.groupid');

        if ($fromuid == $uid) return json(['code' => 1, 'msg' => '无法删除自己']);

        $time = time();
        Db::name('chat_group_user')->where('groupid', $groupid)->where('uid', $uid)->update([
            'dtime' => $time,
            'utime' => $time,
        ]);
        Db::name('chat_group')->where('groupid', $groupid)->setDec('usernum');
        return json(['code' => 0, 'msg' => '删除成功']);
    }

    public function addOrEdit()
    {
        $time = time();
        $uid = getLoginUid();
        $post = input('post.');
        $post['utime'] = $time;
        unset($post['file']);
        if (isset($post['groupid'])) {
            $data = Db::name('chat_group')->where('fromuid', $uid)->where('groupid', $post['groupid'])->find();
            if (!$data) return json(['code' => 1, 'msg' => '参数错误']);
            if ($post['invite_status'] == $data['invite_status']) {
                unset($post['invite_status']);
            } elseif ($post['invite_status'] == 0) {
                $post['invite_code'] = '';
            } else {
                $post['invite_code'] = $this->createInviteCode($uid, $time);
            }
            $result = Db::name('chat_group')->where('groupid', $post['groupid'])->update($post);
            if (!$result) return json(['code' => 1, 'msg' => '编辑失败']);
            return json(['code' => 0, 'msg' => '编辑成功']);
        } else {
            if ($post['invite_status'] == 1) {
                $post['invite_code'] = $this->createInviteCode($uid, $time);
            }
            $post['ctime'] = $time;
            $post['fromuid'] = $uid;
            $post['usernum'] = 1;
            $groupid = Db::name('chat_group')->insertGetId($post);
            Db::name('chat_group_user')->insert([
                'groupid' => $groupid,
                'uid' => $uid,
                'ctime' => $time,
            ]);
            if (!$groupid) return json(['code' => 1, 'msg' => '新增失败']);
            return json(['code' => 0, 'msg' => '新增成功']);
        }
    }

    protected function createInviteCode($uid, $time)
    {
        return md5('uid_'.$uid.'_time_'.$time);
    }

}
