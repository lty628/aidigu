<?php
namespace app\tools\controller;
use think\Controller;
use think\Db;

class Home extends Controller
{	
	public function index()
    {
        $userInfo = getLoginUserInfo();
        // 自动登录
        if (!$userInfo && cookie('rememberMe')) {
            $userInfo = checkUserCookie(cookie('rememberMe'));
        }

        $host = request()->host();
        $scheme = request()->scheme();

        if ($host == 'aidigu.cn') {
            $host = 'aidigu.com';
        }

        $this->assign('host', $host);
        $this->assign('scheme', $scheme);
        $this->assign('beian', sysConfig('app.beian', ''));
        $this->assign('userInfo', $userInfo);
        // 近期6篇文章
        // $list = Db::name('cms_content')->order('content_id desc')->limit(6)->select();
        // $this->assign('articleList', $list);
        return $this->fetch();
    }
}
