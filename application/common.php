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
		$info = Db::name('user')->field('blog,uid,nickname,theme')->where('uid', $userid)->find();
	}
	return $info;
}
function checkUserCookie($rememberMe, $fields = 'uid,nickname,password,head_image,blog,status,uptime', $isCli = false)
{
	if (!$rememberMe) return false;
	$rememberMe = unserialize(unserialize($rememberMe));
	if (!isset($rememberMe['blog']) || !isset($rememberMe['nickname']) || !isset($rememberMe['uptime']) || !isset($rememberMe['password'])) {
		return false;
	}

	$info = Db::name('user')->where('blog', $rememberMe['blog'])->field($fields)->find();
	if (!$info) return false;
	if ($info['status'] != 0 || $info['nickname'] != $rememberMe['nickname'] || $rememberMe['password'] != encryptionPass($info['password'])) return false;
	if ($info['uptime'] - $rememberMe['uptime'] > 86400*60) return false;

	if (!$isCli) {
		$time = time();
		$rememberMe['uptime'] = $time;
		Db::name('user')->where('uid', $info['uid'])->update(['uptime' => $time]);
		cookie('rememberMe', serialize(serialize($rememberMe)), 86400*60);
		session('userid', $info['uid']);
	}
	
	return $info;
}
function getWsUserInfoByCookie($rememberMe)
{
	// cli模式不更新时间
	return checkUserCookie($rememberMe, 'uid,nickname,password,head_image,blog,status,uptime', true);
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

// 示例： {"media_info":"\/uploads\/longge\/message\/20210313\/dc2147c971bc0f287f7a585726fbabc8","media_type":"jpg"}
function getTypeImg($json, $type = '')
{
	$info = json_decode($json, true);
	$newType = $type ? '_' . $type : $type;
	return $info['media_info'] . $newType . '.' . $info['media_type'];
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

function isMobile()
{
	$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
	$mobile_agents = array(
		'iphone', 'android', 'phone', 'mobile', 'wap', 'netfront', 'java', 'opera mobi',
		'opera mini', 'ucweb', 'windows ce', 'symbian', 'series', 'webos', 'sony', 'blackberry', 'dopod',
		'nokia', 'samsung', 'palmsource', 'xda', 'pieplus', 'meizu', 'midp', 'cldc', 'motorola', 'foma',
		'docomo', 'up.browser', 'up.link', 'blazer', 'helio', 'hosin', 'huawei', 'novarra', 'coolpad',
		'techfaith', 'alcatel', 'amoi', 'ktouch', 'nexian', 'ericsson', 'philips', 'sagem', 'wellcom',
		'bunjalloo', 'maui', 'smartphone', 'iemobile', 'spice', 'bird', 'zte-', 'longcos', 'pantech',
		'gionee', 'portalmmm', 'jig browser', 'hiptop', 'benq', 'haier', '^lct', '320x320', '240x320',
		'176x220', 'windows phone', 'cect', 'compal', 'ctl', 'lg', 'nec', 'tcl', 'daxian', 'dbtel', 'eastcom',
		'konka', 'kejian', 'lenovo', 'mot', 'soutec', 'sgh', 'sed', 'capitel', 'panasonic', 'sonyericsson',
		'sharp', 'panda', 'zte', 'acer', 'acoon', 'acs-', 'abacho', 'ahong', 'airness', 'anywhereyougo.com',
		'applewebkit/525', 'applewebkit/532', 'asus', 'audio', 'au-mic', 'avantogo', 'becker', 'bilbo',
		'bleu', 'cdm-', 'danger', 'elaine', 'eric', 'etouch', 'fly ', 'fly_', 'fly-', 'go.web', 'goodaccess',
		'gradiente', 'grundig', 'hedy', 'hitachi', 'htc', 'hutchison', 'inno', 'ipad', 'ipaq', 'ipod',
		'jbrowser', 'kddi', 'kgt', 'kwc', 'lg ', 'lg2', 'lg3', 'lg4', 'lg5', 'lg7', 'lg8', 'lg9', 'lg-', 'lge-',
		'lge9', 'maemo', 'mercator', 'meridian', 'micromax', 'mini', 'mitsu', 'mmm', 'mmp', 'mobi', 'mot-',
		'moto', 'nec-', 'newgen', 'nf-browser', 'nintendo', 'nitro', 'nook', 'obigo', 'palm', 'pg-',
		'playstation', 'pocket', 'pt-', 'qc-', 'qtek', 'rover', 'sama', 'samu', 'sanyo', 'sch-', 'scooter',
		'sec-', 'sendo', 'sgh-', 'siemens', 'sie-', 'softbank', 'sprint', 'spv', 'tablet', 'talkabout',
		'tcl-', 'teleca', 'telit', 'tianyu', 'tim-', 'toshiba', 'tsm', 'utec', 'utstar', 'verykool', 'virgin',
		'vk-', 'voda', 'voxtel', 'vx', 'wellco', 'wig browser', 'wii', 'wireless', 'xde', 'pad', 'gt-p1000'
	);
	$ismobile = false;
	foreach ($mobile_agents as $device) {
		if (stristr($user_agent, $device)) {
			$ismobile = true;
			break;
		}
	}
	return $ismobile;
}

function handleMessage($message)
{
	$staticDomain = env('app.staticDomain', '');
	foreach ($message as &$data) {
		$data['media'] = $data['media'] ? $staticDomain . $data['media'] : '';
	}
	return $message;
}

function getPrefix()
{
	return config('database.prefix');
}

function getThemeInfo($theme)
{
	if (!$theme) {
		$themeInfo[0] = 'default';
		$themeInfo[1] = '/static/index/images/bg4.svg';
	} else {
		$themeInfo = explode(';', $theme);	
		if (count($themeInfo) < 2){
			$themeInfo[0] = 'default';
			$themeInfo[1] = $theme;
		}
	}
	
	return $themeInfo;
}
