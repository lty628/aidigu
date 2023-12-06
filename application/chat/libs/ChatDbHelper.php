<?php
namespace app\chat\libs;
use think\Db;

class ChatDbHelper
{

    // 用户是否在线
    public static function isOnline($uid)
    {
        return Db::name('chat_online')->where(['uid' => $uid])->find();
    }

    // 用户上线
    public static function upOnlineUserStatus($uid, $data)
    {
        $find = Db::name('chat_online')->where(['uid' => $uid])->find();
        if ($find) {
            return Db::name('chat_online')->where(['uid' => $uid])->update($data);
        }
        return Db::name('chat_online')->insert($data);
    }

    public static function saveFriendChat($data)
    {
        return Db::name('chat_friends_history')->insert($data);
    }

    public static function getChatHistory($data)
    {
        $result = Db::name('chat_friends_history')->alias('chat')->join([getPrefix() . 'user' => 'user'], 'user.uid=chat.fromuid')->where('fromuid', 'in', [$data['touid'], $data['fromuid']])
            ->where('touid', 'in', [$data['touid'], $data['fromuid']])
            ->limit(1000)
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

    public static function getFriendList($uid, $count = 200)
    {
        return Db::name('user')
            ->alias('user')
            ->join([getPrefix() . 'chat_friends' => 'chat_friends'], 'user.uid=chat_friends.touid')->where('chat_friends.fromuid', $uid)->where('chat_friends.touid', '<>', $uid)
            ->field('user.uid,user.nickname,user.province,user.city,user.message_sum,user.head_image,user.blog,chat_friends.touid,chat_friends.fromuid,chat_friends.mutual_concern,chat_friends.message_count')
            ->order('chat_friends.message_count desc,chat_friends.ctime desc')
            ->limit($count)->select();
    }


    // /**
    //  * 登录
    //  */
    // public static function login($listtagid, $fd, $name, $email, $avatar)
    // {
    //     if ($name == "") {
    //         $name = '游客' . time();
    //     }
    //     if ($email == "") {
    //         $email = 'xxx@qq.com';
    //     }
    //     if (!$name || !$email) {

    //         throw new Exception('Fill in all the required fields.');
    //     }
    //     $user = new ChatUser(array(
    //         'listtagid'    => $listtagid,
    //         'fd'        => $fd,
    //         'name'        => htmlspecialchars($name),
    //         'email'        => $email,
    //         'avatar'    => $avatar
    //     ));
    //     if (!$user->save()) {
    //         throw new Exception('This nick is in use.');
    //     }
    // }
    // /**
    //  * 获取用户在线列表
    //  *
    //  */
    // public static function getOnlineUsers()
    // {
    //     $user = new ChatUser();
    //     $lists = $user->getOnlineUsers();
    //     $users = array();
    //     foreach ($lists as $_k => $_v) {
    //         $users[$_k] = $user->getUsers($_k, array_slice($_v, 0, 100));
    //     }
    //     unset($lists);
    //     return $users;
    // }

    // public static function _p($obj = array(), $exit = 0)
    // {
    //     echo "<pre>";
    //     print_r($obj);
    //     echo "</pre>";
    //     $exit && exit;
    // }

    // public static function logout($listtagid, $fd)
    // {
    //     $user = new ChatUser();
    //     $userInfo = $user->getUser($listtagid, $fd);
    //     return $userInfo;
    // }
    // public static function change($data)
    // {
    //     $pushMsg['code'] = 6;
    //     $pushMsg['msg']  = '换房成功';
    //     $user = new ChatUser();
    //     $is_copyed = $user->changeUser($data['oldlisttagid'], $data['fd'], $data['listtagid']);
    //     if ($is_copyed) {
    //     }
    //     $pushMsg['data']['oldlisttagid'] = $data['oldlisttagid'];
    //     $pushMsg['data']['listtagid'] = $data['listtagid'];
    //     $pushMsg['data']['mine'] = 0;
    //     $pushMsg['data']['fd'] = $data['fd'];
    //     $pushMsg['data']['name'] = $data['params']['name'];
    //     $pushMsg['data']['avatar'] = $data['params']['avatar'];
    //     $pushMsg['data']['time'] = date("H:i", time());
    //     unset($data);
    //     return $pushMsg;
    // }
    // public static function noLogin($data)
    // {
    //     $pushMsg['code'] = 5;
    //     $pushMsg['msg'] = "系统不会存储您的Email，只是为了证明你是一个地球人";
    //     if (!$data['params']['name']) {
    //         $pushMsg['msg'] = "输入一个昵称或许可以让更多人的人了解你";
    //     }
    //     $pushMsg['data']['mine'] = 1;
    //     unset($data);
    //     return $pushMsg;
    // }

    // public static function open($data)
    // {
    //     $pushMsg['code'] = 4;
    //     $pushMsg['msg'] = 'success';
    //     $pushMsg['data']['mine'] = 0;
    //     $pushMsg['data']['listtags'] = self::getRooms();
    //     $pushMsg['data']['users'] = self::getOnlineUsers();
    //     unset($data);
    //     return $pushMsg;
    // }
    // public static function doLogout($data)
    // {
    //     //删除
    //     File::logout($data['fd']);
    //     $pushMsg['code'] = 3;
    //     $pushMsg['msg'] = $data['params']['name'] . "退出了群聊";
    //     $pushMsg['data']['fd'] = $data['fd'];
    //     $pushMsg['data']['name'] = $data['params']['name'];
    //     unset($data);
    //     return $pushMsg;
    // }
    // //发送新消息
    // public static function sendNewMsg($data)
    // {
    //     $pushMsg['code'] = 2;
    //     $pushMsg['msg'] = "";
    //     $pushMsg['data']['listtagid'] = $data['listtagid'];
    //     $pushMsg['data']['fd'] = $data['fd'];
    //     $pushMsg['data']['name'] = $data['params']['name'];
    //     $pushMsg['data']['avatar'] = $data['params']['avatar'];
    //     $pushMsg['data']['newmessage'] = escape(htmlspecialchars($data['message']));
    //     $pushMsg['data']['remains'] = array();
    //     if ($data['c'] == 'img') {
    //         $pushMsg['data']['newmessage'] = '<img class="chat-img" onclick="preview(this)" style="display: block; max-width: 120px; max-height: 120px; visibility: visible;" src=' . $pushMsg['data']['newmessage'] . '>';
    //     } else {
    //         global $emotion;
    //         foreach ($emotion as $_k => $_v) {
    //             $pushMsg['data']['newmessage'] = str_replace($_k, $_v, $pushMsg['data']['newmessage']);
    //         }
    //         $tmp = self::remind($data['listtagid'], $pushMsg['data']['newmessage']);
    //         if ($tmp) {
    //             $pushMsg['data']['newmessage'] = $tmp['msg'];
    //             $pushMsg['data']['remains'] = $tmp['remains'];
    //         }
    //         unset($tmp);
    //     }
    //     $pushMsg['data']['time'] = date("H:i", time());
    //     unset($data);
    //     return $pushMsg;
    // }
    // //登录
    // public static function doLogin($data)
    // {
    //     $pushMsg['code'] = 1;
    //     $pushMsg['msg'] = $data['params']['name'] . "加入了群聊";

    //     $pushMsg['data']['listtagid'] = $data['listtagid'];
    //     $pushMsg['data']['fd'] = $data['fd'];
    //     $pushMsg['data']['name'] = $data['params']['name'];
    //     $pushMsg['data']['avatar'] = DOMAIN . '/static/images/avatar/f1/f_' . rand(1, 12) . '.jpg';
    //     $pushMsg['data']['time'] = date("H:i", time());
    //     self::login($data['listtagid'], $data['fd'], $data['params']['name'], $data['params']['email'], $pushMsg['data']['avatar']);
    //     unset($data);
    //     return $pushMsg;
    // }
    // public static function getRooms()
    // {
    //     global $listtags;
    //     $roomss = array();
    //     foreach ($listtags as $_k => $_v) {
    //         $roomss[] = array(
    //             'listtagid'   => $_k,
    //             'listtagname' => $_v
    //         );
    //     }
    //     return $roomss;
    // }


    // public static function remind($listtagid, $msg)
    // {
    //     $data = array();
    //     if ($msg != "") {
    //         $data['msg'] = $msg;
    //         //正则匹配出所有@的人来
    //         $s = preg_match_all('~@(.+?)　~', $msg, $matches);
    //         if ($s) {
    //             $m1 = array_unique($matches[0]);
    //             $m2 = array_unique($matches[1]);
    //             $user = new ChatUser();
    //             $users = $user->getUsersByRoom($listtagid);
    //             $m3 = array();
    //             foreach ($users as $_k => $_v) {
    //                 $m3[$_v['name']] = $_v['fd'];
    //             }
    //             $i = 0;
    //             foreach ($m2 as $_k => $_v) {
    //                 if (array_key_exists($_v, $m3)) {
    //                     $data['msg'] = str_replace($m1[$_k], '<font color="blue">' . trim($m1[$_k]) . '</font>', $data['msg']);
    //                     $data['remains'][$i]['fd'] = $m3[$_v];
    //                     $data['remains'][$i]['name'] = $_v;
    //                     $i++;
    //                 }
    //             }
    //             unset($users);
    //             unset($m1, $m2, $m3);
    //         }
    //     }
    //     return $data;
    // }
}
