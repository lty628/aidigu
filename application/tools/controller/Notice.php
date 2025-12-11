<?php
namespace app\tools\controller;
use app\common\controller\Base;
use think\Db;

/**
 * 站内消息
 */
class Notice extends Base
{	
	public function list()
    {
        return $this->fetch();
    }

    public function getList()
    {
        // $type 0: 转发 1: 评论 2: 回复 3: 好友 4: 私信  5: 群聊 【群聊提醒待定】
        $uid = getLoginUid();
        $get = input('get.');
        $page = $get['page'] ?? 1;
        $limit = $get['limit'] ?? 10;
        $list = Db::name('reminder')
            ->alias('reminder')
            ->leftJoin([getPrefix() . 'message' => 'message'], 'message.msg_id=reminder.msg_id')
            ->field('message.msg_id,message.contents,message.media,message.media_info,reminder.id,reminder.ctime,reminder.status,reminder.type')
            // ->where('reminder.status', 0)
            // ->where('reminder.type', 1)
            ->where('reminder.touid', $uid)
            ->order('reminder.status asc, reminder.id desc')
            ->limit($limit)->page($page)
            ->select();
        $count = Db::name('reminder')->where('type', 1)->where('status', 0)->where('touid', $uid)->count();
        
        foreach ($list as &$value) {
            $value['contents'] = strip_tags($value['contents']);
            $value['remind_time'] = date('Y-m-d H:i:s', $value['ctime']);
        }
        // dump($list);die;
        return json(['code' => 0, 'data' => $list, 'count' => $count]);
    }

    public function del()
    {
        $id = (int)input('post.id');
        $uid = getLoginUid();
        if (!$id) return json(['code' => 1, 'msg' => '取消失败']);

        $result = Db::name('reminder')->where('touid', $uid)->where('id', $id)->update([
            'status' => 1
        ]);

        if (!$result) return json(['code' => 1, 'msg' => '取消失败']);
        
        return json(['code' => 0, 'msg' => '取消成功']);
    }

    public function view()
    {
        $msgId = input('get.msgId');
        $info = Db::name('message')
            ->alias('message')
            ->leftJoin([getPrefix() . 'user' => 'user'], 'message.uid=user.uid')
            ->field('user.blog')
            ->find();
        // thinkphp5 跳转链接
        $this->redirect($info['blog'] . '/message/' . $msgId);
    }

}
