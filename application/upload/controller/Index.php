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
        $dirId = input('get.dirId', 0, 'intval');
        $hiddenHeader = input('get.hiddenHeader', 0, 'intval');
        $this->assign('dirId', $dirId);
        $this->assign('hiddenHeader', $hiddenHeader);
        $this->assign('action', request()->action());
    }

    public function index()
    {
        // $a = \Carbon\Carbon::parse('2020-04-08');
        // $b = \Carbon\Carbon::now();
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
     * 资源管理器方法，根据请求类型返回不同结果，同时支持按目录查询文件和目录
     */
    public function explorer()
    {
        // 获取目录 ID，默认为 0
        $dirId = input('get.dir_id', 0, 'intval');
        if (request()->isAjax()) {
            $get = input('get.');
            $page = $get['page'] ?? 1;
            $pageSize = $get['page_size'] ?? 10;
            $fileName = $get['file_name'] ?? '';
            $uid = getLoginUid();

            // 查询当前路径
            $currentPath = $this->getDirectoryPath($dirId, $uid);

            // 查询目录总数
            $whereDir = [
                ['parent_id', '=', $dirId],
                ['uid', '=', $uid],
                ['is_delete', '=', 0]
            ];
            if ($fileName) {
                $whereDir[] = ['dir_name', 'like',  '%'.$fileName.'%'];
            }
            $dirCount = Db::name('file_dir')->where($whereDir)->count();
            
            // 查询文件总数
            $whereFile = [
                ['dir_id', '=', $dirId],
                ['userid', '=', $uid],
                ['is_delete', '=', 0]
            ];
            if ($fileName) {
                $whereFile[] = ['file_name', 'like',  '%'.$fileName.'%'];
            }
            $fileCount = Db::name('file')->where($whereFile)->count();
            
            // 计算文件夹分页
            $dirPageSize = min($pageSize, $dirCount); // 优先显示所有文件夹
            $dirOffset = ($page - 1) * $pageSize; // 修改1：计算文件夹偏移量
            $dirs = Db::name('file_dir')
                ->where($whereDir)
                ->limit($dirOffset, $dirPageSize)
                ->select();
            
            // 计算文件分页
            $filePageSize = $pageSize - count($dirs); // 剩余空间显示文件
            $fileOffset = max(0, ($page - 1) * $pageSize - $dirCount); // 修改2：简化计算
            
            $files = [];
            if ($filePageSize > 0) {
                $files = Db::name('file')
                    ->where($whereFile)
                    ->order('create_time', 'desc')
                    ->limit($fileOffset, $filePageSize)
                    ->select();
            }

            // 合并结果
            $result = [
                'dirs' => $dirs,
                'files' => $files,
                'total_count' => $dirCount + $fileCount
            ];

            return json(['code' => 0, 'data' => $result, 'current_path' => $currentPath]);
        } else {
            return $this->fetch();
        }
    }

    /**
     * 递归获取目录路径
     * @param int $dirId 目录 ID
     * @param int $uid 用户 ID
     * @return string 目录完整路径
     */
    private function getDirectoryPath($dirId, $uid)
    {
        if ($dirId == 0) {
            return '/';
        }

        $dirInfo = Db::name('file_dir')
            ->where('dir_id', $dirId)
            ->where('uid', $uid)
            ->where('is_delete', 0)
            ->find();

        if (!$dirInfo) {
            return '/';
        }

        $parentPath = $this->getDirectoryPath($dirInfo['parent_id'], $uid);
        if ($parentPath !== '/') {
            $parentPath .= '/';
        }

        return $parentPath . $dirInfo['dir_name'];
    }

    // public function createTable()
    // {
    //     // 创建一个无限极分类的 目录表 wb_file_dir 顶级是 dir_id, parent_id , uid, name, is_delete, create_time, update_time
    //     // 删除表
    //     Db::execute("drop table wb_file_dir");
    //     $sql = "CREATE TABLE IF NOT EXISTS `wb_file_dir` (
    //         `dir_id` int(11) NOT NULL AUTO_INCREMENT,
    //         `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT '父级ID',
    //         `uid` int(11) NOT NULL DEFAULT '0' COMMENT '用户ID',
    //         `dir_name` varchar(255) NOT NULL DEFAULT '' COMMENT '目录名称',
    //         `is_delete` tinyint(1) NOT NULL DEFAULT '0' COMMENT '是否删除',
    //         `create_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
    //         `update_time` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
    //         PRIMARY KEY (`dir_id`)
    //     ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文件目录表';";
    //     // file表增加dir_id字段默认是0(代表根目录)
    //     $alterSql = "ALTER TABLE `wb_file` ADD COLUMN `dir_id` int(11) NOT NULL DEFAULT '0' COMMENT '目录ID';";
    //     Db::execute($sql);
    //     // Db::execute($alterSql);
    // }

    /**
     * 删除文件或目录
     *
     * @return \think\response\Json
     */
    public function deleteFile()
    {
        $id = (int) input('post.id');
        $type = input('post.type');
        $uid = getLoginUid();   
        if ($type == 'dir') {
            // 有文件不能删除
            $fileCount = Db::name('file')->where('dir_id', $id)->where('userid', $uid)->count();
            if ($fileCount > 0) {
                return json(['code' => 1, 'msg' => '该目录下有文件，不能删除']);
            }
            $result = Db::name('file_dir')->where('dir_id', $id)->where('uid', $uid)->update(['is_delete' => 1]);
        } elseif ($type == 'file') {
            $result = Db::name('file')->where('id', $id)->where('userid', $uid)->update(['is_delete' => 1]);
        } else {
            return json(['code' => 1, 'msg' => '无效的删除类型']);
        }

        if (!$result) {
            return json(['code' => 1, 'msg' => '删除失败']);
        }
        return json(['code' => 0, 'msg' => '删除成功']);
    }

    public function createDir()
    {
        $dirName = input('post.folder_name');
        $dirId = input('post.parent_dir_id');

        // 处理前端传递的 '.' 为根目录 ID（假设根目录 ID 为 0）
        if ($dirId === '.') {
            $dirId = 0;
        } else {
            // 尝试将其转换为整数
            $dirId = (int)$dirId;
        }

        // 参数验证
        if (empty($dirName) || !is_numeric($dirId)) {
            return json([
                'code' => 1,
                'msg' => '文件夹名称不能为空，且父目录 ID 必须为有效数字'
            ]);
        }

        $uid = getLoginUid();
        $data = [
            'parent_id' => $dirId,
            'uid' => $uid,
            'dir_name' => $dirName,
            'is_delete' => 0,
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s'),
        ];
        $result = Db::name('file_dir')->insert($data);
        if (!$result) {
            return json([
                'code' => 1,
                'msg' => '创建目录失败'
            ]);
        }
        return json([
            'code' => 0,
            'msg' => '创建目录成功'
        ]);
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

    /**
     * 下载文件方法
     *
     * @return \think\Response
     */
    public function download()
    {
        $id = (int) input('param.id');
        $fileInfo = Db::name('file')->where('id', $id)->where('userid', getLoginUid())->find();
        if (!$fileInfo) {
            return $this->error('无下载权限');
        }

        $storageConfig = config('upload.storage');
        if (isset($storageConfig['type']) && $storageConfig['type'] == 's3') {
            // 处理 S3 存储的下载逻辑
            $url = $fileInfo['file_location'];
            $url = str_replace(' ', '%20', $url);
            $filename = basename($url);
            $headers = get_headers($url, 1);
            if (isset($headers['Content-Length'])) {
                $fileSize = $headers['Content-Length'];
                header('Content-Type: application/octet-stream');
                header('Accept-Ranges: bytes');
                header('Content-Length: ' . $fileSize);
                header('Content-Disposition: attachment; filename="' . $filename . '"');
                readfile($url);
                exit;
            } else {
                return $this->error('获取文件大小失败');
            }
        } else {
            // 处理本地存储的下载逻辑
            $file =  env('root_path') . 'public' . $fileInfo['file_path'];
            if (file_exists($file)) {
                header("Content-type: application/octet-stream");
                header("Content-Disposition: attachment; filename=" . basename($file));
                header("Accept-Ranges: bytes");
                header("Content-Length: " . filesize($file));
                $handle = fopen($file, 'rb');
                while (!feof($handle)) {
                    echo fread($handle, 102400);
                }
                fclose($handle);
                exit;
            } else {
                return $this->error('文件不存在');
            }
        }
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
    /**
     * 站内分享文件
     *
     * @return \think\response\Json|\think\response\View
     */
    public function siteShare()
    {
        $id = (int) input('param.id');
        $fileInfo = Db::name('file')->where('id', $id)->where('userid', getLoginUid())->find();
        if (!$fileInfo) {
            return $this->error('无分享权限');
        }
        if ($fileInfo['share_msg_id']) {
            return $this->error('已分享过了，不能重复分享哦！');
        }
    
        $type = explode('/', $fileInfo['file_type']);
        $pathinfo = pathinfo($fileInfo['file_path']);
        $filePath = $pathinfo['dirname'] . '/' . $pathinfo['filename'];
        $fileExtension = $pathinfo['extension'];
    
        $data['media_info'] = '';
    
        // 根据文件类型生成不同的分享内容
        switch ($type[0]) {
            case 'video':
                if ($type[1] == 'mp4') {
                    $data['content'] = '<p>分享视频' . $fileInfo['file_name'] . '</p>';
                    $data['media'] = $fileInfo['file_path'];
                    $data['media_info'] = json_encode([
                        'media_info' => $filePath,
                        'media_type' => $fileExtension,
                    ]);
                }
                break;
            case 'image':
                $data['content'] = '<p>分享图片</p>';
                $data['media'] = $fileInfo['file_path'];
                $data['media_info'] = json_encode([
                    'media_info' => $filePath,
                    'media_type' => $fileExtension,
                ]);
                break;
            case 'audio':
                if ($fileInfo['file_type'] == 'audio/mpeg') {
                    $data['content'] = '<p>分享 ' . $fileInfo['file_name'] . '</p><p><audio id="' . 'music_' . $id . '" class="music" controls="controls" loop="loop" onplay="stopOther(this)" preload="none" controlsList="nodownload" οncοntextmenu="return false" name="media"><source src="' . $fileInfo['file_path'] . '" type="audio/mpeg"></audio></p>';
                }
                break;
            case 'text':
                $data['content'] = '<p>阅读文件，点击<a href="javascript:;" data-url="/tools/reader?file_id=' . $fileInfo['id'] . '" data-title="' . $fileInfo['file_name'] . '" onclick="showFrameHtml(this, \'100%\', \'100%\')">' . $fileInfo['file_name'] . '</a>阅读</p>';
                break;
            default:
                $data['content'] = '<p>分享文件，点击<a href="' . $fileInfo['file_path'] . '">' . $fileInfo['file_name'] . '</a>下载</p>';
                break;
        }
    
        $result = \app\common\controller\Api::saveMessage($data['content'], $data['media_info']);
        if ($result) {
            Db::name('file')->where('id', $id)->update(['share_msg_id' => $result['msg_id'] ?? 0]);
            return $this->success('分享成功,请在我的首页中查看！');
        } else {
            return $this->error('分享失败，请稍后重试');
        }
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

    /**
     * 重命名文件或目录
     *
     * @return \think\response\Json
     */
    public function renameItem()
    {
        $id = (int) input('post.id');
        $type = input('post.type');
        $newName = input('post.new_name');
        $uid = getLoginUid();

        // 验证新名称是否为空
        if (empty($newName)) {
            return json(['code' => 1, 'msg' => '新名称不能为空']);
        }

        if ($type == 'dir') {
            // 检查目录是否存在且属于当前用户
            $dirInfo = Db::name('file_dir')
                ->where('dir_id', $id)
                ->where('uid', $uid)
                ->where('is_delete', 0)
                ->find();

            if (!$dirInfo) {
                return json(['code' => 1, 'msg' => '目录不存在或无权限操作']);
            }

            // 更新目录名称
            $result = Db::name('file_dir')
                ->where('dir_id', $id)
                ->update(['dir_name' => $newName]);

        } elseif ($type == 'file') {
            // 检查文件是否存在且属于当前用户
            $fileInfo = Db::name('file')
                ->where('id', $id)
                ->where('userid', $uid)
                ->where('is_delete', 0)
                ->find();

            if (!$fileInfo) {
                return json(['code' => 1, 'msg' => '文件不存在或无权限操作']);
            }

            // 更新文件名称
            $result = Db::name('file')
                ->where('id', $id)
                ->update(['file_name' => $newName]);
        } else {
            return json(['code' => 1, 'msg' => '无效的重命名类型']);
        }

        if (!$result) {
            return json(['code' => 1, 'msg' => '重命名失败']);
        }

        return json(['code' => 0, 'msg' => '重命名成功']);
    }

    /**
     * 选择目标文件夹页面
     */
    public function selectDir()
    {
        if (request()->isAjax()) {
            $parentId = input('get.dir_id', 0);
            $uid = getLoginUid();
        
            $dirs = Db::name('file_dir')
                ->where('parent_id', $parentId)
                ->where('uid', $uid)
                ->where('is_delete', 0)
                ->select();
        
            return json([
                'code' => 0,
                'data' => [
                    'dirs' => $dirs
                ]
            ]); 
        } else {
            $type = input('get.type');
            $id = input('get.id');
            $this->assign('type', $type);
            $this->assign('id', $id);
            return $this->fetch();
        }

    }

    /**
     * 移动文件或目录
     *
     * @return \think\response\Json
     */
    public function moveItem()
    {
        $id = (int)$this->request->post('id');
        $type = $this->request->post('type');
        $targetDirId = (int)$this->request->post('target_dir_id');
        $uid = getLoginUid();

        // 参数验证
        if (empty($id) || empty($type)) {
            return json(['code' => 1, 'msg' => '参数缺失']);
        }

        try {
            Db::startTrans(); // 开启事务

            if ($type == 'dir') {
                // 检查目录是否存在且属于当前用户
                $dirInfo = Db::name('file_dir')
                    ->where('dir_id', $id)
                    ->where('uid', $uid)
                    ->where('is_delete', 0)
                    ->find();

                if (!$dirInfo) {
                    throw new \Exception('目录不存在或无权限操作');
                }

                // 更新目录的父级 ID
                $result = Db::name('file_dir')
                    ->where('dir_id', $id)
                    ->update(['parent_id' => $targetDirId]);
            } elseif ($type == 'file') {
                // 检查文件是否存在且属于当前用户
                $fileInfo = Db::name('file')
                    ->where('id', $id)
                    ->where('userid', $uid)
                    ->where('is_delete', 0)
                    ->find();

                if (!$fileInfo) {
                    throw new \Exception('文件不存在或无权限操作');
                }

                // 更新文件的目录 ID
                $result = Db::name('file')
                    ->where('id', $id)
                    ->update(['dir_id' => $targetDirId]);
            } else {
                throw new \Exception('无效的移动类型');
            }

            if (!$result) {
                throw new \Exception('移动失败');
            }

            Db::commit(); // 提交事务
            return json(['code' => 0, 'msg' => '移动成功']);
        } catch (\Exception $e) {
            Db::rollback(); // 回滚事务
            return json(['code' => 1, 'msg' => $e->getMessage()]);
        }
    }
}
