<?php
namespace app\admin\controller;
use app\common\model\Message as MessageModel;
use think\Db;
use think\facade\Request;

class Message extends Base
{
    /**
     * 内容列表
     * @return mixed
     */
    public function index()
    {
        // 渲染模板
        return $this->fetch();
    }
    
    /**
     * 获取表格数据
     * @return json
     */
    public function getList()
    {
        // 获取搜索参数
        $keyword = Request::param('keyword', '');
        $username = Request::param('username', '');
        $start_time = Request::param('start_time', '');
        $end_time = Request::param('end_time', '');
        $page = Request::param('page', 1, 'intval');
        $limit = Request::param('limit', 20, 'intval');
        
        // 构建查询条件
        $where = [];
        $where['is_delete'] = 0; // 只显示未删除的内容
        
        if (!empty($keyword)) {
            $where[] = ['contents|repost', 'like', '%' . $keyword . '%'];
        }
        
        if (!empty($username)) {
            // 根据用户名关联查询
            $user_ids = Db::name('user')
                ->where('username|nickname', 'like', '%' . $username . '%')
                ->column('uid');
            if (!empty($user_ids)) {
                $where['uid'] = ['in', $user_ids];
            } else {
                $where['uid'] = -1; // 不存在的用户ID，确保不返回结果
            }
        }
        
        if (!empty($start_time)) {
            $where['ctime'] = ['>=', strtotime($start_time)];
        }
        
        if (!empty($end_time)) {
            $where['ctime'] = ['<=', strtotime($end_time) + 86399]; // 结束时间加23:59:59
        }
        
        if (!empty($start_time) && !empty($end_time)) {
            $where['ctime'] = ['between', [strtotime($start_time), strtotime($end_time) + 86399]];
        }
        
        // 获取数据总数
        $count = MessageModel::where($where)->count();
        
        // 获取数据列表
        $data = MessageModel::with('user')
            ->where($where)
            ->order('ctime', 'desc')
            ->page($page, $limit)
            ->select();
        
        // 格式化数据
        foreach ($data as &$item) {
            $item['username'] = $item->user->username ?? '-';
        }
        
        // 返回表格数据格式
        return json([
            'msg' => 'success',
            'count' => $count,
            'data' => $data
        ]);
    }
    
    /**
     * 查看内容详情
     * @return mixed
     */
    public function show()
    {
        $msg_id = Request::param('msg_id', 0, 'intval');
        
        if (!$msg_id) {
            $this->error('参数错误');
        }
        
        // 获取内容详情
        $message = MessageModel::with('user')->where('msg_id', $msg_id)->find();
        
        if (!$message || $message['is_delete'] == 1) {
            $this->error('内容不存在或已被删除');
        }
        
        // 渲染模板
        $this->assign('message', $message);
        return $this->fetch();
    }
    
    /**
     * 删除内容
     * @return json
     */
    public function delete()
    {
        $msg_id = Request::param('msg_id', 0, 'intval');
        
        if (!$msg_id) {
            return json(['code' => 0, 'msg' => '参数错误']);
        }
        
        // 检查内容是否存在
        $message = MessageModel::where('msg_id', $msg_id)->find();
        if (!$message) {
            return json(['code' => 0, 'msg' => '内容不存在']);
        }
        
        // 执行删除（软删除）
        $result = MessageModel::where('msg_id', $msg_id)->update(['is_delete' => 1]);
        
        if ($result) {
            // 更新用户的消息数量
            Db::name('user')->where('uid', $message['uid'])->setDec('message_sum', 1);
            
            // 记录操作日志
            $this->log('删除内容 ID: ' . $msg_id);
            
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
        $msg_ids = Request::param('msg_ids', '');
        
        if (empty($msg_ids)) {
            return json(['code' => 0, 'msg' => '请选择要删除的内容']);
        }
        
        // 转换为数组
        if (!is_array($msg_ids)) {
            $msg_ids = explode(',', $msg_ids);
        }
        
        // 开始事务
        Db::startTrans();
        try {
            // 批量删除
            $result = MessageModel::where('msg_id', 'in', $msg_ids)->update(['is_delete' => 1]);
            
            // 更新用户消息数量
            $messages = MessageModel::where('msg_id', 'in', $msg_ids)->field('uid,count(*) as count')->group('uid')->select();
            foreach ($messages as $msg) {
                Db::name('user')->where('uid', $msg['uid'])->setDec('message_sum', $msg['count']);
            }
            
            // 记录操作日志
            $this->log('批量删除内容 IDs: ' . implode(',', $msg_ids));
            
            // 提交事务
            Db::commit();
            
            return json(['code' => 1, 'msg' => '删除成功，共删除 ' . $result . ' 条记录']);
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return json(['code' => 0, 'msg' => '删除失败，错误信息：' . $e->getMessage()]);
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