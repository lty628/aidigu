<?php


function ajaxJson($code, $msg, $data = [])
{
    return json(['code' => $code, 'msg' => $msg, 'data' => $data]);
}
//获取用户信息
function getLoginUserInfo()
{
	$userid = getLoginUid();
	$info = session('userInfo' . $userid);
	if (!$info) {
		$info = Db::name('user')->field('blog,uid')->where('uid', $userid)->find();
	}
	return $info;
}
function checkUserCookie($rememberMe)
{
	if (!$rememberMe) return false;
	$rememberMe = unserialize(unserialize($rememberMe));
	$info = Db::name('user')->where('blog', $rememberMe['blog'])->where('nickname', $rememberMe['nickname'])->where('password', $rememberMe['password'])->field('uid,blog')->find();
	if (!$info) return false;
	session('userid', $info['uid']);
	return $info;
}
function getLoginNickName()
{
	return getLoginUserInfo()['nickname'];
}
function getLoginUserName()
{
	return getLoginUserInfo()['username'];
}
function getLoginUid()
{
	return session('userid');
}
function getLoginMd5Uid()
{
	return md5(getLoginUid());
}

// tus上传路径
function getTusUploadFile($absolute = true)
{
	$path = '/' . trim(config('upload.storge.FileConfig.Path'), '/') . '/' . getLoginMd5Uid();
	$absolutePath = env('root_path') . 'public' . $path;
	if (!is_dir($absolutePath)) {
		mkdir($absolutePath);
	}
	return $absolute ? $absolutePath : $path;
	# code...
}
function setLoginUid($userid)
{
	session('userid', $userid);
}
function getLoginBlog()
{
	return getLoginUserInfo()['blog'];
}
function encryptionPass($password)
{
	return md5(sha1($password) . md5('!@#1%#$q'));
}
function getPerson($sex)
{
	return ['TA', '他', '她'][$sex];
}
function setMessageTime($value)
{
	if (!is_int($value)) return $value;
	$timeDifference = time() - $value;
	if ($timeDifference > 259200) {
		$returnTime = date('Y-m-d H:i:s', $value);
	} else if ($timeDifference < 60) {
		$returnTime = $timeDifference . '秒前';
	} else if ($timeDifference < 3600) {
		$returnTime = floor($timeDifference / 60) . '分钟前';
	} else if ($timeDifference < 86400) {
		$returnTime = floor($timeDifference / 3600) . '小时前';
	} else if ($timeDifference < 259200) {
		$returnTime = floor($timeDifference / 86400) . '天前';
	}
	return $returnTime;
}
function getReminderTypeName($type)
{
	$status = [0 => '转发', 1 => '评论', 2 => '回复'];
	return $status[$type];
}

// 示例： {"image_info":"\/uploads\/longge\/message\/20210313\/dc2147c971bc0f287f7a585726fbabc8","image_type":"jpg"}
function getTypeImg($json, $type = '')
{
	$info = json_decode($json, true);
	$newType = $type ? '_' . $type : $type;
	return $info['image_info'] . $newType . '.' . $info['image_type'];
}

function getFilesize($num)
{
	$p = 0;
	$format = 'bytes';
	if ($num > 0 && $num < 1024) {
		$p = 0;
		return number_format($num) . ' ' . $format;
	}
	if ($num >= 1024 && $num < pow(1024, 2)) {
		$p = 1;
		$format = 'KB';
	}
	if ($num >= pow(1024, 2) && $num < pow(1024, 3)) {
		$p = 2;
		$format = 'MB';
	}
	if ($num >= pow(1024, 3) && $num < pow(1024, 4)) {
		$p = 3;
		$format = 'GB';
	}
	if ($num >= pow(1024, 4) && $num < pow(1024, 5)) {
		$p = 4;
		$format = 'TB';
	}
	$num /= pow(1024, $p);
	return number_format($num, 2) . ' ' . $format;
}
