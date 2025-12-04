<?php
namespace app\common\model;
use think\Model;

class AdminBehaviorLog extends Model
{
    protected $table = 'wb_admin_behavior_log';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = false;
    
    // 类型转换
    protected $type = [
        'create_time' => 'integer',
    ];
    
    // 关联用户
    public function user()
    {
        return $this->hasOne('User', 'uid', 'uid')->bind('username,nickname');
    }
    
    // 获取行为日志列表
    public static function getBehaviorLogs($where = [], $page = 1, $limit = 20)
    {
        return self::with('user')
            ->where($where)
            ->order('create_time', 'desc')
            ->paginate([
                'list_rows' => $limit,
                'page' => $page,
                'query' => request()->param()
            ]);
    }
    
    // 记录行为日志
    public static function recordBehavior($uid, $module, $controller, $action, $ip, $content = '', $params = [])
    {
        $log = new self();
        $log->uid = $uid;
        $log->module = $module;
        $log->controller = $controller;
        $log->action = $action;
        $log->ip = $ip;
        $log->content = $content;
        $log->params = !empty($params) ? json_encode($params, JSON_UNESCAPED_UNICODE) : '';
        $log->save();
        return $log;
    }
}