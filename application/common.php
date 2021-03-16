<?php
//获取用户信息
function getLoginUserInfo()
{
	$userid = getLoginUid();
	$info = session('userInfo'.$userid);
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
	return md5(sha1($password).md5('!@#1%#$q'));
}
function getPerson($sex)
{
	return ['TA', '他', '她'][$sex];
}
function setMessageTime($value)
{
	if (!is_int($value)) return $value;
	$timeDifference = time()-$value;
	if ($timeDifference > 259200) {
		$returnTime = date('Y-m-d H:i:s', $value);
	} else if ($timeDifference < 60) {
		$returnTime = $timeDifference.'秒前';
	} else if ($timeDifference < 3600) {
		$returnTime = floor($timeDifference/60).'分钟前';
	} else if ($timeDifference < 86400) {
		$returnTime = floor($timeDifference/3600).'小时前';
	} else if ($timeDifference < 259200) {
		$returnTime = floor($timeDifference/86400).'天前';
	}
	return $returnTime;
}
function getReminderTypeName($type)
{
	$status = [0=>'转发',1=>'评论',2=>'回复'];
	return $status[$type];
}

// 示例： {"image_info":"\/uploads\/longge\/message\/20210313\/dc2147c971bc0f287f7a585726fbabc8","image_type":"jpg"}
function getTypeImg($json, $type = '')
{
	$info = json_decode($json, true);
	$newType = $type ? '_' . $type : $type;
	return $info['image_info'] . $newType . '.' . $info['image_type'];
}