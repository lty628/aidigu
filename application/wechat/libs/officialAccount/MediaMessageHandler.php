<?php

namespace app\wechat\libs\officialAccount;

// 微信用户处理类
use app\wechat\libs\officialAccount\User;


class MediaMessageHandler
{
    public function handle($message = null)
    {
        // 根据不同消息类型进行处理
        switch ($message['MsgType']) {
            case 'text':
                return $this->handleTextMessage($message);
            case 'image':
                return $this->handleImageMessage($message);
            case 'voice':
                return $this->handleVoiceMessage($message);
            case 'video':
                return $this->handleVideoMessage($message);
            case 'event':
                return $this->handleEventMessage($message);
            default:
                return '暂不支持该类型消息';
        }
    }

    /**
     * 处理文本消息
     */
    private function handleTextMessage($message)
    {
        // 简单回复逻辑
        return '您发送的内容是：' . $message['Content'];
    }
    
    /**
     * 处理图片消息
     */
    private function handleImageMessage($message)
    {
        return '收到图片消息';
    }
    
    /**
     * 处理语音消息
     */
    private function handleVoiceMessage($message)
    {
        return '收到语音消息';
    }
    
    /**
     * 处理视频消息
     */
    private function handleVideoMessage($message)
    {
        return '收到视频消息';
    }
    
    /**
     * 处理事件消息
     */
    private function handleEventMessage($message)
    {
        $event = $message['Event'];
        
        switch ($event) {
            case 'subscribe':
                // 检查是否通过二维码关注
                if (isset($message['EventKey']) && strpos($message['EventKey'], 'qrscene_') === 0) {
                    // 通过二维码关注
                    return $this->handleQrScanEvent($message);
                }
                return '欢迎关注我们的公众号！';
            case 'unsubscribe':
                // 用户取消关注
                return '';
            case 'SCAN':
                // 已关注用户扫描二维码
                return $this->handleQrScanEvent($message);
            case 'CLICK':
                // 自定义菜单点击事件
                return $this->handleClickEvent($message);
            default:
                return '收到事件消息';
        }
    }
    
    /**
     * 处理菜单点击事件
     */
    private function handleClickEvent($message)
    {
        $eventKey = $message['EventKey'];
        return '您点击了菜单：' . $eventKey;
    }
    
    /**
     * 处理二维码扫描事件
     */
    private function handleQrScanEvent($message)
    {
        $eventKey = $message['EventKey'];
        $openId = $message['FromUserName'];
        
        // 记录扫描状态到session，用于登录检查
        // 移除qrscene_前缀获取真实的scene值
        if (strpos($eventKey, 'qrscene_') === 0) {
            $sceneStr = substr($eventKey, 8); // 移除'qrscene_'前缀
        } else {
            $sceneStr = $eventKey;
        }
        
        // 如果是登录二维码，记录扫描状态
        if (strpos($sceneStr, 'login_') === 0) {
            // 提取sceneId
            $sceneId = substr($sceneStr, 6); // 移除'login_'前缀
            
            // 存储扫描状态到session
            $scanStatus = [
                'scanned' => true,
                'openid' => $openId,
                'sceneId' => $sceneId,
                'scan_time' => time()
            ];
            
            session('scan_status_' . $sceneId, $scanStatus);
        }
        
        return '扫码成功';
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