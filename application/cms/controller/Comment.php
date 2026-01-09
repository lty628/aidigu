<?php
namespace app\cms\controller;

use app\cms\model\Comment as CommentModel;
use app\cms\model\Reply as ReplyModel;
use app\cms\model\Content;

class Comment
{	
    // 发表评论
	public function commit(CommentModel $comment, Content $content)
    {
        $info = input('post.info');
        if (time() - session('commit_expire_time') < 60) return ajaxJson(0, '评论间隔1分钟，请不要频繁评论'); 
        session('commit_expire_time', time());
        $info['uid'] = getUserIdd();
        $result = $comment->add($info);
        if (!$result) return ajaxJson(0, '评论失败'); 
        $content->where(['content_id' => $info['content_id']])->setInc('comment_num');
        // $commentId = $comment->id;
        return ajaxJson(1, '评论成功'); 
    }

    // 评论列表
    public function list(CommentModel $commentModel, ReplyModel $replyModel)
    {
        $contentId = input('get.content_id');
        // 前 10 条评论
        $comments = $commentModel->pageList(['content_id' => $contentId], '*', 'comment_id desc');
        // dump($comments);die;
        foreach ($comments as $key => $comment) {
            $comments[$key]['reply'] = $replyModel->getLimitData(['comment_id' => $comment['content_id']], '*', 2, 'reply_id desc');
        }
        return ajaxJson(1, 'ok', $comments);  
    }
}
