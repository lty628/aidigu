<?php
namespace app\admin\controller;
use think\facade\Request;
use app\common\model\AdminBehaviorLog as BehaviorLogModel;


class BehaviorLog extends Base
{
    /**
     * 行为日志列表页面
     * @return mixed
     */
    public function index()
    {
        // 渲染模板
        return $this->fetch();
    }
    
    /**
     * 获取行为日志列表数据
     * @return json
     */
    public function getList()
    {
        // 获取搜索参数
        $uid = Request::param('uid', 0, 'intval');
        $module = Request::param('module', '');
        $controller = Request::param('controller', '');
        $action = Request::param('action', '');
        $ip = Request::param('ip', '');
        $start_time = Request::param('start_time', '');
        $end_time = Request::param('end_time', '');
        
        // 构建查询条件
        $where = [];
        
        if (!empty($uid)) {
            $where['uid'] = $uid;
        }
        
        if (!empty($module)) {
            $where['module'] = $module;
        }
        
        if (!empty($controller)) {
            $where['controller'] = $controller;
        }
        
        if (!empty($action)) {
            $where['action'] = $action;
        }
        
        if (!empty($ip)) {
            $where['ip'] = ['like', '%' . $ip . '%'];
        }
        
        if (!empty($start_time)) {
            $where['create_time'] = ['>=', strtotime($start_time)];
        }
        
        if (!empty($end_time)) {
            if (isset($where['create_time'])) {
                $where['create_time'][1] = ['<=', strtotime($end_time) + 86400];
            } else {
                $where['create_time'] = ['<=', strtotime($end_time) + 86400];
            }
        }
        
        // 获取分页参数
        $page = Request::param('page', 1, 'intval');
        $limit = Request::param('limit', 20, 'intval');
        
        // 查询数据
        $logs = BehaviorLogModel::getBehaviorLogs($where, $page, $limit);
        
        // 处理返回数据
        $data = [];
        foreach ($logs as $log) {
            $data[] = [
                'id' => $log->id,
                'uid' => $log->uid,
                'username' => $log->user->username ?? '-',
                'nickname' => $log->user->nickname ?? '-',
                'module' => $log->module,
                'controller' => $log->controller,
                'action' => $log->action,
                'ip' => $log->ip,
                'content' => $log->content,
                'params' => $log->params,
                'create_time' => $log->create_time
            ];
        }
        
        // 返回layui table所需的JSON格式
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $logs->total(),
            'data' => $data
        ]);
    }
    
    /**
     * 查看行为详情
     * @return json
     */
    public function detail()
    {
        $id = Request::param('id', 0, 'intval');
        if (!$id) {
            return json(['code' => 1, 'msg' => '参数错误']);
        }
        
        $log = BehaviorLogModel::with('user')->find($id);
        if (!$log) {
            return json(['code' => 1, 'msg' => '日志不存在']);
        }
        
        return json([
            'code' => 0,
            'data' => [
                'id' => $log->id,
                'uid' => $log->uid,
                'username' => $log->user->username ?? '-',
                'nickname' => $log->user->nickname ?? '-',
                'module' => $log->module,
                'controller' => $log->controller,
                'action' => $log->action,
                'ip' => $log->ip,
                'content' => $log->content,
                'params' => $log->params,
                'create_time' => date('Y-m-d H:i:s', $log->create_time)
            ]
        ]);
    }
}