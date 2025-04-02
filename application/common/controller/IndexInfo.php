<?php

namespace app\common\controller;

use app\common\controller\Info;
use think\Db;
// use think\facade\Session;
use app\common\model\Message;
// use app\common\model\User;
use app\common\model\Fans;

class IndexInfo extends Info
{
    // 首页
    public function index()
    {
        // 修改自定义首页
        $defaultIndex = env('app.defaultIndex');
        if ($defaultIndex && $defaultIndex != '/' && !isMobile() && $this->userid) {
            $defaultIndex = explode('@', $defaultIndex);
            return app($defaultIndex[0])->{$defaultIndex[1]}();
        }

        if ($this->userid) {
            $allowDelete = 0;
        } else {
            $allowDelete = -1;
        }
        if (request()->isAjax()) {
            $userMessage = $this->getMessage('', 8);
            $userMessage = $userMessage->toArray()['data'];
            return json(array('status' =>  1, 'msg' => 'ok', 'data' => ['data' => handleMessage($userMessage), 'allow_delete' => $allowDelete]));
        }

        $this->assign('siteUser', 0);
        $this->assign('userMessage', []);
        return $this->fetch('index');
    }
    // 个人首页
    public function home()
    {
        // 我的主页
        if ($this->isSiteUser) {
            $userMessage = Db::name('message')
                ->alias('message')
                ->join([$this->prefix . 'fans' => 'fans'], 'message.uid=fans.touid and fans.fromuid=' . $this->siteUserId)
                ->join([$this->prefix . 'user' => 'user'], 'user.uid=fans.touid')
                ->order('message.ctime desc')
                ->field('user.uid,user.nickname,user.head_image,user.blog,message.ctime,message.contents,message.repost,message.refrom,message.repostsum,message.media,message.media_info,message.commentsum,message.msg_id')
                ->where('message.is_delete', 0)
                ->paginate(8, false, ['page' => request()->param('page/d', 1), 'path' => '[PAGE].html']);
        } else {
            $userMessage = Db::name('message')
                ->alias('message')
                ->join([$this->prefix . 'fans' => 'fans'], 'message.uid=fans.touid and fans.fromuid=' . $this->siteUserId)
                ->join([$this->prefix . 'user' => 'user'], 'user.uid=fans.touid')
                ->order('message.ctime desc')
                ->field('user.uid,user.nickname,user.head_image,user.blog,message.ctime,message.contents,message.repost,message.refrom,message.repostsum,message.media,message.media_info,message.commentsum,message.msg_id')
                ->where('message.is_delete', 0)
                ->where(function ($query) {
                    $query->where('user.invisible', 0)->whereOr('user.uid', $this->siteUserId);
                })
                ->paginate(8, false, ['page' => request()->param('page/d', 1), 'path' => '[PAGE].html']);
        }
        
        $this->assign('userMessage', []);
        if (request()->isAjax()) {
            $userMessage = $userMessage->toArray()['data'];
            return json(array('status' =>  1, 'msg' => 'ok', 'data' => ['data' => handleMessage($userMessage), 'allow_delete' => 0]));
        }
        return $this->fetch('index');
    }
    public function own()
    {
        if (request()->isAjax()) {
            $userMessage = $this->getMessage($this->siteUserId, 8);
            $allwoDelete = 1;
            if ($this->siteUserId != $this->userid) $allwoDelete = 0;
            $userMessage = $userMessage->toArray()['data'];
            return json(array('status' =>  1, 'msg' => 'ok', 'data' => ['data' => handleMessage($userMessage), 'allow_delete' => $allwoDelete]));
        }
        $this->assign('siteUser', $this->siteUserId);
        $this->assign('userMessage', []);
        return $this->fetch('index');
    }
    // 广场
    public function blog()
    {
        $this->assign('siteUser', $this->siteUserId);
        if (request()->isAjax()) {
            $userMessage = $this->getMessage('', 8);
            $userMessage = $userMessage->toArray()['data'];
            return json(array('status' =>  1, 'msg' => 'ok', 'data' => ['data' => handleMessage($userMessage), 'allow_delete' => 0]));
        }
        $this->assign('userMessage', []);
        return $this->fetch('index');
    }

    // 话题
    public function topic()
    {
        $topicId = input('topic_id');
        $this->assign('siteUser', $this->siteUserId);
        if (request()->isAjax()) {
            $userMessage = $this->getMessage('', 20, $topicId);
            $userMessage = $userMessage->toArray()['data'];
            return json(array('status' =>  1, 'msg' => 'ok', 'data' => ['data' => handleMessage($userMessage), 'allow_delete' => 0]));
        }
        Db::name('topic')->where('topic_id', $topicId)->setInc('count',1);
        $topic = Db::name('topic')->where('topic_id', $topicId)->find();
        $this->assign('topicTitle', $topic['title']);
        $this->assign('userMessage', []);
        return $this->fetch('index');
    }

    // 话题
    public function topicList()
    {
        $topic = Db::name('topic')->order('count desc')->order('topic_id desc')->paginate(30, false, ['page' => request()->param('page/d', 1), 'path' => '[PAGE].html']);
        $this->assign('topicArr', $topic);
        return $this->fetch('topic');
    }

    // 我的话题
    public function myTopicList()
    {
        $topic = Db::name('topic')->where('uid', $this->userid)->order('count desc')->order('topic_id desc')->paginate(30, false, ['page' => request()->param('page/d', 1), 'path' => '[PAGE].html']);
        $this->assign('topicArr', $topic);
        return $this->fetch('topic');
    }

    public function fans()
    {
        $userFans = $this->getMyFans($this->siteUserId, $this->userid, 20);
        $this->assign('userFans', $userFans);
        return $this->fetch('fansInfo');
    }
    public function concern()
    {
        $userFans = $this->getMyConcern($this->siteUserId, $this->userid, 20);
        $this->assign('userFans', $userFans);
        return $this->fetch('fansInfo');
    }
    public function messageInfo()
    {
        $this->getMessageById((int)input('param.msg_id'));
        $this->assign('siteUser', $this->siteUserId);
        return $this->fetch('message');
    }
    public function setting()
    {
        return $this->fetch('setting_info');
    }
    public function avatar()
    {
        return $this->fetch('setting_avatar');
    }
    public function background()
    {
        return $this->fetch('setting_background');
    }
    public function passwd()
    {
        return $this->fetch('setting_passwd');
    }
    public function newrepeat()
    {
        $this->getReminderMsg();
        return $this->fetch();
    }
    public function newreply()
    {
        $this->getReminderMsg();
        return $this->fetch();
    }
    public function newcomment()
    {
        $this->getReminderMsg();
        return $this->fetch();
    }

    public function tools()
    {
        $appType = input('param.appType');
        if ($appType == 1) {
            $where = ['app_status' => 1];
            $cacheKey = 'app_status_1';
        } elseif ($appType == 2) {
            $where = ['app_status' => 2];
            $cacheKey = 'app_status_2';
        } else {
            $where = [['app_status', '>', 0]];
            $cacheKey = 'app_status_all';
        }

        $list = Db::name('app')->where('fromuid', 0)->where($where)->cache($cacheKey, 120)->select();
        $this->assign('list', $list);
        return $this->fetch();
    }

    public function search()
    {
        $keyword = input('keyword');

        $keyword = base64_decode($keyword);
        // 移除html标记
        $keyword = strip_tags($keyword);
        // 输入过滤
        $keyword = htmlspecialchars($keyword);
        // 移除空格
        $keyword = trim($keyword);
        $keyword = str_replace('&amp;', '&', $keyword);
        parse_str($keyword, $keywordArr);
        // 移除空
        $keywordArr = array_filter($keywordArr);

        $userMessage = [];
        if (request()->isAjax()) {
            
            if (!$keywordArr) return json(array('status' =>  1,'msg' => 'ok', 'data' => ['data' => [], 'allow_delete' => 0]));

            $findUserSearch = Db::name('search')->where('keyword', $keyword)->cache(60)->find();
            if ($findUserSearch && $findUserSearch['uid'] == $this->userid) {
                if (time() - strtotime($findUserSearch['create_time']) >= 120) {
                    Db::name('search')->where('search_id', $findUserSearch['search_id'])->setInc('count',1);
                }
            } else {
                $keywordData = [
                    'keyword' => $keyword,
                    'uid' => $this->userid,
                    'create_time' => date('Y-m-d H:i:s'),
                    'count' => 1
                ];
    
                Db::name('search')->insert($keywordData);
            }

            $page = request()->param('page/d', 1);

            $where = [];
            if (isset($keywordArr['searchContent'])) {
                $where[] = ['message.contents', 'like', '%'. $keywordArr['searchContent'] .'%'];
            }
            if (isset($keywordArr['userNickname'])) {
                $user = Db::name('user')->where('blog', $keywordArr['userNickname'])->whereOr('nickname', $keywordArr['userNickname'])->cache(600)->field('uid')->find();
                if ($user) {
                    $where[] = ['message.uid', '=', $user['uid']];
                } else {
                    return json(array('status' =>  1,'msg' => 'ok', 'data' => ['data' => [], 'allow_delete' => 0]));
                }
            }

            if (isset($keywordArr['startDate'])) {
                $where[] = ['message.ctime', '>=', strtotime($keywordArr['startDate'])];
            }

            if (isset($keywordArr['endDate'])) {
                $where[] = ['message.ctime', '<=', strtotime($keywordArr['endDate'])];
            }

            $userMessage = cache('search_'.$keyword.'_'.$page);
            $userMessage = '';
            if (!$userMessage) {
                $userMessage = Db::name('message')
                ->alias('message')
                ->join([$this->prefix . 'user' => 'user'], 'user.uid=message.uid')
                ->order('message.ctime asc')
                ->field('user.uid,user.nickname,user.head_image,user.blog,message.ctime,message.contents,message.repost,message.refrom,message.repostsum,message.media,message.media_info,message.commentsum,message.msg_id')
                ->where('message.is_delete', 0)
                // ->where('message.contents', 'like', '%'.$keyword.'%')
                ->where($where)
                ->where(function ($query) {
                    $query->where('user.invisible', 0)->whereOr('user.uid', $this->siteUserId);
                })
                ->paginate(8, false, ['page' => $page, 'path' => '[PAGE].html']);
                $userMessage = $userMessage->toArray()['data'];
                cache('search_'.$keyword.'_'.$page, $userMessage, 3600);
            }

            return json(array('status' =>  1, 'msg' => 'ok', 'data' => ['data' => handleMessage($userMessage), 'allow_delete' => 0]));
        }
        
        $this->assign('userMessage', []);
        $this->assign('keywordArr', $keywordArr);
        return $this->fetch();
    }

    public function info()
    {
        return $this->fetch();
    }

    // 我的收藏
    public function collect()
    {
        if (request()->isAjax()) {
            $page = input('get.page');
            $collect = Db::name('user_collect')->where('delete_time', 0)->where('fromuid', $this->siteUserId)->order('collect_id desc')->limit(20)->page($page)->select();
            $msgIdArr = array_column($collect, 'msg_id');
            $userMessage = $this->getMessageIdArr($msgIdArr);
            // $userMessage = $userMessage->toArray()['data'];
            $tmp = [];
            foreach ($userMessage as $value) {
                $tmp[$value['msg_id']] = $value;
            }

            $data = [];
            foreach ($msgIdArr as $v) {
                if (isset($tmp[$v])){
                    $data[$v] = $tmp[$v];
                }
            }

            return json(array('status' =>  1, 'msg' => 'ok', 'data' => ['data' => handleMessage(array_values($data)), 'allow_delete' => 0, 'is_collect' => 1]));
        }
        $this->assign('siteUser', $this->siteUserId);
        $this->assign('userMessage', []);
        return $this->fetch('index');
    }

    public function collectMsg()
    {
        $msgId = input('post.msgId');
        $isCancel = input('post.isCancel');
        $time = time();
        $data['delete_time'] = 0;
        $data['fromuid'] = $this->userid;
        $data['msg_id'] = $msgId;
        $data['collect_time'] = date('Y-m-d H:i:s', $time);
        
        if ($isCancel) {
            $data['collect_time'] = date('Y-m-d H:i:s', $time);
            Db::name('user_collect')->where('fromuid', $this->userid)->where('msg_id', $msgId)->update([
                'delete_time' => $time
            ]);
            Db::name('message')->where('msg_id', $msgId)->setDec('collectsum');
            $msg = '已取消收藏';
        } else {
            $findInfo = Db::name('user_collect')->where('fromuid', $this->userid)->where('msg_id', $msgId)->find();
            if ($findInfo && $findInfo['delete_time'] == 0) {
                $msg = '已经收藏过了';
            } elseif ($findInfo && $findInfo['delete_time'] != 0) {
                Db::name('user_collect')->where('fromuid', $this->userid)->where('msg_id', $msgId)->update([
                    'delete_time' => 0
                ]);
                Db::name('message')->where('msg_id', $msgId)->setInc('collectsum');
                $msg = '收藏成功';
            } else {
                Db::name('user_collect')->insert($data);
                Db::name('message')->where('msg_id', $msgId)->setInc('collectsum');
                $msg = '收藏成功';
            }
        }
        
        return json(array('status' =>  1, 'msg' => $msg));
        
    }

}
