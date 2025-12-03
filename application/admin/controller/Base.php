<?php
namespace app\admin\controller;

use think\Controller;

class Base extends Controller
{
    public function initialize()
    {
        $uid = getLoginUid();
        if (!$uid && $uid != 1) {
            $this->error('请先登录', '/');
        }

    }
}