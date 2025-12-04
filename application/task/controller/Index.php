<?php
namespace app\task\controller;

use app\common\libs\CurlHelpers;
use think\Db;

class Index
{
    public function __construct()
    {
        // 是否命令行
        if (request()->isCli() == false) {
            die('非法访问');
        }
    }

    // 每日热点新闻采集
    public function hotToday()
    {

        $appKey = sysConfig('caiji.juheToutiaoAppkey');
        $uid = sysConfig('caiji.juheToutiaoToUid');

        if (!$appKey || !$uid) {
            dump('请配置juheToutiaoAppkey和juheToutiaoToUid');
            return;
        }

        $apiUrl = 'http://v.juhe.cn/toutiao/index';

        // 构造请求参数
        $params = [
            'type' => 'top', // 头条新闻
            'key' => $appKey
        ];

        // 拼接完整的请求 URL
        $fullUrl = $apiUrl . '?' . http_build_query($params);

        // 发起 HTTP GET 请求获取新闻数据
        $result = CurlHelpers::getInstance()->curlApiGet($fullUrl);

        // 解析返回的 JSON 数据
        $newsData = json_decode($result, true);

        if ($newsData && $newsData['error_code'] === 0 && isset($newsData['result']['data'])) {
            foreach ($newsData['result']['data'] as $news) {
                // 提取新闻标题和内容
                $title = $news['title'] ?? '';
                $content = $news['content'] ?? '';

                if ($title && $content) {
                    // 准备要保存到数据库的数据
                    $contents = $title. "<br/>". $content;

                    $this->saveMsg($uid, $contents, '');
                }
            }
            dump('热点新闻采集完成');
        } else {
            dump('未获取到有效新闻数据，错误信息: ' . ($newsData['reason'] ?? '未知错误'));
        }
    }
    
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
            $uid = mt_rand(73, 98);
            $this->saveMsg($uid, $content, $media);
        }
        dump('任务完成');
    }

    protected function saveMsg($uid, $contents, $mediaInfo)
    {

        if ($mediaInfo) {
            $data['media_info'] = json_encode($mediaInfo);
            $data['media'] = $mediaInfo[0]['href'] ?? '';
        }

        $data['uid'] = $uid;
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
        preg_match('/(640x480_[a-z0-9A-Z]*\.(png|jpg|JPEG|gif))/', $htmlStr, $media);
        dump($htmlStr);
        // dump($media);
        // dump($time[1]);
        // dump($content);
        $media = $media[1] ?? '';
        $data['ctime'] = strtotime($time[1]);
        $data['contents'] = trim(strip_tags($content[0]));
        if (!$data['contents'] && !$media) return false;
        if ($data['contents']) $data['contents'] = '<p>'.$data['contents'].'</p>';
        if ($media) {
            $typeArr = explode('.', $media);
            $mediaInfo['media_info'] = '/uploads/'.md5($uid).'/aidigu/images/'.str_replace('.'.$typeArr[1], '', $media);
            $mediaInfo['media_type'] = $typeArr[1];
            $data['media'] = '/uploads/'.md5($uid).'/aidigu/images/'.$media;
            if (!file_exists('.'.$data['media'])) return false;
            $data['media_info'] = json_encode($mediaInfo, 320);
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