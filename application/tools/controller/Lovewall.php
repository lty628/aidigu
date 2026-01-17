<?php
namespace app\tools\controller;
use app\common\controller\Base;
use think\Db;

/**
 * 表白墙控制器
 */

class Lovewall extends Base
{	
	public function index()
    {
        return $this->fetch();
    }

    /**
     * 获取表白墙列表
     */
    public function getList()
    {
        $uid = getLoginUid();
        $get = input('get.');
        $page = $get['page'] ?? 1;
        $limit = $get['limit'] ?? 50; // 增加到50条，适应墙式展示


        $count = Db::name('tools_love_wall')
            ->where('status', 1)
            ->count();

        if (!$count) {
            return json(['code' => 0, 'data' => [], 'count' => 0]);
        }

        if ($count > 50) {
            $count = 50;
        }
        
        // 获取表白墙列表，按时间倒序排列
        $list = Db::name('tools_love_wall')
            ->where('status', 1)
            ->order('create_time desc')
            ->limit($limit)
            ->page($page)
            ->select();
            
        
        
        // 格式化时间
        foreach ($list as &$item) {
            $item['create_time_format'] = date('Y-m-d H:i', strtotime($item['create_time']));
            // 如果是匿名发布，则不显示发布者信息
            if ($item['is_anonymous'] == 1) {
                $item['publisher'] = '匿名用户';
            } else {
                $user = Db::name('user')->where('uid', $item['uid'])->find();
                $item['publisher'] = $user ? $user['nickname'] : '未知用户';
            }
        }
        
        return json(['code' => 0, 'data' => $list, 'count' => $count]);
    }

    /**
     * 发布表白
     */
    public function publish()
    {
        if (request()->isPost()) {
            $uid = getLoginUid();
            $content = input('post.content', '');
            $toName = input('post.to_name', '');
            $isAnonymous = input('post.is_anonymous', 0);
            
            if (empty($content)) {
                return json(['code' => 1, 'msg' => '表白内容不能为空']);
            }
            
            if (empty($toName)) {
                return json(['code' => 1, 'msg' => '对方姓名不能为空']);
            }
            
            $data = [
                'uid' => $uid,
                'content' => $content,
                'to_name' => $toName,
                'is_anonymous' => $isAnonymous,
                'status' => 1,
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ];
            
            $result = Db::name('tools_love_wall')->insert($data);
            
            if ($result) {
                return json(['code' => 0, 'msg' => '表白成功']);
            } else {
                return json(['code' => 1, 'msg' => '表白失败']);
            }
        }
        
        return json(['code' => 1, 'msg' => '请求方式错误']);
    }

    /**
     * 删除表白（仅限管理员或发布者）
     */
    public function delete()
    {
        $id = (int)input('post.id');
        $uid = getLoginUid();
        
        if (!$id) {
            return json(['code' => 1, 'msg' => '参数错误']);
        }
        
        // 检查是否是发布者或管理员
        $confession = Db::name('tools_love_wall')->where('id', $id)->find();
        if (!$confession) {
            return json(['code' => 1, 'msg' => '表白信息不存在']);
        }
        
        // 这里简化处理，实际应该检查管理员权限
        if ($confession['uid'] != $uid) {
            return json(['code' => 1, 'msg' => '无权限删除']);
        }
        
        $result = Db::name('tools_love_wall')->where('id', $id)->update([
            'status' => 0,
            'update_time' => date('Y-m-d H:i:s')
        ]);
        
        if ($result) {
            return json(['code' => 0, 'msg' => '删除成功']);
        } else {
            return json(['code' => 1, 'msg' => '删除失败']);
        }
    }
    
    /**
     * 获取单个表白详情
     */
    public function detail()
    {
        $id = (int)input('get.id');
        
        if (!$id) {
            return json(['code' => 1, 'msg' => '参数错误']);
        }
        
        $confession = Db::name('tools_love_wall')
            ->where('id', $id)
            ->where('status', 1)
            ->find();
            
        if (!$confession) {
            return json(['code' => 1, 'msg' => '表白信息不存在']);
        }
        
        // 格式化数据
        $confession['create_time_format'] = date('Y-m-d H:i', strtotime($confession['create_time']));
        if ($confession['is_anonymous'] == 1) {
            $confession['publisher'] = '匿名用户';
        } else {
            $user = Db::name('user')->where('uid', $confession['uid'])->find();
            $confession['publisher'] = $user ? $user['nickname'] : '未知用户';
        }
        
        return json(['code' => 0, 'data' => $confession]);
    }
}