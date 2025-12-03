<?php
namespace app\admin\controller;
use think\facade\Request;
use think\facade\Validate;
use app\common\model\App as AppModel;

class App extends Base
{
    /**
     * 应用列表页面
     * @return mixed
     */
    public function index()
    {
        // 渲染模板
        return $this->fetch();
    }
    
    /**
     * 获取应用列表数据（layui table所需的JSON数据）
     * @return json
     */
    public function getList()
    {
        // 获取搜索参数
        $keyword = Request::param('keyword', '');
        $app_status = Request::param('app_status', '');
        $app_type = Request::param('app_type', '');
        
        // 构建查询条件
        $where = [];
        
        if (!empty($keyword)) {
            $where[] = ['app_name|app_url', 'like', '%' . $keyword . '%'];
        }
        
        if ($app_status !== '') {
            $where['app_status'] = $app_status;
        }
        
        if ($app_type !== '') {
            $where['app_type'] = $app_type;
        }
        
        // 获取分页参数
        $page = Request::param('page', 1, 'intval');
        $limit = Request::param('limit', 20, 'intval');
        
        // 获取应用列表
        $list = AppModel::getAppList($where, $page, $limit);
        
        // 处理数据，添加编辑链接
        $data = $list->items();
        foreach ($data as &$item) {
            $item['edit_url'] = url('edit', ['id' => $item['id']]);
        }
        
        // 返回layui table所需的JSON格式
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $list->total(),
            'data' => $data
        ]);
    }
    
    /**
     * 创建应用
     * @return mixed
     */
    public function create()
    {
        if (Request::isPost()) {
            // 获取表单数据
            $data = Request::post();
            
            // 验证表单数据
            $validate = Validate::rule([
                'app_name' => 'require|max:255',
                'app_url' => 'require|max:255|url',
                'app_image' => 'require|max:255',
                'app_status' => 'require|in:0,1,2',
                'app_type' => 'require|in:0,1,2',
                'open_type' => 'require|in:0,1,2',
            ]);
            
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            
            // 设置创建者
            $data['fromuid'] = session('admin.uid', 0);
            
            // 如果app_config为空，设置为默认值
            if (empty($data['app_config'])) {
                $data['app_config'] = '{}';
            }
            
            // 创建应用
            $app = new App();
            $result = $app->allowField(true)->save($data);
            
            if ($result) {
                // 记录操作日志
                $this->log('创建应用：' . $data['app_name']);
                $this->success('创建成功', 'index');
            } else {
                $this->error('创建失败');
            }
        }
        
        // 渲染模板
        $this->assign('statusOptions', [0 => '关闭', 1 => '站内', 2 => '站外']);
        $this->assign('typeOptions', [0 => '全部', 1 => 'PC', 2 => '手机']);
        $this->assign('openTypeOptions', [0 => 'Frame', 1 => '直接打开', 2 => '新窗口打开']);
        
        return $this->fetch();
    }
    
    /**
     * 编辑应用
     * @return mixed
     */
    public function edit()
    {
        $id = Request::param('id', 0, 'intval');
        
        if (!$id) {
            $this->error('参数错误');
        }
        
        // 获取应用信息
        $app = AppModel::getAppById($id);
        
        if (!$app) {
            $this->error('应用不存在');
        }
        
        if (Request::isPost()) {
            // 获取表单数据
            $data = Request::post();
            
            // 验证表单数据
            $validate = Validate::rule([
                'app_name' => 'require|max:255',
                'app_url' => 'require|max:255|url',
                'app_image' => 'require|max:255',
                'app_status' => 'require|in:0,1,2',
                'app_type' => 'require|in:0,1,2',
                'open_type' => 'require|in:0,1,2',
            ]);
            
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
            
            // 如果app_config为空，设置为默认值
            if (empty($data['app_config'])) {
                $data['app_config'] = '{}';
            }
            
            // 更新应用
            $result = $app->allowField(true)->save($data);
            
            if ($result) {
                // 记录操作日志
                $this->log('编辑应用：' . $data['app_name']);
                $this->success('更新成功', 'index');
            } else {
                $this->error('更新失败');
            }
        }
        
        // 渲染模板
        $this->assign('app', $app);
        $this->assign('statusOptions', [0 => '关闭', 1 => '站内', 2 => '站外']);
        $this->assign('typeOptions', [0 => '全部', 1 => 'PC', 2 => '手机']);
        $this->assign('openTypeOptions', [0 => 'Frame', 1 => '直接打开', 2 => '新窗口打开']);
        
        return $this->fetch();
    }
    
    /**
     * 删除应用
     * @return json
     */
    public function delete()
    {
        $id = Request::param('id', 0, 'intval');
        
        if (!$id) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }
        
        // 检查应用是否存在
        $app = AppModel::getAppById($id);
        if (!$app) {
            return json(['code' => 0, 'msg' => '应用不存在']);
        }
        
        // 执行删除
        $result = $app->delete();
        
        if ($result) {
            // 记录操作日志
            $this->log('删除应用：' . $app['app_name']);
            return json(['code' => 1, 'msg' => '删除成功']);
        } else {
            return json(['code' => 0, 'msg' => '删除失败']);
        }
    }
    
    /**
     * 批量删除
     * @return json
     */
    public function batchDelete()
    {
        $ids = Request::param('ids', '');
        
        if (empty($ids)) {
            return json(['code' => 0, 'msg' => '请选择要删除的应用']);
        }
        
        // 转换为数组
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }
        
        // 执行批量删除
        $result = AppModel::destroy($ids);
        
        if ($result) {
            // 记录操作日志
            $this->log('批量删除应用，ID：' . implode(',', $ids));
            return json(['code' => 1, 'msg' => '删除成功，共删除 ' . $result . ' 个应用']);
        } else {
            return json(['code' => 0, 'msg' => '删除失败']);
        }
    }
    
    /**
     * 切换应用状态
     * @return json
     */
    public function toggleStatus()
    {
        $id = Request::param('id', 0, 'intval');
        
        if (!$id) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }
        
        // 获取应用信息
        $app = AppModel::getAppById($id);
        if (!$app) {
            return json(['code' => 0, 'msg' => '应用不存在']);
        }
        
        // 切换状态
        $newStatus = $app['app_status'] == 0 ? 1 : 0;
        $result = $app->save(['app_status' => $newStatus]);
        
        // 注意：这里需要修改返回值，添加new_status字段
        if ($result) {
            // 记录操作日志
            $statusText = $newStatus == 0 ? '关闭' : '开启';
            $this->log($statusText . '应用：' . $app['app_name']);
            return json(['code' => 1, 'msg' => '操作成功', 'data' => ['new_status' => $newStatus]]);
        } else {
            return json(['code' => 0, 'msg' => '操作失败']);
        }
    }
    
    /**
     * 记录操作日志
     * @param string $content 日志内容
     */
    protected function log($content)
    {
        // 这里可以根据实际需求实现日志记录功能
        // 例如：写入数据库或文件
    }
}