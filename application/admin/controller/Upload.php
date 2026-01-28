<?php
namespace app\admin\controller;

use think\Controller;
use think\Request;


/**
 * 文件上传控制器
 */
class Upload extends Controller
{
    /**
     * 通用上传方法
     * 支持不同类型文件上传
     */
    public function upload(Request $request)
    {
        try {
            // 获取上传的文件
            $file = $request->file('file');
            
            if (!$file) {
                return json(['code' => 0, 'msg' => '没有上传文件']);
            }

            // 获取参数
            $type = $request->param('type', 'common'); // 默认为普通文件
            $allowExt = [];
            $maxSize = 2 * 1024 * 1024; // 默认2MB

            // 根据类型设置不同的限制
            switch ($type) {
                case 'logo':
                case 'icon':
                case 'app':
                    $allowExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                    $maxSize = 2 * 1024 * 1024; // 2MB
                    break;
                case 'avatar':
                    $allowExt = ['jpg', 'jpeg', 'png'];
                    $maxSize = 1 * 1024 * 1024; // 1MB
                    break;
                case 'document':
                    $allowExt = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'txt'];
                    $maxSize = 10 * 1024 * 1024; // 10MB
                    break;
                case 'video':
                    $allowExt = ['mp4', 'avi', 'mov', 'wmv', 'flv'];
                    $maxSize = 50 * 1024 * 1024; // 50MB
                    break;
                default:
                    $allowExt = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'pdf', 'doc', 'docx', 'txt'];
                    $maxSize = 5 * 1024 * 1024; // 5MB
            }

            // 验证文件
            $info = $file->validate([
                'size' => $maxSize,
                'ext' => implode(',', $allowExt)
            ])->move('uploads/admin/' . $type . 's/');

            if ($info) {
                // 上传成功
                $filePath = '/uploads/admin/' . $type . 's/' . str_replace('\\', '/', $info->getSaveName());
                
                return json([
                    'code' => 1,
                    'msg' => '上传成功',
                    'data' => [
                        'src' => $filePath,
                        'name' => $info->getFilename(),
                        'size' => $info->getSize(),
                        'type' => $info->getExtension(),
                        'savename' => $info->getSaveName()
                    ]
                ]);
            } else {
                // 上传失败
                return json(['code' => 0, 'msg' => $file->getError()]);
            }
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * Logo上传专用方法
     */
    public function uploadLogo(Request $request)
    {
        try {
            // 获取上传的文件
            $file = $request->file('file');
            
            if (!$file) {
                return json(['code' => 0, 'msg' => '没有上传文件']);
            }

            // 验证Logo文件
            $info = $file->validate([
                'size' => 2 * 1024 * 1024, // 2MB
                'ext' => 'jpg,jpeg,png,gif,webp'
            ])->move('uploads/admin/logos/');

            if ($info) {
                // 上传成功
                $filePath = '/uploads/admin/logos/' . str_replace('\\', '/', $info->getSaveName());
                
                return json([
                    'code' => 1,
                    'msg' => 'Logo上传成功',
                    'data' => [
                        'src' => $filePath,
                        'name' => $info->getFilename(),
                        'size' => $info->getSize(),
                        'type' => $info->getExtension(),
                        'savename' => $info->getSaveName()
                    ]
                ]);
            } else {
                // 上传失败
                return json(['code' => 0, 'msg' => $file->getError()]);
            }
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * Icon上传专用方法
     */
    public function uploadIcon(Request $request)
    {
        try {
            // 获取上传的文件
            $file = $request->file('file');
            
            if (!$file) {
                return json(['code' => 0, 'msg' => '没有上传文件']);
            }

            // 验证Icon文件 - 更严格的尺寸限制
            $info = $file->validate([
                'size' => 1 * 1024 * 1024, // 1MB
                'ext' => 'jpg,jpeg,png,gif,webp,ico'
            ])->move('uploads/admin/icons/');

            if ($info) {
                // 上传成功
                $filePath = '/uploads/admin/icons/' . str_replace('\\', '/', $info->getSaveName());
                
                return json([
                    'code' => 1,
                    'msg' => 'Icon上传成功',
                    'data' => [
                        'src' => $filePath,
                        'name' => $info->getFilename(),
                        'size' => $info->getSize(),
                        'type' => $info->getExtension(),
                        'savename' => $info->getSaveName()
                    ]
                ]);
            } else {
                // 上传失败
                return json(['code' => 0, 'msg' => $file->getError()]);
            }
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 多文件上传方法
     */
    public function uploadMultiple(Request $request)
    {
        try {
            // 获取上传的文件数组
            $files = $request->file('files');
            
            if (!$files) {
                return json(['code' => 0, 'msg' => '没有上传文件']);
            }

            $results = [];
            $successCount = 0;
            $errorCount = 0;

            foreach ($files as $file) {
                // 验证文件
                $info = $file->validate([
                    'size' => 5 * 1024 * 1024, // 5MB
                    'ext' => 'jpg,jpeg,png,gif,webp,pdf,doc,docx,txt'
                ])->move('uploads/admin/common/');

                if ($info) {
                    // 上传成功
                    $filePath = '/uploads/admin/common/' . str_replace('\\', '/', $info->getSaveName());
                    
                    $results[] = [
                        'code' => 1,
                        'msg' => '上传成功',
                        'data' => [
                            'src' => $filePath,
                            'name' => $info->getFilename(),
                            'size' => $info->getSize(),
                            'type' => $info->getExtension(),
                            'savename' => $info->getSaveName()
                        ]
                    ];
                    $successCount++;
                } else {
                    // 上传失败
                    $results[] = [
                        'code' => 0,
                        'msg' => $file->getError()
                    ];
                    $errorCount++;
                }
            }

            return json([
                'code' => 1,
                'msg' => "批量上传完成，成功{$successCount}个，失败{$errorCount}个",
                'data' => [
                    'results' => $results,
                    'success_count' => $successCount,
                    'error_count' => $errorCount
                ]
            ]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => $e->getMessage()]);
        }
    }

    /**
     * 删除上传的文件
     */
    public function deleteFile(Request $request)
    {
        $filePath = $request->param('file_path');
        
        if (!$filePath) {
            return json(['code' => 0, 'msg' => '文件路径不能为空']);
        }

        // 安全检查，防止目录遍历攻击
        if (strpos($filePath, '..') !== false || strpos($filePath, './') !== false) {
            return json(['code' => 0, 'msg' => '非法文件路径']);
        }

        $fullPath = 'public' . $filePath;
        
        if (file_exists($fullPath)) {
            if (unlink($fullPath)) {
                return json(['code' => 1, 'msg' => '文件删除成功']);
            } else {
                return json(['code' => 0, 'msg' => '文件删除失败']);
            }
        } else {
            return json(['code' => 0, 'msg' => '文件不存在']);
        }
    }
}