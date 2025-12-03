<?php
namespace app\common\model;
use think\Model;

class FileLog extends Model
{
    protected $table = 'wb_file_log';
    protected $autoWriteTimestamp = true;
    protected $createTime = 'create_time';
    protected $updateTime = false;
    
    // 类型转换
    protected $type = [
        'create_time' => 'integer',
        'media_size' => 'float',
    ];
    
    // 获取文件类型中文描述
    public function getTypeTextAttr($value, $data)
    {
        $typeMap = [
            1 => '头像',
            2 => '微博',
            3 => '聊天',
            4 => '素材',
            5 => '主题',
            6 => '网盘',
        ];
        return $typeMap[$data['type']] ?? '未知';
    }
    
    // 获取删除状态文本
    public function getIsDelTextAttr($value, $data)
    {
        return $data['is_del'] == 1 ? '已删除' : '正常';
    }
    
    // 关联用户
    public function user()
    {
        return $this->hasOne('User', 'uid', 'uid')->bind('username,nickname');
    }
    
    // 获取文件日志列表
    public static function getFileLogs($where = [], $page = 1, $limit = 20)
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
    
    // 根据ID获取文件日志
    public static function getFileLogById($id)
    {
        return self::with('user')->where('id', $id)->find();
    }
}