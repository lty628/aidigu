<?php
namespace app\common\controller;

use think\Controller;

/**
 * 	自定义基类
 */
class Base extends Controller
{
	protected function initialize()
	{
		// checkUserCookie(cookie('rememberMe'));
		$uid = getLoginUid();
		if (!\app\common\libs\Irrigation::check($uid) && request()->isAjax()) {
			$this->error('超出请求次数限制！');
		}
		if (!$uid) {
			$isRightCookie = checkUserCookie(cookie('rememberMe'));
			if (!$isRightCookie) return $this->redirect('/login/?url=' . request()->url());
			// if (!$isRightCookie) return $this->error('请先登录', '/login/');
			// return $this->redirect('/'.$isRightCookie['blog'].'/');
			
		}
	}
}