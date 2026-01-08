<?php
namespace app\tools\controller;
use app\common\controller\Base;
use think\Db;

class Nav extends Base
{	
    // ALTER TABLE `wb_app` ADD COLUMN `fromuid` int UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建用户' AFTER `app_url`;
    // ALTER TABLE `wb_app` MODIFY COLUMN `app_status` tinyint NOT NULL DEFAULT 1 COMMENT '0关闭，1开启（默认）' AFTER `fromuid`;
    protected $uid;
    protected $userInfo;

	// 导航页
    public function initialize()
    {
        parent::initialize();
        $this->userInfo = getLoginUserInfo();
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

    // 导航管理列表（合并版）
    public function list()
    {
        if (request()->isAjax()) {
            $list = Db::name('app')->where('app_status', '>', 0)->where('fromuid', $this->uid)->select();
            return json(['code' => 0, 'data' => $list]);
        } else {
            return $this->fetch('list');
        }
    }
    
    // 默认应用列表
    public function defaultList()
    {
        if (request()->isAjax()) {
            // 获取系统默认应用
            $defaultApps = $this->getDefaultAppList();
            return json(['code' => 0, 'data' => $defaultApps]);
        } else {
            return $this->fetch('default_list');
        }
    }

    // 获取默认应用列表
    public function getDefaultApp()
    {
        if (request()->isAjax()) {
            $defaultApps = $this->getDefaultAppList();
            return json(['code' => 0, 'data' => $defaultApps]);
        } else {
            return json(['code' => 1, 'msg' => '非法请求']);
        }
    }

    protected function getDefaultAppList()
    {
        // 定义应用列表和对应的背景颜色
        $apps = [
            ['app_name' => '百度', 'app_url' => 'https://www.baidu.com', 'bg_color' => '#317EF3'],
            ['app_name' => '新浪', 'app_url' => 'https://www.sina.com.cn', 'bg_color' => '#E6162D'],
            ['app_name' => '抖音', 'app_url' => 'https://www.douyin.com', 'bg_color' => '#7F7F7F'], // 抖音实际是灰色
            ['app_name' => '网易', 'app_url' => 'https://www.163.com', 'bg_color' => '#ED1C24'],
            ['app_name' => '360', 'app_url' => 'https://www.so.com', 'bg_color' => '#00B22D'],
            ['app_name' => '必应', 'app_url' => 'https://www.bing.com', 'bg_color' => '#0067B8'],
            ['app_name' => '谷歌', 'app_url' => 'https://www.google.com', 'bg_color' => '#4285F4'],
            ['app_name' => '腾讯', 'app_url' => 'https://www.tencent.com', 'bg_color' => '#12B7F5'],
            ['app_name' => '淘宝', 'app_url' => 'https://www.taobao.com', 'bg_color' => '#FF4400'],
            ['app_name' => '京东', 'app_url' => 'https://www.jd.com', 'bg_color' => '#E1251B'],
            ['app_name' => '知乎', 'app_url' => 'https://www.zhihu.com', 'bg_color' => '#0084FF'],
            ['app_name' => '哔哩哔哩', 'app_url' => 'https://www.bilibili.com', 'bg_color' => '#FB7299'],
            ['app_name' => '网易云音乐', 'app_url' => 'https://music.163.com/', 'bg_color' => '#ff0000ff'],
            ['app_name' => '凤凰新闻', 'app_url' => 'https://www.ifeng.com/', 'bg_color' => '#ef0000ff'],
            ['app_name' => '爱奇艺', 'app_url' => 'https://www.iqiyi.com', 'bg_color' => '#52db64ff'],
            ['app_name' => '腾讯视频', 'app_url' => 'https://v.qq.com', 'bg_color' => '#FF0000'],
        ];
        
        // 一次性获取用户已添加的所有应用名称
        $userAddedApps = Db::name('app')->where('fromuid', $this->uid)->column('app_name');
        $userAddedAppsSet = array_flip($userAddedApps); // 转换为键值对，方便快速查找
        
        // 生成默认应用列表
        $defaultApps = [];
        foreach ($apps as $app) {
            // 检查用户是否已经添加该应用 - 在PHP中快速检查
            $isAdded = isset($userAddedAppsSet[$app['app_name']]);
            
            // 生成SVG图像URL
            $app_image = '/svg/font.php?text=' . urlencode($app['app_name']) . '&color=ffffff&bgcolor=' . ltrim($app['bg_color'], '#') . '&width=64&height=64&size=24';
            
            $defaultApps[] = [
                'app_name' => $app['app_name'],
                'app_url' => $app['app_url'],
                'bg_color' => $app['bg_color'], // 添加bg_color字段
                'app_status' => 2,
                'fromuid' => 0,
                'app_image' => $app_image,
                'open_type' => 2,
                'create_time' => date('Y-m-d H:i:s'),
                'is_added' => $isAdded ? 1 : 0
            ];
        }
        
        return $defaultApps;
    }

    // 将默认应用添加到用户的应用列表
    public function addDefaultApp()
    {
        if (request()->isAjax()) {
            $post = input('post.');
            $app_name = $post['app_name'];
            $app_url = $post['app_url'];
            $bg_color = $post['bg_color'] ?? '#1890ff'; // 接收bg_color参数，设置默认值
            
            // 检查用户是否已经添加过该应用
            $exists = Db::name('app')->where('app_name', $app_name)->where('fromuid', $this->uid)->find();
            if ($exists) {
                return json(['code' => 1, 'msg' => '该应用已经存在于您的应用列表中']);
            }
            
            // 生成SVG图像URL
            $app_image = '/svg/font.php?text=' . urlencode($app_name) . '&color=ffffff&bgcolor=' . ltrim($bg_color, '#') . '&width=64&height=64&size=24';
            // 添加到用户的应用列表
            $time = time();
            $data = [
                'app_name' => $app_name,
                'app_url' => $app_url,
                'app_image' => $app_image,
                'app_status' => 2, // 站外
                'app_type' => 0, // 全部
                'open_type' => 2, // 新窗口打开
                'app_config' => '',
                'fromuid' => $this->uid,
                'create_time' => date('Y-m-d H:i:s', $time),
                'update_time' => date('Y-m-d H:i:s', $time),
            ];
            
            $result = Db::name('app')->insert($data);
            if (!$result) {
                return json(['code' => 1, 'msg' => '添加失败']);
            }
            
            return json(['code' => 0, 'msg' => '添加成功']);
        } else {
            return json(['code' => 1, 'msg' => '非法请求']);
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