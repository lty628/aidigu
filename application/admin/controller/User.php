<?php
namespace app\admin\controller;
use think\Request;
use app\common\model\User as UserModel;

class User extends Base
{
    // 显示用户列表
    public function index()
    {
        return $this->fetch();
    }
    
    // 获取表格数据
    public function getList()
    {
        $keyword = input('post.keyword', '');
        $page = input('post.page', 1, 'intval');
        $limit = input('post.limit', 10, 'intval');
        
        $query = UserModel::order('uid desc');
        
        // 关键词搜索
        if ($keyword) {
            $query->where('nickname', 'like', '%' . $keyword . '%')
                  ->whereOr('username', 'like', '%' . $keyword . '%')
                  ->whereOr('blog', 'like', '%' . $keyword . '%')
                  ->whereOr('phone', 'like', '%' . $keyword . '%');
        }
        
        $total = $query->count();
        $users = $query->page($page, $limit)->select();
        
        // 返回表格数据格式
        return json([
            'msg' => 'success',
            'count' => $total,
            'data' => $users
        ]);
    }
    
    // 创建用户页面
    public function create()
    {
        return $this->fetch();
    }
    
    // 保存用户
    public function save(Request $request)
    {
        $data = $request->post();
        
        // 验证数据
        $validate = $this->validate($data, [
            'nickname|昵称' => 'require|length:2,11',
            'username|用户名' => 'length:0,10',
            'password|密码' => 'require|length:6,20',
            'phone|手机号' => 'length:0,11',
            'email|邮箱' => 'email|length:0,32',
        ]);
        
        if ($validate !== true) {
            return json(['code' => 0, 'msg' => $validate]);
        }
        
        // 检查用户是否已存在
        if (UserModel::where('nickname', $data['nickname'])->find()) {
            return json(['code' => 0, 'msg' => '昵称已存在']);
        }
        
        if (!empty($data['phone']) && UserModel::where('phone', $data['phone'])->find()) {
            return json(['code' => 0, 'msg' => '手机号已存在']);
        }
        
        if (!empty($data['email']) && UserModel::where('email', $data['email'])->find()) {
            return json(['code' => 0, 'msg' => '邮箱已存在']);
        }
        
        // 密码加密
        $data['password'] = md5($data['password']);
        $data['ctime'] = time();
        
        // 创建用户
        $user = new UserModel();
        if ($user->save($data)) {
            return json(['code' => 1, 'msg' => '创建成功', 'url' => url('index')]);
        } else {
            return json(['code' => 0, 'msg' => '创建失败']);
        }
    }
    
    // 编辑用户信息
    public function edit($uid)
    {
        $user = UserModel::where('uid', $uid)->find();
        if (!$user) {
            $this->error('用户不存在');
        }
        
        $this->assign('user', $user);
        return $this->fetch();
    }
    
    // 更新用户信息
    public function update(Request $request, $uid)
    {
        $user = UserModel::where('uid', $uid)->find();
        if (!$user) {
            return json(['code' => 0, 'msg' => '用户不存在']);
        }
        
        $data = $request->post();
        
        // 验证数据
        $validate = $this->validate($data, [
            'nickname|昵称' => 'require|length:2,11',
            'username|用户名' => 'length:0,10',
            'phone|手机号' => 'length:0,11',
            'email|邮箱' => 'email|length:0,32',
            'sex|性别' => 'between:0,2',
            'province|省份' => 'length:0,10',
            'city|城市' => 'length:0,25',
            'intro|简介' => 'length:0,210',
        ]);
        
        if ($validate !== true) {
            return json(['code' => 0, 'msg' => $validate]);
        }
        
        // 检查唯一字段是否重复
        if (UserModel::where('nickname', $data['nickname'])->where('uid', '<>', $uid)->find()) {
            return json(['code' => 0, 'msg' => '昵称已存在']);
        }
        
        if (!empty($data['phone']) && UserModel::where('phone', $data['phone'])->where('uid', '<>', $uid)->find()) {
            return json(['code' => 0, 'msg' => '手机号已存在']);
        }
        
        if (!empty($data['email']) && UserModel::where('email', $data['email'])->where('uid', '<>', $uid)->find()) {
            return json(['code' => 0, 'msg' => '邮箱已存在']);
        }
        
        // 更新用户信息
        if ($user->save($data)) {
            return json(['code' => 1, 'msg' => '更新成功', 'url' => url('index')]);
        } else {
            return json(['code' => 0, 'msg' => '更新失败']);
        }
    }
    
    // 修改密码页面
    public function changePassword($uid)
    {
        $this->assign('uid', $uid);
        return $this->fetch();
    }
    
    // 保存新密码
    public function savePassword(Request $request, $uid)
    {
        $user = UserModel::where('uid', $uid)->find();
        if (!$user) {
            return json(['code' => 0, 'msg' => '用户不存在']);
        }
        
        $data = $request->post();
        
        // 验证密码
        $validate = $this->validate($data, [
            'password|新密码' => 'require|length:6,20',
            'confirm_password|确认密码' => 'require|confirm:password',
        ]);
        
        if ($validate !== true) {
            return json(['code' => 0, 'msg' => $validate]);
        }
        
        // 更新密码
        $user->password = md5($data['password']);
        
        if ($user->save()) {
            return json(['code' => 1, 'msg' => '密码修改成功', 'url' => url('index')]);
        } else {
            return json(['code' => 0, 'msg' => '密码修改失败']);
        }
    }
    
    // 禁止/启用用户
    public function toggleStatus($uid)
    {
        $user = UserModel::where('uid', $uid)->find();
        if (!$user) {
            return json(['code' => 0, 'msg' => '用户不存在']);
        }
        
        // 切换status状态：0表示正常，1表示禁止
        $user->status = $user->status == 0 ? 1 : 0;
        
        if ($user->save()) {
            $msg = $user->status == 1 ? '用户已禁止' : '用户已启用';
            return json(['code' => 1, 'msg' => $msg]);
        } else {
            return json(['code' => 0, 'msg' => '操作失败']);
        }
    }
    
    // 查找用户（AJAX接口）
    public function search(Request $request)
    {
        $keyword = $request->post('keyword', '');
        
        if (empty($keyword)) {
            return json(['code' => 0, 'msg' => '搜索关键词不能为空']);
        }
        
        $users = UserModel::where('nickname', 'like', '%' . $keyword . '%')
                          ->whereOr('username', 'like', '%' . $keyword . '%')
                          ->whereOr('blog', 'like', '%' . $keyword . '%')
                          ->whereOr('phone', 'like', '%' . $keyword . '%')
                          ->whereOr('email', 'like', '%' . $keyword . '%')
                          ->field('uid, nickname, username, blog, status, ctime')
                          ->limit(20)
                          ->select();
        
        return json(['code' => 1, 'data' => $users]);
    }
    
    // 获取用户详情（AJAX接口）
    public function detail($uid)
    {
        $user = UserModel::where('uid', $uid)->find();
        
        if ($user) {
            return json(['code' => 1, 'data' => $user]);
        } else {
            return json(['code' => 0, 'msg' => '用户不存在']);
        }
    }
}