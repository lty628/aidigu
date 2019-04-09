<?php
namespace app\index\controller;
use app\index\controller\Base;
use think\Db;
use app\index\model\Message;
use app\index\model\User;
use app\index\model\Fans;
use app\index\model\Comment;
use app\index\model\Reminder;
class Ajax extends Base
{	
	//首页
    public function index()
    {
        $data = $this->saveMessage();
    	if (!$data) return json(array('status' =>  0,'msg' => '发布失败'));
    	return json(array('status' =>  1,'msg' => '发表成功', 'data'=>$data));
    }
    public function repost()
    {
        $data = $this->saveMessage();
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
        $comment = new Comment();
        $comment->fromuid = getLoginUid();
        $comment->msg = strip_tags(input('get.comment'), '<p><a>');
        $comment->touid = (int)input('get.uid');
        $msgId = (int)input('get.commentId');
        $comment->msg_id = $msgId;
        if ($msgId) {
            if (getLoginUid()!=(int)input('get.uid'))
            Reminder::saveReminder($msgId, getLoginUid(), (int)input('get.uid'), 2);
        }
        if (!$comment->save()) return json(array('status' =>  0,'msg' => '回复失败'));
        return json(array('status' =>  1,'msg' => '回复成功'));
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
    protected function saveComment()
    {
    	$comment = new Comment();
    	$comment->fromuid = getLoginUid();
        $comment->msg = strip_tags(input('get.comment'), '<p><a>');
    	$comment->touid = (int)input('get.uid');
        $msgId = (int)input('get.commentId');
    	$comment->msg_id = $msgId;
        if ($msgId) {
            $message = new Message();
            $message->where('msg_id',$msgId)->setInc('commentsum',1);
            if (getLoginUid()!=(int)input('get.uid'))
                Reminder::saveReminder($msgId, getLoginUid(), (int)input('get.uid'), 1);
        }
    	return $comment->save();
    }
    protected function saveMessage()
    {
    	$contents = input('get.contents');
        if (!$contents) return false;
    	$repost = input('get.repost');
    	$message = new Message();
        $data['repost'] = '';
    	if ($repost)  $data['repost'] = $this->getMessage(strip_tags($repost, '<p><a>'));
        $data['contents'] = $this->getMessage(strip_tags($contents, '<p><a>'));
        $data['uid'] = getLoginUid();
        $data['refrom'] = '网站';
    	$result = $message->save($data);
        if (!$result) return false;
        $msgId = (int)input('get.msg_id');
        if ($msgId) {
            $message->where('msg_id', $msgId)->setInc('repostsum',1);
        }
        if ($repost) {
            if (getLoginUid()!=(int)input('get.fromuid'))
            Reminder::saveReminder($msgId, getLoginUid(), (int)input('get.fromuid'), 0);
        }
        $this->updateUserMessageSum();
        $data['msg_id'] = $message->id;
        return $data;
    }
    protected function getMessage($contents = null) 
    {
    	return preg_replace_callback(
	        // '/^@(\w*[0-9]*[\u4e00-\u9fa5]*)(:){0,1}(:.;)*$/',
	        // '/^@(\w*[0-9]*)(:){0,1}([:\.;])*$/ius',
	        '/@{1}(\w*[0-9]*[\x{4e00}-\x{9fa5}]*)([：:]){0,1}([：:\.;])*/ui',
	        function ($matches) {
	        	// dump($matches);die;
	            $findUser = User::where('nickname',$matches[1])->field('uid,blog')->find();
	            if ($findUser) {
	            	return str_replace('@'.$matches[1], '<a href="/'.$findUser['blog'].'/">@'.$matches[1].'</a>', $matches[0]);
	            } else {
	            	return str_replace('@'.$matches[1], '<a href="javascript:;">@'.$matches[1].'</a>', $matches[0]);
	            }
	        },
	        $contents
	    );

    }
    public function updateUserMessageSum()
    {
        User::where('uid', getLoginUid())->setInc('message_sum', 1);
    }
    public function delMessage()
    {
        $result = Message::delMessageById((int)input('param.msg_id'), getLoginUid());
        if (!$result) 
            return $this->error('删除失败');
        return $this->success('删除成功', '/'.getLoginBlog().'/');
    }
}
