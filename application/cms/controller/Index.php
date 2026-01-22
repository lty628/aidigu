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
        $this->assign('categoryName', 'commit');
        return $this->fetch('commit');
    }

    public function commitEdit(ContentModel $contentModel)
    {
        $contentId = input('param.id');
        $article = $contentModel->getOne(['content_id' => $contentId]);
        $article['content'] = json_encode($article['content'], JSON_HEX_TAG | JSON_HEX_AMP);
        $this->assign([
            'article' => $article
        ]);
        $this->assign('categoryName', 'commitEdit');
        return $this->fetch('commit-edit');
    }

    // 文章详情
    public function details(ContentModel $contentModel)
    {
        $contentId = input('param.id');
        $article = $contentModel->getOne(['content_id' => $contentId]);
        
        $this->assign('article', $article);
        $this->assign('categoryName', $article['category']['category_name']);
        return $this->fetch('details');
    }

    public function index(CategoryModel $categoryModel, ContentModel $contentModel)
    {
        $input = input();
        $input['category_name'] = $input['category_name'] ?? '';
        $categoryArr = $categoryModel->getOne(['category_name' => $input['category_name']]);
        $where['category_id'] =  $categoryArr['category_id'] ?? '';
        $where = array_filter($where);

        // content_id 降序
        $list = $contentModel->pageList($where, '*', 'content_id desc');
        $page = $list->render();
        // dump($list);
        // die;
        $this->assign('list', $list);
        $this->assign('page', $page);
        $this->assign('categoryName', $input['category_name']);
        // if ($input['category_name']) {
            return $this->fetch('index2');
        // }
        
        return $this->fetch();
    }

}