<?php

namespace app\common\controller;

use app\common\controller\Info;
use think\Db;
// use think\facade\Session;
use app\common\model\Message;
// use app\common\model\User;
use app\common\model\Fans;

class IndexInfo extends Info
{
    // 个人首页
    public function index()
    {
        $userMessage = Db::name('message')
            ->alias('message')
            ->join([$this->prefix . 'fans' => 'fans'], 'message.uid=fans.touid and fans.fromuid=' . $this->siteUserId)
            ->join([$this->prefix . 'user' => 'user'], 'user.uid=fans.touid')
            ->order('message.ctime desc')
            ->field('user.uid,user.nickname,user.head_image,user.blog,message.ctime,message.contents,message.repost,message.refrom,message.repostsum,message.image,message.image_info,message.commentsum,message.msg_id')
            ->where('message.is_delete', 0)
            ->paginate(30, false, ['page' => request()->param('page/d', 1), 'path' => '[PAGE].html']);
        $this->assign('userMessage', []);
        if (request()->isAjax()) {
            $userMessage = $userMessage->toArray()['data'];
            return json(array('status' =>  1, 'msg' => 'ok', 'data' => ['data' => $userMessage, 'allow_delete' => 0]));
        }
        return $this->fetch();
    }
    public function own()
    {
        $userMessage = $this->getMessage($this->siteUserId, 30);
        if (request()->isAjax()) {
            $allwoDelete = 1;
            if ($this->siteUserId != $this->userid) $allwoDelete = 0;
            $userMessage = $userMessage->toArray()['data'];
            return json(array('status' =>  1, 'msg' => 'ok', 'data' => ['data' => $userMessage, 'allow_delete' => $allwoDelete]));
        }
        $this->assign('siteUser', $this->siteUserId);
        $this->assign('userMessage', []);
        return $this->fetch('index');
    }
    // 广场
    public function blog()
    {
        $userMessage = $this->getMessage('', 30);
        $this->assign('siteUser', $this->siteUserId);
        if (request()->isAjax()) {
            $userMessage = $userMessage->toArray()['data'];
            return json(array('status' =>  1, 'msg' => 'ok', 'data' => ['data' => $userMessage, 'allow_delete' => 0]));
        }
        $this->assign('userMessage', []);
        return $this->fetch('index');
    }

    // 话题
    public function topic()
    {
        $topicId = input('topic_id');
        $userMessage = $this->getMessage('', 30, $topicId);
        $this->assign('siteUser', $this->siteUserId);
        if (request()->isAjax()) {
            $userMessage = $userMessage->toArray()['data'];
            return json(array('status' =>  1, 'msg' => 'ok', 'data' => ['data' => $userMessage, 'allow_delete' => 0]));
        }
        Db::name('topic')->where('topic_id', $topicId)->setInc('count',1);
        $topic = Db::name('topic')->where('topic_id', $topicId)->find();
        $this->assign('topicTitle', $topic['title']);
        $this->assign('userMessage', []);
        return $this->fetch('index');
    }

    // 话题
    public function topicSquare()
    {
        $topic = Db::name('topic')->order('count', 'desc')->paginate(30, false, ['page' => request()->param('page/d', 1), 'path' => '[PAGE].html']);
        $this->assign('topicArr', $topic);
        return $this->fetch('topic');
    }

    public function fans()
    {
        $userFans = $this->getMyFans($this->siteUserId, 20);
        $this->assign('userFans', $userFans);
        return $this->fetch('fansInfo');
    }
    public function concern()
    {
        $userFans = $this->getMyConcern($this->siteUserId, 20);
        $this->assign('userFans', $userFans);
        return $this->fetch('fansInfo');
    }
    public function messageInfo()
    {
        $this->getMessageById((int)input('param.msg_id'));
        $this->assign('siteUser', $this->siteUserId);
        return $this->fetch('message');
    }
    public function setting()
    {
        return $this->fetch('setting_info');
    }
    public function avatar()
    {
        return $this->fetch('setting_avatar');
    }
    public function background()
    {
        return $this->fetch('setting_background');
    }
    public function passwd()
    {
        return $this->fetch('setting_passwd');
    }
    public function newrepeat()
    {
        $this->getReminderMsg();
        return $this->fetch();
    }
    public function newreply()
    {
        $this->getReminderMsg();
        return $this->fetch();
    }
    public function newcomment()
    {
        $this->getReminderMsg();
        return $this->fetch();
    }
}
