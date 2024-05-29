<?php

namespace app\tools\controller;

use think\Controller;

class HotSearch
{
  // 少数派 热榜
  public function sspai()
  {
    $jsonRes = json_decode($this->Curl('https://sspai.com/api/v1/article/tag/page/get?limit=100000&tag=%E7%83%AD%E9%97%A8%E6%96%87%E7%AB%A0', null, null, "https://sspai.com"), true);
    $tempArr = [];
    foreach ($jsonRes['data'] as $k => $v) {
      array_push($tempArr, [
        'index' => $k + 1,
        'title' => $v['title'],
        'createdAt' => date('Y-m-d', $v['released_time']),
        'other' => $v['author']['nickname'],
        'like_count' => $v['like_count'],
        'comment_count' => $v['comment_count'],
        'url' => 'https://sspai.com/post/' . $v['id'],
        'mobilUrl' => 'https://sspai.com/post/' . $v['id']
      ]);
    }
    $result = [
      'success' => true,
      'title' => '少数派',
      'subtitle' => '热榜',
      'update_time' => date('Y-m-d h:i:s', time()),
      'data' => $tempArr
    ];
    dump($result);
  }

  // CSDN 头条榜
  // public function csdn()
  // {
  //   $_resHtml = $this->Curl('https://www.csdn.net', null, "User-Agent: Mozilla/5.0 (iPhone; CPU iPhone OS 10_3_1 like Mac OS X) AppleWebKit/603.1.30 (KHTML, like Gecko) Version/10.0 Mobile/14E304 Safari/602.1", "https://www.csdn.net");
  //   preg_match('/window.__INITIAL_STATE__=(.*?);<\/script>/', $_resHtml, $_resHtmlArr);
  //   dump($_resHtml);die;
  //   $jsonRes = json_decode($_resHtmlArr[1], true);
  //   $tempArr = [];
  //   //头条
  //   foreach ($jsonRes['pageData']['data']['Headimg'] as $k => $v) {
  //     array_push($tempArr, [
  //       'index' => $k + 1,
  //       'title' => $v['title'],
  //       'url' => $v['url'],
  //       'mobilUrl' => $v['url']
  //     ]);
  //   }
  //   //头条1
  //   foreach ($jsonRes['pageData']['data']['www-Headlines'] as $k => $v) {
  //     array_push($tempArr, [
  //       'index' => $k + 17,
  //       'title' => $v['title'],
  //       'url' => $v['url'],
  //       'mobilUrl' => $v['url']
  //     ]);
  //   }
  //   //头条2
  //   foreach ($jsonRes['pageData']['data']['www-headhot'] as $k => $v) {
  //     array_push($tempArr, [
  //       'index' => $k + 48,
  //       'title' => $v['title'],
  //       'url' => $v['url'],
  //       'mobilUrl' => $v['url']
  //     ]);
  //   }
  //   $result = [
  //     'success' => true,
  //     'title' => 'CSDN',
  //     'subtitle' => '头条榜',
  //     'update_time' => date('Y-m-d h:i:s', time()),
  //     'data' => $tempArr
  //   ];
  //   dump($result);
  // }

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
      array_push($tempArr, [
        'index' => $k + 1,
        'title' => $v['year'] . '年-' . strip_tags($v['title']),
        'url' => 'https://www.douyin.com/search/' . urlencode($v['title']),
        'mobilUrl' => 'https://www.douyin.com/search/' . urlencode($v['title'])
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
      array_push($tempArr, [
        'index' => $k + 1,
        'title' => $v['word'],
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

  // 哔哩哔哩 全站日榜
  // public function bilibili_rankall()
  // {
  //   $jsonRes = json_decode($this->Curl('https://api.bilibili.com/x/web-interface/ranking/v2?rid=0&type=all', null, null, "https://www.bilibili.com"), true);
  //   $tempArr = [];
  //   foreach ($jsonRes['data']['list'] as $k => $v) {
  //     array_push($tempArr, [
  //       'index' => $k + 1,
  //       'title' => $v['title'],
  //       'pic' => $v['pic'],
  //       'desc' => $v['desc'],
  //       'hot' => round($v['stat']['view'] / 10000, 1) . '万',
  //       'url' => $v['short_link'],
  //       'mobilUrl' => $v['short_link']
  //     ]);
  //   }
  //   $result = [
  //     'success' => true,
  //     'title' => '哔哩哔哩',
  //     'subtitle' => '全站日榜',
  //     'update_time' => date('Y-m-d h:i:s', time()),
  //     'data' => $tempArr
  //   ];
  //   dump($result);
  // }

  // 哔哩哔哩 热搜榜
  public function bilibili_hot()
  {
    $jsonRes = json_decode($this->Curl('https://app.bilibili.com/x/v2/search/trending/ranking', null, null, "https://www.bilibili.com"), true);
    $tempArr = [];
    //return $jsonRes;
    foreach ($jsonRes['data']['list'] as $k => $v) {
      array_push($tempArr, [
        'index' => $v['position'],
        'title' => $v['keyword'],
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
      preg_match('/\d+/',  $v['detail_text'], $hot);
      array_push($tempArr, [
        'index' => $k + 1,
        'title' => $v['target']['title'],
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
      array_push($tempArr, [
        'index' => $k + 1,
        'title' => $v['note'],
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
        array_push($tempArr, [
          'index' => $k + 1,
          'title' => $_v['word'],
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
