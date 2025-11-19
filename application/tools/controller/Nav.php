<?php
namespace app\tools\controller;
use think\Controller;
use think\Db;

class Nav extends Controller
{	
    // ALTER TABLE `wb_app` ADD COLUMN `fromuid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建用户' AFTER `app_url`;
    // ALTER TABLE `wb_app` MODIFY COLUMN `app_status` tinyint NOT NULL DEFAULT 1 COMMENT '0关闭，1开启（默认）' AFTER `fromuid`;
    protected $uid;
    protected $userInfo;

	// 导航页
    public function initialize()
    {
        $this->userInfo = getLoginUserInfo();
        if (!$this->userInfo) $this->error('请先登录');
        $this->uid = $this->userInfo['uid'];
    }

    public function index()
    {
        $list = Db::name('app')->where(function ($query) {
            $query->where('app_status', '>', 0)->where('fromuid', $this->uid);
        })
        // ->whereOr(function ($query) {
        //     $query->where('app_status', 2)->where('fromuid', 0);
        // })
        ->select();

        // $bg = cache('bg_nav'.$this->uid);
        $bg = getThemeInfo($this->userInfo['theme'])[1] ?? '';
        if (!$bg) {
            // if (isset($this->userInfo['theme']) && $this->userInfo['theme']) {
            //     $bg = getThemeInfo($this->userInfo['theme'])[1];
            //     echo $bg;die;
            // } else {
                $bg = '/static/index/images/bg4.svg';
            // }
            // cache('bg_nav'.$this->uid, $bg, 600);
        }

        $this->assign('bg', $bg);
        $this->assign('list', $list);
        $this->assign('uid', $this->uid);
        return $this->fetch('tools@nav/index');
    }

    // 导航管理列表
    public function list()
    {
        if (request()->isAjax()) {
            $list = Db::name('app')->where('app_status', '>', 0)->where('fromuid', $this->uid)->select();
            return json(['code' => 0, 'data' => $list]);
        } else {
            return $this->fetch('list');
        }
    }

    // 编辑导航
    public function edit()
    {
        $id = input('get.id');
        $data = Db::name('app')->where('fromuid', $this->uid)->where('id', $id)->find();
        if (!$data) $this->error('参数错误');
        $data['app_config'] = json_decode($data['app_config'], true);
        if (!$data['app_config']) {
            $data['width'] = '';
            $data['height'] = '';
        } else {
            $data['width'] = $data['app_config']['area'][0];
            $data['height'] = $data['app_config']['area'][1];
        }
        $this->assign('data', $data);
        return $this->fetch();
    }

    public function add()
    {
        // $count = Db::name('app')->where('fromuid', $this->uid)->count();
        // if ($count >= 10) $this->error('创建群数量不能多余10个');
        return $this->fetch();
    }

    public function del()
    {
        $fromuid = getLoginUid();
        $id = input('post.id');
        $isAdmin = Db::name('app')->where('id', $id)->where('fromuid', $this->uid)->find();
        if (!$isAdmin) return json(['code' => 1, 'msg' => '无法删除1']);

        if ($isAdmin['fromuid'] != $this->uid) return json(['code' => 1, 'msg' => '无法删除2']);

        $time = time();
        Db::name('app')->where('id', $id)->where('fromuid', $this->uid)->update([
            'app_status' => -1,
            'update_time' => date('Y-m-d H:i:s', $time),
        ]);
        return json(['code' => 0, 'msg' => '删除成功']);
    }

    public function addOrEdit()
    {
        $time = time();
        
        $post = input('post.');
        $post['update_time'] = date('Y-m-d H:i:s', $time);
        $post['app_config'] = $this->handleAppConfig($post, $this->uid);
        unset($post['file']);
        unset($post['width']);
        unset($post['height']);
        if (isset($post['id'])) {
            $data = Db::name('app')->where('fromuid', $this->uid)->where('id', $post['id'])->find();
            if (!$data) return json(['code' => 1, 'msg' => '参数错误']);
            $result = Db::name('app')->where('fromuid', $this->uid)->where('id', $post['id'])->update($post);
            if (!$result) return json(['code' => 1, 'msg' => '编辑失败']);
            return json(['code' => 0, 'msg' => '编辑成功']);
        } else {
            $post['create_time'] = $post['update_time'];
            $post['fromuid'] = $this->uid;

            // 全部
            $post['app_type'] = 0;
            // 站外
            $post['app_status'] = 2;

            $result = Db::name('app')->insert($post);
            if (!$result) return json(['code' => 1, 'msg' => '新增失败']);
            return json(['code' => 0, 'msg' => '新增成功']);
        }
    }

    protected function handleAppConfig($post, $uid)
    {
        if ($post['open_type'] !=0 ) {
            return '';
        }
        $width = $post['width'] ?? '60%';
        $height = $post['height']?? '80%';
        if (isset($post['width']) && !empty($post['width'])) {
            // 移除最右面的%
            $checkWidth = rtrim($width, '%');
            $checkWidth = rtrim($checkWidth, 'px');
            // 判断是否为数字
            if (!is_numeric($checkWidth)) {
                $width = '60%';
            }
        } else {
            $width = '60%'; 
        }
        
        if (isset($post['height']) &&!empty($post['height'])) {
            // 移除最右面的%
            $checkHeight = rtrim($height, '%');
            $checkHeight = rtrim($checkHeight, 'px'); 
            // 判断是否为数字
            if (!is_numeric($checkHeight)) {
                $height = '80%';  
            }
        } else {
            $height = '80%'; 
        }

        $defaultJson = '{"title":"title","shade":0.6,"closeBtn":true,"shadeClose":true,"area":["100%","100%"],"resize":true,"maxmin":true,"skin":"layui-layer-win10","id":"app_10","hideOnClose":true,"scrollbar":false}';
        $jsonObj = json_decode($defaultJson, true);
        $jsonObj['title'] = $post['app_name'];
        $jsonObj['area'] = [$width, $height];
        $jsonObj['id'] = 'app_'.$uid.'_'.md5($post['update_time']);
        return json_encode($jsonObj, 320);
    }
}
