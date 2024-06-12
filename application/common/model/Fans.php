<?php
namespace app\common\model;
use think\Model;
class Fans extends Model
{
	protected $autoWriteTimestamp = true;
	protected $createTime = 'ctime';

	// public function user()
	// {
	// 	return $this->hasMany('User','uid','touid')->bind('username,blog,head_image,nickname');
	// }
	// public function message()
	// {
	// 	return $this->hasMany('Message','uid','touid');
	// }

	// public function message()
	// {
	// 	return $this->hasManyThrough('User','Message','uid','uid','fromuid');
	// }

	// public function follow()
	// {
	// 	return $this->hasOne('User','uid','touid')->bind('username,blog,head_image,nickname');
	// }
	// public function concern()
	// {
	// 	return $this->hasOne('User','uid','fromuid')->bind('username,blog,head_image,nickname');
	// }
	// public static function getFans($userid = '', $count = 9)
	// {
	// 	return self::with('user')->where('touid',$userid)->limit($count)->select();
	// }
	// public static function getConcern($userid = '', $count = 9)
	// {
	// 	return self::with('user')->where('fromuid',$userid)->limit($count)->select();
	// }
	// public static function gertFansInfo($userid)
	// {
	// 	// $fansInfo = self::User()->where('uid',$userid)->select();
	// 	// dump($fansInfo);die;
	// 	$fansIds = self::field('fansid,fromuid')->join()->where('touid', $userid)->select();
	// 	// $fansInfo = 
	// 	// dump($fansIds);die;
	// }
	public static function addFollow($userid, $loginUid)
	{
		$hasbeFollow = self::where('touid', $loginUid)->where('fromuid', $userid)->find();
		$mutualConcern = 0;
        if ($hasbeFollow) {
        	$mutualConcern = 1;
        	self::where('touid', $loginUid)->where('fromuid', $userid)->update(['mutual_concern' => $mutualConcern]);
        }
        $success = self::where('fromuid', $loginUid)->where('touid', $userid)->find();
        if ($success) return -1;
        User::where('uid', $loginUid)->setInc('follownum',1);
        User::where('uid', $userid)->setInc('fansnum',1);
        return self::create([
			'fromuid' => $loginUid,
			'touid' => $userid,
			'mutual_concern' => $mutualConcern
		]);
	}
	public static function unfollow($userid, $loginUid)
	{
		$hasbeFollow = self::where('touid', $loginUid)->where('fromuid', $userid)->find();
        if ($hasbeFollow) {
        	$mutualConcern = 0;
        	self::where('touid', $loginUid)->where('fromuid', $userid)->update(['mutual_concern' => $mutualConcern]);
        }
        User::where('uid', $loginUid)->setDec('follownum',1);
        User::where('uid', $userid)->setDec('fansnum',1);
       return self::where('touid', $userid)->where('fromuid', $loginUid)->delete();
	}

	public static function isUserFans($fromuid, $touid) 
	{
		return self::name('fans')->where(function ($query) use ($fromuid, $touid) {
			$query->where([
				'fromuid' => $touid,
				'touid' => $fromuid,
			]);
		})->whereOr(function ($query) use ($fromuid, $touid) {
			$query->where([
				'fromuid' => $fromuid,
				'touid' => $touid,
			]);
		})->find();
		
	}

}