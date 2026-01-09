<?php
namespace app\cms\controller;

use app\cms\model\Content as ContentModel;

class Content
{	
    // 发表文章
	public function commit(ContentModel $content)
    {
        $info = input('post.info');
        $result = $content->add($info);
        if (!$result) return ajaxJson(0, '添加失败'); 
        // $contentId = $content->id;
        return ajaxJson(1, '添加成功'); 
    }

    // 文章列表
    public function index(ContentModel $content)
    {
        $list = $content->pageList(10);
        dump($list);
    }
}
