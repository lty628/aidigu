<?php
namespace app\tools\controller;
use app\common\controller\Base;
use think\Db;
use app\common\helper\Reminder;

/**
 * 站内消息
 */
class Notice extends Base
{	
	public function list()
    {
        return $this->fetch();
    }

    /**
     * 获取提醒列表（新版本，使用Reminder类）
     */
    public function getListNew()
    {
        $uid = getLoginUid();
        $get = input('get.');
        $page = $get['page'] ?? 1;
        $limit = $get['limit'] ?? 20;
        $type = $get['type'] ?? null;

        // 使用新的Reminder类获取提醒列表
        $reminders = Reminder::getUserReminders($uid, $page, $limit, $type);
        $count = Db::name('reminder')->where('touid', $uid)->when($type, function($query, $type) {
            return $query->where('type', $type);
        })->count();

        foreach ($reminders as &$reminder) {
            // 解析extra字段
            $extra = json_decode($reminder['extra'], true);
            $reminder['extra'] = $extra ?: [];
            
            // 格式化时间
            $reminder['remind_time'] = date('Y-m-d H:i:s', $reminder['ctime']);
        }

        return json(['code' => 0, 'data' => $reminders, 'count' => $count]);
    }

    /**
     * 获取旧版提醒列表（兼容原有功能）
     */
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

    /**
     * 标记提醒为已读
     */
    public function markRead()
    {
        $id = (int)input('post.id');
        $uid = getLoginUid();
        
        if (!$id) {
            return json(['code' => 1, 'msg' => '参数错误']);
        }

        // 使用新的Reminder类
        $result = Reminder::markAsRead($id, $uid);

        if ($result) {
            return json(['code' => 0, 'msg' => '标记已读成功']);
        } else {
            return json(['code' => 1, 'msg' => '标记已读失败']);
        }
    }

    /**
     * 删除提醒
     */
    public function delete()
    {
        $id = (int)input('post.id');
        $uid = getLoginUid();
        
        if (!$id) {
            return json(['code' => 1, 'msg' => '参数错误']);
        }

        $result = Db::name('reminder')
            ->where('touid', $uid)
            ->where('id', $id)
            ->delete();

        if ($result) {
            return json(['code' => 0, 'msg' => '删除成功']);
        } else {
            return json(['code' => 1, 'msg' => '删除失败']);
        }
    }

    /**
     * 获取提醒数量
     */
    public function count()
    {
        $uid = getLoginUid();
        
        if (!$uid) {
            return json(['code' => 1, 'msg' => '未登录']);
        }

        $count = Reminder::getUnreadCount($uid);
        
        return json(['code' => 0, 'count' => $count]);
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
        $type = input('get.type', 'normal'); // 添加类型参数
        
        // 根据不同类型的提醒跳转到相应的查看页面
        switch($type) {
            case 'comment':
                // 评论类型，跳转到评论所在的消息页面
                $messageInfo = Db::name('message')->where('msg_id', $msgId)->find();
                if ($messageInfo) {
                    $userBlog = Db::name('user')->where('uid', $messageInfo['uid'])->value('blog');
                    if ($userBlog) {
                        $this->redirect("/{$userBlog}/message/{$msgId}");
                    }
                }
                break;
            case 'reply':
                // 回复类型，跳转到回复所在的消息页面
                $replyInfo = Db::name('reply')->where('rid', $msgId)->find();
                if ($replyInfo && $replyInfo['msg_id']) {
                    $messageInfo = Db::name('message')->where('msg_id', $replyInfo['msg_id'])->find();
                    if ($messageInfo) {
                        $userBlog = Db::name('user')->where('uid', $messageInfo['uid'])->value('blog');
                        if ($userBlog) {
                            $this->redirect("/{$userBlog}/message/{$replyInfo['msg_id']}");
                        }
                    }
                }
                break;
            case 'mention':
                // @提及类型
                $messageInfo = Db::name('message')->where('msg_id', $msgId)->find();
                if ($messageInfo) {
                    $userBlog = Db::name('user')->where('uid', $messageInfo['uid'])->value('blog');
                    if ($userBlog) {
                        $this->redirect("/{$userBlog}/message/{$msgId}");
                    }
                }
                break;
            default:
                // 默认跳转
                $info = Db::name('message')
                    ->alias('message')
                    ->leftJoin([getPrefix() . 'user' => 'user'], 'message.uid=user.uid')
                    ->field('user.blog, message.msg_id')
                    ->where('message.msg_id', $msgId)
                    ->find();
                    
                if ($info && $info['blog']) {
                    $this->redirect("/{$info['blog']}/message/{$msgId}");
                }
                break;
        }
        
        // 如果找不到对应信息，跳转到首页
        $this->redirect('/');
    }

}