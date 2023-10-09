<?php
namespace app\common\model;
use think\Model;
class User extends Model
{
	protected $autoWriteTimestamp = true;
	protected $createTime = 'ctime';
	protected $updateTime = false;

	public function getAreaAttr($value, $data)
	{
		if (!$data['province'] && !$data['city']) return '保密';
		if (!$data['city']) return $data['province'];
		return $data['province'].'-'.$data['city'];
	}
	public function getHeadBigAttr($value, $data)
	{
		$headBig = json_decode($data['head_image_info'], true);
		if (!$headBig) return $data['head_image'];
		return $headBig['image_info'].'_big.'.$headBig['image_type'];
	}

	public static function getBlogInfo($blog)
	{
		return User::where('blog', $blog)->field('uid,blog,invisible')->find();
	}
	public static function getUserInfo($userid)
	{
		return User::where('uid', $userid)->field('uid,nickname,intro,sex,blog,head_image,head_image_info,province,city,follownum,fansnum,message_sum,theme,invisible')->find();
	}
	public static function getUserSetting($userid)
	{
		return User::where('uid', $userid)->field('uid,nickname,head_image_info,username,email,phone,sex,intro,blog,head_image,province,city,follownum,fansnum,message_sum,theme,invisible')->find();
	}
}