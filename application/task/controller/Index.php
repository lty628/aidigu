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
        $argv = request()->server()['argv'] ?? [];
        $uid = $argv[2] ?? 0;
        $pageCount = $argv[3] ?? 0;
        if (!$uid || !$pageCount) exit('参数异常');

        $path = './uploads/'.md5($uid).'/aidigu';
        for ($i=$pageCount-1; $i > 0; $i--) { 
            dump('导入'.$i.'页');
            $str = file_get_contents($path . '/html/'.$i.'.html');
            preg_match('/<ul id="timeline".*>(.*)<\/ul>/isu', $str, $ul);
            preg_match_all('/<li.*>(.*)<\/li>/isU', $ul[0], $li);
            $arr = $li[1];
            $count = count($arr);
            for ($j=$count-1; $j >= 0; $j--) { 
                $this->pregData($arr[$j], $uid, $i);
            }
        }
    }

    protected function pregData($htmlStr, $uid, $page)
    {
        // '<div class="content"></div> <div class="pic"><a target="_blank" onclick="return hs.expand(this);" href="..\images\640x480_2638b0bd7cfe80fea2379cb2afd77ef8.jpg"><img src="..\images\100x75_2638b0bd7cfe80fea2379cb2afd77ef8.jpg" class="h_postimg" alt=""></a> </div>'
        preg_match('/<div class="content">([\s\S]*)/', $htmlStr, $content);
        preg_match('/class="source">(.*)<\/a><\/span>/', $htmlStr, $time);
        preg_match('/(640x480_[a-z0-9A-Z]*\.(png|jpg|JPEG|gif))/', $htmlStr, $image);
        dump($htmlStr);
        // dump($image);
        // dump($time[1]);
        // dump($content);
        $image = $image[1] ?? '';
        $data['ctime'] = strtotime($time[1]);
        $data['contents'] = trim(strip_tags($content[0]));
        if (!$data['contents'] && !$image) return false;
        if ($data['contents']) $data['contents'] = '<p>'.$data['contents'].'</p>';
        if ($image) {
            $typeArr = explode('.', $image);
            $ImageInfo['image_info'] = '/uploads/'.md5($uid).'/aidigu/images'.str_replace('.'.$typeArr[1], '', $image);
            $ImageInfo['image_type'] = $typeArr[1];
            $data['image'] = '/uploads/'.md5($uid).'/aidigu/images/'.$image;
            if (!file_exists('.'.$data['image'])) return false;
            $data['image_info'] = json_encode($ImageInfo, 320);
        }
        $data['uid'] = $uid;
        $data['refrom'] = '网站';
        $data['repost'] = '';
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


}