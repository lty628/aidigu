<?php
namespace app\chat\libs;


class TagInfo
{

    public static function getTagInfo($uid)
    {
        $data['Friends'] = self::friendList($uid);
        $data['PrivateLetter'] = self::privateLetterList($uid);
        $data['Group'] = self::groupList($uid);
        $data['MessageChat'] = self::messageChatList($uid);

        return [
            'code' => 4,
            'msg' => 'success',
            'data' => [
                'mine' => 0,
                'listtags' => self::tagList(),
                'users' => $data
            ]
        ];
    }

    // 好友列表
    public static function friendList($uid)
    {
        $list = \app\chat\libs\ChatDbHelper::getFriendList($uid, 200);
        // {"code":4,"msg":"success","data":{"mine":0,"listtags":[{"listtagid":"1","listtagname":"我的好友"},{"listtagid":"2","listtagname":"我的群聊"}],"users":{"1":[{"fd":7,"name":"bookapi","avatar":"/uploads/c81e728d9d4c2f636f067f89cc14862c/avatar/20231121/dbdc4662fc214ba89ada1ab7088905b7_middle.jpeg","email":"3528@qq.com","time":"10:31","listtagid":"a"}],"2":[]}}}
        $data = [];
        foreach ($list as $info) {
            $data['index_'.$info['uid']]['nickname'] = $info['nickname'];
            $data['index_'.$info['uid']]['head_image'] = $info['head_image'];
            $data['index_'.$info['uid']]['message_count'] = $info['message_count'];
            $data['index_'.$info['uid']]['uid'] = $info['uid'];
        }
        return $data;
    }

    public static function privateLetterList($uid)
    {
        $list = \app\chat\libs\ChatDbHelper::getPrivateLetterList($uid, 200);
        // {"code":4,"msg":"success","data":{"mine":0,"listtags":[{"listtagid":"1","listtagname":"我的好友"},{"listtagid":"2","listtagname":"我的群聊"}],"users":{"1":[{"fd":7,"name":"bookapi","avatar":"/uploads/c81e728d9d4c2f636f067f89cc14862c/avatar/20231121/dbdc4662fc214ba89ada1ab7088905b7_middle.jpeg","email":"3528@qq.com","time":"10:31","listtagid":"a"}],"2":[]}}}
        $data = [];
        foreach ($list as $info) {
            $data['index_'.$uid]['nickname'] = $info['nickname'];
            $data['index_'.$uid]['head_image'] = $info['head_image'];
            $data['index_'.$uid]['message_count'] = $info['message_count'];
            $data['index_'.$uid]['uid'] = $info['uid'];
        }
        return $data;
    }

    public static function messageChatList($uid)
    {
        $list = \app\chat\libs\ChatDbHelper::getPrivateLetterList($uid, 200);
        // {"code":4,"msg":"success","data":{"mine":0,"listtags":[{"listtagid":"1","listtagname":"我的好友"},{"listtagid":"2","listtagname":"我的群聊"}],"users":{"1":[{"fd":7,"name":"bookapi","avatar":"/uploads/c81e728d9d4c2f636f067f89cc14862c/avatar/20231121/dbdc4662fc214ba89ada1ab7088905b7_middle.jpeg","email":"3528@qq.com","time":"10:31","listtagid":"a"}],"2":[]}}}
        $data = [];
        foreach ($list as $info) {
            $data['index_'.$info['uid']]['nickname'] = $info['nickname'];
            $data['index_'.$info['uid']]['head_image'] = $info['head_image'];
            $data['index_'.$info['uid']]['message_count'] = $info['message_count'];
            $data['index_'.$info['uid']]['uid'] = $info['uid'];
        }
        return $data;
    }

    public static function groupList($uid)
    {
        $list = \app\chat\libs\ChatDbHelper::getGroupList($uid, 50);
        // {"code":4,"msg":"success","data":{"mine":0,"listtags":[{"listtagid":"1","listtagname":"我的好友"},{"listtagid":"2","listtagname":"我的群聊"}],"users":{"1":[{"fd":7,"name":"bookapi","avatar":"/uploads/c81e728d9d4c2f636f067f89cc14862c/avatar/20231121/dbdc4662fc214ba89ada1ab7088905b7_middle.jpeg","email":"3528@qq.com","time":"10:31","listtagid":"a"}],"2":[]}}}
        $data = [];
        foreach ($list as $info) {
            $data['index_'.$info['groupid']]['groupname'] = $info['groupname'];
            $data['index_'.$info['groupid']]['head_image'] = $info['head_image'];
            $data['index_'.$info['groupid']]['message_count'] = $info['message_count'];
            $data['index_'.$info['groupid']]['groupid'] = $info['groupid'];
        }
        return $data;
    }

    public static function tagList()
    {
        return [
            [
                'listtagid' => 'PrivateLetter',
                'listtagname' => '私信',
                'display' => 'block'
            ],
            [
                'listtagid' => 'Friends',
                'listtagname' => '好友',
                'display' => 'block'
            ],
            [
                'listtagid' => 'Group',
                'listtagname' => '群聊',
                'display' => 'block'
            ],
            [
                'listtagid' => 'MessageChat',
                'listtagname' => '评论',
                'display' => 'none'
            ],
            [
                'listtagid' => 'ChannelMessageChat',
                'listtagname' => '频道评论',
                'display' => 'none'
            ],
        ];
    }

}