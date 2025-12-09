<?php
namespace app\chat\libs;
use think\Db;

class ChatDbHelper
{

    public static function initFd()
    {
        return Db::name('chat_online')->where('fd', '>', 0)->update([
            'fd' => 0
        ]);
    }

    // 用户是否在线
    public static function isOnline($where)
    {
        return Db::name('chat_online')->where($where)->find();
    }
    // 群在线信息
    public static function groupOnlineInfo($groupId)
    {
        return Db::name('chat_group_user')->alias('chat_group_user')
            ->join([getPrefix() . 'chat_online' => 'chat_online'], 'chat_online.uid=chat_group_user.uid')
            ->where('groupid', $groupId)
            ->select();
    }
    
    // 在线评论信息
    public static function messageChatOnlineInfo($msgId, $fromuid)
    {
        // $type 0: 转发 1: 评论 2: 回复 3: 好友 4: 私信  5: 群聊 【群聊提醒待定】
        // ->where('status', 0)
        $userIds = Db::name('reminder')->where('msg_id', $msgId)->where('type', 1)->field('fromuid')->group('fromuid')->limit(300)->order('id desc')->select();
        // 去重
        // $userIds = array_unique(array_column($userIds, 'fromuid'));
        $userIds = array_column($userIds, 'fromuid');
        array_push($userIds, $fromuid);
        // dump($userIds);
        $onlineInfo = Db::name('chat_online')
            ->where('uid', 'in', $userIds)
            ->select();
        // $info['uids'] = $userIds;
        $info['onlineInfo'] = $onlineInfo;
        // dump($info);
        return $info;
    }

    // 频道消息在线信息
    public static function channelMessageChatOnlineInfo($msgId, $fromuid)
    {
        // 获取频道中的所有用户ID（这里假设有一个频道消息提醒表或者可以通过频道成员表获取）
        $userIds = Db::name('channel_user')
            ->where('channel_id', $msgId)
            ->field('uid')
            ->select();
            
        $userIds = array_column($userIds, 'uid');
        // 确保发送者也在通知列表中
        if (!in_array($fromuid, $userIds)) {
            array_push($userIds, $fromuid);
        }
        
        $onlineInfo = Db::name('chat_online')
            ->where('uid', 'in', $userIds)
            ->select();
            
        $info['onlineInfo'] = $onlineInfo;
        return $info;
    }

    public static function saveComentChatHistory($data)
    {
        $pattern = '/@{1}(\w*[\.0-9]*[\x{4e00}-\x{9fa5}]*)([：:]){0,1}([：:\.;])*/ui';
        if (preg_match($pattern, $data['msg'], $matches)) {
            $findUser = Db::name('user')->where('nickname',$matches[1])->field('uid,blog')->find();
            if ($findUser) {
                $data['ctype'] = 1;
                self::upReplayReminder($data, $findUser['uid']);
            }
        } else {
            self::upComentReminder($data);
        }
        return Db::name('comment')->insert($data);
    }

    // 保存频道消息历史
    public static function saveChannelMessageChatHistory($data)
    {
        // 插入频道消息到channel_message表
        return Db::name('channel_message')->insert($data);
    }

    public static function upReplayReminder($data, $touid)
    {
        if (!Db::name('reminder')->where('msg_id', $data['msg_id'])->where('fromuid', $data['fromuid'])->where('touid', $touid)->find()) {
            Db::name('reminder')->insert([
                'touid'	=>	$touid,
                'fromuid'	=>	$data['fromuid'],
                'msg_id'	=>	$data['msg_id'],
                'status'	=>	0,
                'type'	=>	2,
                'ctime'	=>	time()
            ]);
        } else {
            Db::name('reminder')->where('msg_id', $data['msg_id'])->where('fromuid', $data['fromuid'])->where('touid', $touid)->update([
                'status'	=>	0
            ]);
        }
    }

    public static function getMessageChatHistory($data)
    {
        $result = Db::name('comment')->alias('comment')->join([getPrefix() . 'user' => 'user'], 'user.uid=comment.fromuid')
            ->where('msg_id', $data['msgid'])
            ->limit(300)
            ->order('comment.cid', 'desc')
            ->field('user.head_image,user.nickname,comment.cid as chat_id,comment.fromuid,comment.msg_id as groupid,comment.msg as content,DATE_FORMAT(FROM_UNIXTIME(comment.ctime), "%Y-%m-%d %H:%i:%s") AS create_time')->select();
        return $result;
    }

    // 获取频道消息历史
    public static function getChannelMessageChatHistory($data)
    {
        $result = Db::name('channel_message')
            ->alias('channel_message')
            ->join([getPrefix() . 'user' => 'user'], 'user.uid=channel_message.fromuid')
            ->where('msg_id', $data['msgid'])
            ->limit(300)
            ->order('channel_message.cid', 'desc')
            ->field('user.head_image,user.nickname,channel_message.cid as chat_id,channel_message.fromuid,channel_message.msg_id as groupid,channel_message.msg as content,DATE_FORMAT(FROM_UNIXTIME(channel_message.ctime), "%Y-%m-%d %H:%i:%s") AS create_time')
            ->select();
        return $result;
    }

    public static function upComentReminder($data)
    {
        Db::name('message')->where('msg_id',$data['msg_id'])->setInc('commentsum',1);
        // // $uids 移除 $data['fromuid']
        // $uids = array_diff($uids, [$data['fromuid']]);

        // if (!Db::name('reminder')->where('msg_id', $data['msg_id'])->where('fromuid', $data['fromuid'])->find()) {
        //     Db::name('reminder')->insert([
        //         'touid'	=>	$data['touid'],
        //         'fromuid'	=>	$data['fromuid'],
        //         'msg_id'	=>	$data['msg_id'],
        //         'status'	=>	1,
        //         'type'	=>	1,
        //         'ctime'	=>	time()
        //     ]);
        // }
        Db::name('reminder')->where('msg_id', $data['msg_id'])->where('fromuid', $data['fromuid'])->where('touid', $data['touid'])->update(['status' => 0]);
        // if ($data['touid'] == $data['fromuid']) {
        //     Db::name('reminder')->where('msg_id', $data['msg_id'])->where('fromuid', $data['fromuid'])->where('touid', $data['touid'])->update(['status' => 0]);
        // } else {
        //     Db::name('reminder')->where('msg_id', $data['msg_id'])->where('touid', $data['touid'])->update(['status' => 0]);
        // }
        
        return true;
    }

    public static function updateMessageCount($tableName, $where)
    {
        return Db::name($tableName)->where($where)->update([
            'message_count'		=>	Db::raw('message_count+1'),
            'utime'	=>	time()
        ]);
    }

    // 用户上线
    public static function upOnlineUserStatus($where, $data)
    {
        $find = Db::name('chat_online')->where($where)->find();
        if ($find) {
            return Db::name('chat_online')->where($where)->update($data);
        }
        return Db::name('chat_online')->insert($data);
    }

    public static function saveChatPrivateLetterHistory($data)
    {
        return Db::name('chat_private_letter_history')->insert($data);
    }

    public static function saveChatFriendHistory($data)
    {
        return Db::name('chat_friends_history')->insert($data);
    }

    public static function saveChatGroupHistory($data)
    {
        return Db::name('chat_group_history')->insert($data);
    }

    public static function getChatFriendHistory($data)
    {
        $result = Db::name('chat_friends_history')->alias('chat')->join([getPrefix() . 'user' => 'user'], 'user.uid=chat.fromuid')->where('fromuid', 'in', [$data['touid'], $data['fromuid']])
            ->where('touid', 'in', [$data['touid'], $data['fromuid']])
            ->limit(200)
            ->order('chat_id', 'desc')
            ->field('user.head_image,user.nickname,chat.*')->select();
        Db::name('chat_friends_history')->where('fromuid', $data['touid'])->where('touid', $data['fromuid'])->where('send_status', 0)->update([
            'send_status' => 1
        ]);
        Db::name('chat_friends')->where('fromuid', $data['fromuid'])->where('touid', $data['touid'])->update([
            'message_count' => 0
        ]);
        return $result;
    }

    public static function getChatPrivateLetterHistory($data)
    {
        $result = Db::name('chat_private_letter_history')->alias('chat')->join([getPrefix() . 'user' => 'user'], 'user.uid=chat.fromuid')->where('fromuid', 'in', [$data['touid'], $data['fromuid']])
            ->where('touid', 'in', [$data['touid'], $data['fromuid']])
            ->limit(200)
            ->order('chat_id', 'desc')
            ->field('user.head_image,user.nickname,chat.*')->select();
        Db::name('chat_private_letter_history')->where('fromuid', $data['touid'])->where('touid', $data['fromuid'])->where('send_status', 0)->update([
            'send_status' => 1
        ]);
        Db::name('chat_private_letter')->where('fromuid', $data['fromuid'])->where('touid', $data['touid'])->update([
            'message_count' => 0
        ]);
        return $result;
    }

    public static function getChatGroupHistory($data)
    {
        $result = Db::name('chat_group_history')->alias('chat')
            ->join([getPrefix() . 'user' => 'user'], 'user.uid=chat.fromuid')
            ->where('groupid', $data['groupid'])
            ->limit(200)
            ->order('chat_id', 'desc')
            ->field('user.head_image,user.nickname,chat.*')->select();
        Db::name('chat_group_user')->where('uid', $data['uid'])->where('groupid', $data['groupid'])->update([
            'message_count' => 0
        ]);
        return $result;
    }

    // 获取频道消息历史（新增方法）
    public static function getChannelMessageHistory($data)
    {
        $result = Db::name('channel_message')
            ->alias('channel_message')
            ->join([getPrefix() . 'user' => 'user'], 'user.uid=channel_message.fromuid')
            ->where('channel_id', $data['channel_id'])
            ->limit(200)
            ->order('channel_message.cid', 'desc')
            ->field('user.head_image,user.nickname,channel_message.*')
            ->select();
            
        // 更新用户在该频道的消息计数为0
        Db::name('channel_user')
            ->where('uid', $data['uid'])
            ->where('channel_id', $data['channel_id'])
            ->update(['message_count' => 0]);
            
        return $result;
    }

    public static function getFriendList($uid, $count = 200)
    {
        return Db::name('user')
            ->alias('user')
            ->join([getPrefix() . 'chat_friends' => 'chat_friends'], 'user.uid=chat_friends.touid')->where('chat_friends.fromuid', $uid)->where('chat_friends.touid', '<>', $uid)
            ->field('user.uid,user.nickname,user.head_image,chat_friends.touid,chat_friends.fromuid,chat_friends.mutual_concern,chat_friends.message_count')
            ->order('chat_friends.utime asc')
            ->limit($count)->select();
    }

    public static function getPrivateLetterList($uid, $count = 200)
    {
        return Db::name('user')
            ->alias('user')
            ->join([getPrefix() . 'chat_private_letter' => 'chat_private_letter'], 'user.uid=chat_private_letter.touid')->where('chat_private_letter.fromuid', $uid)->where('chat_private_letter.touid', '<>', $uid)
            ->field('user.uid,user.nickname,user.head_image,chat_private_letter.touid,chat_private_letter.fromuid,chat_private_letter.mutual_concern,chat_private_letter.message_count')
            ->order('chat_private_letter.utime asc')
            ->limit($count)->select();
    }

    public static function getGroupList($uid, $count = 50)
    {
        return Db::name('chat_group')
            ->alias('chat_group')
            ->join([getPrefix() . 'chat_group_user' => 'chat_group_user'], 'chat_group.groupid=chat_group_user.groupid')
            ->where('chat_group_user.uid', $uid)
            ->where('chat_group_user.dtime', 0)
            ->field('chat_group.groupid,chat_group.groupname,chat_group.head_image,chat_group_user.uid,chat_group_user.message_count')
            ->order('chat_group_user.utime asc')
            ->limit($count)->select();
    }
}