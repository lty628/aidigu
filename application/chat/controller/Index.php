<?php
namespace app\chat\controller;
use think\Controller;
use app\chat\model\ChatPrivateLetter;
use app\common\model\Message;
use think\Db;

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
            $this->checkReminder($fromuid, $messageChatId);
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

    protected function checkReminder($fromuid, $messageChatId)
    {
        // $type 0: 转发 1: 评论 2: 回复 3: 好友 4: 私信  5: 群聊 【群聊提醒待定】
        if (!Db::name('reminder')->where('msg_id', $messageChatId)->where('touid',$fromuid)->where('fromuid',$fromuid)->find()) {
            Db::name('reminder')->insert([
                'touid'	=>	$fromuid,
                'fromuid'	=>	$fromuid,
                'msg_id'	=>	$messageChatId,
                'status'	=>	1,
                'type'	=>	1,
                'ctime'	=>	time()
            ]);
        } else {
            Db::name('reminder')->where('msg_id', $messageChatId)->where('touid',$fromuid)->update(['status' => 1]);
        }
    }
}
