<?php
namespace app\tools\controller;
use think\Controller;


class Onlinecar extends Controller
{	
	public function index()
    {
        return $this->fetch();
    }
}
