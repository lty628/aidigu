<?php
namespace app\chat\libs;


class PrivateLetter extends Base
{
    protected static $listtagid = 'PrivateLetter';


    public static function chatOnline($server, $frame, $frameData)
    {
        // dump($frameData);
        $frameData['listtagid'] = self::$listtagid;
        $frameData = self::initMessageData($frameData);
        $data['create_time'] = $frameData['create_time'];
        $data['fromuid'] = $frameData['fromuid'];
        $data['content'] = $frameData['content'];
        $data['content_type'] = $frameData['content_type'];
        $data['touid'] = $frameData['touid'];
        $server->push($frame->fd, json_encode([
            'code' => 2,
            'msg' => 'success',
            'data' => $frameData
        ], 320));
        $isOnline = \app\chat\libs\ChatDbHelper::isOnline(['uid' => $data['touid']]);
        if ($isOnline && $isOnline['fd']) {
            $server->push($isOnline['fd'], json_encode([
                'code' => 2,
                'msg' => 'success',
                'data' => $frameData
            ], 320));
            $data['send_status'] = 1;
        } else {
            \app\common\libs\Remind::open($data['touid'], 'chat');
        }
        \app\chat\libs\ChatDbHelper::updateMessageCount('chat_private_letter', ['fromuid' => $data['touid'], 'touid' => $data['fromuid']]);
        \app\chat\libs\ChatDbHelper::saveChatPrivateLetterHistory($data);
    }

    public static function chatHistory($server, $frame, $frameData)
    {
        $chatHisory = \app\chat\libs\ChatDbHelper::getChatPrivateLetterHistory($frameData);
        $server->push($frame->fd, json_encode([
            'code' => 3,
            'msg' => 'success',
            'data' => $chatHisory,
            'listtagid' => self::$listtagid
        ], 320));
    }

}