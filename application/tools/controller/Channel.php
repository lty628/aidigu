<?php
namespace app\tools\controller;
use think\Db;
use app\common\controller\Base;

class Channel extends Base
{	
    // 频道列表页面
	public function index()
    {
        return $this->fetch();
    }

    // 获取频道列表
    public function getList()
    {
        $uid = getLoginUid();
        $list = Db::name('channel')->where('owner_uid', $uid)->order('channel_id', 'desc')->select();
        return json(['code' => 0, 'data' => $list]);
    }

    // 编辑频道页面
    public function edit()
    {
        $uid = getLoginUid();
        $channelId = input('get.channel_id');
        $data = Db::name('channel')->where('owner_uid', $uid)->where('channel_id', $channelId)->find();
        $this->assign('data', $data);
        return $this->fetch();
    }

    // 添加频道页面
    public function add()
    {
        $uid = getLoginUid();
        $count = Db::name('channel')->where('owner_uid', $uid)->count();
        if ($count >= 10) $this->error('创建频道数量不能多余10个');
        return $this->fetch();
    }

    // 添加或编辑频道
    public function addOrEdit()
    {
        $time = time();
        $uid = getLoginUid();
        $post = input('post.');
        $post['update_time'] = $time;
        unset($post['file']);
        
        // 处理密码字段
        if (isset($post['password']) && $post['password']) {
            $post['password'] = encryptionPass($post['password']);
        } elseif (isset($post['password'])) {
            // 如果密码为空，则设为空字符串
            $post['password'] = '';
        }
        
        if (isset($post['channel_id'])) {
            // 编辑频道
            $data = Db::name('channel')->where('owner_uid', $uid)->where('channel_id', $post['channel_id'])->find();
            if (!$data) return json(['code' => 1, 'msg' => '参数错误']);
            
            // 如果没有提供密码或者密码为空，则保持原密码不变
            if (!isset($post['password']) || $post['password'] === '') {
                unset($post['password']);
            }
            
            // 处理邀请状态
            if (isset($post['invite_status']) && $post['invite_status'] == $data['invite_status']) {
                unset($post['invite_status']);
            } elseif (isset($post['invite_status']) && $post['invite_status'] == 0) {
                $post['invite_code'] = '';
            } elseif (isset($post['invite_status']) && $post['invite_status'] == 1) {
                $post['invite_code'] = $this->createInviteCode($uid, $time);
            }
            
            $result = Db::name('channel')->where('channel_id', $post['channel_id'])->update($post);
            if (!$result) return json(['code' => 1, 'msg' => '编辑失败']);
            return json(['code' => 0, 'msg' => '编辑成功']);
        } else {
            // 新增频道
            if (isset($post['invite_status']) && $post['invite_status'] == 1) {
                $post['invite_code'] = $this->createInviteCode($uid, $time);
            }
            $post['create_time'] = $time;
            $post['owner_uid'] = $uid;
            $post['member_count'] = 1;
            
            // 设置默认值
            if (!isset($post['allow_speak'])) $post['allow_speak'] = 1;
            if (!isset($post['allow_comment'])) $post['allow_comment'] = 0;
            
            $channelId = Db::name('channel')->insertGetId($post);
            Db::name('channel_user')->insert([
                'channel_id' => $channelId,
                'uid' => $uid,
                'role' => 2, // 创建者
                'join_time' => $time,
            ]);
            if (!$channelId) return json(['code' => 1, 'msg' => '新增失败']);
            return json(['code' => 0, 'msg' => '新增成功']);
        }
    }

    // 删除频道成员
    public function delChannelUser()
    {
        $uid = getLoginUid();
        $channelId = input('post.channel_id');
        $isAdmin = Db::name('channel')->where('channel_id', $channelId)->where('owner_uid', $uid)->find();
        if (!$isAdmin) return json(['code' => 1, 'msg' => '无权限删除']);
        $delUid = input('post.del_uid');

        if ($uid == $delUid) return json(['code' => 1, 'msg' => '无法删除自己']);

        $time = time();
        Db::name('channel_user')->where('channel_id', $channelId)->where('uid', $delUid)->update([
            'leave_time' => $time,
            'update_time' => $time,
        ]);
        Db::name('channel')->where('channel_id', $channelId)->setDec('member_count');
        return json(['code' => 0, 'msg' => '删除成功']);
    }

    // 频道成员管理页面
    public function users()
    {
        $uid = getLoginUid();
        $channelId = input('get.channel_id');
        $isOwner = Db::name('channel')->where('channel_id', $channelId)->where('owner_uid', $uid)->find();
        $isUser = Db::name('channel_user')->where('channel_id', $channelId)->where('uid', $uid)->where('leave_time', 0)->find();
        if (!$isUser) $this->error('您不在频道中无法查看频道信息');
        $this->assign('channel_id', $channelId);
        $this->assign('isOwner', $isOwner);
        $this->assign('isUser', $isUser);
        return $this->fetch();
    }

    // 获取频道成员列表
    public function usersList()
    {
        $uid = getLoginUid();
        $channelId = input('get.channel_id');
        $data = Db::name('user')
            ->alias('user')
            ->join([getPrefix() . 'channel_user' => 'channel_user'], 'user.uid=channel_user.uid')
            ->where('channel_user.channel_id', $channelId)
            ->where('channel_user.leave_time', 0)
            ->field('user.uid,user.head_image,user.nickname,user.blog,channel_user.role')
            ->order('channel_user.join_time asc')
            ->select();

        return json(['code' => 0, 'data' => $data]);
    }

    // 加入频道
    public function join()
    {
        $uid = getLoginUid();
        $inviteCode = input('get.invite_code');
        $channelId = input('get.channel_id');
        if (!$channelId) {
            return $this->error('加入频道码异常，请联系频道管理员');
        }
        if (!$inviteCode) {
            return $this->error('加入频道码异常，请联系频道管理员');
        }
        
        $info = Db::name('channel')->where('channel_id', $channelId)->where('invite_code', $inviteCode)->find();
        if (!$info) return $this->error('加入频道码异常，请联系频道管理员');

        // 检查是否需要密码
        // if ($info['password']) {
        //     $password = input('get.password', '');
        //     if (!$password || encryptionPass($password) != $info['password']) {
        //         return $this->error('频道密码错误');
        //     }
        // }

        $hasUser = Db::name('channel_user')->where('channel_id', $info['channel_id'])->where('uid', $uid)->where('leave_time', 0)->find();

        if (!$hasUser) {
            Db::name('channel_user')->insert([
                'channel_id' => $info['channel_id'],
                'uid' => $uid,
                'role' => 0, // 普通成员
                'join_time' => time(),
            ]);
            Db::name('channel')->where('channel_id', $info['channel_id'])->setInc('member_count');
        }

        return $this->success('已进入频道, 即将进入应用页', '/channel/' . $info['channel_id'] . '/');
    }
    
    // 删除频道
    public function delete()
    {
        $uid = getLoginUid();
        $channelId = input('post.channel_id');
        
        // 验证频道是否存在且属于当前用户
        $channel = Db::name('channel')->where('channel_id', $channelId)->where('owner_uid', $uid)->find();
        if (!$channel) {
            return json(['code' => 1, 'msg' => '频道不存在或无权限删除']);
        }
        
        // 删除频道
        Db::name('channel')->where('channel_id', $channelId)->delete();
        
        // 删除频道用户关系
        Db::name('channel_user')->where('channel_id', $channelId)->delete();
        
        // 删除频道消息
        Db::name('channel_message')->where('channel_id', $channelId)->delete();
        
        return json(['code' => 0, 'msg' => '删除成功']);
    }

    // 生成邀请码
    protected function createInviteCode($uid, $time)
    {
        return md5('uid_'.$uid.'_time_'.$time.'_channel');
    }
}