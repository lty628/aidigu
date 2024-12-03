<?php
namespace app\common\controller;
use app\common\controller\Base;
use think\Db;
use app\common\model\Fans;
use app\common\model\Message;
use app\common\model\User;
use app\common\helper\Reminder;

class Api extends Base
{	
    public static $refrom = '网站';
	//首页
    public function index()
    {
        $contents = input('get.contents');
        $mediaInfo = input('get.mediaInfo');
        $data = self::saveMessage($contents, $mediaInfo);
    	if (!$data) return json(array('status' =>  0,'msg' => '发布失败'));
    	return json(array('status' =>  1,'msg' => '发表成功', 'data'=>$data));
    }
    public function repost()
    {
        $contents = input('get.contents');
        $mediaInfo = input('get.mediaInfo');
        $data = self::saveMessage($contents, $mediaInfo);
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
            Reminder::saveReminder($data['msg_id'], getLoginUid(), (int)input('get.uid'), 2);
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

        \app\chat\model\ChatFriends::editFriend($userid, $loginUid);
        
        return json(array('status' =>  1,'msg' => '已成功取消关注'));
    }
    public function addFollow()
    {
        $userid = (int)input('get.userid');
        $userInfo = User::getUserInfo($userid);

        $loginUid = getLoginUid();

        if (!$userInfo) return json(array('status' =>  0,'msg' => '没有这个用户'));

        \app\chat\model\ChatFriends::addFriend($userid, $loginUid);

        if ($userInfo['invisible'] && !Fans::isUserFans($userid, $loginUid)) {
            return json(array('status' =>  0,'msg' => '对方开启隐身模式，无法关注TA'));
        }

        //是否被跟随
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
            Db::name('message')->where('msg_id',$data['msg_id'])->setInc('commentsum',1);
            Db::name('comment')->insert($data);
            if (getLoginUid()!=(int)input('get.uid'))
                Reminder::saveReminder($data['msg_id'], getLoginUid(), (int)input('get.uid'), 1);
            // 提交事务
            Db::commit();
            return true;
        } catch (\Exception $e) {
            // 回滚事务
            Db::rollback();
            return false;
        }
    }
    public static function saveMessage($contents, $mediaInfo)
    {
        $contents = \app\common\libs\SmartVideo::parse($contents);
        if ($mediaInfo) {
            $mediaInfo = json_decode($mediaInfo, true);
            if ($mediaInfo['media_type'] == 'html') {
                $frameStr = '<iframe sandbox="allow-same-origin allow-scripts" src="'.$mediaInfo['media_info'].'" allowfullscreen="true" allowtransparency="true" width="100%" onload="changeFrameHeight(this)" frameborder="0" scrolling="auto"></iframe>';
                $contents = $contents . '<p>'.$frameStr.'</p>';
            } else {
                $info['media_info'] = $mediaInfo['media_info'];
                $info['media_type'] = $mediaInfo['media_type'];
                $data['media'] = $info['media_info'].'.'.$info['media_type'];
                $data['media_info'] = json_encode($info);
            }
            
        }
        if (!$contents && !$mediaInfo) return false;
    	$repost = input('get.repost');
        $data['repost'] = '';
    	if ($repost)  $data['repost'] = self::getMessage(strip_tags($repost, '<source><video><img><p><a>'));
        $data['uid'] = getLoginUid();
        $topic = self::getTopic($contents, $data['uid']);
        $data['topic_id'] = $topic['topic_id'];
        $data['contents'] = self::getMessage($topic['contents']);
        $data['refrom'] = self::$refrom;
        $data['ctime'] = time();
        Db::startTrans();
        try {
            $data['msg_id'] = Db::name('message')->insertGetId($data);
            $msgId = (int)input('get.msg_id');
            if ($msgId) {
                Db::name('message')->where('msg_id', $msgId)->setInc('repostsum',1);
            }
            if ($repost && getLoginUid()!=(int)input('get.fromuid')) {
                Reminder::saveReminder($msgId, getLoginUid(), (int)input('get.fromuid'), 0);
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

    protected static function getTopic($contents, $uid)
    {
        $topic = [
            'contents' => $contents,
            'topic_id' => 0
        ];

        if (!preg_match('/^\#.*\#/ui', strip_tags($contents), $matches)) {
            return $topic;
        }

        $title = $matches[0];

        $findTopic = Db::name('topic')->where('title', $title)->find();

        if (!$findTopic) {
            $topicData = [
                'title' => $title,
                'uid' => $uid,
                'create_time' => date('Y-m-d H:i:s'),
                'count' => 1
            ];
            $topicId = Db::name('topic')->insertGetId($topicData);
            $topic['topic_id'] = $topicId;
        } else {
            Db::name('topic')->where('topic_id', $findTopic['topic_id'])->setInc('count',1);
            $topic['topic_id'] = $findTopic['topic_id'];
        }

        $topic['contents'] = str_replace($title, '<a href="/topic/'.$topic['topic_id'].'/">'.$title.'</a>', $contents);
        
        return $topic;
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
	            	// return str_replace('@'.$matches[1], '<a href="javascript:;">@'.$matches[1].'</a>', $matches[0]);
                    return $matches[0];
	            }
	        },
	        $contents
	    );

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

    public function checkRemind()
    {
        $uid = getLoginUid();
        if (!$uid) {
            return json(array('status' =>  0,'msg' => ''));
        }
        $data = \app\common\libs\Remind::checkAll($uid);
        if (!$data['chatRemind'] && !$data['messageRemind']) {
            return json(array('status' =>  0,'msg' => ''));
        }
        return json(array('status' =>  1,'msg' => 'ok', 'data' => $data));
    }
}
