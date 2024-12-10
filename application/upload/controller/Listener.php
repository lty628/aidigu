<?php
namespace app\upload\controller;
use think\Db;
use Tus;

class Listener
{
    public function index()
    {
        $fileMeta = input('post.fileInfo');
        $uploadURL = input('post.uploadURL');
        // $key = basename($uploadURL);
        $uid = getLoginUid();
        $result = Db::name('file')->where('userid', $uid)->where('file_location', $uploadURL)->find();
        if ($result) {
            return json(['code'=>1]);
        }
        $typeArr = ['File' => 0, 'S3File' => 1];
        $storageConfig = config('upload.storge');
        if ($storageConfig['type'] == 'S3File') {
            $fileMeta['file_path'] = rtrim($storageConfig['S3FileConfig']['endpoint'],'/').'/'.$storageConfig['S3FileConfig']['bucket'].'/'.$fileMeta['name'];
        } else {
            $data['file_path'] = $fileMeta['file_path'] ?? getTusUploadFile(false).'/'.$fileMeta['name'];
        }
        // $savePath = config('tus.uploadDir').'/'.$fileMeta['name'];
        $data['type'] = $typeArr[$storageConfig['type']] ?? 0;
        $data['file_name'] = $fileMeta['name'];
        $data['file_size'] = $fileMeta['size'];
        // 过期时间
        $data['file_type'] = $fileMeta['type'];
        $data['userid'] = $uid;
        $data['file_location'] = $uploadURL;
        $data['create_time'] = date('Y-m-d H:i:s');
        $result = Db::name('file')->insert($data);
        if (!$result) {
            file_put_contents('error.txt', json_encode($fileMeta), FILE_APPEND);
            return json(['code'=>0]); 
        }

        \app\common\libs\FileLog::add($uid, 6, $data['file_type'], [
            'media_info' => $data['file_path'],
			'media_type' => $data['type'],
			'media_size' => $fileMeta['size'],
			'media_name' => $fileMeta['file_name'],
        ]);
        return json(['code'=>1]); 
    }
}