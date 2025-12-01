<?php
namespace app\tools\controller;

use think\Controller;
use think\Request;
use think\Db;

class Task extends Controller
{
    /**
     * 显示任务列表页面
     * @return mixed
     */
    public function index()
    {
        // 从cookie获取任务列表
        $taskList = $this->getTasksFromCookie();
        $this->assign('taskList', $taskList);
        return $this->fetch();
    }
    
    /**
     * 显示创建任务页面
     * @return mixed
     */
    public function add()
    {
        return $this->fetch();
    }
    
    /**
     * 保存任务到cookie
     * @param Request $request
     * @return \think\response\Redirect
     */
    public function save(Request $request)
    {
        if ($request->isPost()) {
            // 获取表单数据
            $taskData = [
                'id' => time(), // 使用时间戳作为唯一ID
                'title' => $request->post('title'),
                'content' => $request->post('content', ''),
                'status' => $request->post('status', 'pending'),
                'create_time' => date('Y-m-d H:i:s')
            ];
            
            // 获取现有任务列表
            $taskList = $this->getTasksFromCookie();
            
            // 添加新任务
            array_unshift($taskList, $taskData); // 添加到开头
            
            // 保存到cookie，设置过期时间为30天
            cookie('tasks', json_encode($taskList), 30*24*3600);
            
            // 重定向到任务列表页
            $this->success('任务创建成功', 'task/index');
        }
        
        return $this->error('请求方式错误');
    }
    
    /**
     * 从cookie获取任务列表
     * @return array
     */
    private function getTasksFromCookie()
    {
        $taskList = cookie('tasks');
        if (empty($taskList)) {
            // 返回模拟数据，用于展示
            return [
                [
                    'id' => 1,
                    'title' => '完成项目文档',
                    'content' => '编写项目需求文档和技术方案',
                    'status' => 'pending',
                    'create_time' => '2024-12-01 10:00:00'
                ],
                [
                    'id' => 2,
                    'title' => '修复登录页面bug',
                    'content' => '解决用户无法正常登录的问题',
                    'status' => 'active',
                    'create_time' => '2024-12-02 14:30:00'
                ],
                [
                    'id' => 3,
                    'title' => '更新用户界面',
                    'content' => '优化首页设计和用户体验',
                    'status' => 'completed',
                    'create_time' => '2024-12-03 09:15:00'
                ]
            ];
        }
        return json_decode($taskList, true);
    }
    
    /**
     * 更新任务状态（支持关联素材）
     * @param Request $request
     * @return \think\response\Json
     */
    public function updateStatus(Request $request)
    {
        $taskId = $request->post('id');
        $status = $request->post('status');
        $materialId = $request->post('materialId', 0); // 新增素材ID参数，默认为0
        
        if ($taskId && in_array($status, ['pending', 'active', 'completed'])) {
            $taskList = $this->getTasksFromCookie();
            foreach ($taskList as &$task) {
                if ($task['id'] == $taskId) {
                    $task['status'] = $status;
                    // 如果提供了素材ID，则关联到任务
                    if ($materialId) {
                        $task['material_id'] = $materialId;
                        // 获取素材信息
                        $material = Db::name('source_material')->where('id', $materialId)->where('uid', getLoginUid())->find();
                        if ($material) {
                            $task['material_info'] = [
                                'id' => $material['id'],
                                'title' => $material['title'],
                                'create_time' => $material['create_time']
                            ];
                        }
                    }
                    break;
                }
            }
            
            cookie('tasks', json_encode($taskList), 30*24*3600);
            return json(['code' => 0, 'msg' => '状态更新成功']);
        }
        
        return json(['code' => 1, 'msg' => '参数错误']);
    }

    /**
     * 获取用户的素材列表
     * @return \think\response\Json
     */
    public function getMaterialList()
    {
        $title = input('get.title', '');
        $where[] = ['uid', '=', getLoginUid()];
        $where[] = ['status', '=', 1];
        
        if ($title) {
            $where[] = ['title', 'like', '%' . $title . '%'];
        }
        
        $list = Db::name('source_material')
            ->where($where)
            ->order('id', 'desc')
            ->select();
        
        return json(['code' => 0, 'data' => $list]);
    }
    
    /**
     * 删除任务
     * @param $id
     * @return \think\response\Redirect
     */
    public function delete($id)
    {
        $taskList = $this->getTasksFromCookie();
        $newList = array_filter($taskList, function($task) use ($id) {
            return $task['id'] != $id;
        });
        
        cookie('tasks', json_encode(array_values($newList)), 30*24*3600);
        $this->success('任务删除成功', 'task/index');
    }
}