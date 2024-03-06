<?php
namespace app\tools\controller;
use think\Controller;
use think\Db;


class Sourcematerial extends Controller
{	
    public function list()
    {
        return $this->fetch();
    }

    public function add()
    {
        return $this->fetch();
    }

    public function share()
    {
        return $this->fetch();
    }

    public function siteShare()
    {
        $id = input('post.id');
        $uid = getLoginUid();
        $time = time();
        $find =  Db::name('source_material')->where('uid', $uid)->where('id', $id)->find();

        if (!$find) {
            return json(['code' => 1, 'msg' => '无权限']);
        }
        if (($time - $find['push_time'] < 86400) && $find['push_time'] != 0) {
            return json(['code' => 1, 'msg' => '每天只能发布一次']);
        }

        $url = '/tools/Sourcematerial/preview?id='.$id;

        Db::name('source_material')->where('uid', $uid)->where('id', $id)->update([
            'push_time' => $time,
            'share_msg_id' => 1
        ]);

        return json(['code' => 0, 'msg' => 'ok', 'data' =>['url'=> $url, 'title' => $find['title']]]);

    }

    public function preview()
    {
        $id = (int)input('get.id');
        if (request()->isAjax()) {
            
            $find =  Db::name('source_material')->where('id', $id)->find();
            if (!$find || $find['share_msg_id'] == 0) return $this->error('未知错误');
    
            $relation = Db::name('source_material_relation')->field('id,media_info,media_type')->where('source_material_id', $find['id'])->select();

            return json(['code' => 0, 'data' => $relation, 'count' => count($relation)]);
        }
        $this->assign('id', $id);

        return $this->fetch();
    }

    public function addData()
    {
        $title = input('post.source_material_title');
        $relation = input('post.source_material_relation');
        $time = time();

        if (count($relation) > 20) {
            return json(['code' => 1, 'msg' => '素材多于20个']); 
        }

        $insertId = Db::name('source_material')->insertGetId([
            'title' => $title,
            'status' => 1,
            'uid' => getLoginUid(),
            'push_time' => 0,
            'create_time' => date('Y-m-d H:i:s', $time),
        ]);

        $relationData = [];
        $i = 0;
        foreach ($relation as $value) {
            $value = json_decode($value, true);
            $relationData[$i]['file_name'] = $value['file_name'];
            $relationData[$i]['file_size'] = $value['file_size'];
            $relationData[$i]['media_info'] = $value['media_info'];
            $relationData[$i]['media_type'] = $value['media_type'];
            $relationData[$i]['source_material_id'] = $insertId;
            $relationData[$i]['create_time'] = date('Y-m-d H:i:s', $time);
            $i++;
        }

        Db::name('source_material_relation')->insertAll($relationData);

        return json(['code' => 0, 'msg' => '创建成功']);

    }

	public function getList()
    {
        $get = input('get.');
        $page = $get['page'] ?? 1;
        $limit = $get['limit'] ?? 10;

        $title = $get['title'] ?? '';
        $where[] = ['uid', '=', getLoginUid()];
        $where[] = ['status', '=', 1];
        if ($title) {
            $where[] = ['title', 'like',  '%'.$title.'%'];
        }

        $list = Db::name('source_material')
            ->where($where)
            ->limit($limit)->page($page)
            ->order('id', 'desc')
            ->select();
        $count = Db::name('source_material')->where($where)->count();
        return json(['code' => 0, 'data' => $list, 'count' => $count]);
    }

    public function push()
    {
        $id = input('post.id');
        $uid = getLoginUid();
        $time = time();
        $find =  Db::name('source_material')->where('uid', $uid)->where('id', $id)->find();

        if (!$find) {
            return json(['code' => 1, 'msg' => '无权限']);
        }
        if (($time - $find['push_time'] < 86400) && $find['push_time'] != 0) {
            return json(['code' => 1, 'msg' => '每天只能推送一次']);
        }

        $frameStr = '<iframe src="/tools/Sourcematerial/preview?id='.$id.'" allowfullscreen="true" allowtransparency="true" width="100%" onload="changeFrameHeight(this)" frameborder="0" scrolling="auto"></iframe>';

        $result = \app\common\controller\Api::saveMessage($frameStr, '');

        Db::name('source_material')->where('uid', $uid)->where('id', $id)->update([
            'push_time' => $time,
            'share_msg_id' => $result['msg_id']
        ]);

        return json(['code' => 0, 'msg' => '推送成功，请在广场或首页中查看']);
    }

    public function del()
    {
        $id = input('post.id');
        $uid = getLoginUid();

        $find =  Db::name('source_material')->where('uid', $uid)->where('id', $id)->find();

        if (!$find) {
            return json(['code' => 1, 'msg' => '无权限']);
        }

        $messageId = $find['share_msg_id'];
        Db::name('message')->where('msg_id', $messageId)->update([
            'is_delete' => 1
        ]);
        Db::name('source_material')->where('uid', $uid)->where('id', $id)->update([
            'status' => 0
        ]);
        return json(['code' => 0, 'msg' => '删除成功']);
    }

}
