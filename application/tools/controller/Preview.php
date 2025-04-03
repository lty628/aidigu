<?php
namespace app\tools\controller;
use think\Controller;
use think\Db;

class Preview extends Controller
{
    /**
     * 获取媒体资源路径列表
     * @return \think\response\Json
     */
    public function getMediaList()
    {
        // 模拟从数据库或文件系统查询媒体资源路径
        // $mediaList = [
        //     '/path/to/image1.jpg',
        //     '/path/to/video1.mp4',
        //     '/path/to/document1.pdf',
        //     '/path/to/text1.txt'
        // ];
        $mediaList = [];
        $fileList = Db::name('file')->select();
        foreach ($fileList as $file) {
            $mediaList[] = $file['file_path'];
        }

        return json($mediaList);
    }

    public function index()
    {
        // $mediaList = Db::name('file')->select();
        // dump($mediaList);

        return $this->fetch();
    }

    /**
     * 处理文件预览请求
     * @return \think\Response
     */
    public function previewFile()
    {
        if ($this->request->isPost()) {
            if ($this->request->file('file')) {
                $file = $this->request->file('file');
                $info = $file->validate(['ext' => 'jpg,png,gif,jpeg,mp4,avi,mov,pdf,txt'])->getInfo();
                if ($info) {
                    // 读取文件内容
                    $fileContent = file_get_contents($info['tmp_name']);
                    // 设置响应头
                    return response($fileContent, 200, ['Content-Type' => $info['type']]);
                } else {
                    return $this->error($file->getError());
                }
            } else {
                return $this->error('未选择文件');
            }
        } else {
            return $this->error('请求方法不允许');
        }
    }
}
