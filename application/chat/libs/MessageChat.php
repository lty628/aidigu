<?php
namespace app\chat\libs;


class MessageChat extends Base
{
    protected static $listtagid = 'MessageChat';


    public static function chatOnline($server, $frame, $frameData)
    {
        $frameData['listtagid'] = self::$listtagid;
        $frameData = self::initMessageData($frameData);
        $data['ctime'] = strtotime($frameData['create_time']);
        $data['fromuid'] = $frameData['fromuid'];
        $data['msg'] = $frameData['content'];
        $data['touid'] = $frameData['touid'];
        // $data['content_type'] = $frameData['content_type'];
        $data['msg_id'] = $frameData['msgid'];
        // $offLineUser = [];
        \app\chat\libs\ChatDbHelper::saveComentChatHistory($data);
        $info = \app\chat\libs\ChatDbHelper::messageChatOnlineInfo($data['msg_id']);
        $onlineInfo = $info['onlineInfo'];
        $uids = $info['uids'];
        if ($onlineInfo) {
            foreach ($onlineInfo as $isOnline) {
                if (!$isOnline['fd']) continue;
                $server->push($isOnline['fd'], json_encode([
                    'code' => 2,
                    'msg' => 'success',
                    'data' => $frameData
                ], 320));
            }
        }
        // if ($offLineUser) {
        //     \app\chat\libs\ChatDbHelper::updateMessageCount('chat_group_user', [['uid', 'in', $offLineUser], ['groupid', '=', $data['groupid']]]);
        // }
        \app\chat\libs\ChatDbHelper::upComentInfo($data, $uids);
    }

    public static function chatHistory($server, $frame, $frameData)
    {
        $chatHisory = \app\chat\libs\ChatDbHelper::getMessageChatHistory($frameData);
        $server->push($frame->fd, json_encode([
            'code' => 3,
            'msg' => 'success',
            'data' => $chatHisory,
            'listtagid' => self::$listtagid
        ], 320));
    }

}