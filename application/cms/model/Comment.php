<?php

namespace app\cms\model;

class Comment extends BaseModel
{

    public function user()
	{
		return $this->hasOne('User','uid','uid')->bind('nickname,head_image');
	}

    public function pageList($where = array(), $field = '*', $order = '', $pageCount = 20)
    {
        return $this->with('user')->where($where)->field($field)->order($order)->paginate($pageCount, false, [
            'query' => request()->param(),
        ]);
    }

    /**
     * 自定义数据量查询
     */
    public function getLimitData($where = array(), $field = '*', $limit = 10, $order = '')
    {
        return $this->with('user')->where($where)->field($field)->order($order)->limit($limit)->select();
    }

}