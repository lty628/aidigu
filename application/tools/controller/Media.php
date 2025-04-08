<?php

namespace app\tools\controller;

use think\Controller;
use think\Db;

class Media extends Controller
{
    public function video()
    {
        $id = input('param.id');
        // echo $id;die;
        $this->assign('videoUrl', base64_decode($id));
        return $this->fetch();
    }
    public function index()
    {
        return $this->fetch();
    }
    
    // 新增获取日记列表方法
    public function getDiaryList()
    {
        $userid = getLoginUid();
        $date = input('param.date', '');
        
        $prefix = config('database.prefix');
        $query = Db::name('message')
            ->alias('message')
            ->join([$prefix . 'fans' => 'fans'], 'message.uid=fans.touid and fans.fromuid=' . $userid)
            ->join([$prefix . 'user' => 'user'], 'user.uid=fans.touid')
            ->where('message.is_delete', 0);
            
        // 添加日期筛选条件
        if ($date) {
            $dateStart = strtotime($date . ' 00:00:00');
            // $dateEnd = strtotime($date . ' 23:59:59');
            $dateEnd = time();
            $query->where('message.ctime', 'between', [$dateStart, $dateEnd]);
        }
            
        $userMessage = $query
            ->order('message.ctime asc')
            ->field('user.uid,user.nickname,user.head_image,user.blog,message.ctime,message.contents,message.repost,message.refrom,message.repostsum,message.media,message.media_info,message.commentsum,message.msg_id')
            ->paginate(8, false, ['page' => request()->param('page/d', 1), 'path' => '[PAGE].html']);
            
        $userMessage = $userMessage->toArray()['data'];
        return json(array('status' =>  1, 'msg' => 'ok', 'data' => ['data' => handleMessage($userMessage), 'allow_delete' => 0]));
    }
    public function getDiaryDates() {
        $uid = getLoginUid();
        $month = input('param.month', date('Y-m')); // 默认当前月
        
        // 计算月份的开始和结束时间
        $startTime = strtotime($month . '-01 00:00:00');
        $endTime = strtotime(date('Y-m-t 23:59:59', $startTime));
        
        $dates = Db::name('message')
            ->where('uid', $uid)
            ->where('ctime', 'between', [$startTime, $endTime])
            ->field("FROM_UNIXTIME(ctime, '%Y-%m-%d') as date")
            ->group("FROM_UNIXTIME(ctime, '%Y-%m-%d')")
            ->order('date desc')
            ->select();
        
        return json([
            'status' => 1, 
            'msg' => 'ok', 
            'data' => $dates,
            'current_month' => date('Y-m', $startTime) // 返回当前查询的月份
        ]);
    }
}
