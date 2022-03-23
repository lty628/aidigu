<?php
namespace app\upload\controller;
use think\Controller;
use think\Db;
// use app\upload\libs\Upload;

class Upload extends Controller
{
    public function __construct()
    {
        \app\upload\libs\Config::set(env('CONFIG_PATH').'upload.php');
    }
    public function index()
    {
        $listener = new \app\upload\listener\Index();
        $basePath = config('upload.storge.FileConfig.Path');
        $server   = new \app\upload\libs\Server('file'); // Either redis, file or apcu. Leave empty for file based cache.
        $server->setUploadDir($basePath);
        $server->event()->addListener('tus-server.upload.complete', [$listener, 'postUploadOperation']);
        $response = $server->serve();
        $response->send();
        exit(0); // Exit from current PHP process.
    }

}
