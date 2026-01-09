<?php

namespace app\cms\model;


class Reply extends BaseModel
{
    public function user()
	{
		return $this->hasOne('User','uid','uid')->bind('nickname,head_image');
	} 

    /**
     * 自定义数据量查询
     */
    public function getLimitData($where = array(), $field = '*', $limit = 10, $order = '')
    {
        return $this->with('User')->where($where)->field($field)->order($order)->limit($limit)->select();
    }

}