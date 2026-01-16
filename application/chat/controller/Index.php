<?php
namespace app\chat\controller;
use think\Controller;
use app\chat\model\ChatPrivateLetter;
use app\chat\model\ChannelMessage;
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
        $channelMessageChatId = input('channelMessageChatId');
        $fromuid = getLoginUid();
        $wsserver = sysConfig('app.chatSocketDomain');

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
            $this->checkReminder($fromuid, $touid, $messageChatId);
        }
        
        if ($channelMessageChatId) {
            $channelMessageModel = new ChannelMessage();
            $channelMsgInfo = $channelMessageModel->field('uid')->where('msg_id', $channelMessageChatId)->find();
            if (!$channelMsgInfo) {
                $this->error('消息不存在');
            }
            $touid = $channelMsgInfo['uid'];
            $this->checkChannelReminder($fromuid, $touid, $channelMessageChatId);
        }

        // $domain = 'https://'.$urlDomainRoot;
        $this->assign('wsserver', $wsserver);
        $this->assign('touid', $touid);
        $this->assign('messageChatId', $messageChatId);
        $this->assign('channelMessageChatId', $channelMessageChatId);
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

    protected function checkReminder($fromuid, $touid, $messageChatId)
    {
        if ($fromuid == $touid) {
            return Db::name('reminder')->where('msg_id', $messageChatId)->where('touid', $touid)->update(['status' => 1]);
        };
        // $type 0: 转发 1: 评论 2: 回复 3: 好友 4: 私信  5: 群聊 【群聊提醒待定】
        if (!Db::name('reminder')->where('msg_id', $messageChatId)->where('touid',$touid)->where('fromuid',$fromuid)->find()) {
            Db::name('reminder')->insert([
                'touid'	=>	$touid,
                'fromuid'	=>	$fromuid,
                'msg_id'	=>	$messageChatId,
                'status'	=>	1,
                'type'	=>	1,
                'ctime'	=>	time()
            ]);
        } else {
            Db::name('reminder')->where('msg_id', $messageChatId)->where('touid', $fromuid)->update(['status' => 1]);
        }
    }
    
    // 添加频道提醒检查方法（如果尚未存在）
    protected function checkChannelReminder($fromuid, $touid, $channelMessageChatId)
    {
        // 这里可以添加频道提醒的逻辑
        // 目前留空，可以根据实际需求实现
    }
}