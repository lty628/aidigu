<?php
namespace app\tools\controller;
use think\Controller;
use think\Db;


class Sourcematerial extends Controller
{	
    public function initialize()
    {
        if (!getLoginUid()) {
            $pubIndex = env('app.pubIndex', '');
            if (!$pubIndex || isMobile()) {
                // 无逻辑处理
                $this->error('未登录', '/login/');
            }
        }
        
    }

    public function list()
    {
        $this->assign('isMobile', isMobile());
        return $this->fetch();
    }

    public function add()
    {
        return $this->fetch();
    }

    public function share()
    {
        $this->assign('isMobile', isMobile());
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
        $staticDomain = env('app.staticDomain', '');

        // if (request()->isAjax()) {
            
        //     $find =  Db::name('source_material')->where('id', $id)->find();
        //     if (!$find) return $this->error('未知错误');
        //     if ($find['uid'] != getLoginUid() && $find['share_msg_id'] == 0) {
        //         $this->error('未知错误');
        //     }
    
        //     $relation = Db::name('source_material_relation')->field('id,media_info,media_type,file_name')->where('source_material_id', $find['id'])->select();

        //     return json(['code' => 0, 'data' => $relation, 'count' => count($relation)]);
        // }

        $find =  Db::name('source_material')->where('id', $id)->find();
        if (!$find) {
            $relation = [];
        } elseif ($find['uid'] != getLoginUid() && $find['share_msg_id'] == 0) {
            $relation = [];
        } else {
            $relation = Db::name('source_material_relation')->field('id,media_info,media_type,file_name')->where('source_material_id', $find['id'])->select();
        }

        $imgArray = ['jiff', 'jpg', 'bmp', 'jpeg', 'png', 'gif'];
        $videoArray = ['mp4'];
        // $otherArray = ['zip', 'rar', '7z', 'pdf'];
        $textArray = ['txt'];
        $data = [];
        $data['img'] = [];
        $data['video'] = [];
        $data['other'] = [];
        $data['text'] = [];
        $imgIndex = 0;
        $videoIndex = 0;
        $otherIndex = 0;
        $textIndex = 0;
        foreach ($relation as $value) {
            $mediaUrl = $staticDomain . $value['media_info'] . '.' . $value['media_type'];
            $value['mediaUrl'] = $mediaUrl;
        	if (in_array($value['media_type'], $imgArray)) {
        		// $data['imgStr'] .= '<li><a href="javascript:;"><img data-index="'.$imgIndex.'" onclick="showMessageImg(this)" src="'.$mediaUrl.'"></li>';
                $data['img'][$imgIndex] = $value;
                $imgIndex++;
        	} elseif (in_array($value['media_type'], $videoArray)) {
        		// $data['videoStr'].= '<li class="flow-div-video showVideo'.$value['id'].'"><video onclick="showVideoPopup(this)" src="'.$mediaUrl.'" controls="" name="media"><source src="'.$mediaUrl.'" type="video/mp4"></video></li>';
                $data['video'][$videoIndex] = $value;
                $videoIndex++;
        	} elseif (in_array($value['media_type'], $textArray)) {
        		// $data['textStr'].= '<li class="flow-div-other showOther"><span><a href="javascript:;" data-title="'.$value['file_name'].'" data-url="/tools/reader?material_relation_id='.$value['id'].'" onclick="showTextPopup(this)">点击阅读 '.$value['file_name'].'</a></span></li>';
                $data['text'][$textIndex] = $value;
                $textIndex++;
        	} else {
                // $data['otherStr'].= '<li class="flow-div-other showOther"><span><a href="'.$mediaUrl.'">点击下载 '.$value['file_name'].'</a></span></li>';
                $data['other'][$otherIndex] = $value;
                $otherIndex++;
            }
        }

        $this->assign('isMobile', isMobile());
        $this->assign('data', $data);
        $this->assign('id', $id);
        // $this->assign('staticDomain', $staticDomain);

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

        // $frameStr = '<iframe sandbox="allow-same-origin allow-scripts allow-popups" id="iframe-sourcematerial-'.$id.'" src="/tools/Sourcematerial/preview?id='.$id.'" allowfullscreen="true" allowtransparency="true" width="100%" onload="changeFrameHeight(this)" frameborder="0" scrolling="auto"></iframe>';
        $frameStr = '<iframe sandbox="allow-same-origin allow-scripts allow-popups" id="iframe-sourcematerial-'.$id.'" src="/tools/Sourcematerial/preview?id='.$id.'" allowfullscreen="true" allowtransparency="true" width="100%" frameborder="0" scrolling="auto"></iframe>';

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
