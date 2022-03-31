<?php
namespace app\wechat\controller;

use EasyWeChat\Factory;
use EasyWeChat\Kernel\Messages\Message;
use app\wechat\libs\officialAccount\MediaMessageHandler;

class Index
{
    // 服务器接入（域名不能被举报）
    public function index()
    {  
        $config = config('wechat.');
        // dump($config);die;
        $app = Factory::officialAccount($config);
        // file_put_contents('1.txt', json_encode(input()));
        $app->server->push(MediaMessageHandler::class, Message::EVENT|Message::TEXT|Message::VOICE|Message::VIDEO|Message::SHORT_VIDEO);
        // $app->server->push([$this, 'testFunc']);
        $response = $app->server->serve();
        // 将响应输出
        $response->send();exit;
    }

    // public function getWeChatUserInfo($message)
    // {
    //     $openId = $message['FromUserName'];
    //     $userInfo = $app->user->get($openId);
    //     file_put_contents('1.txt', json_encode($userInfo), FILE_APPEND);
    // }   

    // 需做缓存处理（未完成）
    public function qrcodeLogin()
    {
        $cache = session('qrSceneStr');
        if ($cache) {
            $returnData = $cache;
        } else {
            $config = config('wechat.');
            $app = Factory::officialAccount($config);
            $qrSceneStr = md5(time() . uniqid());
            $result = $app->qrcode->temporary($qrSceneStr, 6 * 24 * 3600);
            $ticket = $result['ticket'];
            $url = $app->qrcode->url($ticket);
            // $content = file_get_contents($url);
            $returnData = ['qrSceneStr' => $qrSceneStr, 'img' => '<img src="'.$url.'" height="150" />'];
            session('qrSceneStr', $returnData);
        }
        return ajaxJson(1, 'ok', $returnData); 
    }
}