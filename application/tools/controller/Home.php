<?php
namespace app\tools\controller;
use app\common\controller\Base;


class Home extends Base
{	
	public function index()
    {
        $this->assign('beian', sysConfig('app.beian', ''));
        $this->assign('userInfo', getLoginUserInfo());
        return $this->fetch();
    }
}
