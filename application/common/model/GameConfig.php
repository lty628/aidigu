<?php
namespace app\common\model;
use think\Model;

class GameConfig extends Model
{
    protected $table = 'wb_game_config';
    
    // 配置类型获取器
    public function getConfigTypeTextAttr($value, $data)
    {
        $typeMap = [
            1 => '系统默认',
            2 => '用户自定义'
        ];
        return $typeMap[$data['config_type']] ?? '未知';
    }
    
    // 状态获取器
    public function getStatusTextAttr($value, $data)
    {
        $statusMap = [
            0 => '禁用',
            1 => '启用'
        ];
        return $statusMap[$data['status']] ?? '未知';
    }
    
    // 关联用户
    public function user()
    {
        return $this->hasOne('User', 'uid', 'uid')->bind('username');
    }
    
    // 获取游戏配置列表
    public static function getConfigList($where = [], $page = 1, $limit = 20)
    {
        return self::with('user')
            ->where($where)
            ->order('sort_order', 'asc')
            ->order('created_at', 'desc')
            ->paginate([
                'list_rows' => $limit,
                'page' => $page,
                'query' => request()->param()
            ]);
    }
    
    // 根据游戏key和用户ID获取配置
    public static function getConfigByGameKey($gameKey, $uid = 0)
    {
        // 先尝试获取用户自定义配置
        if ($uid > 0) {
            $config = self::where('game_key', $gameKey)
                ->where('uid', $uid)
                ->where('status', 1)
                ->find();
            if ($config) {
                return $config;
            }
        }
        
        // 获取系统默认配置
        return self::where('game_key', $gameKey)
            ->where('config_type', 1)
            ->where('status', 1)
            ->find();
    }
    
    // 解析配置数据
    public function getConfigDataArrayAttr($value, $data)
    {
        return json_decode($data['config_data'], true) ?: [];
    }
}