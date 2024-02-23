<?php
namespace app\tools\controller;
use think\Controller;
use think\Db;

/**
 * 收藏管理
 */
class Collect extends Controller
{	
	public function list()
    {
        return $this->fetch();
    }

    public function getList()
    {
        $uid = getLoginUid();
        $get = input('get.');
        $page = $get['page'] ?? 1;
        $limit = $get['limit'] ?? 10;
        $list = Db::name('user_collect')
            ->alias('user_collect')
            ->leftJoin([getPrefix() . 'message' => 'message'], 'message.msg_id=user_collect.msg_id')
            ->field('message.msg_id,message.contents,message.media,message.media_info,user_collect.collect_id,user_collect.collect_time')
            ->where('user_collect.fromuid', $uid)
            ->where('user_collect.delete_time', 0)
            ->order('user_collect.collect_id desc')
            ->limit($limit)->page($page)
            ->select();
        foreach ($list as &$value) {
            $value['contents'] = strip_tags($value['contents']);
        }
        return json(['code' => 0, 'data' => $list]);
    }

    public function del()
    {
        $id = (int)input('post.id');
        $uid = getLoginUid();
        if (!$id) return json(['code' => 1, 'msg' => '取消失败']);

        Db::name('user_collect')->where('fromuid', $uid)->where('collect_id', $id)->update([
            'delete_time' => time()
        ]);

        return json(['code' => 0, 'msg' => '取消成功']);
    }

}
