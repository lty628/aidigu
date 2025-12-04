<?php
namespace app\common\model;
use think\Model;

class AdminSystemSetting extends Model
{
    protected $table = 'wb_admin_system_setting';
    protected $autoWriteTimestamp = true;
    // protected $createTime = 'created_at';
    // protected $updateTime = 'updated_at';

    // 获取所有配置分组
    public static function getSections()
    {
        return self::distinct('section')
            ->field('section')
            ->order('section')
            ->column('section');
    }
    
    // 根据分组获取配置列表
    public static function getSettingsBySection($section = '', $page = 1, $limit = 20)
    {
        $query = self::order(['section' => 'asc', 'key' => 'asc']);
        
        if (!empty($section)) {
            $query->where('section', $section);
        }
        
        return $query->paginate([
            'list_rows' => $limit,
            'page' => $page,
            'query' => request()->param()
        ]);
    }
    
    // 根据section和key获取配置值
    public static function getSettingValue($section, $key, $default = null)
    {
        $setting = self::where(['section' => $section, 'key' => $key])->find();
        return $setting ? $setting->value : $default;
    }
    
    // 批量更新配置
    public static function batchUpdate($data)
    {
        $result = ['success' => 0, 'error' => 0];
        
        foreach ($data as $section => $configs) {
            foreach ($configs as $key => $value) {
                try {
                    $saveData = ['value' => $value];
                    $where = ['section' => $section, 'key' => $key];
                    
                    // 检查是否存在，存在则更新，不存在则新增
                    $count = self::where($where)->count();
                    if ($count > 0) {
                        self::where($where)->update($saveData);
                    } else {
                        $saveData = array_merge($where, $saveData);
                        self::create($saveData);
                    }
                    $result['success']++;
                } catch (\Exception $e) {
                    $result['error']++;
                }
            }
        }
        
        return $result;
    }
}