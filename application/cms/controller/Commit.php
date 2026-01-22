<?php
namespace app\cms\controller;

use app\cms\model\Content;

class Commit
{	
    // 发表文章
	public function commit(Content $content)
    {
        $info = input('post.info');
        $article_media = input('post.article_media');
        // 此处有 bug 待完善 unfinish
        $info['uid'] = getUserId();
        $info['content_extra'] = '';
        if ($article_media) {
            $article_media_info = pathinfo($article_media);
            $article_media_type = $article_media_info['extension'];
            $info['content_extra'] = json_encode(['article_media' => $article_media, 'article_media_type' => $article_media_type]);
        }
        $result = $content->add($info);
        if (!$result) return ajaxJson(0, '添加失败'); 
        // $contentId = $content->id;
        return ajaxJson(1, '添加成功'); 
    }

    public function commitEdit(Content $content)
    {
        $info = input('post.info');
        $article_media = input('post.article_media');
        $contentId = $info['content_id'];
        if (!$contentId) return ajaxJson(0, '参数不正确！'); 
        $find = $content->getOne(['content_id' => $contentId]);
        if (!$find) return ajaxJson(0, '参数不正确！'); 
        $info['uid'] = getUserId();
        if ($find['uid'] != $info['uid']) return ajaxJson(0, '您没有权限编辑！'); 
        $info['content_extra'] = '';
        dump($article_media);
        die;
        if ($article_media) {
            $article_media_info = pathinfo($article_media);
            $article_media_type = $article_media_info['extension'];
            $info['content_extra'] = json_encode(['article_media' => $article_media, 'article_media_type' => $article_media_type]);
        }
        
        // 此处有 bug 待完善 unfinish
        $where['uid'] = $info['uid'];
        $result = $content->edit($where, $info);
        if (!$result) return ajaxJson(0, '编辑失败'); 
        // $contentId = $content->id;
        return ajaxJson(1, '编辑成功'); 
    }

    public function addLike(Content $content)
    {
        $contentId = input('post.content_id');
        if (!$contentId) return ajaxJson(0, '参数不正确！'); 
        if (!getUserId()) return ajaxJson(0, '点赞失败，请先登录！'); 
        $info = $content->getOne(['content_id' => $contentId]);
        if (!$info)  return ajaxJson(0, '参数不正确'); 
        $num = $info['like_num'] + 1;
        $result = $content->edit(['content_id' => $contentId], ['like_num' => $num]);
        if (!$result) return ajaxJson(0, '点赞失败，请稍后重试'); 
        return ajaxJson(1, '点赞成功', $num); 
    }

}
