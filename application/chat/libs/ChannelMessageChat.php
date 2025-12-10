<?php
namespace app\chat\libs;


class ChannelMessageChat extends Base
{
    protected static $listtagid = 'ChannelMessageChat';


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
        \app\chat\libs\ChatDbHelper::saveChannelMessageChatHistory($data);
        $info = \app\chat\libs\ChatDbHelper::channelMessageChatOnlineInfo($data['msg_id'], $data['fromuid']);
        $onlineInfo = $info['onlineInfo'];
        $uids = [];

        if ($onlineInfo) {
            foreach ($onlineInfo as $isOnline) {
                if (!$isOnline['fd']) {
                    // $uids[] = $isOnline['uid'];
                    continue;
                };
                $server->push($isOnline['fd'], json_encode([
                    'code' => 2,
                    'msg' => 'success',
                    'data' => $frameData
                ], 320));
            }
        }

        // \app\chat\libs\ChatDbHelper::upComentInfo($data);

    }

    public static function chatHistory($server, $frame, $frameData)
    {
        $chatHisory = \app\chat\libs\ChatDbHelper::getChannelMessageChatHistory($frameData);
        $server->push($frame->fd, json_encode([
            'code' => 3,
            'msg' => 'success',
            'data' => $chatHisory,
            'listtagid' => self::$listtagid
        ], 320));
    }

}