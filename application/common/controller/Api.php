<?php
namespace app\common\controller;
use app\common\controller\Base;
use think\Db;
use app\common\model\Fans;
use app\common\model\Message;
class Api extends Base
{	
	//首页
    public function index()
    {
        $contents = input('get.contents');
        $imageInfo = input('get.imageInfo');
        $data = self::saveMessage($contents, $imageInfo);
    	if (!$data) return json(array('status' =>  0,'msg' => '发布失败'));
    	return json(array('status' =>  1,'msg' => '发表成功', 'data'=>$data));
    }
    public function repost()
    {
        $contents = input('get.contents');
        $imageInfo = input('get.imageInfo');
        $data = self::saveMessage($contents, $imageInfo);
    	if (!$data) return json(array('status' =>  0,'msg' => '转发失败'));
    	return json(array('status' =>  1,'msg' => '转发成功', 'data'=>$data));
    }
    public function comment()
    {
    	if (!$this->saveComment()) return json(array('status' =>  0,'msg' => '评论失败'));
        //评论成功
        // Message::save
    	return json(array('status' =>  1,'msg' => '评论成功'));
    }
    public function reply()
    {
        $data['fromuid'] = getLoginUid();
        $data['msg'] = strip_tags(input('get.comment'), '<img><p><a>');
        $data['touid'] = (int)input('get.uid');
        $data['msg_id'] = (int)input('get.commentId');
        $data['ctime'] = time();
        Db::startTrans();
        try {
            Db::name('comment')->insert($data);
            if (getLoginUid()!=(int)input('get.uid'))
            self::saveReminder($data['msg_id'], getLoginUid(), (int)input('get.uid'), 2);
            // 提交事务
            Db::commit();
            return json(array('status' =>  1,'msg' => '回复成功'));
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return json(array('status' =>  0,'msg' => '回复失败'));
        }
    }
    //fans 粉丝关注
    public function unfollow()
    {
        $userid = (int)input('get.userid');
        $loginUid = getLoginUid();
        $result = Fans::unfollow($userid, $loginUid);
        if (!$result) return json(array('status' =>  0,'msg' => '取消关注失败'));
        return json(array('status' =>  1,'msg' => '已成功取消关注'));
    }
    public function addFollow()
    {
        $userid = (int)input('get.userid');
        //是否被跟随
        $loginUid = getLoginUid();
        $result = Fans::addFollow($userid, $loginUid);
        if ($loginUid == $userid ) return json(array('status' =>  0,'msg' => '您不能关注自己')); 
        if ($result == '-1') return json(array('status' =>  0,'msg' => '您已关注，不能重复关注'));
        if (!$result) return json(array('status' =>  0,'msg' => '关注失败')); 
        return json(array('status' =>  1,'msg' => '成功关注TA'));
    }
    protected static function saveComment()
    {
    	$data['fromuid'] = getLoginUid();
        $data['msg'] = strip_tags(input('get.comment'), '<img><p><a>');
    	$data['touid'] = (int)input('get.uid');
        $data['msg_id'] = (int)input('get.commentId');
        $data['ctime'] = time();
        Db::startTrans();
        try {
            Db::name('message')->where('msg_id',$data['msg_id'])->setInc('commentsum',1);\
            Db::name('comment')->insert($data);
            if (getLoginUid()!=(int)input('get.uid'))
                self::saveReminder($data['msg_id'], getLoginUid(), (int)input('get.uid'), 1);
            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
    }
    public static function saveMessage($contents, $imageInfo)
    {
        if ($imageInfo) {
            $imageInfo = json_decode($imageInfo, true);
            $info['image_info'] = $imageInfo['image_info'];
            $info['image_type'] = $imageInfo['image_type'];
            $data['image'] = $info['image_info'].'.'.$info['image_type'];
            $data['image_info'] = json_encode($info);
        }
        if (!$contents) return false;
    	$repost = input('get.repost');
        $data['repost'] = '';
    	if ($repost)  $data['repost'] = self::getMessage(strip_tags($repost, '<source><video><img><p><a>'));
        $data['contents'] = self::getMessage($contents);
        $data['uid'] = getLoginUid();
        $data['refrom'] = '网站';
        $data['ctime'] = time();
        Db::startTrans();
        try {
            $data['msg_id'] = Db::name('message')->insertGetId($data);
            $msgId = (int)input('get.msg_id');
            if ($msgId) {
                Db::name('message')->where('msg_id', $msgId)->setInc('repostsum',1);
            }
            if ($repost && getLoginUid()!=(int)input('get.fromuid')) {
                self::saveReminder($msgId, getLoginUid(), (int)input('get.fromuid'), 0);
            }
            self::updateUserMessageSum();
            // 提交事务
            Db::commit();
            return $data;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
    }
    protected static function getMessage($contents = null) 
    {
    	return preg_replace_callback(
	        // '/^@(\w*[0-9]*[\u4e00-\u9fa5]*)(:){0,1}(:.;)*$/',
	        // '/^@(\w*[0-9]*)(:){0,1}([:\.;])*$/ius',
	        '/@{1}(\w*[\.0-9]*[\x{4e00}-\x{9fa5}]*)([：:]){0,1}([：:\.;])*/ui',
	        function ($matches) {
	        	// dump($matches);die;
	            $findUser = Db::name('user')->where('nickname',$matches[1])->field('uid,blog')->find();
	            if ($findUser) {
	            	return str_replace('@'.$matches[1], '<a href="/'.$findUser['blog'].'/">@'.$matches[1].'</a>', $matches[0]);
	            } else {
	            	return str_replace('@'.$matches[1], '<a href="javascript:;">@'.$matches[1].'</a>', $matches[0]);
	            }
	        },
	        $contents
	    );

    }
    // $type 0 转发 1 评论 2 回复
    protected static function saveReminder($msgId, $fromuid, $touid, $type)
    {
        $ctime = time();
        return Db::name('reminder')->insert(['msg_id'=>$msgId, 'fromuid'=>$fromuid, 'touid'=>$touid, 'type'=>$type, 'ctime'=>$ctime]);
    }
    public static function updateUserMessageSum()
    {
        return Db::name('user')->where('uid', getLoginUid())->setInc('message_sum', 1);
    }
    public function delMessage()
    {
        $result = Message::delMessageById((int)input('param.msg_id'), getLoginUid());
        if (!$result) 
            return $this->error('删除失败');
        return $this->success('删除成功', '/'.getLoginBlog().'/own/');
    }
}
