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
    public function createTable()
    {
        try {
            // 自增id, uid, 网站地址， 网站名称， 用户名， 密码， 盐， 创建时间， 更新时间
            $sql = "CREATE TABLE IF NOT EXISTS `wb_password` (
                `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
                `uid` int(11) UNSIGNED NOT NULL DEFAULT 0 COMMENT '创建用户',
                `url` varchar(255) NOT NULL DEFAULT '' COMMENT '网站地址',
                `name` varchar(255) NOT NULL DEFAULT '' COMMENT '网站名称',
                `username` varchar(255) NOT NULL DEFAULT '' COMMENT '用户名',
                `password` varchar(255) NOT NULL DEFAULT '' COMMENT '密码',
                `salt` varchar(255) NOT NULL DEFAULT '' COMMENT '盐',
                `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
                `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
                PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='密码表';";
            Db::execute($sql);
            return json(['code' => 0, 'msg' => '表创建成功']);
        } catch (\Exception $e) {
            // 记录错误日志
            \think\facade\Log::error('创建表失败: ' . $e->getMessage());
            return json(['code' => -1, 'msg' => '表创建失败，请稍后重试']);
        }
    }

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
                $list = Db::name('password')->where('uid', $this->uid)->limit($limit)->page($page)
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
            // 数据验证
            $validate = new \think\Validate([
                'url' => 'require|url',
                'name' => 'require',
                'username' => 'require',
                'password' => 'require'
            ]);

            if (!$validate->check($data)) {
                return json(['code' => -1, 'msg' => $validate->getError()]);
            }

            // 密码加密
            $salt = md5(uniqid(mt_rand(), true));
            $encryptedPassword = password_hash($data['password'] . $salt, PASSWORD_DEFAULT);

            $saveData = [
                'uid' => $this->uid,
                'url' => $data['url'],
                'name' => $data['name'],
                'username' => $data['username'],
                'password' => $encryptedPassword,
                'salt' => $salt
            ];

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
}
