<?php
namespace app\cms\controller;

use app\cms\model\Content as ContentModel;
use app\cms\model\Category as CategoryModel;
use app\cms\model\Attachment as AttachmentModel;
use app\cms\model\User as UserModel;

class Index extends Base  
{	
    // 通用文章编辑页面（发表和编辑）
    public function articleEdit(ContentModel $contentModel, CategoryModel $categoryModel)
    {
        $contentId = input('param.id');
        $article = null;
        
        // 如果有ID，获取文章信息（编辑模式）
        if ($contentId) {
            $article = $contentModel->getOne(['content_id' => $contentId]);
            if (!$article) {
                // 文章不存在，跳转到发表页面
                return redirect('/blog/commit');
            }
            // 检查权限
            if ($article['uid'] != getUserId()) {
                // 没有权限编辑，跳转到首页
                return redirect('/');
            }
        }
        
        // 获取分类列表
        $categoryList = $categoryModel->getAll();
        
        $this->assign([
            'article' => $article,
            'categoryList' => $categoryList
        ]);
        
        // 使用通用的文章编辑模板
        return $this->fetch('article_edit');
    }

    // 发表文章（旧方法，保持兼容）
	public function commit(CategoryModel $categoryModel)
    {
        $categoryList = $categoryModel->getAll();
        $this->assign('categoryList', $categoryList);
        $this->assign('categoryName', 'commit');
        $article = null;
        $this->assign('article', $article);
        return $this->fetch('article_edit'); // 重定向到通用页面
    }

    // 文章编辑（旧方法，保持兼容）
    public function commitEdit(ContentModel $contentModel, CategoryModel $categoryModel)
    {
        $contentId = input('param.id');
        $article = $contentModel->getOne(['content_id' => $contentId]);
        $categoryList = $categoryModel->getAll();
        
        $this->assign([
            'article' => $article,
            'categoryList' => $categoryList
        ]);
        $this->assign('categoryName', 'commitEdit');
        return $this->fetch('article_edit'); // 重定向到通用页面
    }

    // 文章详情
    public function details(ContentModel $contentModel)
    {
        $contentId = input('param.id');
        $article = $contentModel->getOne(['content_id' => $contentId]);
        
        $this->assign('article', $article);
        $this->assign('categoryName', $article['category']['category_name']);
        return $this->fetch('article_details');
    }

    public function index(CategoryModel $categoryModel, ContentModel $contentModel)
    {
        $input = input();
        $input['category_name'] = $input['category_name'] ?? '';
        $categoryArr = [];
        if ($input['category_name']) {
            $categoryArr = $categoryModel->getOne(['category_name' => $input['category_name']]);
        }
        $where['category_id'] =  $categoryArr['category_id'] ?? '';
        $where = array_filter($where);

        // content_id 降序
        $list = $contentModel->pageList($where, '*', 'content_id desc');
        $page = $list->render();
        
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('categoryName', $input['category_name']);

        return $this->fetch('article_list');
    }
}