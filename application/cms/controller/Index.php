<?php
namespace app\cms\controller;

use app\cms\model\Content as ContentModel;
use app\cms\model\Category as CategoryModel;
use app\cms\model\Attachment as AttachmentModel;
use app\cms\model\User as UserModel;

class Index extends Base  
{	
    // 发表文章
	public function commit()
    {
        return $this->fetch('commit');
    }

    public function commitEdit(ContentModel $contentModel)
    {
        $contentId = input('param.id');
        $article = $contentModel->getOne(['content_id' => $contentId]);
        $this->assign([
            'article' => $article
        ]);
        return $this->fetch('commit-edit');
    }

    // 文章详情
    public function details(ContentModel $contentModel)
    {
        $contentId = input('param.id');
        $article = $contentModel->getOne(['content_id' => $contentId]);
        
        $this->assign('article', $article);
        return $this->fetch('details');
    }

    public function index(CategoryModel $categoryModel, ContentModel $contentModel)
    {
        $input = input();
        $input['category_name'] = $input['category_name'] ?? '';
        $categoryArr = $categoryModel->getList(['category_name' => $input['category_name']]);
        $where['category_id'] =  $categoryArr[$input['category_name']] ?? '';
        $where = array_filter($where);

        // content_id 降序
        $list = $contentModel->pageList([], '*', 'content_id desc');
        $page = $list->render();
        // dump($list);
        // die;
        $this->assign('list', $list);
        $this->assign('page', $page);

        // if ($input['category_name']) {
            return $this->fetch('index2');
        // }
        
        return $this->fetch();
    }

}