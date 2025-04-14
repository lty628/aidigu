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

    // 修改获取日记列表方法，不再分页，获取指定日期的所有数据
    public function getDiaryList()
    {
        $userid = getLoginUid();
        $date = input('param.date', '');

        // 如果没有提供日期，默认查询当前日期
        if (!$date) {
            $date = date('Y-m-d');
        }

        $query = Db::name('message')
            ->alias('message')
            ->where('message.uid', $userid) // 只查询自己的日记
            ->where('message.is_delete', 0);

        // 添加日期筛选条件
        $dateStart = strtotime($date . ' 00:00:00');
        $dateEnd = strtotime($date . ' 23:59:59');
        $query->where('message.ctime', 'between', [$dateStart, $dateEnd]);

        $userMessage = $query
            ->order('message.ctime asc')
            ->field('uid,is_delete,commentsum,repostsum,collectsum', true)
            ->select();

        // dump($userMessage);die;
        return json(array('status' =>  1, 'msg' => 'ok', 'data' => [
            'data' => handleMessage($userMessage),
            'allow_delete' => 1,
            'date' => $date, // 返回查询的日期
        ]));
    }

    public function getDiaryDates() {
        $uid = getLoginUid();
        $month = input('param.month', date('Y-m')); // 默认当前月

        // 验证 month 参数格式
        if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
            return json([
                'status' => 0, 
                'msg' => '参数格式错误，month 应为 Y-m 格式', 
                'data' => [],
                'current_month' => null
            ]);
        }

        // 计算月份的开始和结束时间
        $startTime = strtotime($month . '-01 00:00:00');
        $endTime = strtotime(date('Y-m-t 23:59:59', $startTime));

        $dates = Db::name('message')
            ->where('uid', $uid)
            ->where('ctime', 'between', [$startTime, $endTime])
            ->field("FROM_UNIXTIME(ctime, '%Y-%m-%d') as date")
            ->group("FROM_UNIXTIME(ctime, '%Y-%m-%d')")
            ->where('message.is_delete', 0)
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
