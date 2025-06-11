<?php
namespace app\tools\controller;
use think\Controller;


class Fortune extends Controller
{	
    // 秤骨算命
	public function cg()
    {
        return $this->fetch();
    }
}
