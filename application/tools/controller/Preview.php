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
        // 功能暂未启用，返回空数组
        $mediaList = [];
        return json([
            'mediaList' => [],
            'startIndex' => 1
        ]);
        $fileList = Db::name('file')->select();
        foreach ($fileList as $file) {
            $mediaList[] = $file['file_path'];
        }

        // 假设这里从数据库或者其他逻辑获取起始索引，这里简单模拟设置为 1
        $startIndex = 1; 

        $responseData = [
            'mediaList' => $mediaList,
            'startIndex' => $startIndex
        ];

        return json($responseData);
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
