<?php
namespace app\tools\controller;
use think\Controller;


class Movie extends Controller
{	
	public function index()
    {
        return $this->fetch();
    }
}
