<?php
namespace app\tools\controller;
use think\Controller;
use think\Db;

class Password extends Controller
{	
    protected $uid;

	// 导航页
    public function initialize()
    {
        $this->uid = getLoginUid();
        if (!$this->uid) $this->error('请先登录');
    }

    // 密码保存工具
    // public function createTable()
    // {
    //     try {
    //         // 自增id, uid, 网站地址， 网站名称， 用户名， 密码， 盐， 创建时间， 更新时间
    //         $sql = "CREATE TABLE IF NOT EXISTS `wb_password` (
    //             `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    //             `uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建用户',
    //             `url` varchar(255) NOT NULL DEFAULT '' COMMENT '网站地址',
    //             `name` varchar(255) NOT NULL DEFAULT '' COMMENT '网站名称',
    //             `username` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名',
    //             `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
    //             `salt` varchar(255) NOT NULL DEFAULT '' COMMENT '盐',
    //             `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    //             `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    //             PRIMARY KEY (`id`)
    //         ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='密码表';";
    //         Db::execute($sql);
    //         return json(['code' => 0, 'msg' => '表创建成功']);
    //     } catch (\Exception $e) {
    //         // 记录错误日志
    //         \think\facade\Log::error('创建表失败: ' . $e->getMessage());
    //         return json(['code' => -1, 'msg' => '表创建失败，请稍后重试']);
    //     }
    // }

    public function list()
    {
        if (request()->isAjax()) {
            try {
                $get = input('get.');
                $page = $get['page'] ?? 1;
                $limit = $get['limit'] ?? 10;
                // 验证页码和每页数量
                if (!is_numeric($page) || !is_numeric($limit)) {
                    return json(['code' => -1, 'msg' => '页码和每页数量必须为数字']);
                }
                $list = Db::name('password')->field('id,url,name,username,create_time,update_time')->where('uid', $this->uid)->limit($limit)->page($page)
                    ->select();
                $count = Db::name('password')->where('uid', $this->uid)->count();
                return json(['code' => 0, 'data' => $list, 'count' => $count]);
            } catch (\Exception $e) {
                // 记录错误日志
                \think\facade\Log::error('获取密码列表失败: ' . $e->getMessage());
                return json(['code' => -1, 'msg' => '获取密码列表失败，请稍后重试']);
            }
        } else {
            return $this->fetch('list');
        }
    }

    // 完善 addOrEdit 方法
    public function addOrEdit()
    {
        try {
            $data = input('post.');

            if (isset($data['id'])) {
                // 验证记录是否属于当前用户
                $count = Db::name('password')->where('id', $data['id'])->where('uid', $this->uid)->count();
                
                if ($count == 0) {
                    return json(['code' => -1,'msg' => '您无权编辑该记录']);
                }

                $validate = new \think\Validate([
                    'url' =>'url',
                    'name' =>'require',
                    'username' =>'require',
                ]);
            } else {
                // 数据验证
                $validate = new \think\Validate([
                    'url' => 'require|url',
                    'name' => 'require',
                    'username' => 'require',
                    'password' => 'require'
                ]);
            }
            

            if (!$validate->check($data)) {
                return json(['code' => -1, 'msg' => $validate->getError()]);
            }

            $saveData = [
                'uid' => $this->uid,
                'url' => $data['url'],
                'name' => $data['name'],
                'username' => $data['username'],
            ];

            

            if (isset($data['password']) && $data['password']) {
                $salt = md5(uniqid(mt_rand(), true));
                $encryptedPassword = $this->encryptPassword($data['password'], $salt);
                $saveData['password'] = $encryptedPassword;
                $saveData['salt'] = $salt;
            }

            // 密码加密
            if (isset($data['id'])) {
                // 更新操作
                Db::name('password')->where('id', $data['id'])->update($saveData);
                return json(['code' => 0, 'msg' => '密码信息更新成功']);
            } else {
                // 新增操作
                Db::name('password')->insert($saveData);
                return json(['code' => 0, 'msg' => '密码信息保存成功']);
            }
        } catch (\Exception $e) {
            // 记录错误日志
            \think\facade\Log::error('保存或更新密码失败: ' . $e->getMessage());
            return json(['code' => -1, 'msg' => '保存或更新密码失败，请稍后重试']);
        }
    }

    // 完善 del 方法
    public function del()
    {
        try {
            $id = input('post.id');
            if (!$id) {
                return json(['code' => -1, 'msg' => '请提供要删除的密码记录ID']);
            }
            // 验证记录是否属于当前用户
            $count = Db::name('password')->where('id', $id)->where('uid', $this->uid)->count();
            if ($count == 0) {
                return json(['code' => -1, 'msg' => '您无权删除该记录']);
            }
            Db::name('password')->where('id', $id)->delete();
            return json(['code' => 0, 'msg' => '删除成功']);
        } catch (\Exception $e) {
            // 记录错误日志
            \think\facade\Log::error('删除密码记录失败: ' . $e->getMessage());
            return json(['code' => -1, 'msg' => '删除密码记录失败，请稍后重试']);
        }
    }

    public function add()
    {
        // $count = Db::name('app')->where('fromuid', $this->uid)->count();
        // if ($count >= 10) $this->error('创建群数量不能多余10个');
        return $this->fetch();
    }

    public function edit()
    {
        $id = input('get.id');
        $data = Db::name('password')->where('uid', $this->uid)->where('id', $id)->find();
        if (!$data) $this->error('参数错误');
        $this->assign('data', $data);
        return $this->fetch('edit');
    }

    public function getPassword()
    {
        $id = input('post.id');
        $data = Db::name('password')->field('password,salt')->where('uid', $this->uid)->where('id', $id)->find();
        if (!$data) return json(['code' => -1,'msg' => '参数错误']);
        // password_hash 解码
        $returnData['password'] = $this->decryptPassword($data['password'], $data['salt']);
        return json(['code' => 0,'msg' => '获取成功', 'data' => $returnData]);
    }

    protected function encryptPassword($password, $salt)
    {
       // aes 加密
       $cipher = "AES-256-CBC";
       $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length($cipher));
       $encryptedData = openssl_encrypt($password, $cipher, $salt, 0, $iv);
       // 将 IV 和加密数据拼接，使用分隔符分隔，这里用冒号
       $encryptedPassword = base64_encode($iv) . ':' . $encryptedData;
       return $encryptedPassword;
    }

    protected function decryptPassword($encryptedPassword, $salt)
    {
        // aes 解密
        $cipher = "AES-256-CBC";
        // 分离 IV 和加密数据
        list($base64Iv, $encryptedData) = explode(':', $encryptedPassword, 2);
        $iv = base64_decode($base64Iv);
        $decryptedPassword = openssl_decrypt($encryptedData, $cipher, $salt, 0, $iv);
        return $decryptedPassword;
    }
}
