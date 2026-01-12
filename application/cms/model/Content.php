<?php

namespace app\cms\model;

class Content extends BaseModel
{
    // 关联用户表
    public function user()
	{
		return $this->hasOne('User','uid','uid')->bind('uid,nickname,head_image');
	}

    public function category()
	{
		return $this->hasOne('Category','category_id','category_id')->bind('category_name');
	}

	public function getContentExtraAttr($value, $content)
    {
		return json_decode($content['content_extra'], true);
    }
}