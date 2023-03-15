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


}