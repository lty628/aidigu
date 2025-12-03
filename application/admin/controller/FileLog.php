<?php
namespace app\admin\controller;
use think\facade\Request;
use app\common\model\FileLog as FileLogModel;
use think\Db;

class FileLog extends Base
{
    /**
     * 文件日志列表
     * @return mixed
     */
    public function index()
    {
        // 获取搜索参数
        $keyword = Request::param('keyword', '');
        $uid = Request::param('uid', 0, 'intval');
        $type = Request::param('type', '', 'intval');
        $is_del = Request::param('is_del', '');
        $start_time = Request::param('start_time', '');
        $end_time = Request::param('end_time', '');
        
        // 构建查询条件
        $where = [];
        
        if (!empty($keyword)) {
            $where[] = ['media_name|media_info', 'like', '%' . $keyword . '%'];
        }
        
        if (!empty($uid)) {
            $where['uid'] = $uid;
        }
        
        if ($type !== '') {
            $where['type'] = $type;
        }
        
        if ($is_del !== '') {
            $where['is_del'] = $is_del;
        }
        
        if (!empty($start_time)) {
            $where['create_time'] = ['>=', strtotime($start_time)];
        }
        
        if (!empty($end_time)) {
            $where['create_time'] = ['<=', strtotime($end_time) + 86399]; // 结束时间加23:59:59
        }
        
        if (!empty($start_time) && !empty($end_time)) {
            $where['create_time'] = ['between', [strtotime($start_time), strtotime($end_time) + 86399]];
        }
        
        // 获取分页参数
        $page = Request::param('page', 1, 'intval');
        $limit = Request::param('limit', 20, 'intval');
        
        // 获取文件日志列表
        $list = FileLogModel::getFileLogs($where, $page, $limit);
        
        // 渲染模板
        $this->assign('list', $list);
        $this->assign('keyword', $keyword);
        $this->assign('uid', $uid);
        $this->assign('type', $type);
        $this->assign('is_del', $is_del);
        $this->assign('start_time', $start_time);
        $this->assign('end_time', $end_time);
        
        // 获取类型选项
        $typeOptions = [
            1 => '头像',
            2 => '微博',
            3 => '聊天',
            4 => '素材',
            5 => '主题',
            6 => '网盘',
        ];
        $this->assign('typeOptions', $typeOptions);
        
        return $this->fetch();
    }
    
    /**
     * 删除文件日志
     * @return json
     */
    public function delete()
    {
        $id = Request::param('id', 0, 'intval');
        
        if (!$id) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }
        
        // 检查日志是否存在
        $fileLog = FileLogModel::getFileLogById($id);
        if (!$fileLog) {
            return json(['code' => 0, 'msg' => '文件日志不存在']);
        }
        
        // 执行软删除
        $result = FileLogModel::where('id', $id)->update(['is_del' => 1]);
        
        if ($result) {
            // 记录操作日志
            $this->log('删除文件日志 ID: ' . $id);
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
            return json(['code' => 0, 'msg' => '请选择要删除的记录']);
        }
        
        // 转换为数组
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }
        
        // 执行批量删除
        $result = FileLogModel::where('id', 'in', $ids)->update(['is_del' => 1]);
        
        if ($result) {
            // 记录操作日志
            $this->log('批量删除文件日志 IDs: ' . implode(',', $ids));
            return json(['code' => 1, 'msg' => '删除成功，共删除 ' . $result . ' 条记录']);
        } else {
            return json(['code' => 0, 'msg' => '删除失败']);
        }
    }
    
    /**
     * 恢复已删除的文件日志
     * @return json
     */
    public function restore()
    {
        $id = Request::param('id', 0, 'intval');
        
        if (!$id) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }
        
        // 执行恢复
        $result = FileLogModel::where('id', $id)->update(['is_del' => 0]);
        
        if ($result) {
            // 记录操作日志
            $this->log('恢复文件日志 ID: ' . $id);
            return json(['code' => 1, 'msg' => '恢复成功']);
        } else {
            return json(['code' => 0, 'msg' => '恢复失败']);
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