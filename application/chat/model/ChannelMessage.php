<?php
namespace app\chat\model;
use think\Model;

class ChannelMessage extends Model
{
    // 设置完整的数据表名（包含前缀）
    protected $table = 'wb_channel_message';
    
    // 自动写入时间戳
    protected $autoWriteTimestamp = true;
    protected $createTime = 'ctime';
    protected $updateTime = false;
    
    // 定义时间戳字段类型为int
    protected $type = [
        'ctime' => 'integer',
    ];
}