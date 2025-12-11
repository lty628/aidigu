<?php
namespace app\tools\controller;
use app\common\controller\Base;


class Fortune extends Base
{	
    // 秤骨算命
	public function cg()
    {
        return $this->fetch();
    }

    public function qm()
    {
        return $this->fetch();
    }
}
