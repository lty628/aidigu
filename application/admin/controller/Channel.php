<?php
namespace app\admin\controller;
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
        $keyword = input('post.keyword', '');
        $page = input('post.page', 1, 'intval');
        $limit = input('post.limit', 10, 'intval');
        
        $query = Db::name('channel')->order('channel_id', 'desc');
        
        // 关键词搜索
        if ($keyword) {
            $query->where('channel_name', 'like', '%' . $keyword . '%');
        }
        
        $total = $query->count();
        $list = $query->page($page, $limit)->select();
        
        return json(['code' => 0, 'msg' => 'success', 'count' => $total, 'data' => $list]);
    }

    // 编辑频道页面
    public function edit()
    {
        $channelId = input('get.channel_id');
        $data = Db::name('channel')->where('channel_id', $channelId)->find();
        $this->assign('data', $data);
        return $this->fetch();
    }

    // 添加频道页面
    public function add()
    {
        return $this->fetch();
    }

    // 添加或编辑频道
    public function addOrEdit()
    {
        $time = time();
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
            $data = Db::name('channel')->where('channel_id', $post['channel_id'])->find();
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
                $post['invite_code'] = $this->createInviteCode($data['owner_uid'], $time);
            }
            
            $result = Db::name('channel')->where('channel_id', $post['channel_id'])->update($post);
            if (!$result) return json(['code' => 1, 'msg' => '编辑失败']);
            return json(['code' => 0, 'msg' => '编辑成功']);
        } else {
            // 新增频道
            if (isset($post['invite_status']) && $post['invite_status'] == 1) {
                // 获取一个默认用户作为创建者
                $defaultUser = Db::name('user')->order('uid asc')->find();
                if ($defaultUser) {
                    $post['invite_code'] = $this->createInviteCode($defaultUser['uid'], $time);
                    $post['owner_uid'] = $defaultUser['uid'];
                }
            }
            $post['create_time'] = $time;
            if (!isset($post['owner_uid'])) {
                // 如果没有指定创建者，默认使用第一个用户
                $defaultUser = Db::name('user')->order('uid asc')->find();
                if ($defaultUser) {
                    $post['owner_uid'] = $defaultUser['uid'];
                }
            }
            $post['member_count'] = 1;
            
            // 设置默认值
            if (!isset($post['allow_speak'])) $post['allow_speak'] = 1;
            if (!isset($post['allow_comment'])) $post['allow_comment'] = 0;
            
            $channelId = Db::name('channel')->insertGetId($post);
            
            // 添加创建者到频道用户表
            if (isset($post['owner_uid'])) {
                Db::name('channel_user')->insert([
                    'channel_id' => $channelId,
                    'uid' => $post['owner_uid'],
                    'role' => 2, // 创建者
                    'join_time' => $time,
                ]);
            }
            
            if (!$channelId) return json(['code' => 1, 'msg' => '新增失败']);
            return json(['code' => 0, 'msg' => '新增成功']);
        }
    }

    // 删除频道
    public function delete()
    {
        $channelId = input('post.channel_id');
        
        // 验证频道是否存在
        $channel = Db::name('channel')->where('channel_id', $channelId)->find();
        if (!$channel) {
            return json(['code' => 1, 'msg' => '频道不存在']);
        }
        
        // 删除频道
        Db::name('channel')->where('channel_id', $channelId)->delete();
        
        // 删除频道用户关系
        Db::name('channel_user')->where('channel_id', $channelId)->delete();
        
        // 删除频道消息
        Db::name('channel_message')->where('channel_id', $channelId)->delete();
        
        return json(['code' => 0, 'msg' => '删除成功']);
    }

    // 频道成员管理页面
    public function users()
    {
        $channelId = input('get.channel_id');
        $channel = Db::name('channel')->where('channel_id', $channelId)->find();
        if (!$channel) $this->error('频道不存在');
        $this->assign('channel_id', $channelId);
        $this->assign('channel', $channel);
        return $this->fetch();
    }

    // 获取频道成员列表
    public function usersList()
    {
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

    // 删除频道成员
    public function delChannelUser()
    {
        $channelId = input('post.channel_id');
        $delUid = input('post.del_uid');

        $time = time();
        Db::name('channel_user')->where('channel_id', $channelId)->where('uid', $delUid)->update([
            'leave_time' => $time,
            'update_time' => $time,
        ]);
        Db::name('channel')->where('channel_id', $channelId)->setDec('member_count');
        return json(['code' => 0, 'msg' => '删除成功']);
    }

    // 生成邀请码
    protected function createInviteCode($uid, $time)
    {
        return md5('uid_'.$uid.'_time_'.$time.'_channel');
    }
    
    // 频道内容管理页面
    public function content()
    {
        $channelId = input('get.channel_id');
        $channel = Db::name('channel')->where('channel_id', $channelId)->find();
        if (!$channel) $this->error('频道不存在');
        $this->assign('channel_id', $channelId);
        $this->assign('channel', $channel);
        return $this->fetch();
    }
    
    // 获取频道内容列表
    public function getContentList()
    {
        $channelId = input('get.channel_id');
        $page = input('get.page', 1, 'intval');
        $limit = input('get.limit', 10, 'intval');
        
        // 验证频道是否存在
        $channel = Db::name('channel')->where('channel_id', $channelId)->find();
        if (!$channel) {
            return json(['code' => 1, 'msg' => '频道不存在']);
        }
        
        // 查询频道消息，关联用户信息
        $list = Db::name('channel_message')
            ->alias('cm')
            ->join('user u', 'cm.uid = u.uid', 'LEFT')
            ->field('cm.*, u.nickname, u.head_image')
            ->where('cm.channel_id', $channelId)
            ->where('cm.is_delete', 0)
            ->order('cm.ctime', 'desc')
            ->page($page, $limit)
            ->select();
            
        $total = Db::name('channel_message')
            ->where('channel_id', $channelId)
            ->where('is_delete', 0)
            ->count();
            
        // 处理消息内容
        foreach ($list as &$item) {
            $item['contents'] = strip_tags($item['contents']);
            $item['ctime_format'] = date('Y-m-d H:i:s', $item['ctime']);
        }
        
        return json(['code' => 0, 'msg' => 'success', 'count' => $total, 'data' => $list]);
    }
    
    // 删除频道消息
    public function deleteMessage()
    {
        $msgId = input('post.msg_id');
        
        // 验证消息是否存在
        $message = Db::name('channel_message')->where('msg_id', $msgId)->find();
        if (!$message) {
            return json(['code' => 1, 'msg' => '消息不存在']);
        }
        
        // 删除消息
        $result = Db::name('channel_message')->where('msg_id', $msgId)->update([
            'is_delete' => 1
        ]);
        
        if ($result) {
            // 更新频道消息数
            Db::name('channel')->where('channel_id', $message['channel_id'])->setDec('message_count');
            return json(['code' => 0, 'msg' => '删除成功']);
        } else {
            return json(['code' => 1, 'msg' => '删除失败']);
        }
    }
}