<?php
namespace app\upload\listener;
use think\Db;

class Index
{
    //{"name":"winserver\u90e8\u7f72\u6587\u6863.docx","size":1292338,"offset":1292338,"checksum":"","location":"http:\/\/tus.test\/files\/aad076e4-e83e-444a-9ab0-397095eda37e","file_path":"\/home\/wwwroot\/default\/tus\/upload\/tus_file\/winserver\u90e8\u7f72\u6587\u6863.docx","metadata":{"relativePath":"null","name":"winserver\u90e8\u7f72\u6587\u6863.docx","type":"application\/vnd.openxmlformats-officedocument.wordprocessingml.document","filetype":"application\/vnd.openxmlformats-officedocument.wordprocessingml.document","filename":"winserver\u90e8\u7f72\u6587\u6863.docx"},"created_at":"Tue, 10 Dec 2019 19:11:02 GMT","expires_at":"Wed, 11 Dec 2019 19:11:02 GMT"}
    public function postUploadOperation(\app\upload\libs\Events\BaseEvent $event)
    {
        $fileMeta = $event->getFile()->details();
        // file_put_contents('error.txt', json_encode($fileMeta), FILE_APPEND);
        $typeArr = ['File' => 0, 'S3File' => 1];
        $storageConfig = config('upload.storage');
        $config = config('upload.storge');
        if ($config['type'] == 'S3File') {
            $fileMeta['file_path'] = rtrim($storageConfig['S3FileConfig']['endpoint'],'/').'/'.$storageConfig['S3FileConfig']['bucket'].'/'.$fileMeta['name'];
        }
        // $savePath = config('tus.uploadDir').'/'.$fileMeta['name'];
        $data['file_path'] = $fileMeta['file_path'];
        $data['file_name'] = $fileMeta['name'];
        $data['file_size'] = $fileMeta['size'];
        $data['type'] = $typeArr[$storageConfig['type']] ?? 0;
        // 过期时间
        // $data['expires_at'] = $fileMeta['expires_at'];
        $data['file_type'] = $fileMeta['metadata']['filetype'];
        // $data['userid'] = getLoginUid();
        $data['file_location'] = basename($fileMeta['location']);
        $data['create_time'] = date('Y-m-d H:i:s');
        $result = Db::name('file')->insert($data);
        // file_put_contents('error.txt', json_encode($fileMeta), FILE_APPEND);
        if (!$result) {
            file_put_contents('error.txt', json_encode($fileMeta), FILE_APPEND);
        }
    }
}