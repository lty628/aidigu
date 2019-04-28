<?php
namespace app\common\model;
use think\Model;
class Message extends Model
{
	protected $autoWriteTimestamp = true;
	protected $createTime = 'ctime';
	protected $type = [
		'ctime' => 'integer',
	];
	// protected $updateTime = 'update_at';

	// public function getCtimeAttr($value)
	// {
	// 	$timeDifference = time()-$value;
	// 	if ($timeDifference > 259200) {
	// 		$returnTime = date('Y-m-d H:i:s', $value);
	// 	} else if ($timeDifference < 59) {
	// 		$returnTime = $timeDifference.'秒前';
	// 	} else if ($timeDifference < 3600) {
	// 		$returnTime = floor($timeDifference/60).'分钟前';
	// 	} else if ($timeDifference < 86400) {
	// 		$returnTime = floor($timeDifference/3600).'小时前';
	// 	} else if ($timeDifference < 259200) {
	// 		$returnTime = floor($timeDifference/86400).'天前';
	// 	}
	// 	return $returnTime;
	// }
	public function user()
	{
		return $this->hasOne('User','uid','uid')->bind('username,blog,head_image,nickname');
	}
	public function comments()
	{
		return $this->hasMany('Comment','msg_id', 'msg_id');
	}
	public function fans()
	{
		return $this->hasMany('Fans','uid','touid');
	}
	public static function getMessage($userid = '', $count = 50)
	{
		// return self::with(['user' => function($query){$query->field('username,blog,head_image,uid,nickname');}])->where('uid',$userid)->order('msg_id','desc')->paginate($count);
		if ($userid) return self::with('user')->where('uid',$userid)->order('msg_id','desc')->paginate($count);
		return self::with('user')->order('msg_id','desc')->paginate($count);
	}
	public static function getUserMessage($userid = '', $count = 50)
	{
		return self::with('fans')->where('uid',$userid)->order('msg_id','desc')->paginate($count);
		// return self::with('user')->order('msg_id','desc')->paginate($count);
	}
	public static function getMessageById($msgId = '')
	{
		return self::with('User')->where('msg_id',$msgId)->find();
		// return self::with('comments')->with('User')->where('msg_id',$msgId)->find();
	}
	public static function delMessageById($msgId = '', $userid = '')
	{
		if (!$msgId || !$userid) return false;
		User::where('uid',$userid)->setDec('message_sum',1);
		Comment::where('msg_id',$msgId)->where('touid',$userid)->delete();
		Reminder::where('msg_id',$msgId)->delete();
		return self::where('msg_id',$msgId)->where('uid',$userid)->delete();
	}
}