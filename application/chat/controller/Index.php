<?php
namespace app\chat\controller;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        $wsserver = env('app.chatSocketDomain');
        // $domain = 'https://'.$urlDomainRoot;
        $this->assign('wsserver', $wsserver);
        return $this->fetch();
    }
}
