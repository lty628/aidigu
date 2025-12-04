<?php
namespace app\tools\controller;
use think\Controller;


class Home extends Controller
{	
	public function index()
    {
        $this->assign('beian', sysConfig('app.beian', ''));
        return $this->fetch();
    }
}
