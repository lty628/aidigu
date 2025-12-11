<?php
namespace app\tools\controller;
use app\common\controller\Base;
use think\Db;


class Reader extends Base
{	
	public function index()
    {
        $materialRelationId = (int) input('get.material_relation_id');
        $fileId = (int) input('get.file_id');
        $path = '';
        $contents = '';
        $filename = '';

        if ($materialRelationId) {
            $materialRelation = Db::name('source_material_relation')->field('id,media_info,media_type,file_name')->where('id', $materialRelationId)->find();
            if ($materialRelation) {
                $path = $materialRelation['media_info'] . '.' . $materialRelation['media_type'];
                $filename = $materialRelation['file_name'];
            }
        }

        if ($fileId) {
            $fileInfo = Db::name('file')->where('id', $fileId)->find();
            if ($fileInfo) {
                $path = $fileInfo['file_path'];
                $filename = $fileInfo['file_name'];
            }
        }

        if ($path) {
            $path = sysConfig('root_path') . 'public' . $path;
        }

        if (file_exists($path)) {
            $contents = file_get_contents($path);
            // 判断字符集并改为utf8
            $charset = mb_detect_encoding($contents, array("ASCII", "GB2312", "GBK", "UTF-8"));
            if ($charset != 'UTF-8') {
                $contents = mb_convert_encoding($contents, 'UTF-8', $charset);
            }

        }

        if ($path) {
            $path = 1;
        }
        
        $this->assign('path',$path);
        $this->assign('contents',$contents);
        $this->assign('filename',$filename);
        return $this->fetch();
    }
}
