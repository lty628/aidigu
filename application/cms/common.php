<?php

function formatTime($sTime, $formt = 'Y-m-d H:i:s')
{
    $sTime = strtotime($sTime);
    //sTime=源时间，cTime=当前时间，dTime=时间差
    $cTime = time();
    $dTime = $cTime - $sTime;
    $dDay = intval(date('z', $cTime)) - intval(date('z', $sTime));
    $dYear = intval(date('Y', $cTime)) - intval(date('Y', $sTime));

    //n秒前，n分钟前，n小时前，日期
    if ($dTime < 60) {
        if ($dTime < 10) {
            return '刚刚';
        } else {
            return intval(floor($dTime / 10) * 10) . '秒前';
        }
    } elseif ($dTime < 3600) {
        return intval($dTime / 60) . '分钟前';
    } elseif ($dTime >= 3600 && $dDay == 0) {
        return intval($dTime / 3600) . '小时前';
    } elseif ($dDay > 0 && $dDay <= 7) {
        return intval($dDay) . '天前';
    } elseif ($dDay > 7 &&  $dDay <= 30) {
        return intval($dDay / 7) . '周前';
    } elseif ($dDay > 30) {
        return intval($dDay / 30) . '个月前';
    } elseif ($dYear == 0) {
        return date('m月d日', $sTime);
    } else {
        return date($formt, $sTime);
    }
}

 /**
 * 截取html字符串
 *
 * @param $s 字符串
 * @param $zi 长度
 * @param $ne 没有结束符的html标签
 * @return string
 */
function htmlCut($s, $len, $ne = ',br,hr,input,img,') {
    $s = preg_replace('/\s{2,}/', ' ', $s);
    $os = preg_split('/<[\S\s]+?>/', $s);
    preg_match_all('/<[\S\s]+?>/', $s, $or);
    $s = '';
    $tag = array();
    foreach ($os as $k => $v) {
        if ($v != '' && $v != ' ') {
            $l = strlen($v);
            for ($i = 0; $i < $l; $i++) {
                if (ord($v[$i]) > 127) {
                    $s.=$v[$i] . $v[++$i] . $v[++$i];
                } else {
                    $s.=$v[$i];
                }
                $len--;
                if ($len < 1) {
                    break 2;
                }
            }
        }
        preg_match('/<\/?([^\s>]+)[\s>]{1}/', $or[0][$k], $t);
        $s.=$or[0][$k];
        if (strpos($ne, ',' . strtolower($t[1]) . ',') === false && $t[1] != '' && $t[1] != ' ') {
            $k = array_search('</' . $t[1] . '>', $tag);
            if ($k !== false) {
                unset($tag[$k]);
            } else {
                array_unshift($tag, '</' . $t[1] . '>');
            }
        }
    }
 
    return $s . '...<a href="/login.html">登录后显示所有</a>' . implode('', $tag);
}

/**
 * 自动截取网页长度
 */
function cutAutoLen($html)
{
    // 总长度
    $len = mb_strlen(strip_tags($html));
    // 长度过小或者已登录显示所有
    if ($len < 200 || getUserIdd()) {
        return $html;
    } else {
        $subLen = ceil($len / 3);
        return htmlCut($html, $subLen);
    }
    
}   


/**
 * 跨域设置
 * allowOrigin 数组（允许的域名）
 */
function setOrigin($originConfig = [])
{
    if ($originConfig['allow'] != true) {
        return;
    }
    $allowOrigin = $originConfig['domain'];
    header('Access-Control-Allow-Credentials: true');
    if (is_string($allowOrigin)) {
        header('Access-Control-Allow-Origin:' . $allowOrigin);
        return;
    }
    $origin = $_SERVER['HTTP_ORIGIN'] ?: '';
    if (in_array($origin, $allowOrigin)) {
        header('Access-Control-Allow-Origin:' . $origin);
    } else {
        return;
    }
    header('Access-Control-Allow-Headers: Authorization,Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With');
    header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE');
    header('Access-Control-Max-Age: 1728000');
    if(request()->isOptions()){
        exit();
    }
}

// 默认头像
function getRegisterHeadImage()
{
    // '/static/index/media/avatars/150-1.jpg' 到 '/static/index/media/avatars/150-26.jpg',
    return '/static/index/media/avatars/150-'. mt_rand(1, 26) .'.jpg';
}

function getUserIdd()
{
    return getLoginUid();
}

function getUserInfo()
{
    return session('userInfo');
}