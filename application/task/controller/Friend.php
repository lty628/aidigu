<?php
namespace app\task\controller;

use think\Db;

class Friend
{
    public function __construct()
    {
        // 是否命令行
        if (request()->isCli() == false) {
            die('非法访问');
        }
    }
    
    public function index()
    {
        $info = Db::name('fans')->select();

        foreach ($info as $val) {
            if (Db::name('chat_friends')->where([
                'fromuid' => $val['fromuid'],
                'touid' => $val['touid'],
            ])->find()) continue;
            Db::name('chat_friends')->insert([
                'fromuid' => $val['fromuid'],
                'touid' => $val['touid'],
                'mutual_concern' => $val['mutual_concern'],
                'ctime' => time(),
            ]);
        }
        dump('任务完成');
    }
}