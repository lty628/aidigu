<?php
namespace app\admin\controller;

use think\Controller;
use app\common\model\AdminBehaviorLog;
use think\facade\Request;

class Base extends Controller
{
    public function initialize()
    {
        $userInfo = getLoginUserInfo();
        $uid = $userInfo['uid'];
        if (!$uid || $uid != 1) {
            $this->redirect('/'.$userInfo['blog'].'/');
        }
        
        // 记录行为日志
        $this->recordBehavior();
    }
    
    /**
     * 记录用户行为
     */
    public function recordBehavior($customContent = '')
    {
        $userInfo = getLoginUserInfo();
        $uid = $userInfo['uid'] ?? 0;
        
        if ($uid) {
            $module = app('request')->module();
            $controller = app('request')->controller();
            $action = app('request')->action();
            $ip = app('request')->ip();
            $params = app('request')->param();
            
            // 过滤敏感信息
            if (isset($params['password'])) unset($params['password']);
            if (isset($params['old_password'])) unset($params['old_password']);
            if (isset($params['new_password'])) unset($params['new_password']);

            if (!$params) {
                return false;
            }
            
            // 自动生成操作描述
            if (empty($customContent)) {
                $customContent = $this->generateActionDesc($controller, $action);
            }
            
            // 异步记录，不影响主流程
            AdminBehaviorLog::recordBehavior($uid, $module, $controller, $action, $ip, $customContent, $params);
        }
    }
    
    /**
     * 生成操作描述
     */
    protected function generateActionDesc($controller, $action)
    {
        $actionMap = [
            'create' => '创建',
            'edit' => '编辑',
            'delete' => '删除',
            'index' => '查看',
            'list' => '列表',
            'save' => '保存',
            'update' => '更新',
            'status' => '修改状态',
        ];
        
        $controllerMap = [
            'User' => '用户',
            'App' => '应用',
            'Message' => '消息',
            'FileLog' => '文件日志',
            'BehaviorLog' => '行为日志',
        ];
        
        $controllerText = $controllerMap[$controller] ?? $controller;
        $actionText = $actionMap[$action] ?? $action;
        
        return "{$controllerText}{$actionText}";
    }
}