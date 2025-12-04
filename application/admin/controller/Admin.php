<?php
namespace app\admin\controller;
use think\Db;

class Admin extends Base
{
    /**
     * 显示管理员首页
     * @return mixed
     */
    public function index()
    {
        return $this->fetch();
    }

    public function dashboard()
    {
        // 获取用户总数
        $totalUsers = Db::name('user')->count();
        
        // 获取内容总数（未删除的消息）
        $totalContent = Db::name('message')->where('is_delete', 0)->count();
        
        // 获取评论总数
        $totalComments = Db::name('comment')->count();
        
        // 用户表 uptime 是今天的用户数
        $todayVisits = Db::name('user')->where('uptime', '>', strtotime(date('Y-m-d')))->count();
        
        // 获取最近6个月的用户增长数据
        $userGrowthData = $this->getUserGrowthData();
        
        // 获取内容分类统计（根据media_info字段判断类型）
        $contentCategoryData = $this->getContentCategoryData();
        
        // 获取最近活动
        $recentActivities = $this->getRecentActivities();
        
        // 赋值给模板
        $this->assign([
            'totalUsers' => $totalUsers,
            'totalContent' => $totalContent,
            'totalComments' => $totalComments,
            'todayVisits' => $todayVisits,
            'userGrowthData' => $userGrowthData,
            'contentCategoryData' => $contentCategoryData,
            'recentActivities' => $recentActivities
        ]);
        
        return $this->fetch();
    }
    
    /**
     * 获取用户增长数据
     */
    private function getUserGrowthData()
    {
        $months = [];
        $data = [];
        
        // 获取最近6个月的数据
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-{$i} month"));
            $months[] = date('m月', strtotime("-{$i} month"));
            
            // 计算该月新增用户数
            $startTime = strtotime($month . '-01');
            $endTime = strtotime("+1 month", $startTime) - 1;
            
            $count = Db::name('user')
                ->where('ctime', '>=', $startTime)
                ->where('ctime', '<=', $endTime)
                ->count();
            
            $data[] = $count;
        }
        
        return [
            'months' => $months,
            'data' => $data
        ];
    }
    
    /**
     * 获取内容分类统计
     */
    private function getContentCategoryData()
    {
        // 统计不同类型的内容数量
        $result = Db::name('message')
            ->field("CASE 
                WHEN media_info IS NULL THEN '纯文本'
                WHEN media_info LIKE '%mp4%' THEN '视频'
                WHEN media_info LIKE '%image%' OR media_info LIKE '%jpg%' OR media_info LIKE '%png%' THEN '图片'
                ELSE '其他'
              END AS category, 
              COUNT(*) AS count")
            ->where('is_delete', 0)
            ->group('category')
            ->select();
        
        // 转换为ECharts需要的格式
        $categories = [];
        $counts = [];
        
        foreach ($result as $item) {
            $categories[] = $item['category'];
            $counts[] = [
                'value' => $item['count'],
                'name' => $item['category']
            ];
        }
        
        return [
            'categories' => $categories,
            'data' => $counts
        ];
    }
    
    /**
     * 获取最近活动
     */
    private function getRecentActivities()
    {
        // 获取最近的10条用户注册记录
        $userActivities = Db::name('user')
            ->field('nickname, ctime, 1 as type')
            ->order('ctime', 'desc')
            ->limit(5)
            ->select();
        
        // 获取最近的5条内容发布记录
        $contentActivities = Db::name('message')
            ->alias('m')
            ->join('wb_user u', 'm.uid = u.uid')
            ->field('u.nickname, m.ctime, 2 as type, m.contents')
            ->where('m.is_delete', 0)
            ->order('m.ctime', 'desc')
            ->limit(5)
            ->select();
        
        // 合并并排序
        $activities = array_merge($userActivities, $contentActivities);
        
        // 按时间倒序排序
        usort($activities, function($a, $b) {
            return $b['ctime'] - $a['ctime'];
        });
        
        // 只取前5条
        return array_slice($activities, 0, 5);
    }
}