<?php
namespace app\admin\controller;
use think\Db;
use app\common\controller\Base;

class Cms extends Base
{
    // ======================= 内容分类管理 =======================
    
    // 内容分类列表页面
    public function contentCategoryIndex()
    {
        return $this->fetch('content_category_index');
    }

    // 获取内容分类列表
    public function getContentCategoryList()
    {
        $keyword = input('post.keyword', '');
        $page = input('post.page', 1, 'intval');
        $limit = input('post.limit', 10, 'intval');
        
        $query = Db::name('cms_category')->order('category_id', 'asc');
        
        // 关键词搜索
        if ($keyword) {
            $query->where('category_name', 'like', '%' . $keyword . '%');
        }
        
        $total = $query->count();
        $list = $query->page($page, $limit)->select();
        
        return json(['code' => 0, 'msg' => 'success', 'count' => $total, 'data' => $list]);
    }

    // 编辑内容分类页面
    public function contentCategoryEdit()
    {
        $categoryId = input('get.category_id');
        $data = Db::name('cms_category')->where('category_id', $categoryId)->find();
        $this->assign('data', $data);
        return $this->fetch('content_category_edit');
    }

    // 添加内容分类页面
    public function contentCategoryAdd()
    {
        return $this->fetch('content_category_add');
    }

    // 添加或编辑内容分类
    public function contentCategoryAddOrEdit()
    {
        $post = input('post.');
        
        if (isset($post['category_id'])) {
            // 编辑分类
            $result = Db::name('cms_category')->where('category_id', $post['category_id'])->update($post);
            if (!$result) return json(['code' => 1, 'msg' => '编辑失败']);
            return json(['code' => 0, 'msg' => '编辑成功']);
        } else {
            // 新增分类
            $categoryId = Db::name('cms_category')->insertGetId($post);
            if (!$categoryId) return json(['code' => 1, 'msg' => '新增失败']);
            return json(['code' => 0, 'msg' => '新增成功']);
        }
    }

    // 删除内容分类
    public function contentCategoryDelete()
    {
        $categoryId = input('post.category_id');
        
        // 验证分类是否存在
        $category = Db::name('cms_category')->where('category_id', $categoryId)->find();
        if (!$category) {
            return json(['code' => 1, 'msg' => '分类不存在']);
        }
        
        // 检查是否有内容使用该分类
        $contentCount = Db::name('cms_content')->where('category_id', $categoryId)->count();
        if ($contentCount > 0) {
            return json(['code' => 1, 'msg' => '该分类下有内容，无法删除']);
        }
        
        // 删除分类
        Db::name('cms_category')->where('category_id', $categoryId)->delete();
        
        return json(['code' => 0, 'msg' => '删除成功']);
    }

    // ======================= 内容管理 =======================
    
    // 内容列表页面
    public function contentIndex()
    {
        // 获取所有分类用于筛选
        $categories = Db::name('cms_category')->order('category_id', 'asc')->select();
        $this->assign('categories', $categories);
        return $this->fetch('content_index');
    }

    // 获取内容列表
    public function getContentList()
    {
        $keyword = input('get.keyword', '');
        $categoryId = input('get.category_id', 0, 'intval');
        $status = input('get.status', -1, 'intval');
        $page = input('get.page', 1, 'intval');
        $limit = input('get.limit', 10, 'intval');
        
        $query = Db::name('cms_content')
            ->alias('content')
            ->join('cms_category category', 'content.category_id = category.category_id')
            ->join('user user', 'content.uid = user.uid')
            ->field('content.*, category.category_name, user.nickname')
            ->order('content.content_id', 'desc');
        
        // 关键词搜索
        if ($keyword) {
            $query->where('content.title', 'like', '%' . $keyword . '%');
        }

        // 状态筛选
        if ($status >= 0) {
            $query->where('content.status', $status);
        }
        
        // 分类筛选
        if ($categoryId > 0) {
            $query->where('content.category_id', $categoryId);
        }
        
        $total = $query->count();
        $list = $query->page($page, $limit)->select();
        
        return json(['code' => 0, 'msg' => 'success', 'count' => $total, 'data' => $list]);
    }

    // 编辑内容页面
    public function contentEdit()
    {
        $contentId = input('get.content_id');
        $data = Db::name('cms_content')->where('content_id', $contentId)->find();
        
        // 获取所有分类
        $categories = Db::name('cms_category')->order('category_id', 'asc')->select();
        
        $this->assign('data', $data);
        $this->assign('categories', $categories);
        return $this->fetch('content_edit');
    }

    // 添加内容页面
    public function contentAdd()
    {
        // 获取所有分类
        $categories = Db::name('cms_category')->order('category_id', 'asc')->select();
        $this->assign('categories', $categories);
        return $this->fetch('content_add');
    }

    // 添加或编辑内容
    public function contentAddOrEdit()
    {
        $post = input('post.');
        $post['create_time'] = date('Y-m-d H:i:s');
        $post['update_time'] = date('Y-m-d H:i:s');
        
        if (isset($post['content_id'])) {
            // 编辑内容
            // 移除创建时间，只更新修改时间
            unset($post['create_time']);
            $result = Db::name('cms_content')->where('content_id', $post['content_id'])->update($post);
            if (!$result) return json(['code' => 1, 'msg' => '编辑失败']);
            return json(['code' => 0, 'msg' => '编辑成功']);
        } else {
            // 新增内容
            // 默认使用当前管理员用户ID
            $post['uid'] = session('admin_id');
            $contentId = Db::name('cms_content')->insertGetId($post);
            if (!$contentId) return json(['code' => 1, 'msg' => '新增失败']);
            return json(['code' => 0, 'msg' => '新增成功']);
        }
    }

    // 删除内容
    public function contentDelete()
    {
        $contentId = input('post.content_id');
        
        // 验证内容是否存在
        $content = Db::name('cms_content')->where('content_id', $contentId)->find();
        if (!$content) {
            return json(['code' => 1, 'msg' => '内容不存在']);
        }
        
        // 删除内容
        Db::name('cms_content')->where('content_id', $contentId)->delete();
        
        // 删除相关评论
        Db::name('cms_comment')->where('content_id', $contentId)->delete();
        
        return json(['code' => 0, 'msg' => '删除成功']);
    }

    // 切换内容状态
    public function contentToggleStatus()
    {
        $contentId = input('post.content_id');
        $content = Db::name('cms_content')->where('content_id', $contentId)->find();
        
        if (!$content) {
            return json(['code' => 1, 'msg' => '内容不存在']);
        }
        
        // 切换状态
        $newStatus = $content['status'] == 1 ? 0 : 1;
        $result = Db::name('cms_content')->where('content_id', $contentId)->update(['status' => $newStatus]);
        
        if ($result !== false) {
            return json(['code' => 0, 'msg' => '状态更新成功', 'status' => $newStatus]);
        } else {
            return json(['code' => 1, 'msg' => '状态更新失败']);
        }
    }

    // ======================= 友情链接管理 =======================
    
    // 切换友情链接状态
    public function linkToggleStatus()
    {
        $linkId = input('post.link_id');
        $link = Db::name('cms_link')->where('link_id', $linkId)->find();
        
        if (!$link) {
            return json(['code' => 1, 'msg' => '链接不存在']);
        }
        
        // 切换状态
        $newStatus = $link['status'] == 1 ? 0 : 1;
        $result = Db::name('cms_link')->where('link_id', $linkId)->update(['status' => $newStatus]);
        
        if ($result !== false) {
            return json(['code' => 0, 'msg' => '状态更新成功', 'status' => $newStatus]);
        } else {
            return json(['code' => 1, 'msg' => '状态更新失败']);
        }
    }

    // ======================= 友情链接分类管理 =======================
    
    // 切换友情链接分类状态
    public function linkCategoryToggleStatus()
    {
        $categoryId = input('post.link_category_id');
        $category = Db::name('cms_link_category')->where('link_category_id', $categoryId)->find();
        
        if (!$category) {
            return json(['code' => 1, 'msg' => '分类不存在']);
        }
        
        // 切换状态
        $newStatus = $category['status'] == 1 ? 0 : 1;
        $result = Db::name('cms_link_category')->where('link_category_id', $categoryId)->update(['status' => $newStatus]);
        
        if ($result !== false) {
            return json(['code' => 0, 'msg' => '状态更新成功', 'status' => $newStatus]);
        } else {
            return json(['code' => 1, 'msg' => '状态更新失败']);
        }
    }

    // 友情链接分类列表页面
    public function linkCategoryIndex()
    {
        return $this->fetch('link_category_index');
    }

    // 获取友情链接分类列表
    public function getLinkCategoryList()
    {
        $keyword = input('post.keyword', '');
        $page = input('post.page', 1, 'intval');
        $limit = input('post.limit', 10, 'intval');
        
        $query = Db::name('cms_link_category')->order('sort_order', 'asc');
        
        // 关键词搜索
        if ($keyword) {
            $query->where('category_name', 'like', '%' . $keyword . '%');
        }
        
        $total = $query->count();
        $list = $query->page($page, $limit)->select();
        
        return json(['code' => 0, 'msg' => 'success', 'count' => $total, 'data' => $list]);
    }

    // 编辑友情链接分类页面
    public function linkCategoryEdit()
    {
        $categoryId = input('get.link_category_id');
        $data = Db::name('cms_link_category')->where('link_category_id', $categoryId)->find();
        $this->assign('data', $data);
        return $this->fetch('link_category_edit');
    }

    // 添加友情链接分类页面
    public function linkCategoryAdd()
    {
        return $this->fetch('link_category_add');
    }

    // 添加或编辑友情链接分类
    public function linkCategoryAddOrEdit()
    {
        $post = input('post.');
        
        if (isset($post['link_category_id'])) {
            // 编辑分类
            $result = Db::name('cms_link_category')->where('link_category_id', $post['link_category_id'])->update($post);
            if (!$result) return json(['code' => 1, 'msg' => '编辑失败']);
            return json(['code' => 0, 'msg' => '编辑成功']);
        } else {
            // 新增分类
            $categoryId = Db::name('cms_link_category')->insertGetId($post);
            if (!$categoryId) return json(['code' => 1, 'msg' => '新增失败']);
            return json(['code' => 0, 'msg' => '新增成功']);
        }
    }

    // 删除友情链接分类
    public function linkCategoryDelete()
    {
        $categoryId = input('post.link_category_id');
        
        // 验证分类是否存在
        $category = Db::name('cms_link_category')->where('link_category_id', $categoryId)->find();
        if (!$category) {
            return json(['code' => 1, 'msg' => '分类不存在']);
        }
        
        // 删除分类
        Db::name('cms_link_category')->where('link_category_id', $categoryId)->delete();
        
        // 删除该分类下的所有友情链接
        Db::name('cms_link')->where('link_category_id', $categoryId)->delete();
        
        return json(['code' => 0, 'msg' => '删除成功']);
    }

    // ======================= 友情链接管理 =======================
    
    // 友情链接列表页面
    public function linkIndex()
    {
        // 获取所有分类用于筛选
        $categories = Db::name('cms_link_category')->order('sort_order', 'asc')->select();
        $this->assign('categories', $categories);
        return $this->fetch('link_index');
    }

    // 获取友情链接列表
    public function getLinkList()
    {
        $keyword = input('post.keyword', '');
        $categoryId = input('post.link_category_id', 0, 'intval');
        $page = input('post.page', 1, 'intval');
        $limit = input('post.limit', 10, 'intval');
        
        $query = Db::name('cms_link')
            ->alias('link')
            ->join('cms_link_category category', 'link.link_category_id = category.link_category_id')
            ->field('link.*, category.category_name')
            ->order('link.sort_order', 'asc');
        
        // 关键词搜索
        if ($keyword) {
            $query->where('link.site_name', 'like', '%' . $keyword . '%');
        }
        
        // 分类筛选
        if ($categoryId > 0) {
            $query->where('link.link_category_id', $categoryId);
        }
        
        $total = $query->count();
        $list = $query->page($page, $limit)->select();
        
        return json(['code' => 0, 'msg' => 'success', 'count' => $total, 'data' => $list]);
    }

    // 编辑友情链接页面
    public function linkEdit()
    {
        $linkId = input('get.link_id');
        $data = Db::name('cms_link')->where('link_id', $linkId)->find();
        
        // 获取所有分类
        $categories = Db::name('cms_link_category')->order('sort_order', 'asc')->select();
        
        $this->assign('data', $data);
        $this->assign('categories', $categories);
        return $this->fetch('link_edit');
    }

    // 添加友情链接页面
    public function linkAdd()
    {
        // 获取所有分类
        $categories = Db::name('cms_link_category')->order('sort_order', 'asc')->select();
        $this->assign('categories', $categories);
        return $this->fetch('link_add');
    }

    // 添加或编辑友情链接
    public function linkAddOrEdit()
    {
        $post = input('post.');
        $post['create_time'] = date('Y-m-d H:i:s');
        
        if (isset($post['link_id'])) {
            // 编辑链接
            // 移除创建时间，避免更新
            unset($post['create_time']);
            $result = Db::name('cms_link')->where('link_id', $post['link_id'])->update($post);
            if (!$result) return json(['code' => 1, 'msg' => '编辑失败']);
            return json(['code' => 0, 'msg' => '编辑成功']);
        } else {
            // 新增链接
            $linkId = Db::name('cms_link')->insertGetId($post);
            if (!$linkId) return json(['code' => 1, 'msg' => '新增失败']);
            return json(['code' => 0, 'msg' => '新增成功']);
        }
    }

    // 删除友情链接
    public function linkDelete()
    {
        $linkId = input('post.link_id');
        
        // 验证链接是否存在
        $link = Db::name('cms_link')->where('link_id', $linkId)->find();
        if (!$link) {
            return json(['code' => 1, 'msg' => '链接不存在']);
        }
        
        // 删除链接
        Db::name('cms_link')->where('link_id', $linkId)->delete();
        
        return json(['code' => 0, 'msg' => '删除成功']);
    }
}