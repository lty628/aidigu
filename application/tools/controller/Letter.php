<?php
// application/tools/controller/Letter.php
namespace app\tools\controller;
use think\Db;
use app\common\controller\Base;
use LimitIterator;
use think\Request;

class Letter extends Base
{
    public function index()
    {
        return view();
    }
    
    // 获取信件列表
    public function getList(Request $request)
    {
        $status = $request->param('status'); // 0未读，1已读，空则全部
        $page = $request->param('page', 1); // 当前页码
        $limit = $request->param('limit', 10); // 每页数量
        
        $query = Db::name('tools_letters')->where('receiver', getLoginNickName());
        
        // 根据status参数进行筛选
        if ($status !== null && $status !== '') {
            $query = $query->where('status', $status);
        }
        
        // 计算总数用于分页
        $totalCount = $query->count();
        
        $list = $query
            ->order('created_at desc')
            ->field('id, sender_name, title, content, receive_time, status, created_at')
            ->page($page, $limit)
            ->select();
            
        // 返回分页信息
        return json([
            'code' => 0, 
            'data' => $list,
            'pagination' => [
                'currentPage' => (int)$page,
                'perPage' => (int)$limit,
                'total' => (int)$totalCount,
                'totalPages' => ceil($totalCount / $limit)
            ]
        ]);
    }
    
    // 获取信件详情
    public function getDetail(Request $request)
    {
        $id = $request->param('id'); // 获取URL路径中的第一个参数为id
        // dump($id);
        $detail = Db::name('tools_letters')->find($id);
        if ($detail && $detail['status'] == 0) {
            Db::name('tools_letters')->where('id', $id)->update(['status' => 1]);
        }
        return json(['code' => 0, 'data' => $detail]);
    }
    
    // 发送信件
    public function send(Request $request)
    {
        $data = [
            'sender_id' => getLoginUid(),
            'sender_name' => getLoginNickName(), // 添加发信人姓名
            'receiver' => $request->param('receiver'),
            'title' => $request->param('title'),
            'content' => $request->param('content'),
            'receive_time' => $request->param('receive_time') ?: date('Y-m-d H:i:s'),
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $result = Db::name('tools_letters')->insertGetId($data);
        if ($result) {
            return json(['code' => 0, 'msg' => '发送成功']);
        } else {
            return json(['code' => 1, 'msg' => '发送失败']);
        }
    }
}

// CREATE TABLE `wb_tools_letters` (
//   `id` int(11) NOT NULL AUTO_INCREMENT,
//   `sender_id` int(11) NOT NULL COMMENT '发信人ID',
//   `sender_name` varchar(255) NOT NULL COMMENT '发信人姓名',
//   `receiver` varchar(255) NOT NULL COMMENT '收信人',
//   `title` varchar(255) NOT NULL COMMENT '信件标题',
//   `content` text NOT NULL COMMENT '信件内容',
//   `send_time` datetime DEFAULT NULL COMMENT '发送时间',
//   `receive_time` datetime NOT NULL COMMENT '接收时间',
//   `status` tinyint(1) DEFAULT 0 COMMENT '状态：0未读，1已读',
//   `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
//   `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
//   PRIMARY KEY (`id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='信件表';