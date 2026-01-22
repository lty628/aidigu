<?php

namespace app\cms\model;

use think\Model;

class BaseModel extends Model
{
    protected $autoWriteTimestamp = 'datetime';

    /**
     * 获取列表信息
     */
    public function getList($where = array(), $field = '*', $limit = 1, $order = '')
    {
        return $this->where($where)->field($field)->limit($limit)->order($order)->select();
    }

    /**
     * 手动分页数据查询
     */
    public function getPageData($where = array(), $field = '*', $page = 1, $limit = 10, $order = '')
    {
        return $this->where($where)->field($field)->order($order)->page($page)->limit($limit)->select();
    }

    // 系统自带分页查询
    public function pageList($where = array(), $field = '*', $order = '', $pageCount = 16)
    {
        return $this->where($where)->field($field)->order($order)->paginate($pageCount, false, ['page' => request()->param('page/d', 1), 'path' => '[PAGE].html']);
    }

    /**
     * 获取单个信息
     */
    public function getOne($where = array(), $field = '*')
    {
        return $this->where($where)->field($field)->find();
    }

    /**
     * 数据写入
     */
    public function add($data)
    {
        return $this->allowField(true)->save($data);
    }

    /** 
     * 编辑数据
     */
    public function edit($where = array(), $data = array())
    {
        return $this->allowField(true)->where($where)->update($data);
    }

    /**
     * 删除数居
     */
    public function del($where)
    {
        return $this->where($where)->update(['is_delete' => 1]);
    }
}