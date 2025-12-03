<?php
namespace app\admin\controller;

class Admin extends Base
{
    /**
     * 显示管理员首页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    public function dashboard()
    {
        return $this->fetch();
    }
}