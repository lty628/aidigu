<?php
namespace app\upload\controller;
use think\Controller;
use think\Db;
// use app\upload\libs\Upload;

class Upload extends Controller
{
    public function __construct()
    {
        // \app\upload\libs\Config::set(env('CONFIG_PATH') . 'upload.php');
        \app\upload\libs\Config::set($this->getConfig());
    }
    public function index()
    {
        $listener = new \app\upload\listener\Index();
        $basePath = getTusUploadFile();
        $server   = new \app\upload\libs\Server('file'); // Either redis, file or apcu. Leave empty for file based cache.
        $server->setUploadDir($basePath);
        $server->event()->addListener('tus-server.upload.complete', [$listener, 'postUploadOperation']);
        $response = $server->serve();
        $response->send();
        exit(0); // Exit from current PHP process.
    }

    protected function getConfig()
    {
        return [

            'cache' => [
                'redis' => [
                    'host' => getenv('REDIS_HOST') !== false ? getenv('REDIS_HOST') : '127.0.0.1',
                    'port' => getenv('REDIS_PORT') !== false ? getenv('REDIS_PORT') : '6379',
                    'database' => getenv('REDIS_DB') !== false ? getenv('REDIS_DB') : 0,
                ],

                /**
                 * File cache configs.
                 */
                'file' => [
                    'dir' => env('ROOT_PATH') . '.cache' . DIRECTORY_SEPARATOR,
                    'name' => 'php.server.cache',
                ],
            ],
            'storge' => [
                // 值为类名
                'type' => sysConfig('storage.type'), // File S3File ...
                'FileConfig' => [
                    'Path' => '/uploads/cloud_upload/'
                ],
                // 在S3File中使用的配置
                'S3FileConfig' => [
                    'credentials' => [
                        'key'    => sysConfig('s3Config.awsAccessKey'),
                        'secret' => sysConfig('s3Config.awsSecretKey'),
                    ],
                    'Bucket' => sysConfig('s3Config.bucket'),
                    'region' => sysConfig('s3Config.region'),
                    'version' => 'latest',
                    'endpoint' => sysConfig('s3Config.endpoint'),
                    'ACL'    => 'public-read',
                    'use_path_style_endpoint' => true,
                    // 保存路径
                    'Path' => '',
                ],
            ],
        ];
    }
}
