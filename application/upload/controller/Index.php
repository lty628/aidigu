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
        $this->assign('action', request()->action());
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
        // $result = Db::name('file')->where('is_delete', 0)->order('create_time', 'desc')->where('userid', getLoginUid())->paginate(7, false, ['page' => request()->param('page/d', 1), 'path' => '[PAGE].html']);
        // $this->assign('fileList', $result);
        return $this->fetch();
    }

    public function share()
    {
        // $result = Db::name('file')->where('is_delete', 0)->order('create_time', 'desc')->where('userid', getLoginUid())->paginate(7, false, ['page' => request()->param('page/d', 1), 'path' => '[PAGE].html']);
        // $this->assign('fileList', $result);
        return $this->fetch();
    }

    /**
     * 无用
     */
    public function shareHtml()
    {
        $id = (int) input('param.id');
        $fileInfo = Db::name('file')->where('id', $id)->where('userid', getLoginUid())->find();
        if (!$fileInfo) return $this->error('无分享权限');
        $type = explode('/', $fileInfo['file_type']);
        return $this->success('ok', null, [
            'type' => $type[0],
            'file_path' => $fileInfo['file_path'],
            'file_name' => $fileInfo['file_name'],
        ]);
    }

    public function getFiles()
    {
        $get = input('get.');
        $page = $get['page'] ?? 1;
        $limit = $get['limit'] ?? 10;
        $fileName = $get['file_name'] ?? '';
        $where[] = ['userid', '=', getLoginUid()];
        if ($fileName) {
            $where[] = ['file_name', 'like',  '%'.$fileName.'%'];
        }
        $count = Db::name('file')->where('is_delete', 0)->where($where)->count();
        $result = Db::name('file')->where('is_delete', 0)->order('create_time', 'desc')->where($where)->limit($limit)->page($page)->select();
        return json(['code' => 0, 'data'=> $result, 'count' => $count]);
    }

    // 回收站
    public function getCollection()
    {
        $get = input('get.');
        $page = $get['page'] ?? 1;
        $limit = $get['limit'] ?? 10;
        $fileName = $get['file_name'] ?? '';
        $where[] = ['userid', '=', getLoginUid()];
        if ($fileName) {
            $where[] = ['file_name', 'like',  '%'.$fileName.'%'];
        }
        $count = Db::name('file')->where('is_delete', 1)->where($where)->count();
        $result = Db::name('file')->where('is_delete', 1)->order('create_time', 'desc')->where($where)->limit($limit)->page($page)->select();
        return json(['code' => 0, 'data'=> $result, 'count' => $count]);
    }

    // 回收站
    public function collection()
    {
        // $result = Db::name('file')->where('is_delete', 1)->order('create_time', 'desc')->where('userid', getLoginUid())->paginate(7, false, ['page' => request()->param('page/d', 1), 'path' => '[PAGE].html']);
        // $this->assign('fileList', $result);
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
        $result = Db::name('file')->where('id', $id)->update(['is_delete' => 1]);
        if (!$result) return $this->error('删除失败');
        return $this->success('删除成功');
    }

    public function reduction()
    {
        $id = (int) input('param.id');
        $fileInfo = Db::name('file')->where('id', $id)->where('userid', getLoginUid())->find();
        if (!$fileInfo) return $this->error('无权限');
        $result = Db::name('file')->where('id', $id)->update(['is_delete' => 0]);
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

    // 影院分享，已取消
    // $(function () {
    //     $("#siteShare").click(function () {
    //         var url = $("#url").val()
    //         var jk = $("#jk").val()
    //         if (!url || !jk) {
    //             return false
    //         }
    //         layer.prompt({title: '请输入分享标题'}, function(value, index, elem){
    //             if(value === '') return elem.focus();
    //             $.ajax({
    //                 type: "GET",
    //                 url: "/upload/index/siteUrlShare",
    //                 data: {'url': jk+url, 'title': value},
    //                 dataType: "json",
    //                 success: function (response) {
    //                     layer.msg(response.msg);
    //                 }
    //             });
    //             // 关闭 prompt
    //             layer.close(index);
    //         });
    //     })
    // })
    public function siteUrlShare()
    {
        $url = input('param.url');
        $title = input('param.title');
        $data['media_info'] = '';
        $data['content'] = '<p>#观影分享# 正在看【<a href="javascript:;" data-title="" data-url="'.$url.'" onclick="showFrameUrl(this, \'80%\', \'60%\')">'.$title.'</a>】</p>';
        \app\common\controller\Api::saveMessage($data['content'], $data['media_info']);
        return $this->success('分享成功,请在我的首页中查看！');
    }

    //站内分享
    public function siteShare()
    {
        $id = (int) input('param.id');
        $fileInfo = Db::name('file')->where('id', $id)->where('userid', getLoginUid())->find();
        if (!$fileInfo) return $this->error('无分享权限');
        if ($fileInfo['share_msg_id']) return $this->error('已分享过了，不能重复分享哦！');
        $type = explode('/', $fileInfo['file_type']);
        $pathinfo = pathinfo($fileInfo['file_path']);
        $filePath = $pathinfo['dirname'] . '/' . $pathinfo['filename'];
        $fileExtension = $pathinfo['extension'];
        // dump($type);die;
        // 视频分享
        // {"media_info":"\/uploads\/72\/message\/20220326\/6b5b2500aade4230a7778bdc143eca51","media_type":"png"}
        // $data['media_info']['media_type'] = $type[1];
        $data['media_info'] = '';
        if ($type[1] == 'mp4') {
            $data['content'] = '<p>分享视频' . $fileInfo['file_name'] . '</p>';
            $data['media'] = $fileInfo['file_path'];
            $data['media_info'] = json_encode([
                'media_info' => $filePath,
                'media_type' => $fileExtension,
            ]);
        } elseif ($type[0] == 'image') {
            $data['content'] = '<p>分享图片</p>';
            $data['media'] = $fileInfo['file_path'];
            $data['media_info'] = json_encode([
                'media_info' => $filePath,
                'media_type' => $fileExtension,
            ]);
        } elseif ($fileInfo['file_type'] == 'audio/mpeg') {
            $data['content'] = '<p>分享 ' . $fileInfo['file_name'] . '</p><p><audio id="' . 'music_' . $id . '" class="music" controls="controls" loop="loop" onplay="stopOther(this)" preload="none" controlsList="nodownload" οncοntextmenu="return false" name="media"><source src="' . $fileInfo['file_path'] . '" type="audio/mpeg"></audio></p>';
        } elseif ($type[0] == 'text') {
            $data['content'] = '<p>阅读文件，点击<a href="javascript:;" data-url="/tools/reader?file_id=' . $fileInfo['id'] . '" data-title="' . $fileInfo['file_name'] . '" onclick="showFrameHtml(this, \'100%\', \'100%\')">' . $fileInfo['file_name'] . '</a>阅读</p>';
        } else {
            $data['content'] = '<p>分享文件，点击<a href="' . $fileInfo['file_path'] . '">' . $fileInfo['file_name'] . '</a>下载</p>';
        }
        $result = \app\common\controller\Api::saveMessage($data['content'], $data['media_info']);
        Db::name('file')->where('id', $id)->update(['share_msg_id' => $result['msg_id'] ?? 0]);
        return $this->success('分享成功,请在我的首页中查看！');
    }

    public function siteCanleShare()
    {
        $id = (int) input('param.id');
        $fileInfo = Db::name('file')->where('id', $id)->where('userid', getLoginUid())->find();
        if (!$fileInfo) return $this->error('无分享权限');
        $messageId = $fileInfo['share_msg_id'];
        Db::name('message')->where('msg_id', $messageId)->update([
            'is_delete' => 1
        ]);
        Db::name('file')->where('id', $id)->update(['share_msg_id' => 0]);
        return $this->success('已取消分享！');
    }
}
