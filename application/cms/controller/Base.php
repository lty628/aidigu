<?php
namespace app\cms\controller;
use think\Db;

use think\Controller;

use app\cms\model\Attachment as AttachmentModel;

class Base extends Controller
{
    protected $userInfo = [];

    public function initialize()
    {
        $userInfo = getLoginUserInfo();
        $rememberMe = cookie('rememberMe');
        
        // 自动登录
        if (!$userInfo && $rememberMe) {
            if ($rememberMe) {
                $userInfo = checkUserCookie($rememberMe);
            } else {
                cookie('rememberMe', -1);
            }
            
        }
        // Db::table('wb_user')->field('uid,nickname,head_image,blog,status,uptime,theme')->where('uid', $userid)->find();
        $this->userInfo = $userInfo;
        // var_dump($this->userInfo);die;
        $this->assign(
            [
                'userInfo' => $this->userInfo
            ]
        );
        // $this->aside();
        $this->getCategory();
        // dump(strtolower(request()->routeInfo()["route"]));die;
        // 未登录用户在特定路由下给提示
        $this->redirectLogin();
        $this->assign('beian', sysConfig('app.beian', ''));
        $this->assign(
            'router', strtolower(request()->routeInfo()["route"] ?? '')
        );

        $host = request()->host();
        if ($host == 'aidigu.com') {
            $host = 'aidigu.cn';
        }

        $this->assign('host', $host);
    }

    public function getUserId()
    {

        return $this->userInfo['uid'] ?? '';
    }

    public function getCategory()
    {
        $categoryList = Db::name('category')->select();
        $this->assign([
            'categoryList' => $categoryList
        ]);
    }

    // 页面右侧文件下载（部分页面调用使用）
    // public function aside()
    // {
    //     $attachmentModel = new AttachmentModel();
    //     $attachmentList = $attachmentModel->getPageData([],'attach_id,attach_title,uid,create_time',1, 4, 'attach_id desc');
    //     $this->assign([
    //         'asideData' => $attachmentList
    //     ]);
    //     // dump($attachmentList);die;
    // }

    // 部分路由 未登录 必须登录后才能访问
    protected function redirectLogin()
    {
        if ($this->userInfo) return;
        $path = request()->path();
        $denyPaths = [
            'commit',
            'share',
        ];
        // 暂不启用
        if (in_array(trim($path, '/'), $denyPaths)) return $this->error('未登录，需登录');
    }
}