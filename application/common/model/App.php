<?php
namespace app\common\model;
use think\Model;

class App extends Model
{
    protected $table = 'wb_app';
    // protected $autoWriteTimestamp = true;
    // protected $createTime = 'create_time';
    // protected $updateTime = 'update_time';
    // protected $dateFormat = 'Y-m-d H:i:s';
    
    // 应用状态获取器
    public function getAppStatusTextAttr($value, $data)
    {
        $statusMap = [
            0 => '关闭',
            1 => '站内',
            2 => '站外'
        ];
        return $statusMap[$data['app_status']] ?? '未知';
    }
    
    // 应用类型获取器
    public function getAppTypeTextAttr($value, $data)
    {
        $typeMap = [
            0 => '全部',
            1 => 'PC',
            2 => '手机'
        ];
        return $typeMap[$data['app_type']] ?? '未知';
    }
    
    // 打开方式获取器
    public function getOpenTypeTextAttr($value, $data)
    {
        $openMap = [
            0 => 'Frame',
            1 => '直接打开',
            2 => '新窗口打开'
        ];
        return $openMap[$data['open_type']] ?? '未知';
    }
    
    // 关联用户
    public function user()
    {
        return $this->hasOne('User', 'uid', 'fromuid')->bind('username');
    }
    
    // 获取应用列表
    public static function getAppList($where = [], $page = 1, $limit = 20)
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
    
    // 根据ID获取应用
    public static function getAppById($id)
    {
        return self::where('id', $id)->find();
    }
}