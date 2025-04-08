<?php
namespace app\common\libs;

use think\Db;

class FileLog
{
    // type 1头像，2微博，3聊天，4素材，5主题, 6网盘, 7icon
    // media_type 1图片，2视频，3音频，4其他
    // \app\common\model\FileLog::add();
    public static function add($uid, $type, $mediaType, $fileInfo)
    {
        // 'media_info' = $fileMeta['file_path'],
        // 'media_type' = $fileMeta['type'],
        // 'media_size' = $fileMeta['size'],
        // $data['media_name'] = $info->getInfo()['name'];
	    // $data['media_mime'] = $info->getInfo()['type'];
        if ($type != 6) {
            // $mimeInfo = self::getApplication($mediaType);
            $mimeInfo = $fileInfo['media_mime'];
            $mediaInfo = $fileInfo['media_info'].'.'.$fileInfo['media_type'];
        } else {
            $mimeInfo = $mediaType;
            // 获取链接的后缀
            $mediaType = pathinfo($fileInfo['media_info'], PATHINFO_EXTENSION);
            $mediaInfo = $fileInfo['media_info'];
        }
        
        return Db::name('file_log')->insert([
            'uid' => $uid,
            'type' => $type,
            'media_type' => $mediaType,
            'media_size' => $fileInfo['media_size'],
            'media_mime' => $mimeInfo,
            'media_info' => $mediaInfo,
            'media_name' => $fileInfo['media_name'],
            'create_time' => time()
        ]);
    }

    public static function getList($uid, $type, $limit = 5)
    {
        return Db::name('file_log')->where('uid', $uid)->where('type', $type)->order('create_time desc')->limit($limit)->select();    
    }

    protected static function getApplication($type)
    {
        $mimeTypes = array(
            'gif' => 'image/gif',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpe' => 'image/jpeg',
            'bmp' => 'image/bmp',
            'png' => 'image/png',
            'tif' => 'image/tiff',
            'tiff' => 'image/tiff',
            'pict' => 'image/x-pict',
            'pic' => 'image/x-pict',
            'pct' => 'image/x-pict',
            'tif' => 'image/tiff',
            'tiff' => 'image/tiff',
            'psd' => 'image/x-photoshop',
            'swf' => 'application/x-shockwave-flash',
            'js' => 'application/x-javascript',
            'pdf' => 'application/pdf',
            'ps' => 'application/postscript',
            'eps' => 'application/postscript',
            'ai' => 'application/postscript',
            'wmf' => 'application/x-msmetafile',
            'css' => 'text/css',
            'htm' => 'text/html',
            'html' => 'text/html',
            'txt' => 'text/plain',
            'xml' => 'text/xml',
            'wml' => 'text/wml',
            'wbmp' => 'image/vnd.wap.wbmp',
            'mid' => 'audio/midi',
            'wav' => 'audio/wav',
            'mp3' => 'audio/mpeg',
            'mp2' => 'audio/mpeg',
            'avi' => 'video/x-msvideo',
            'mpeg' => 'video/mpeg',
            'mpg' => 'video/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'lha' => 'application/x-lha',
            'lzh' => 'application/x-lha',
            'z' => 'application/x-compress',
            'gtar' => 'application/x-gtar',
            'gz' => 'application/x-gzip',
            'gzip' => 'application/x-gzip',
            'tgz' => 'application/x-gzip',
            'tar' => 'application/x-tar',
            'bz2' => 'application/bzip2',
            'zip' => 'application/zip',
            'arj' => 'application/x-arj',
            'rar' => 'application/x-rar-compressed',
            'hqx' => 'application/mac-binhex40',
            'sit' => 'application/x-stuffit',
            'bin' => 'application/x-macbinary',
            'uu' => 'text/x-uuencode',
            'uue' => 'text/x-uuencode',
            'latex'=> 'application/x-latex',
            'ltx' => 'application/x-latex',
            'tcl' => 'application/x-tcl',
            'pgp' => 'application/pgp',
            'asc' => 'application/pgp',
            'exe' => 'application/x-msdownload',
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'mdb' => 'application/x-msaccess',
            'wri' => 'application/x-mswrite',
        );
        return $mimeTypes[$type]?? 'application/octet-stream';
    }
}