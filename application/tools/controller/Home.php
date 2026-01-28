<?php
namespace app\tools\controller;
use app\common\controller\Base;
use think\Db;

class Home extends Base
{	
	public function index()
    {
        $this->assign('beian', sysConfig('app.beian', ''));
        $this->assign('userInfo', getLoginUserInfo());
        // 近期6篇文章
        $list = Db::name('cms_content')->order('content_id desc')->limit(6)->select();
        $this->assign('articleList', $list);
        return $this->fetch();
    }
}
