<?php

namespace app\cms\model;

class Link extends BaseModel
{
    protected $name = 'link';
    protected $autoWriteTimestamp = 'datetime';

    // 关联友情链接
    public function links()
    {
        return $this->hasMany('Link', 'link_category_id', 'link_category_id')->order('sort_order asc');
    }
    // 关联分类
    public function category()
    {
        return $this->hasOne('LinkCategory', 'link_category_id', 'link_category_id')->bind('category_name');
    }
}