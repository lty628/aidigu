<?php
namespace app\admin\controller;

use app\common\model\Comment as CommentModel;
use think\Request;

class Comment extends Base
{
    // 评论列表页
    public function index()
    {
        return $this->fetch();
    }

    // 获取评论列表数据
    public function getList(Request $request)
    {
        // 获取参数
        $keyword = $request->param('keyword', '');
        $username = $request->param('username', '');
        $start_time = $request->param('start_time', '');
        $end_time = $request->param('end_time', '');
        $page = $request->param('page', 1);
        $limit = $request->param('limit', 20);

        // 构建查询条件
        $where = [];
        
        if ($keyword) {
            $where[] = ['msg', 'like', '%' . $keyword . '%'];
        }
        
        if ($username) {
            $where[] = ['username', 'like', '%' . $username . '%'];
        }
        
        if ($start_time) {
            $where[] = ['ctime', '>=', strtotime($start_time)];
        }
        
        if ($end_time) {
            $where[] = ['ctime', '<=', strtotime($end_time . ' 23:59:59')];
        }

        // 查询数据
        $commentModel = new CommentModel();
        $total = $commentModel->with('user')->where($where)->count();
        $comments = $commentModel->with('user')
            ->where($where)
            ->order('cid desc')
            ->page($page, $limit)
            ->select()
            ->toArray();

        // 返回结果
        return json([
            'code' => 0,
            'msg' => '获取成功',
            'count' => $total,
            'data' => $comments
        ]);
    }

    // 删除评论
    public function delete(Request $request)
    {
        $cid = $request->param('cid');
        
        if (!$cid) {
            return json([
                'code' => 1,
                'msg' => '参数错误'
            ]);
        }

        $commentModel = new CommentModel();
        $result = $commentModel->where('cid', $cid)->delete();
        
        if ($result) {
            // 记录操作日志
            $this->recordBehavior('删除评论', ['cid' => $cid]);
            return json([
                'code' => 0,
                'msg' => '删除成功'
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '删除失败'
            ]);
        }
    }

    // 批量删除评论
    public function batchDelete(Request $request)
    {
        $cids = $request->param('cids');
        
        if (!$cids) {
            return json([
                'code' => 1,
                'msg' => '参数错误'
            ]);
        }

        $cidArray = explode(',', $cids);
        
        $commentModel = new CommentModel();
        $result = $commentModel->where('cid', 'in', $cidArray)->delete();
        
        if ($result) {
            // 记录操作日志
            $this->recordBehavior('批量删除评论', ['cids' => $cids]);
            return json([
                'code' => 0,
                'msg' => '批量删除成功'
            ]);
        } else {
            return json([
                'code' => 1,
                'msg' => '批量删除失败'
            ]);
        }
    }
}