<?php

namespace app\wechat\libs\officialAccount;

use EasyWeChat\Kernel\Contracts\EventHandlerInterface;
// 微信用户处理类
use app\wechat\libs\officialAccount\User;


class MediaMessageHandler implements EventHandlerInterface
{
    public function handle($message = null)
    {

        switch ($message['MsgType']) {
                
                //收到事件消息
                case 'event':
                    return $this->event($message);
                case 'text':
                    return '收到文字消息';
                    break;
                case 'image':
                    return '收到图片消息';
                    break;
                case 'voice':
                    return '收到语音消息';
                    break;
                case 'video':
                    return '收到视频消息';
                    break;
                case 'location':
                    return '收到坐标消息';
                    break;
                case 'link':
                    return '收到链接消息';
                    break;
                case 'file':
                    return '收到文件消息';
                // ... 其它消息
                default:
                    return '收到其它消息';
                    break;
            }
    }

    public function event($message)
    {
        // file_put_contents('1.txt', json_encode($message));
        $message = json_decode(json_encode($message), true);
        $event = $message['Event'];
        $openId = $message['FromUserName'];
        $userInfo = User::info($openId);
        // // 检查用户并登录
        User::checkUser(json_decode(json_encode($userInfo), true), $message['EventKey']);
        return '欢迎关注爱嘀咕~';
    }
}