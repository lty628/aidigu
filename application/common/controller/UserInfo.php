<?php
namespace app\common\controller;
use app\common\model\User as UserModel;
use app\common\model\Fans;
use app\common\validate\User as Validate;
use think\Controller;
/**
 * 登录注册
 */
class UserInfo extends Controller
{
	public function login()
	{
		return $this->fetch();
	}
	public function register()
	{
		return $this->fetch();
	}
	public function forgot()
	{
		return $this->fetch();
	}
	public function registerAjax()
	{
		$data['blog'] = strtolower(trim(input('get.account')));
		$data['nickname'] = trim(input('get.nickname'));
		$data['password'] = trim(input('get.password'));
		$validate = new Validate();
		if (!$validate->check($data)) {
			return json(['status'=>0, 'msg'=>$validate->getError()]);
		}
		$user = new UserModel();
		$hasUser = $user->where('blog', $data['blog'])->whereOr('nickname', $data['nickname'])->find();
		if ($hasUser) return json(['status'=>0, 'msg'=>'账号或昵称已存在']);
		$data['password'] = encryptionPass($data['password']);
		$data['head_image'] = '/static/index/images/noavatar_small.gif';
		if (!$user->save($data)) return json(['status'=>0, 'msg'=>'注册失败']);
		$userid = $user->id;
		setLoginUid($userid);
		Fans::create(['fromuid'=>$userid, 'touid'=>$userid, 'mutual_concern'=>1]);
		$this->rememberMe(input('get.remember'), $data);
		return json(['status'=>1, 'msg'=>'注册成功']);
	}
	public function loginAjax()
	{
		$username = trim(input('get.username'));
		$password = trim(input('get.password'));
		if (!$username || !$password) return json(['status'=>0, 'msg'=>'账号或密码不能为空']);
		$result = UserModel::where('blog', $username)->whereOr('nickname', $username)->field('uid,nickname,blog,password')->find();
		if (!$result) return json(['status'=>0, 'msg'=>'输入的账号不存在']);
		if (encryptionPass($password) != $result['password']) return json(['status'=>0, 'msg'=>'密码不正确']);
		setLoginUid($result['uid']);
		$this->rememberMe(input('get.remember'), $result);
		return json(['status'=>1, 'msg'=>'登录成功','data'=>$result['blog']]);
	}
	public function checkInfo()
	{
		$type = input('get.type');
		$text = input('get.text');
		if ($type == 1) {
			if (in_array($text, ['setting','page','blog','admin','login','register','logout'])) return json(['status'=>0, 'msg'=>'账号账号为关键词']);
			$str = 'blog';
			$errorMsg = '账号已存在';
		}
		if ($type == 2) {
			$str = 'nickname';
			$errorMsg = '昵称已存在';
		}
		$result = UserModel::where($str,$text)->field('uid')->find();
		if ($result) return json(['status'=>0, 'msg'=>$errorMsg]);
		return json(['status'=>1, 'msg'=>'ok']);
	}
	public function checkInfo2()
	{
		$type = input('get.type');
		$text = input('get.text');
		if ($type == 1) {
			if (in_array($text, ['setting','page','blog','admin','login','register','logout'])) return json(['status'=>0, 'msg'=>'账号账号为关键词']);
			$str = 'blog';
			$errorMsg = '账号已存在';
		}
		if ($type == 2) {
			$str = 'nickname';
			$errorMsg = '昵称已存在';
		}
		$result = UserModel::where($str,$text)->field('uid')->find();
		if ($result && $result['uid']!=getLoginUid()) return json(['status'=>0, 'msg'=>$errorMsg]);
		// if (UserModel::where($str,$text)->find()) return json(['status'=>0, 'msg'=>$errorMsg]);
		return json(['status'=>1, 'msg'=>'ok']);
	}
	public function logout()
	{
		session(null);
		cookie(null);
		$this->redirect('/login/');
	}
	public function rememberMe($isRemember = false, $data)
	{
		if ($isRemember) {
			unset($data['uid']);
			cookie('rememberMe', serialize(serialize($data)), 86400*15);
		}
	}

	// public function changeInfo()
	// {
	// 	$data = input('get.info');
	// 	$user = new UserModel();
	// 	$userid = getLoginUid();
	// 	$blogInfo = $user->where('blog', $data['blog'])->field('uid')->find();
	// 	if ($blogInfo['uid'] != $userid) return json(['status'=>0, 'msg'=>'账号已存在']);
	// 	$nicknameInfo = $user->where('nickname', $data['nickname'])->field('uid')->find();
	// 	if ($nicknameInfo['uid'] != $userid) return json(['status'=>0, 'msg'=>'昵称已存在']);
	// 	$result = $user->allowField(['username','blog','nickname','phone','email','sex','province','city','intro'])->save($data, ['uid'=>$userid]);
	// 	if (!$result) return json(['status'=>0, 'msg'=>'修改失败']);
	// 	return json(['status'=>0, 'msg'=>'修改成功']);
	// }
}