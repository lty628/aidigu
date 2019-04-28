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
	//首页
    public function index()
    {
        $userMessage = Db::name('message')
            ->alias('message')
            ->join([$this->prefix.'fans'=>'fans'],'message.uid=fans.touid and fans.fromuid='.$this->siteUserId)
            ->join([$this->prefix.'user'=>'user'],'user.uid=fans.touid')
            ->order('message.msg_id desc')
            ->field('user.uid,user.nickname,user.head_image,user.blog,message.ctime,message.contents,message.repost,message.refrom,message.repostsum,message.image,message.commentsum,message.msg_id')
            ->paginate(30);
        $this->assign('userMessage', $userMessage);
        return $this->fetch();
    }
    public function own()
    {
        $this->getMessage($this->siteUserId, 30);
        $this->assign('siteUser', $this->siteUserId);
        return $this->fetch('index');
    }
    public function blog()
    {
        $this->getMessage('', 30);
        $this->assign('siteUser', $this->siteUserId);
        return $this->fetch('index');
    }
    public function fans()
    {
        $userFans = $this->getMyFans($this->siteUserId, 20);
        $this->assign('userFans', $userFans);
        return $this->fetch('fansInfo');
    }
    public function concern()
    {
        $userFans =$this->getMyConcern($this->siteUserId, 20);
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
