<?php
namespace app\chat\libs;


class Group extends Base
{
    protected static $listtagid = 'Group';


    public static function chatOnline($server, $frame, $frameData)
    {
        // dump($frameData);
        $frameData['listtagid'] = self::$listtagid;
        $frameData = self::initMessageData($frameData);
        $data['create_time'] = $frameData['create_time'];
        $data['fromuid'] = $frameData['fromuid'];
        $data['content'] = $frameData['content'];
        $data['content_type'] = $frameData['content_type'];
        $data['groupid'] = $frameData['groupid'];
        $offLineUser = [];
        $onlineInfo = \app\chat\libs\ChatDbHelper::groupOnlineInfo($data['groupid']);
        if ($onlineInfo) {
            foreach ($onlineInfo as $isOnline) {
                if ($isOnline['dtime'] != 0) continue;
                if (!$isOnline['fd']) {
                    $offLineUser[] = $isOnline['uid'];
                    \app\common\libs\Remind::open($isOnline['uid'], 'chat');
                    continue;
                };
                $server->push($isOnline['fd'], json_encode([
                    'code' => 2,
                    'msg' => 'success',
                    'data' => $frameData
                ], 320));
            }
        }
        if ($offLineUser) {
            \app\chat\libs\ChatDbHelper::updateMessageCount('chat_group_user', [['uid', 'in', $offLineUser], ['groupid', '=', $data['groupid']]]);
        }
        \app\chat\libs\ChatDbHelper::saveChatGroupHistory($data);
    }

    public static function chatHistory($server, $frame, $frameData)
    {
        $chatHisory = \app\chat\libs\ChatDbHelper::getChatGroupHistory($frameData);
        $server->push($frame->fd, json_encode([
            'code' => 3,
            'msg' => 'success',
            'data' => $chatHisory,
            'listtagid' => self::$listtagid
        ], 320));
    }

}