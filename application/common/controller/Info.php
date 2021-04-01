<?php
namespace app\common\controller;

use app\common\controller\Base;
use think\Db;
use app\common\model\User;
use app\common\model\Fans;
use app\common\model\Message;
use app\common\model\Reminder;
/**
 * 	自定义基类
 */
class Info extends Base
{
	protected $prefix = '';
	protected $loginUserInfo = [];
	protected $siteUserId = '';
	protected $userid = '';
	protected $isSiteUser = true;
	protected function initialize()
	{
		parent::initialize();
		$this->userid = getLoginUid();
		$this->prefix = config('database.prefix');
		$blog = input('param.name');
		$this->loginUserInfo = getLoginUserInfo();
		$this->siteUserId = $this->userid;
		if ($blog) {
			$siteUser = User::getBlogInfo($blog);
			if ($siteUser) {
				$this->siteUserId = $siteUser['uid'];
			} else {
				$this->redirect('/'.$this->loginUserInfo['blog'].'/');
			}
		}
		if ($this->siteUserId != $this->userid) $this->isSiteUser = false;
    	$this->getSiteUserInfo($this->siteUserId);
    	$this->setMyFans($this->siteUserId);
    	$this->setMyConcern($this->siteUserId);
		// $this->assign('siteUserId', $this->siteUserId);
		$this->assign('loginUserInfo', $this->loginUserInfo);
	}
	protected function getSiteUserInfo($userid) 
	{
		if (request()->action() == 'setting') {
			$userInfo = User::getUserSetting($userid);
		} else {
			$userInfo = User::getUserInfo($userid);
		}
		// $userInfo['repeat_sum'] = Reminder::where('type', 0)->where('touid', $this->userid)->count();
		// $userInfo['comment_sum'] = Reminder::where('type', 1)->where('touid', $this->userid)->count();
		$userInfo['reply_sum'] = Reminder::where('touid', $this->userid)->count();
		// $userInfo['fans_count'] = Fans::where('touid', $userid)->count();
		// $userInfo['concern_count'] = Fans::where('fromuid', $userid)->count();
		// $userInfo['fans_count'] = Fans::where('touid', $userid)->where('fromuid','<>',$userid)->count();
		// $userInfo['concern_count'] = Fans::where('fromuid', $userid)->where('touid','<>',$userid)->count();
		// $userInfo['message_count'] = Message::where('uid', $userid)->count();
		$userInfo['is_concern'] = Fans::where('touid', $userid)->where('fromuid',$this->userid)->find();
		$userInfo['isSiteUser'] = $this->isSiteUser;
		$this->assign('userInfo', $userInfo);
	}
	protected function setMyFans($userid)
	{
		$fansInfo = Db::name('user')
			->alias('user')
			->join([$this->prefix.'fans'=>'fans'],'user.uid=fans.fromuid')->where('fans.touid',$userid)->where('fans.fromuid','<>',$userid)
			->field('user.uid,user.nickname,user.province,user.city,user.message_sum,user.head_image,user.blog,fans.touid,fans.fromuid,fans.mutual_concern')
			->order('fans.ctime desc')
			->limit(9)
			->select();
		$this->assign('fansInfo', $fansInfo);
	}
	protected function setMyConcern($userid)
	{
		$getConcern = Db::name('user')
			->alias('user')
			->join([$this->prefix.'fans'=>'fans'],'user.uid=fans.touid')->where('fans.fromuid',$userid)->where('fans.touid','<>',$userid)
			->field('user.uid,user.nickname,user.province,user.city,user.message_sum,user.head_image,user.blog,fans.touid,fans.fromuid,fans.mutual_concern')
			->order('fans.ctime desc')
			->limit(9)
			->select();
		$this->assign('getMyConcern', $getConcern);
	}
	//关注我的
	protected function getMyFans($userid, $count = 9)
	{
		$fansInfo = Db::name('user')
			->alias('user')
			->join([$this->prefix.'fans'=>'fans'],'user.uid=fans.fromuid')->where('fans.touid',$userid)->where('fans.fromuid','<>',$userid)
			->field('user.uid,user.nickname,user.province,user.city,user.message_sum,user.head_image,user.blog,fans.touid,fans.fromuid,fans.mutual_concern')
			->order('fans.ctime desc')
			->paginate($count);
		return $fansInfo;
	}
	//我关注的
	protected function getMyConcern($userid, $count = 9)
	{
		$getConcern = Db::name('user')
			->alias('user')
			->join([$this->prefix.'fans'=>'fans'],'user.uid=fans.touid')->where('fans.fromuid',$userid)->where('fans.touid','<>',$userid)
			->field('user.uid,user.nickname,user.province,user.city,user.message_sum,user.head_image,user.blog,fans.touid,fans.fromuid,fans.mutual_concern')
			->order('fans.ctime desc')
			->paginate($count);
		return $getConcern;	
	}
	protected function getMessage($userid, $count = 50) 
	{
		// $userMessage = Message::where('uid', $userid)->order('msg_id','desc')->paginate($count);
		// $page = $userMessage->render();
		// $this->assign('page', $page);
		$userMessage = Message::getMessage($userid, $count);
		$this->assign('userMessage', $userMessage);
	}
	protected function getMessageById($msgId = '')
	{
		Reminder::where('touid', $this->userid)->where('msg_id', $msgId)->delete();
		$userMessage[0] = Message::getMessageById($msgId);
		$messageBlock = $userMessage[0]->comments()->where('msg_id',$msgId)->with('User')->order('ctime','desc')->paginate(20);
		$this->assign('userMessage', $userMessage);
		$this->assign('messageBlock', $messageBlock);

	}
	protected function getReminderMsg($type = '', $count=20)
	{

		$reminderMsg = Db::name('Reminder')
			->alias('reminder')
			->join([$this->prefix.'message'=>'message'],'reminder.msg_id=message.msg_id')
			->join([$this->prefix.'user'=>'user'],'user.uid=reminder.fromuid')
			->field('user.uid,user.nickname,message.image,message.image_info,message.contents,message.msg_id,message.repost,message.refrom,message.repostsum,message.commentsum,message.ctime,user.head_image,user.blog,reminder.type,reminder.touid,reminder.fromuid')
			->order('reminder.ctime desc')
			->where('touid', $this->userid)
			->paginate($count);
		$this->assign('userMessage', $reminderMsg);
		// $result = Reminder::getReminderMsg($this->userid);
		// // dump($result);die;
		// foreach ($result as $key => $value) {
		// 	dump($value->message->user);
		// }
		// $this->assign('userMessage', $result);
	}
}