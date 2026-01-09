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
        $userid = getUserIdd();
        $info = [];
        if ($userid) {
            $info = Db::table('wb_user')->field('uid,nickname,head_image,blog,status,uptime,theme')->where('uid', $userid)->find();
        }
        $this->userInfo = $info;
        // var_dump($this->userInfo);die;
        $this->assign(
            [
                'userInfo' => $this->userInfo
            ]
        );
        $this->aside();
        // dump(strtolower(request()->routeInfo()["route"]));die;
        // 未登录用户在特定路由下给提示
        $this->redirectLogin();
        $this->assign(
            'router', strtolower(request()->routeInfo()["route"] ?? '')
        );
    }

    public function getUserIdd()
    {

        return $this->userInfo['uid'] ?? '';
    }

    // 页面右侧面试题（部分页面调用使用）
    public function aside()
    {
        $attachmentModel = new AttachmentModel();
        $attachmentList = $attachmentModel->getPageData([],'attach_id,attach_title,uid,create_time',1, 4, 'attach_id desc');
        $this->assign([
            'asideData' => $attachmentList
        ]);
        // dump($attachmentList);die;
    }

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