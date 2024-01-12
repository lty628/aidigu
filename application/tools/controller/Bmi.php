<?php
namespace app\tools\controller;
use think\Controller;


class Bmi extends Controller
{	
	public function index()
    {
        return $this->fetch();
    }
}
