<?php
namespace app\tools\controller;
use app\common\controller\Base;
use think\Db;

/**
 * 表白墙控制器
 */
// CREATE TABLE `wb_love_wall` (
//   `id` int UNSIGNED NOT NULL,
//   `uid` int UNSIGNED NOT NULL DEFAULT '0' COMMENT '发布用户ID',
//   `content` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '表白内容',
//   `to_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '表白对象姓名',
//   `is_anonymous` tinyint UNSIGNED NOT NULL DEFAULT '0' COMMENT '是否匿名 0:否 1:是',
//   `status` tinyint UNSIGNED NOT NULL DEFAULT '1' COMMENT '状态 0:删除 1:正常',
//   `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
//   `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci COMMENT='表白墙表' ROW_FORMAT=DYNAMIC;
// ALTER TABLE `wb_love_wall`
//   ADD PRIMARY KEY (`id`) USING BTREE,
//   ADD KEY `idx_uid` (`uid`) USING BTREE,
//   ADD KEY `idx_status` (`status`) USING BTREE,
//   ADD KEY `idx_create_time` (`create_time`) USING BTREE;

class LoveWall extends Base
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
        
        // 获取表白墙列表，按时间倒序排列
        $list = Db::name('love_wall')
            ->where('status', 1)
            ->order('create_time desc')
            ->limit($limit)
            ->page($page)
            ->select();
            
        // $count = Db::name('love_wall')
        //     ->where('status', 1)
        //     ->count();
        
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
        
        return json(['code' => 0, 'data' => $list, 'count' => 50]);
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
            
            $result = Db::name('love_wall')->insert($data);
            
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
        $confession = Db::name('love_wall')->where('id', $id)->find();
        if (!$confession) {
            return json(['code' => 1, 'msg' => '表白信息不存在']);
        }
        
        // 这里简化处理，实际应该检查管理员权限
        if ($confession['uid'] != $uid) {
            return json(['code' => 1, 'msg' => '无权限删除']);
        }
        
        $result = Db::name('love_wall')->where('id', $id)->update([
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
        
        $confession = Db::name('love_wall')
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