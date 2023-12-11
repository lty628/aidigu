<?php
namespace app\chat\model;
use think\Model;
class ChatFriends extends Model
{
	protected $autoWriteTimestamp = true;
	protected $createTime = 'ctime';

	public static function addFriend($userid, $loginUid)
	{
		$hasbeFollow = self::where('touid', $loginUid)->where('fromuid', $userid)->find();
		$mutualConcern = 0;
        if ($hasbeFollow) {
        	$mutualConcern = 1;
        	self::where('touid', $loginUid)->where('fromuid', $userid)->update(['mutual_concern' => $mutualConcern]);
        }
        $success = self::where('fromuid', $loginUid)->where('touid', $userid)->find();
        if ($success) return -1;
        return self::create([
			'fromuid' => $loginUid,
			'touid' => $userid,
			'mutual_concern' => $mutualConcern
		]);
	}
	
	public static function editFriend($userid, $loginUid)
	{
		$hasbeFollow = self::where('touid', $loginUid)->where('fromuid', $userid)->find();
        if ($hasbeFollow) {
        	$mutualConcern = 0;
        	self::where('touid', $loginUid)->where('fromuid', $userid)->update(['mutual_concern' => $mutualConcern]);
        }
       return self::where('touid', $userid)->where('fromuid', $loginUid)->delete();
	}
}