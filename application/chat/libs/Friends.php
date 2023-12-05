<?php
namespace app\chat\libs;


class Friends
{

    // 好友列表
    public static function list($uid)
    {
        $friendList = \app\common\helper\Fans::getMyConcern($uid, 200)->toArray();
        // {"code":4,"msg":"success","data":{"mine":0,"listtags":[{"listtagid":"1","listtagname":"我的好友"},{"listtagid":"2","listtagname":"我的群聊"}],"users":{"1":[{"fd":7,"name":"bookapi","avatar":"/uploads/c81e728d9d4c2f636f067f89cc14862c/avatar/20231121/dbdc4662fc214ba89ada1ab7088905b7_middle.jpeg","email":"3528@qq.com","time":"10:31","listtagid":"a"}],"2":[]}}}
        $data = [];
        foreach ($friendList['data'] as $key =>$userInfo) {
            $data['friends'][$userInfo['uid']]['nickname'] = $userInfo['nickname'];
            $data['friends'][$userInfo['uid']]['head_image'] = $userInfo['head_image'];
            $data['friends'][$userInfo['uid']]['uid'] = $userInfo['uid'];
        }
        return [
            'code' => 4,
            'msg' => 'success',
            'data' => [
                'mine' => 0,
                'listtags' => self::getTagInfo(),
                'users' => $data
            ]
        ];
    }

    public static function getTagInfo()
    {
        return [
            // [
            //     'listtagid' => 'private_letter',
            //     'listtagname' => '私信'
            // ],
            [
                'listtagid' => 'friends',
                'listtagname' => '好友'
            ],
            // [
            //     'listtagid' => 'group',
            //     'listtagname' => '群聊'
            // ],
        ];
    }

}