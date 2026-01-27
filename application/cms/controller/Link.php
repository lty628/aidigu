<?php

namespace app\cms\controller;

use app\cms\model\LinkCategory;
use app\cms\model\Link as LinkModel;

class Link extends Base
{
    // 友情链接首页
    public function link()
    {
        // 获取所有友情链接分类及其下的链接
        $categories = LinkCategory::with('links')->order('sort_order asc')->select();
        
        $this->assign('categories', $categories);
        return $this->fetch('link');
    }
}