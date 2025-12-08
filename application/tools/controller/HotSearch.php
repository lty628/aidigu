<?php

namespace app\tools\controller;

use think\Controller;

class HotSearch extends Controller
{
    // 获取所有平台的热搜数据
    public function getAllHotSearch($limit = null)
    {
        $allData = [];

        // 获取百度热点
        $baiduData = $this->getBaiduHotSearch();
        if (!empty($baiduData)) {
            // 如果设置了限制数量，则只取前几条
            if ($limit !== null && is_numeric($limit)) {
                $baiduData = array_slice($baiduData, 0, $limit);
            }
            $allData[] = [
                'platform' => '百度',
                'title' => '百度热点指数',
                'data' => $baiduData
            ];
        }
        
        // 获取微博热搜
        $weiboData = $this->getWeiboHotSearch();
        if (!empty($weiboData)) {
            // 如果设置了限制数量，则只取前几条
            if ($limit !== null && is_numeric($limit)) {
                $weiboData = array_slice($weiboData, 0, $limit);
            }
            $allData[] = [
                'platform' => '微博',
                'title' => '微博热搜榜',
                'data' => $weiboData
            ];
        }
        
        
        // 获取哔哩哔哩热搜
        $bilibiliData = $this->getBilibiliHotSearch();
        if (!empty($bilibiliData)) {
            // 如果设置了限制数量，则只取前几条
            if ($limit !== null && is_numeric($limit)) {
                $bilibiliData = array_slice($bilibiliData, 0, $limit);
            }
            $allData[] = [
                'platform' => '哔哩哔哩',
                'title' => '哔哩哔哩热搜榜',
                'data' => $bilibiliData
            ];
        }
        
        // 获取抖音热搜
        $douyinData = $this->getDouyinHotSearch();
        if (!empty($douyinData)) {
            // 如果设置了限制数量，则只取前几条
            if ($limit !== null && is_numeric($limit)) {
                $douyinData = array_slice($douyinData, 0, $limit);
            }
            $allData[] = [
                'platform' => '抖音',
                'title' => '抖音热搜榜',
                'data' => $douyinData
            ];
        }
        
        // 获取历史上的今天
        $historyData = $this->getHistoryData();
        if (!empty($historyData)) {
            // 如果设置了限制数量，则只取前几条
            if ($limit !== null && is_numeric($limit)) {
                $historyData = array_slice($historyData, 0, $limit);
            }
            $allData[] = [
                'platform' => '历史上的今天',
                'title' => '历史上的今天',
                'data' => $historyData
            ];
        }
        
        // // 获取澎湃新闻热榜
        // $thepaperData = $this->getThePaperHotSearch();
        // if (!empty($thepaperData)) {
        //     // 如果设置了限制数量，则只取前几条
        //     if ($limit !== null && is_numeric($limit)) {
        //         $thepaperData = array_slice($thepaperData, 0, $limit);
        //     }
        //     $allData[] = [
        //         'platform' => '澎湃新闻',
        //         'title' => '澎湃新闻热榜',
        //         'data' => $thepaperData
        //     ];
        // }
        
        return $allData;
    }
    
    // 获取历史上的今天数据
    public function getHistoryData()
    {
        // 计算缓存过期时间（今日凌晨）
        $tomorrow = strtotime(date('Y-m-d', strtotime('+1 day')));
        $expireTime = $tomorrow - time();
        
        // 尝试从缓存中获取数据
        $cacheKey = 'history_data_' . date('md'); // 按月日作为缓存键
        $cachedData = cache($cacheKey);
        
        if ($cachedData !== false) {
            return $cachedData;
        }
        
        $month = date('m', time());
        $day = date('d', time());
        //获取接口数据
        $jsonRes = json_decode($this->Curl('https://baike.baidu.com/cms/home/eventsOnHistory/' . $month . '.json', null, null, "https://baike.baidu.com"), true);
        
        $tempArr = [];
        if (isset($jsonRes[$month][$month . $day])) {
            //统计当日总数
            // $countnum = count($jsonRes[$month][$month . $day]) - 1;
            foreach ($jsonRes[$month][$month . $day] as $k => $v) {
                $title = $v['year'] . '年-' . strip_tags(strip_tags($v['title']));
                // 移除标题中的#号
                $title = str_replace('#', '', $title);
                array_push($tempArr, [
                    'index' => $k + 1,
                    'title' => $title,
                    // 'url' => 'https://baike.baidu.com/item/' . urlencode(strip_tags($v['title'])),
                    // 'mobilUrl' => 'https://baike.baidu.com/item/' . urlencode(strip_tags($v['title']))
                ]);
            }
        }
        
        // 将数据存入缓存，缓存到今日凌晨
        cache($cacheKey, $tempArr, $expireTime);
        
        return $tempArr;
    }
    
    // 获取微博热搜数据
    public function getWeiboHotSearch()
    {
        $_md5 = md5(time());
        $cookie = "Cookie: {$_md5}:FG=1";
        $jsonRes = json_decode($this->Curl('https://weibo.com/ajax/side/hotSearch', null, $cookie, "https://s.weibo.com"), true);
        
        $tempArr = [];
        if (isset($jsonRes['data']['realtime'])) {
            foreach ($jsonRes['data']['realtime'] as $k => $v) {
                $title = $v['note'];
                // 移除标题中的#号
                $title = str_replace('#', '', $title);
                array_push($tempArr, [
                    'index' => $k + 1,
                    'title' => $title,
                    'hot' => round($v['num'] / 10000, 1) . '万',
                    'url' => "https://s.weibo.com/weibo?q=" . $v['note'] . "&Refer=index",
                    'mobilUrl' => "https://s.weibo.com/weibo?q=" . $v['note'] . "&Refer=index"
                ]);
            }
        }
        
        return $tempArr;
    }
    
    // 获取百度热点数据
    public function getBaiduHotSearch()
    {
        $_resHtml = str_replace(["\n", "\r", " "], '', $this->Curl('https://top.baidu.com/board?tab=realtime', null));
        preg_match('/<!--s-data:(.*?)-->/', $_resHtml, $_resHtmlArr);
        $jsonRes = json_decode($_resHtmlArr[1], true);
        
        $tempArr = [];
        if (isset($jsonRes['data']['cards'])) {
            foreach ($jsonRes['data']['cards'] as $v) {
                foreach ($v['content'] as $k => $_v) {
                    $title = $_v['word'];
                    // 移除标题中的#号
                    $title = str_replace('#', '', $title);
                    array_push($tempArr, [
                        'index' => $k + 1,
                        'title' => $title,
                        'desc' => $_v['desc'],
                        'pic' => $_v['img'],
                        'url' => $_v['url'],
                        'hot' => round($_v['hotScore'] / 10000, 1) . '万',
                        'mobilUrl' => $_v['appUrl']
                    ]);
                }
            }
        }
        return $tempArr;
    }
    
    // 获取哔哩哔哩热搜数据
    public function getBilibiliHotSearch()
    {
        $jsonRes = json_decode($this->Curl('https://app.bilibili.com/x/v2/search/trending/ranking', null, null, "https://www.bilibili.com"), true);
        $tempArr = [];
        if (isset($jsonRes['data']['list'])) {
            foreach ($jsonRes['data']['list'] as $k => $v) {
                $title = $v['keyword'];
                // 移除标题中的#号
                $title = str_replace('#', '', $title);
                array_push($tempArr, [
                    'index' => $v['position'],
                    'title' => $title,
                    'url' => 'https://search.bilibili.com/all?keyword=' . $v['keyword'] . '&order=click',
                    'mobilUrl' => 'https://search.bilibili.com/all?keyword=' . $v['keyword'] . '&order=click'
                ]);
            }
        }
        return $tempArr;
    }
    
    // 获取抖音热搜数据
    public function getDouyinHotSearch()
    {
        $jsonRes = json_decode($this->Curl('https://www.iesdouyin.com/web/api/v2/hotsearch/billboard/word/', null, null, "https://www.douyin.com"), true);
        $tempArr = [];
        if (isset($jsonRes['word_list'])) {
            foreach ($jsonRes['word_list'] as $k => $v) {
                $title = $v['word'];
                // 移除标题中的#号
                $title = str_replace('#', '', $title);
                array_push($tempArr, [
                    'index' => $k + 1,
                    'title' => $title,
                    'hot' => round($v['hot_value'] / 10000, 1) . '万',
                    'url' => 'https://www.douyin.com/search/' . urlencode($v['word']),
                    'mobilUrl' => 'https://www.douyin.com/search/' . urlencode($v['word'])
                ]);
            }
        }
        return $tempArr;
    }
    
    // 少数派 热榜 (保留方法但不在聚合中使用)
    public function getSspaiHotSearch()
    {
        $jsonRes = json_decode($this->Curl('https://sspai.com/api/v1/article/tag/page/get?limit=100000&tag=%E7%83%AD%E9%97%A8%E6%96%87%E7%AB%A0', null, null, "https://sspai.com"), true);
        $tempArr = [];
        if (isset($jsonRes['data'])) {
            foreach ($jsonRes['data'] as $k => $v) {
                $title = $v['title'];
                // 移除标题中的#号
                $title = str_replace('#', '', $title);
                array_push($tempArr, [
                    'index' => $k + 1,
                    'title' => $title,
                    'createdAt' => date('Y-m-d', $v['released_time']),
                    'other' => $v['author']['nickname'],
                    'like_count' => $v['like_count'],
                    'comment_count' => $v['comment_count'],
                    'url' => 'https://sspai.com/post/' . $v['id'],
                    'mobilUrl' => 'https://sspai.com/post/' . $v['id']
                ]);
            }
        }
        return $tempArr;
    }
    
    // 知乎热榜 (保留方法但不在聚合中使用)
    public function getZhihuHotSearch()
    {
        $jsonRes = json_decode($this->Curl('https://www.zhihu.com/api/v3/feed/topstory/hot-lists/total?limit=50&desktop=true', null, null, "https://www.zhihu.com"), true);
        $tempArr = [];
        if (isset($jsonRes['data'])) {
            foreach ($jsonRes['data'] as $k => $v) {
                $title = $v['target']['title'];
                // 移除标题中的#号
                $title = str_replace('#', '', $title);
                preg_match('/\d+/',  $v['detail_text'], $hot);
                array_push($tempArr, [
                    'index' => $k + 1,
                    'title' => $title,
                    'hot' => isset($hot[0]) ? $hot[0] . '万' : '',
                    'url' => 'https://www.zhihu.com/question/' . urlencode($v['target']['id']),
                    'mobilUrl' => 'https://www.zhihu.com/question/' . urlencode($v['target']['id'])
                ]);
            }
        }
        return $tempArr;
    }
    
    //百度百科  历史上的今天
    public function history()
    {
        $month = date('m', time());
        $day = date('d', time());
        //当前年月日
        $today = date('Y年m月d日');
        //获取接口数据
        $jsonRes = json_decode($this->Curl('https://baike.baidu.com/cms/home/eventsOnHistory/' . $month . '.json', null, null, "https://baike.baidu.com"), true);
        $tempArr = [];
        //统计当日总数
        $countnum = count($jsonRes[$month][$month . $day]) - 1;
        foreach ($jsonRes[$month][$month . $day] as $k => $v) {
            $title = $v['year'] . '年-' . strip_tags($v['title']);
            // 移除标题中的#号
            $title = str_replace('#', '', $title);
            array_push($tempArr, [
                'index' => $k + 1,
                'title' => $title,
                'url' => 'https://baike.baidu.com/item/' . urlencode($v['title']),
                'mobilUrl' => 'https://baike.baidu.com/item/' . urlencode($v['title'])
            ]);
        }
        $result = [
            'success' => true,
            'title' => '百度百科',
            'subtitle' => '历史上的今天',
            'update_time' => date('Y-m-d h:i:s', time()),
            'data' => $tempArr
        ];
        dump($result);
    }

    // 抖音 热搜榜
    public function douyin()
    {
        $jsonRes = json_decode($this->Curl('https://www.iesdouyin.com/web/api/v2/hotsearch/billboard/word/', null, null, "https://www.douyin.com"), true);
        $tempArr = [];
        foreach ($jsonRes['word_list'] as $k => $v) {
            $title = $v['word'];
            // 移除标题中的#号
            $title = str_replace('#', '', $title);
            array_push($tempArr, [
                'index' => $k + 1,
                'title' => $title,
                'hot' => round($v['hot_value'] / 10000, 1) . '万',
                'url' => 'https://www.douyin.com/search/' . urlencode($v['word']),
                'mobilUrl' => 'https://www.douyin.com/search/' . urlencode($v['word'])
            ]);
        }
        $result = [
            'success' => true,
            'title' => '抖音',
            'subtitle' => '热搜榜',
            'update_time' => date('Y-m-d h:i:s', time()),
            'data' => $tempArr
        ];
        dump($result);
    }

    // 哔哩哔哩 热搜榜
    public function bilibili_hot()
    {
        $jsonRes = json_decode($this->Curl('https://app.bilibili.com/x/v2/search/trending/ranking', null, null, "https://www.bilibili.com"), true);
        $tempArr = [];
        //return $jsonRes;
        foreach ($jsonRes['data']['list'] as $k => $v) {
            $title = $v['keyword'];
            // 移除标题中的#号
            $title = str_replace('#', '', $title);
            array_push($tempArr, [
                'index' => $v['position'],
                'title' => $title,
                'url' => 'https://search.bilibili.com/all?keyword=' . $v['keyword'] . '&order=click',
                'mobilUrl' => 'https://search.bilibili.com/all?keyword=' . $v['keyword'] . '&order=click'
            ]);
        }
        $result = [
            'success' => true,
            'title' => '哔哩哔哩',
            'subtitle' => '热搜榜',
            'update_time' => date('Y-m-d h:i:s', time()),
            'data' => $tempArr
        ];
        dump($result);
    }

    // 知乎热榜  热度
    public function zhihuHot()
    {
        $jsonRes = json_decode($this->Curl('https://www.zhihu.com/api/v3/feed/topstory/hot-lists/total?limit=50&desktop=true', null, null, "https://www.zhihu.com"), true);
        $tempArr = [];
        foreach ($jsonRes['data'] as $k => $v) {
            $title = $v['target']['title'];
            // 移除标题中的#号
            $title = str_replace('#', '', $title);
            preg_match('/\d+/',  $v['detail_text'], $hot);
            array_push($tempArr, [
                'index' => $k + 1,
                'title' => $title,
                'hot' => $hot[0] . '万',
                'url' => 'https://www.zhihu.com/question/' . urlencode($v['target']['id']),
                'mobilUrl' => 'https://www.zhihu.com/question/' . urlencode($v['target']['id'])
            ]);
        }
        $result = [
            'success' => true,
            'title' => '知乎热榜',
            'subtitle' => '热度',
            'update_time' => date('Y-m-d h:i:s', time()),
            'data' => $tempArr
        ];
        dump($result);
    }

    // 微博 热搜榜
    public function wbresou()
    {
        $_md5 = md5(time());
        $cookie = "Cookie: {$_md5}:FG=1";
        $jsonRes = json_decode($this->Curl('https://weibo.com/ajax/side/hotSearch', null, $cookie, "https://s.weibo.com"), true);
        $tempArr = [];
        foreach ($jsonRes['data']['realtime'] as $k => $v) {
            $title = $v['note'];
            // 移除标题中的#号
            $title = str_replace('#', '', $title);
            array_push($tempArr, [
                'index' => $k + 1,
                'title' => $title,
                'hot' => round($v['num'] / 10000, 1) . '万',
                'url' => "https://s.weibo.com/weibo?q=" . $v['note'] . "&Refer=index",
                'mobilUrl' => "https://s.weibo.com/weibo?q=" . $v['note'] . "&Refer=index"
            ]);
        }
        $result = [
            'success' => true,
            'title' => '微博',
            'subtitle' => '热搜榜',
            'update_time' => date('Y-m-d h:i:s', time()),
            'data' => $tempArr
        ];
        dump($result);
    }

    // 百度热点 指数
    public function baiduredian()
    {
        $_resHtml = str_replace(["\n", "\r", " "], '', $this->Curl('https://top.baidu.com/board?tab=realtime', null));
        preg_match('/<!--s-data:(.*?)-->/', $_resHtml, $_resHtmlArr);
        $jsonRes = json_decode($_resHtmlArr[1], true);
        //return $jsonRes;
        $tempArr = [];
        foreach ($jsonRes['data']['cards'] as $v) {
            foreach ($v['content'] as $k => $_v) {
                $title = $_v['word'];
                // 移除标题中的#号
                $title = str_replace('#', '', $title);
                array_push($tempArr, [
                    'index' => $k + 1,
                    'title' => $title,
                    'desc' => $_v['desc'],
                    'pic' => $_v['img'],
                    'url' => $_v['url'],
                    'hot' => round($_v['hotScore'] / 10000, 1) . '万',
                    'mobilUrl' => $_v['appUrl']
                ]);
            }
        }
        $result = [
            'success' => true,
            'title' => '百度热点',
            'subtitle' => '指数',
            'update_time' => date('Y-m-d h:i:s', time()),
            'data' => $tempArr
        ];
        dump($result);
    }

    // 澎湃新闻 热榜
    public function thepaper()
    {
        $jsonRes = json_decode($this->Curl('https://api.thepaper.cn/contentapi/nodeCont/getByChannelId?channelId=119908'), true);
        $tempArr = [];
        foreach ($jsonRes['data']['list'] as $k => $v) {
            $title = $v['name'];
            // 移除标题中的#号
            $title = str_replace('#', '', $title);
            array_push($tempArr, [
                'index' => $k + 1,
                'title' => $title,
                'url' => 'https://www.thepaper.cn/newsDetail_forward_' . $v['contId'],
                'mobilUrl' => 'https://www.thepaper.cn/newsDetail_forward_' . $v['contId']
            ]);
        }
        $result = [
            'success' => true,
            'title' => '澎湃新闻',
            'subtitle' => '热榜',
            'update_time' => date('Y-m-d h:i:s', time()),
            'data' => $tempArr
        ];
        dump($result);
    }
        // 获取澎湃新闻热榜数据
    public function getThePaperHotSearch()
    {
        // 澎湃新闻热榜API
        $jsonRes = json_decode($this->Curl('https://api.thepaper.cn/contentapi/nodeCont/getByChannelId?channelId=119908'), true);
        $tempArr = [];
        if (isset($jsonRes['data']['list'])) {
            foreach ($jsonRes['data']['list'] as $k => $v) {
                $title = $v['name'];
                // 移除标题中的#号
                $title = str_replace('#', '', $title);
                array_push($tempArr, [
                    'index' => $k + 1,
                    'title' => $title,
                    'url' => 'https://www.thepaper.cn/newsDetail_forward_' . $v['contId'],
                    'mobilUrl' => 'https://www.thepaper.cn/newsDetail_forward_' . $v['contId']
                ]);
            }
        }
        return $tempArr;
    }

private function Curl($url, $header = [
        "accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.9",
        "Accept-Encoding: gzip, deflate, br",
        "Accept-Language: zh-CN,zh;q=0.9",
        "Connection: keep-alive",
        "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1"
    ], $cookie = null, $refer = 'https://www.baidu.com')
    {
        $ip = rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255) . '.' . rand(0, 255);
        $header[] = "CLIENT-IP:" . $ip;
        $header[] = "X-FORWARDED-FOR:" . $ip;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url); //设置传输的 url
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header); //发送 http 报头
        curl_setopt($ch, CURLOPT_COOKIE, $cookie); //设置Cookie
        curl_setopt($ch, CURLOPT_REFERER,  $refer); //设置Referer
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate'); // 解码压缩文件
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
        curl_setopt($ch, CURLOPT_TIMEOUT, 5); // 设置超时限制防止死循环
        $output = curl_exec($ch);
        curl_close($ch);
        return $output;
    }
}

// $_type = isset($_GET['type']) ? $_GET['type'] : '';
// $API = new Api;

// switch ($_type) {
//   case 'baidu':
//     $_res = $API->baiduredian();
//     break;
//   case 'zhihu':
//     $_res = $API->zhihuHot();
//     break;
//   case 'weibo':
//     $_res = $API->wbresou();
//     break;
//   case 'bilihot':
//     $_res = $API->bilibili_hot();
//     break;
//   case 'biliall':
//     $_res = $API->bilibili_rankall();
//     break;
//   case 'douyin':
//     $_res = $API->douyin();
//     break;
//   case 'history':
//     $_res = $API->history();
//     break;
//   case 'csdn':
//     $_res = $API->csdn();
//     break;
//   case 'sspai':
//     $_res = $API->sspai();
//     break;
//   default:
//     $_res = ['success' => false, 'message' => '参数不完整'];
//     break;
// }
// $_res['copyright'] = '聚合热搜榜';
// exit(json_encode($_res,JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT));