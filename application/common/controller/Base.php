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
		
		// 读取配置文件检查是否允许首页不登录访问
		$pubIndex = sysConfig('app.pubIndex', '');
		if ($pubIndex && request()->action() == 'index' && !isMobile()) {
			// 无逻辑处理
			return true;
		} else {
			if (!$uid) {
				$isRightCookie = checkUserCookie(cookie('rememberMe'));
				if (!$isRightCookie) {
					$url = request()->url();
					if ($url == '' || $url == '/') {
						$redirectUrl = '';
					} else {
						$redirectUrl = '?url='.str_replace('?', '&', $url);
					}
					return $this->redirect('/login/' . $redirectUrl);
				}
			}
			// if (!$isRightCookie) return $this->error('请先登录', '/login/');
			// return $this->redirect('/'.$isRightCookie['blog'].'/');
			
		}
	}
}