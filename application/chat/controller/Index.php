<?php
namespace app\chat\controller;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        $urlDomainRoot = env('app.urlDomainRoot');
        // $domain = 'https://'.$urlDomainRoot;
        $wsserver = 'ws://'.$urlDomainRoot.'/ws/';
        $this->assign('wsserver', $wsserver);
        return $this->fetch();
    }
}
