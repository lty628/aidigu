<?php
namespace app\chat\controller;
use think\Controller;
use app\chat\model\ChatPrivateLetter;
use app\common\model\Message;
// use think\Db;

class Index extends Controller
{
    public function initialize()
    {
        $this->assign('isMobile', isMobile());
    }

    public function index()
    {
        $touid = input('private');
        $messageChatId = input('messageChatId');
        $fromuid = getLoginUid();
        $wsserver = env('app.chatSocketDomain');

        if (!$fromuid) {
            $this->error('没有登录');
        }

        if ($touid) {
            $this->checkPrivate($touid, $fromuid);
        }

        if ($messageChatId) {
            $messageModel = new Message();
            $msgInfo = $messageModel->field('uid')->where('msg_id', $messageChatId)->find();
            if (!$msgInfo) {
                $this->error('消息不存在');
            }
            $touid = $msgInfo['uid'];
        }

        // $domain = 'https://'.$urlDomainRoot;
        $this->assign('wsserver', $wsserver);
        $this->assign('touid', $touid);
        $this->assign('messageChatId', $messageChatId);
        $this->assign('fromuid', $fromuid);
        return $this->fetch();
    }

    protected function checkPrivate($touid, $fromuid)
    {
        $model = new ChatPrivateLetter();
        $info = $model->where([
            'fromuid' => $fromuid,
            'touid' => $touid
        ])->find();
        if (!$info) {
            return $model->addPrivateFriend($touid, $fromuid);
        }
        return true;
    }
}
