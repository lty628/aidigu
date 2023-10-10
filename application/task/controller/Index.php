<?php
namespace app\task\controller;

use app\common\libs\CurlHelpers;
use think\Db;

class Index
{
    public function test()
    {
        $id = request()->server()['argv'][2] ?? 0;
        if (!$id) {
            exit('参数异常');
        }

        $url = 'https://www.xxxxx.net/MjAyMi8xLzE2/v1/tweet/timeline?cursor='.$id;

        $result = CurlHelpers::getInstance()->curlApiGet($url);
        dump($result);
        $result = json_decode($result, true);



        if (!isset($result['items'])) exit('无数据');
        foreach ($result['items'] as $val) {
            $content = $val['content'];
            $media = $val["ext"]['media'] ?? '';
            $this->saveMsg($content, $media);
        }
        dump('任务完成');
    }

    protected function saveMsg($contents, $imageInfo)
    {

        if ($imageInfo) {
            $data['image_info'] = json_encode($imageInfo);
            $data['image'] = $imageInfo[0]['href'] ?? '';
        }

        $data['uid'] = mt_rand(73, 98);
        $data['refrom'] = '网站';
        $data['repost'] = '';
        $data['ctime'] = time();
        $data['contents'] = $contents;
        Db::startTrans();
        try {
            $data['msg_id'] = Db::name('message')->insertGetId($data);
            $msgId = (int)input('get.msg_id');
            if ($msgId) {
                Db::name('message')->where('msg_id', $msgId)->setInc('repostsum',1);
            }
            Db::name('user')->where('uid', $data['uid'])->setInc('message_sum', 1);
            // 提交事务
            Db::commit();
            return $data;
        } catch (\Exception $e) {
            // 回滚事务
            dump($e);
            Db::rollback();
            return false;
        }
    }

    public function imporAidigu()
    {
        $path = './tmp/stayreal/';
        $path = 'uploads/'.getLoginMd5Uid().'/theme';
        $str = file_get_contents($path . '/html/a1.html');
        preg_match('/<ul id="timeline".*>(.*)<\/ul>/isu', $str, $ul);
        preg_match_all('/<li.*>(.*)<\/li>/isU', $ul[0], $li);

        $arr = $li[1];

        $count = count($arr);
        for ($i=$count-1; $i >= 0; $i--) { 
            $data = $this->pregData($arr[$i]);
        }
    }

    protected function pregData($htmlStr)
    {
        // preg_match('/<div class="content">(.*)<\/div>/', $htmlStr, $content);
        // preg_match('/class="source">(.*)<\/a><\/span>/', $htmlStr, $time);
        preg_match('/(640x480_.*png)/', $htmlStr, $image);
        dump($htmlStr);
        dump($image);die;
        dump($time[1]);
        dump($content[1]);die;
        $image = $image[1] ?? '';
        $data['ctime'] = strtotime($time[1]);
        $data['contents'] = $content[1];
        if ($image) {

            $data['image_info'] = ;
            $data['image'] = '/upload/';
        }

    }


}