<?php
namespace app\cms\controller;

use app\cms\model\Content;

class Commit
{	
    // 发表文章
	public function commit(Content $content)
    {
        $info = input('post.info');
        // 此处有 bug 待完善 unfinish
        $info['uid'] = getUserIdd();
        $result = $content->add($info);
        if (!$result) return ajaxJson(0, '添加失败'); 
        // $contentId = $content->id;
        return ajaxJson(1, '添加成功'); 
    }

    public function commitEdit(Content $content)
    {
        $info = input('post.info');
        // 此处有 bug 待完善 unfinish
        $where['uid'] = getUserIdd();
        $result = $content->edit($where, $info);
        if (!$result) return ajaxJson(0, '编辑失败'); 
        // $contentId = $content->id;
        return ajaxJson(1, '编辑成功'); 
    }

    public function addLike(Content $content)
    {
        $contentId = input('post.content_id');
        if (!$contentId) return ajaxJson(0, '参数不正确！'); 
        if (!getUserIdd()) return ajaxJson(0, '点赞失败，请先登录！'); 
        $info = $content->getOne(['content_id' => $contentId]);
        if (!$info)  return ajaxJson(0, '参数不正确'); 
        $num = $info['like_num'] + 1;
        $result = $content->edit(['content_id' => $contentId], ['like_num' => $num]);
        if (!$result) return ajaxJson(0, '点赞失败，请稍后重试'); 
        return ajaxJson(1, '点赞成功', $num); 
    }

}
