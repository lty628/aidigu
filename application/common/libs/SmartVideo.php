<?php

namespace app\common\libs;

class SmartVideo
{
    public static function parse($content)
    {
        // $content = preg_replace_callback('/<p>(?:(?:<a[^>]+>)?(?<video_url>(?:(http|https):\/\/)+[a-z0-9_\-\/\.\?%#=]+)(?:<\/a>)?)<\/p>/si', array(__CLASS__, 'parseCallback'), $content);
        // <p><a target="_blank" href="https://www.bilibili.com/video/BV14UUAYmExC/?spm_id_from=333.1007.tianma.1-1-1.click">121212</a><br/></p>
        $content = preg_replace_callback('/<p><a.*((http|https):\/\/+([a-z0-9_\-\.]+)(\/.*))\">(.*)<\/a><br\/><\/p>/si', array(__CLASS__, 'parseCallback'), $content);
        return $content;
    }

    public static function parseCallback($matches)
    {        
        // [0] => string(131) "<p><a target="_blank" href="https://www.bilibili.com/video/BV14UUAYmExC/?spm_id_from=333.1007.tianma.1-1-1.click">dddd</a><br/></p>"
        // [1] => string(84) "https://www.bilibili.com/video/BV14UUAYmExC/?spm_id_from=333.1007.tianma.1-1-1.click"
        // [2] => string(5) "https"
        // [3] => string(16) "www.bilibili.com"
        // [4] => string(60) "/video/BV14UUAYmExC/?spm_id_from=333.1007.tianma.1-1-1.click"
        // [5] => string(4) "dddd"
        $is_music = array('music.163.com');
        
        $site = $matches[3] ?? '';

        $providers = array(
            // video
            'www.youtube.com' => array(
                '#https?://www\.youtube\.com/watch\?v=(?<video_id>[a-z0-9_=\-]+)#i',
                'https://www.youtube.com/v/{video_id}',
                'https://www.youtube.com/embed/{video_id}',
            ),
            'youtu.be' => array(
                '#https?://youtu\.be/(?<video_id>[a-z0-9_=\-]+)#i',
                'https://www.youtube.com/v/{video_id}',
                'https://www.youtube.com/embed/{video_id}',
            ),
            'www.douyin.com' => array(
                '#https?://www\.douyin\.com/discover\?modal_id=(?<video_id>\d+)#i',
                '',
                'https:////open.douyin.com/player/video?vid={video_id}&autoplay=0',
            ),
            'www.bilibili.com' => array(
                '#https?://www\.bilibili\.com/video/(?:[av|BV]+)(?:(?<video_id1>[a-zA-Z0-9_=\-]+)/(?:index_|\#page=)(?<video_id2>[a-zA-Z0-9_=\-]+)|(?<video_id>[a-zA-Z0-9_=\-]+))#i',
                '',
                '//player.bilibili.com/player.html?aid={video_id}&bvid={video_id}&cid=&page=1&autoplay=0',
            ),
            'v.youku.com' => array(
                '#https?://v\.youku\.com/v_show/id_(?<video_id>[a-z0-9_=\-]+)#i',
                '',
                'https://player.youku.com/embed/{video_id}?client_id=d0b1b77a17cded3b',
            ),
            'v.qq.com' => array(
                '#https?://v\.qq\.com/(?:[a-z0-9_\./]+\?vid=(?<video_id>[a-z0-9_=\-]+)|(?:[a-z0-9/]+)/(?<video_id2>[a-z0-9_=\-]+))#i',
                '',
                'https://v.qq.com/iframe/player.html?vid={video_id}',
            ),
            'www.dailymotion.com' => array(
                '#https?://www\.dailymotion\.com/video/(?<video_id>[a-z0-9_=\-]+)#i',
                '',
                'https://www.dailymotion.com/embed/video/{video_id}',
            ),
            'www.acfun.cn' => array(
                '#https?://www\.acfun\.cn/v/ac(?<video_id>\d+)#i',
                '',
                'https://www.acfun.cn/player/ac{video_id}',
            ),
            'my.tv.sohu.com' => array(
                '#https?://my\.tv\.sohu\.com/us/(?:\d+)/(?<video_id>\d+)#i',
                '',
                'https://tv.sohu.com/upload/static/share/share_play.html#{video_id}_0_0_9001_0',
            ),
            'www.56.com' => array(
                '#https?://(?:www\.)?56\.com/[a-z0-9]+/(?:play_album\-aid\-[0-9]+_vid\-(?<video_id>[a-z0-9_=\-]+)|v_(?<video_id2>[a-z0-9_=\-]+))#i',
                '',
                'https://www.56.com/iframe/{video_id}',
            ),
            'www.wasu.cn' => array(
                '#https?://www\.wasu\.cn/play/show/id/(?<video_id>\d+)#i',
                '',
                'https://www.wasu.cn/Play/iframe/id/{video_id}',
            ),
            // music
            'music.163.com' => array(
                '#https?://music\.163\.com/\#/song\?id=(?<video_id>\d+)#i',
                '',
                'https://music.163.com/outchain/player?type=2&id={video_id}&auto=0&height=90',
            ),
        );
        $title = $matches[5] ?? '';
        if (!isset($providers[$site]) || !preg_match_all($providers[$site][0], $matches[0], $match)) {
            // return $matches[0];
            return '<p><a href="javascript:;">'.$title.'</a></p>';
        }
        
        $id = $match['video_id'][0] == '' ? $match['video_id2'][0] : $match['video_id'][0];
        $width = '100%';
        $height = '350px';
        if (in_array($site, $is_music)) {
            $height = '110px';
        }
    
        $url = str_replace('{video_id}', $id, $providers[$site][2]);
        $html = sprintf(
            '<iframe sandbox="allow-same-origin allow-scripts" src="%1$s" width="%2$s" height="%3$s" frameborder="0" allowfullscreen="true"></iframe>',
            $url,
            $width,
            $height
        );
        
        return '<p>'. $title .'</p><p><div id="typembed">' . $html . '</div></p>';
    }


    // private static function getIframe($url = '', $source = '', $width = '', $height = '')
    // {
    //     $html = '';
    //     $html .=
    //         '<div class="smartideo">
    //             <div class="player"' . self::getSizeStyle($width, $height) . '>
    //                 <iframe src="' . $url . '" width="100%" height="100%" frameborder="0" allowfullscreen="true"></iframe>
    //             </div>';
    //     if (empty($source)) {
    //         $source = 'javascript:void(0);';
    //     }
    //     $html .=
    //         '<div class="tips">
    //             <a href="' . $source . '" target="_blank" class="smartideo-tips" rel="nofollow"></a>
    //         </div>';
    //     $html .= '</div>';
    //     return $html;
    // }

    // private function get_link($url)
    // {
    //     $html = '';
    //     $html .=
    //         '<div class="smartideo">
    //             <div class="player"' . self::getSizeStyle(0, 0) . '>
    //                 <a href="' . $url . '" target="_blank" class="smartideo-play-link"><div class="smartideo-play-button"></div></a>
    //                 <p style="color: #999;margin-top: 50px;">暂时无法播放，可回源网站播放</p>
    //             </div>
    //         </div>';
    //     return $html;
    // }

    private static function isMobile()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $mobile_browser = array(
            "mqqbrowser", // 手机QQ浏览器
            "opera mobi", // 手机opera
            "juc",
            "iuc",
            'ucbrowser', // uc浏览器
            "fennec",
            "ios",
            "applewebKit/420",
            "applewebkit/525",
            "applewebkit/532",
            "ipad",
            "iphone",
            "ipaq",
            "ipod",
            "iemobile",
            "windows ce", // windows phone
            "240x320",
            "480x640",
            "acer",
            "android",
            "anywhereyougo.com",
            "asus",
            "audio",
            "blackberry",
            "blazer",
            "coolpad",
            "dopod",
            "etouch",
            "hitachi",
            "htc",
            "huawei",
            "jbrowser",
            "lenovo",
            "lg",
            "lg-",
            "lge-",
            "lge",
            "mobi",
            "moto",
            "nokia",
            "phone",
            "samsung",
            "sony",
            "symbian",
            "tablet",
            "tianyu",
            "wap",
            "xda",
            "xde",
            "zte"
        );
        $is_mobile = false;
        foreach ($mobile_browser as $device) {
            if (stristr($user_agent, $device)) {
                $is_mobile = true;
                break;
            }
        }
        return $is_mobile;
    }
}
