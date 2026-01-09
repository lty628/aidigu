<?php
namespace app\cms\controller;

use app\cms\model\Content as ContentModel;
use app\cms\model\Category as CategoryModel;
use app\cms\model\Attachment as AttachmentModel;
use app\cms\model\User as UserModel;

class Index extends Base  
{	
    // 发表文章
	public function commit(CategoryModel $categoryModel)
    {
        $categoryList = $categoryModel->getList();
        $this->assign([
            'categoryList' => $categoryList,
        ]);
        return $this->fetch('commit');
    }

    public function commitEdit(CategoryModel $categoryModel, ContentModel $contentModel)
    {
        $contentId = input('param.id');
        $article = $contentModel->getOne(['content_id' => $contentId]);
        $categoryList = $categoryModel->getList();
        $this->assign([
            'categoryList' => $categoryList,
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

    public function download(CategoryModel $categoryModel, AttachmentModel $attachmentModel)
    {
        $input = input();
        $input['category_name'] = $input['category_name'] ?? '';
        $input['company_name'] = $input['company_name'] ?? '';
        $categoryArr = $categoryModel->getList(['category_name' => $input['category_name']]);
        $where['category_id'] =  $categoryArr[$input['category_name']] ?? '';
        $where['company_id'] = $companyArr[$input['company_name']] ?? '';
        $where = array_filter($where);
        // dump($where);die;
        $categoryList = $categoryModel->getList();
        $attachmentList = $attachmentModel->pageList($where, '*', 'attach_id desc');
        $page = $attachmentList->render();
        $this->assign([
            'categoryList' => $categoryList,
            'attachmentList' => $attachmentList,
            'page' => $page
        ]);
        return $this->fetch('download');
    }

    public function downloadDetails(AttachmentModel $attachmentModel)
    {
        $id = input('param.id');
        $content = $attachmentModel->getOne(['attach_id' => $id]);
        $this->assign([
            'content' => $content
        ]);
        // 自动截取面试题
        $content['attach_content'] = cutAutoLen($content['attach_content']);
        return $this->fetch('download_details');
    }

    // 主页
    public function index(ContentModel $content)
    {
        // content_id 降序
        $list = $content->pageList([], '*', 'content_id desc');
        $page = $list->render();
        // dump($list);
        // die;
        $this->assign('list', $list);
        $this->assign('page', $page);
        
        return $this->fetch('index');
    }

    // 设置密码
    public function password()
    {
        return $this->fetch('password');
    }

    // 验证码
    public function verify()
    {
        return $this->fetch('verify');
    }

    // 分享
    public function share(CategoryModel $categoryModel)
    {
        $categoryList = $categoryModel->getList();
        $this->assign([
            'categoryList' => $categoryList
        ]);
        // dump($companyList);die;
        return $this->fetch('share');
    }

    // 重置密码
    public function passwordReset()
    {
        return $this->fetch('password-reset');
    }

    public function profile()
    {
        return $this->fetch('profile');
    }


    public function settings()
    {
        return $this->fetch('settings');
    }

    //滑动验证码测试
    public function aiverify()
    {
        return View('aiverify');
    }

    public function login(UserModel $userModel)
    {
        $result = $userModel->getOne(['phone' => '18813183651']);
        session('userInfo', $result);
        $this->redirect('/');
        // return $this->fetch('index/login');
    }

    public function register()
    {
        return $this->fetch('index/register');
    }

    public function bindPhone()
    {
        $userInfo = getUserInfo();
        $this->assign('userInfo', $userInfo);
        return $this->fetch('index/bind-phone');
    }
}