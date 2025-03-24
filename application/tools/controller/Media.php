<?php
namespace app\tools\controller;
use think\Controller;


class Media extends Controller
{	
	public function video()
    {
        $id = input('param.id');
        // echo $id;die;
        $this->assign('videoUrl',base64_decode($id));
        return $this->fetch();
    }
}
