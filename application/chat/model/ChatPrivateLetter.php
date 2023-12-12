<?php
namespace app\chat\model;
use think\Model;

class ChatPrivateLetter extends Model
{
	protected $autoWriteTimestamp = true;
	protected $createTime = 'ctime';

	public function addPrivateFriend($touid, $fromuid)
	{
		return $this->saveAll([
			[
				'fromuid' => $fromuid,
				'touid' => $touid,
				'mutual_concern' => 1
			],
			[
				'fromuid' => $touid,
				'touid' => $fromuid,
				'mutual_concern' => 1
			],
		]);	
	}

}