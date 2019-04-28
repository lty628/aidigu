<?php
namespace app\common\validate;
use think\Validate;
class User extends Validate
{
	protected $rule = [
		'blog' => 'require|alphaNum|max:10',
		'nickname' => 'require|length:1,10',
		'password' => 'require|min:6|max:20',
	];
	protected $message = [
		'blog.require' => '账号不能为空',
		'blog.alphaNum' => '账号只能为英文字母或数字',
		'blog.max' => '账号长度不能大于10',
		'nickname.require' => '昵称不能为空',
		'nickname.length' => '昵称不能不能大于10个字',
		'password.require' => '注册密码不能为空',
		'password.min' => '注册密码不少于6位',
		'password.max' => '注册密码不大于20位',
	];
}