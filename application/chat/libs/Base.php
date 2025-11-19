<?php
namespace app\chat\libs;


class Base
{
    public static function initMessageData($data, $listtagid = '')
    {
        $time = time();
        $data['create_time'] = date('Y-m-d H:i:s', $time);
        if (!$data['content_type']) return $data;
        if ($data['content_type'] == 'mp3') {
            // 简洁版音频播放器
            $data['content'] = '<p class="massageImg clear massageAudio"><audio id="music_' . (string)$time . '" class="simple-audio" controls preload="none" controlsList="nodownload"><source src="' . $data['content'] . '" type="audio/mpeg"></audio></p>';
        } elseif($data['content_type'] == 'mp4' || $data['content_type'] == 'm3u8') {
            $data['content'] = '<p  class="massageImg"><video id="video_' . (string)$time . '" width="200px"  controls=""  name="media" onplay="stopOther(this)"><source src="'.$data['content'].'" type="video/mp4"></video></p>';
        } else {
            if ($listtagid == 'PrivateLetter') {
                $data['content'] = '<img width="100px" class="massageImgCommon massageImg_jpg" onclick="showMessageImg(this)" src="/static/upload/images/jpg-1.svg" data-src="'. $data['content']. '">';
            } else {
                $data['content'] = '<img width="150px" class="massageImgCommon massageImg_jpg" onclick="showMessageImg(this)" src="' . $data['content'] . '">';
            }
            
        }
        return $data;
    }
}