<?php
namespace app\chat\controller;
use think\Controller;
use app\chat\model\ChatPrivateLetter;

class Index extends Controller
{
    public function index()
    {
        $touid = input('private');
        $fromuid = getLoginUid();
        $wsserver = env('app.chatSocketDomain');

        if ($touid || $fromuid) {
            $this->checkPrivate($touid, $fromuid);
        }

        // $domain = 'https://'.$urlDomainRoot;
        $this->assign('wsserver', $wsserver);
        $this->assign('touid', $touid);
        $this->assign('fromuid', $fromuid);
        return $this->fetch();
    }

    protected function checkPrivate($touid, $fromuid)
    {
        $model = new ChatPrivateLetter();
        $info = $model->where([
            'fromuid' => $fromuid,
            'touid' => $touid
        ])->find();
        if (!$info) {
            return $model->addPrivateFriend($touid, $fromuid);
        }
        return true;
    }
}
