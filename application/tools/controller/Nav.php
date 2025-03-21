<?php
namespace app\tools\controller;
use think\Controller;
use think\Db;

class Nav extends Controller
{	
	public function index()
    {
        $list = Db::name('app')->where('app_type', 1)->select();
        $this->assign('list', $list);
        return $this->fetch();
    }
}
