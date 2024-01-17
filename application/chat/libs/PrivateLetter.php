<?php
namespace app\chat\libs;


class PrivateLetter
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

    protected static function initMessageData($data)
    {
        $time = time();
        $data['create_time'] = date('Y-m-d H:i:s', $time);
        if (!$data['content_type']) return $data;
        if ($data['content_type'] == 'mp3') {
            $data['content'] = '<p class="massageImg clear"><audio id="music_' . (string)$time . '" class="music" controls="controls" loop="loop" onplay="stopOther(this)" preload="none" controlsList="nodownload" οncοntextmenu="return false" name="media"><source src="' . $data['content'] . '" type="audio/mpeg"></audio></p>';
        } elseif($data['content_type'] == 'mp4' || $data['content_type'] == 'm3u8') {
            $data['content'] = '<p  class="massageImg"><video width="300px"  controls=""  name="media"><source src="'.$data['content'].'" type="video/mp4"></video></p>';
        } else {
            $data['content'] = '<img width="150px" class="massageImgCommon massageImg_jpg" onclick="showMessageImg(this)" src="' . $data['content'] . '">';
        }
        return $data;
    }
}