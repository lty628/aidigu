<?php
namespace app\common\model;
use think\Model;
class Reminder extends Model
{
	protected $autoWriteTimestamp = true;
	protected $createTime = 'ctime';
	protected $updateTime = false;


	// public function getTypeNameAttr($value, $data)
	// {
	// 	$status = [0=>'转发',1=>'评论',2=>'回复'];
	// 	return $status[$data['type']];
	// }
	// public function message()
	// {
	// 	return $this->hasOne('Message','msg_id','msg_id');
	// }
	// public function user()
	// {
	// 	return $this->hasMany('User','uid','fromuid');
	// }

	//$type 0 转发 1 评论 2 回复
	public static function saveReminder($msgId, $fromuid, $touid, $type)
	{
		return self::create(['msg_id'=>$msgId, 'fromuid'=>$fromuid, 'touid'=>$touid, 'type'=>$type]);
	}

	// public static function getReminderMsg($userid)
	// {
	// 	return self::where('touid', $userid)->select();
	// }

}