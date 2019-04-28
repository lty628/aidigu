<?php
namespace app\common\model;
use think\Model;
class Comment extends Model
{
	protected $autoWriteTimestamp = true;
	protected $createTime = 'ctime';
	public function user()
	{
		return $this->hasOne('User','uid','fromuid')->bind('username,blog,head_image,nickname');
	}
}