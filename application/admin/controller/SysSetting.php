<?php
namespace app\admin\controller;
use app\common\model\AdminSystemSetting;
use think\facade\Request;
use think\facade\View;


class SysSetting extends Base
{
    // 配置列表
    public function index()
    {
        $section = Request::param('section', '');
        $page = Request::param('page', 1, 'intval');
        $limit = Request::param('limit', 20, 'intval');
        
        // 获取所有分组
        $sections = AdminSystemSetting::getSections();
        
        // 获取配置列表
        $list = AdminSystemSetting::getSettingsBySection($section, $page, $limit);
        
        View::assign([
            'sections' => $sections,
            'current_section' => $section,
            'list' => $list
        ]);
        
        return View::fetch();
    }
    
    // 添加配置
    public function add()
    {
        if (Request::isPost()) {
            $data = Request::param();
            
            if ($data['new_section']) {
                $data['section'] = $data['new_section'];
            }
            unset($data['new_section']);
            // 验证数据
            if (empty($data['section']) || empty($data['key'])) {
                $this->error('分组和键名不能为空');
            }
            
            // 检查是否已存在
            $exists = AdminSystemSetting::where(['section' => $data['section'], 'key' => $data['key']])->count();
            if ($exists > 0) {
                $this->error('该配置已存在');
            }
            
            // 创建配置
            $setting = AdminSystemSetting::create($data);
            $sectionKey = $data['section'] . '.' . $data['key'];
            cache($sectionKey, $data['value'], 60);
            if ($setting) {
                // 记录行为日志
                // $this->recordBehavior('添加系统配置', ['section' => $data['section'], 'key' => $data['key']]);
                $this->success('添加成功', 'index');
            } else {
                $this->error('添加失败');
            }
        }
        
        // 获取所有分组
        $sections = AdminSystemSetting::getSections();
        View::assign('sections', $sections);
        
        return View::fetch();
    }
    
    // 编辑配置
    public function edit()
    {
        $id = Request::param('id', 0, 'intval');
        $setting = AdminSystemSetting::find($id);
        
        if (!$setting) {
            $this->error('配置不存在');
        }
        
        if (Request::isPost()) {
            $data = Request::param();
            
            // 验证数据
            if (empty($data['value'])) {
                $data['value'] = '';
            }
            
            // 更新配置
            $oldValue = $setting->value;
            $sectionKey = $setting->section . '.' . $setting->key;
            cache($sectionKey, $data['value'], 60);
            if ($setting->update($data)) {
                // 记录行为日志
                // $this->recordBehavior('编辑系统配置', [
                //     'section' => $setting->section,
                //     'key' => $setting->key,
                //     'old_value' => $oldValue,
                //     'new_value' => $data['value']
                // ]);
                $this->success('更新成功', 'index');
            } else {
                $this->error('更新失败');
            }
        }
        
        // 获取所有分组
        $sections = AdminSystemSetting::getSections();
        View::assign([
            'sections' => $sections,
            'setting' => $setting
        ]);
        
        return View::fetch();
    }
    
    // 删除配置
    public function delete()
    {
        $id = Request::param('id', 0, 'intval');
        $setting = AdminSystemSetting::find($id);
        
        if (!$setting) {
            $this->error('配置不存在');
        }
        
        // 记录行为日志
        $logData = [
            'section' => $setting->section,
            'key' => $setting->key,
            'value' => $setting->value
        ];
        
        if ($setting->delete()) {
            // $this->recordBehavior('删除系统配置', $logData);
            $this->success('删除成功');
        } else {
            $this->error('删除失败');
        }
    }
    
    // 批量更新配置
    public function batchSave()
    {
        if (Request::isPost()) {
            $data = Request::param('settings/a', []);
            
            if (empty($data)) {
                $this->error('没有数据需要更新');
            }
            
            $result = AdminSystemSetting::batchUpdate($data);
            
            if ($result['error'] === 0) {
                // $this->recordBehavior('批量更新系统配置', ['count' => $result['success']]);
                $this->success('全部更新成功', 'index');
            } else {
                $this->success("成功更新{$result['success']}项，失败{$result['error']}项", 'index');
            }
        }
    }
    
    // // 记录行为日志
    // private function recordBehavior($content, $params = [])
    // {
    //     $uid = $this->getLoginUserInfo('uid');
    //     $ip = Request::ip();
        
    //     \app\common\model\AdminBehaviorLog::recordBehavior(
    //         $uid,
    //         'admin',
    //         'SysSetting',
    //         Request::action(),
    //         $ip,
    //         $content,
    //         $params
    //     );
    // }
}