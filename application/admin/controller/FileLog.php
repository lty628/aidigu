<?php
namespace app\admin\controller;
use think\facade\Request;
use app\common\model\FileLog as FileLogModel;
use think\Db;

class FileLog extends Base
{
    /**
     * 文件日志列表页面
     * @return mixed
     */
    public function index()
    {
        // 渲染模板
        return $this->fetch();
    }
    
    /**
     * 获取文件日志列表数据（layui table所需的JSON数据）
     * @return json
     */
    public function getList()
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
            if (isset($where['create_time'])) {
                $where['create_time'][1] = ['<=', strtotime($end_time) + 86400];
            } else {
                $where['create_time'] = ['<=', strtotime($end_time) + 86400];
            }
        }
        
        // 获取分页参数
        $page = Request::param('page', 1, 'intval');
        $limit = Request::param('limit', 20, 'intval');
        
        // 查询数据
        $fileLogs = FileLogModel::getFileLogs($where, $page, $limit);
        
        // 处理返回数据
        $data = [];
        foreach ($fileLogs as $log) {
            // 直接使用数据库字段，不做额外处理
            $data[] = [
                'id' => $log->id,
                'uid' => $log->uid,
                'username' => $log->user->username ?? '-',
                'nickname' => $log->user->nickname ?? '-',
                'type' => $log->type,
                'type_text' => $log->type_text,
                'media_name' => $log->media_name,
                'media_type' => $log->media_type,
                'media_size' => $log->media_size,
                'media_mime' => $log->media_mime,
                'media_info' => $log->media_info,
                'is_del' => $log->is_del,
                'is_del_text' => $log->is_del_text,
                'create_time' => $log->create_time
            ];
        }
        
        // 返回layui table所需的JSON格式
        return json([
            'code' => 0,
            'msg' => '',
            'count' => $fileLogs->total(),
            'data' => $data
        ]);
    }
    
    /**
     * 删除文件日志
     * @return json
     */
    public function delete()
    {
        $id = Request::param('id', 0, 'intval');
        
        if (empty($id)) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }
        
        // 执行软删除
        $result = Db::name('file_log')
            ->where('id', $id)
            ->update(['is_del' => 1]);
        
        if ($result) {
            return json(['code' => 1, 'msg' => '删除成功']);
        } else {
            return json(['code' => 0, 'msg' => '删除失败或记录不存在']);
        }
    }
    
    /**
     * 批量删除文件日志
     * @return json
     */
    public function batchDelete()
    {
        $ids = Request::param('ids/a', []);
        
        if (empty($ids)) {
            return json(['code' => 0, 'msg' => '请选择要删除的记录']);
        }
        
        // 执行批量软删除
        $result = Db::name('file_log')
            ->where('id', 'in', $ids)
            ->update(['is_del' => 1]);
        
        if ($result) {
            return json(['code' => 1, 'msg' => '批量删除成功']);
        } else {
            return json(['code' => 0, 'msg' => '批量删除失败']);
        }
    }
    
    /**
     * 恢复文件日志
     * @return json
     */
    public function restore()
    {
        $id = Request::param('id', 0, 'intval');
        
        if (empty($id)) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }
        
        // 执行恢复操作
        $result = Db::name('file_log')
            ->where('id', $id)
            ->update(['is_del' => 0]);
        
        if ($result) {
            return json(['code' => 1, 'msg' => '恢复成功']);
        } else {
            return json(['code' => 0, 'msg' => '恢复失败或记录不存在']);
        }
    }
    
    /**
     * 批量恢复文件日志
     * @return json
     */
    public function batchRestore()
    {
        $ids = Request::param('ids/a', []);
        
        if (empty($ids)) {
            return json(['code' => 0, 'msg' => '请选择要恢复的记录']);
        }
        
        // 执行批量恢复操作
        $result = Db::name('file_log')
            ->where('id', 'in', $ids)
            ->update(['is_del' => 0]);
        
        if ($result) {
            return json(['code' => 1, 'msg' => '批量恢复成功']);
        } else {
            return json(['code' => 0, 'msg' => '批量恢复失败']);
        }
    }
}