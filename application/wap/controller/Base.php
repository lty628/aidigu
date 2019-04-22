<?php
namespace app\wap\controller;

use think\Controller;

/**
 * 	自定义基类
 */
class Base extends Controller
{
	protected function initialize()
	{
		
		// checkUserCookie(cookie('rememberMe'));
		if (!getLoginUid()) {
			$isRightCookie = checkUserCookie(cookie('rememberMe'));
			if (!$isRightCookie) return $this->error('请先登录', '/login/');
			return $this->redirect('/'.$isRightCookie['blog'].'/');
			
		}
	}
}