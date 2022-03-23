<?php
namespace app\upload\controller;
use think\Controller;
use think\Db;
use app\upload\libs\Upload;

class Index extends Controller
{
    public function initialize()
    {
        if (!getLoginUid()) {
            return $this->error('您没有登录, 请先登录！', '/login/');
        }
    }

    public function index()
    {
        // $a = \Carbon\Carbon::parse('2020-04-08');
        // $b = \Carbon\Carbon::now();
        // dump($b);
        return $this->fetch();
    }

    public function show()
    {
        $result = Db::name('file')->where('is_delete', 0)->order('create_time', 'desc')->where('userid', getLoginUid())->paginate(30);
        $this->assign('fileList', $result);
        return $this->fetch();
    }

    // 回收站
    public function collection()
    {
        $result = Db::name('file')->where('is_delete', 1)->order('create_time', 'desc')->where('userid', getLoginUid())->paginate(30);
        $this->assign('fileList', $result);
        return $this->fetch(); 
    }

    public function upload(Upload $upload)
    {
        return $upload->server();
    }

    public function download()
    {
        $id = (int) input('param.id');
        $fileInfo = Db::name('file')->where('id', $id)->where('userid', getLoginUid())->find();
        if (!$fileInfo) return $this->error('无下载权限');
        $storageConfig = config('upload.storage');
        return $this->redirect($fileInfo['file_location']);
        // return $this->curlRequest($fileInfo['file_path']);
        // if ($storageConfig['type'] == 's3') {
        //     return $this->curlRequest($fileInfo['file_path']);
        // }
        // $file = $fileInfo['file_path'];
        // header("Content-type:application/octet-stream");
        // header("Content-Disposition:attachment;filename = " . basename($file));
        // header("Accept-ranges:bytes");
        // header("Accept-length:" . filesize($file));
        // $handle = fopen($file, 'rb');
        // while (!feof($handle)) {
        //     echo fread($handle, 102400);
        // }
        // fclose($handle);
        // exit();
    }

    // protected function curlRequest($url) 
    // {
    //     $url = str_replace(' ', '%20', $url);
    //     $filename = basename($url);
    //     $headers = get_headers($url, 1);
    //     $fileSize = $headers['Content-Length'];
    //     header('Content-Type: application/octet-stream');
    //     header('Accept-Ranges:bytes');
    //     header('Content-Length: ' . $fileSize);
    //     header('Content-Disposition: attachment; filename="' . $filename . '"');
    //     readfile($url);
    //     exit;
    // } 

    public function del()
    {
        $id = (int) input('param.id');
        $fileInfo = Db::name('file')->where('id', $id)->where('userid', getLoginUid())->find();
        if (!$fileInfo) return $this->error('无删除权限');
        $result = Db::name('file')->where('id', $id)->update(['is_delete'=> 1]);
        if (!$result) return $this->error('删除失败');
        return $this->success('删除成功');
    }

    public function reduction()
    {
        $id = (int) input('param.id');
        $fileInfo = Db::name('file')->where('id', $id)->where('userid', getLoginUid())->find();
        if (!$fileInfo) return $this->error('无权限');
        $result = Db::name('file')->where('id', $id)->update(['is_delete'=> 0]);
        if (!$result) return $this->error('还原失败');
        return $this->success('还原成功');
    }

    // 永久删除
    public function deldForever()
    {
        $id = (int) input('param.id');
        $fileInfo = Db::name('file')->where('id', $id)->where('userid', getLoginUid())->find();
        if (!$fileInfo) return $this->error('无删除权限');
        $result = Db::name('file')->where('id', $id)->delete();
        if (!$result) return $this->error('永久删除失败');
        // @unlink($fileInfo['file_path']);
        return $this->success('永久删除成功');
    }

}
