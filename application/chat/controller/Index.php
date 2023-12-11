<?php
namespace app\chat\controller;
use think\Controller;

class Index extends Controller
{
    public function index()
    {
        $touid = input('private');
        $fromuid = getLoginUid();
        $wsserver = env('app.chatSocketDomain');
        // $domain = 'https://'.$urlDomainRoot;
        $this->assign('wsserver', $wsserver);
        $this->assign('touid', $touid);
        $this->assign('fromuid', $fromuid);
        return $this->fetch();
    }
}
