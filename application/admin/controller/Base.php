<?php
namespace app\admin\controller;

use think\Controller;

class Base extends Controller
{
    public function initialize()
    {
        $userInfo = getLoginUserInfo();
        $uid = $userInfo['uid'];
        if (!$uid || $uid != 1) {
            $this->redirect('/'.$userInfo['blog'].'/');
        }

    }
}