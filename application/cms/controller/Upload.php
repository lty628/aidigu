<?php
namespace app\cms\controller;
use app\cms\controller\Base;

/**
 * 通用上传
 */
class Upload 
{
    protected $validate = [];
    protected $dir = '';
    protected $rule = '';
    protected $url = '';
    protected $field = 'file';
    protected $origin = [];

    public function __construct()
    {
        $config = config('upload.');
        $this->validate = array_filter([
            'size'=>$config['size'],
            'ext'=> $config['ext'],
            'type' => $config['type']
        ]);
        $this->field = $config['field'] ?: 'file';
        $this->dir = $config['dir'] ?: 'uploads';
        $this->rule = $config['rule'] ?: 'date';
        $this->origin = $config['origin'];
        $this->url = $config['url'];
        setOrigin($this->origin);
    }

    // 文件上传
    // 示例：{"code":1,"msg":"ok","data":"http:\/\/www.baidu.com\/uploads\/20210916\/f0adcb37360ca1fdf323b91fc9626773.md"}
    public function index()
    {
        // 获取表单上传文件 例如上传了001.jpg
        $file = request()->file($this->field);
        if (!$file) return ajaxJson(0, '无文件');
        $info = $file->rule($this->rule)->validate($this->validate)->move($this->dir);
        if ($info) {
            $linkPath = rtrim($this->url, '/') . '/' . $this->dir . '/' . $info->getSaveName();
            return json([
                'errno' => 0,
                'data' => [
                    [
                        'url' => $linkPath,
                        'alt' => $info->getFilename(),
                        'href' => $linkPath
                    ]
                ]
            ]);
        } else {
            // 上传失败获取错误信息
            return ajaxJson(0, $file->getError());
        }
    }
}
