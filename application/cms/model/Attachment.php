<?php

namespace app\cms\model;

class Attachment extends BaseModel
{
    public function user()
	{
		return $this->hasOne('User','uid','uid')->bind('uid,nickname,head_image');
	}

    public function category()
	{
		return $this->hasOne('Category','category_id','category_id')->bind('category_name');
	}

	public function getAttachSizeAttr($value)
	{
		return getFilesize($value);
	}
}