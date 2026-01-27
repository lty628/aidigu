<?php

namespace app\cms\model;

class LinkCategory extends BaseModel
{
    protected $name = 'link_category';

    // 关联友情链接
    public function links()
    {
        return $this->hasMany('Link', 'link_category_id', 'link_category_id')->order('sort_order asc');
    }
}