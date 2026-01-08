<?php
namespace app\common\controller;
use app\common\model\User as UserModel;
use app\common\model\Fans;
use app\common\controller\Base;
use \think\Image;
/**
 * 设置
 */
class SettingInfo extends Base
{
	public function checkInfo2()
	{
		$type = input('get.type');
		$text = input('get.text');
		if ($type == 1) {
			$str = 'blog';
			$errorMsg = '账号已存在';
		}
		if ($type == 2) {
			$str = 'nickname';
			$errorMsg = '昵称已存在';
		}
		$result = UserModel::where($str,$text)->field('uid')->find();
		if ($result && $result['uid']!=getLoginUid()) return json(['status'=>0, 'msg'=>$errorMsg]);
		// if (UserModel::where($str,$text)->find()) return json(['status'=>0, 'msg'=>$errorMsg]);
		return json(['status'=>1, 'msg'=>'ok']);
	}

	public function changeInfo()
	{
		$data = input('get.info');
		
		// 验证输入数据
		$validate = new \think\Validate([
			'username'  => 'max:20',
			'blog'      => 'alphaNum|length:3,15',
			'nickname'  => 'chsAlphaNum|length:2,20',
			'phone'     => 'regex:/^1[3-9]\d{9}$/',
			'email'     => 'email|max:50',
			'sex'       => 'in:0,1,2',
			'province'  => 'chs|max:20',
			'city'      => 'chs|max:20',
			'intro'     => 'max:200',
			'invisible' => 'in:0,1'
		], [
			'username.max'      => '用户名长度不能超过20个字符',
			'blog.alphaNum'     => '账号只能为英文字母或数字',
			'blog.length'       => '账号长度必须在3-15个字符之间',
			'nickname.chsAlphaNum' => '昵称只能为中文、英文字母或数字',
			'nickname.length'   => '昵称长度必须在2-20个字符之间',
			'phone.regex'       => '手机号格式不正确',
			'email.email'       => '邮箱格式不正确',
			'email.max'         => '邮箱长度不能超过50个字符',
			'sex.in'            => '性别值不正确',
			'province.chs'      => '省份只能为中文',
			'city.chs'          => '城市只能为中文',
			'intro.max'         => '简介长度不能超过200个字符',
			'invisible.in'      => '隐私设置值不正确'
		]);
		
		if (!$validate->check($data)) {
			return json(['status'=>0, 'msg'=>$validate->getError()]);
		}
		
		// 防止XSS攻击，对特定字段进行过滤
		$allowFields = ['username','blog','nickname','phone','email','sex','province','city','intro','invisible'];
		$filteredData = [];
		foreach ($data as $key => $value) {
			if (in_array($key, $allowFields)) {
				// 过滤可能的XSS攻击内容
				if (is_string($value)) {
					$filteredData[$key] = htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
				} else {
					$filteredData[$key] = $value;
				}
			}
		}
		
		$user = new UserModel();
		$userid = getLoginUid();
		
		// 检查账号是否被其他用户使用
		if (isset($filteredData['blog']) && !empty($filteredData['blog'])) {
			$blogInfo = $user->where('blog', $filteredData['blog'])->field('uid')->find();
			if ($blogInfo && $blogInfo['uid'] != $userid) return json(['status'=>0, 'msg'=>'账号已存在']);
		}
		
		// 检查昵称是否被其他用户使用
		if (isset($filteredData['nickname']) && !empty($filteredData['nickname'])) {
			$nicknameInfo = $user->where('nickname', $filteredData['nickname'])->field('uid')->find();
			if ($nicknameInfo && $nicknameInfo['uid'] != $userid) return json(['status'=>0, 'msg'=>'昵称已存在']);
		}
		
		// 检查邮箱是否被其他用户使用
		if (isset($filteredData['email']) && !empty($filteredData['email'])) {
			$emailInfo = $user->where('email', $filteredData['email'])->field('uid')->find();
			if ($emailInfo && $emailInfo['uid'] != $userid) return json(['status'=>0, 'msg'=>'邮箱已存在']);
		}
		
		// 检查手机号是否被其他用户使用
		if (isset($filteredData['phone']) && !empty($filteredData['phone'])) {
			$phoneInfo = $user->where('phone', $filteredData['phone'])->field('uid')->find();
			if ($phoneInfo && $phoneInfo['uid'] != $userid) return json(['status'=>0, 'msg'=>'手机号已存在']);
		}
		
		$result = $user->allowField($allowFields)->save($filteredData, ['uid'=>$userid]);
		if (!$result) return json(['status'=>0, 'msg'=>'修改失败']);
		return json(['status'=>1, 'msg'=>'修改成功']);
	}
	public function uploadAvatar()
	{
		$path = 'uploads/'.getLoginMd5Uid().'/avatar';
		// 4m
		$size = sysConfig('app.picUploadSize', 4194304);
		$file = request()->file('file');
		// 移动到框架应用根目录/uploads/ 目录下
		$info = $file->validate(['size'=>$size,'ext'=>'jpg,jiff,bmp,jpeg,png,gif'])->move($path);
		// $info = $this->uploadImage($size, $path);
		if($info){
			// 成功上传后 获取上传信息
			$fileFath = $path.'/'.$info->getSaveName();
			$fileName = explode('.', $info->getSaveName())[0];
			$image = Image::open($fileFath);
			$image->thumb(50, 50,Image::THUMB_CENTER)->save($path.'/'.$fileName.'_small.'.$info->getExtension());
			$image = Image::open($fileFath);
			$image->thumb(100, 100,Image::THUMB_CENTER)->save($path.'/'.$fileName.'_middle.'.$info->getExtension());
			$image = Image::open($fileFath);
			$image->thumb(200, 200,Image::THUMB_CENTER)->save($path.'/'.$fileName.'_big.'.$info->getExtension());
			$data['media_info'] = '/'.$path.'/'.$fileName;
			$data['media_type'] = $info->getExtension();
			$data['media_size'] = $info->getSize();
			$data['small'] = '/'.$path.'/'.$fileName.'_small.'.$info->getExtension();
			$data['middle'] = '/'.$path.'/'.$fileName.'_middle.'.$info->getExtension();
			$data['big'] = '/'.$path.'/'.$fileName.'_big.'.$info->getExtension();
			$data['media_name'] = $info->getInfo()['name'];
			$data['media_mime'] = $info->getInfo()['type'];
			\app\common\libs\FileLog::add(getLoginUid(), 1, $info->getExtension(), $data);
			return json(['status'=>1, 'msg'=>'上传成功','data'=>$data]);
		}else{
			// 上传失败获取错误信息
			return json(['status'=>0, 'msg'=>$file->getError()]);
		}
	}

	public function uploadThumb()
	{
		$path = 'uploads/'.getLoginMd5Uid().'/thumb';
		// 4m
		$size = sysConfig('app.picUploadSize', 4194304);
		$file = request()->file('file');
		// 移动到框架应用根目录/uploads/ 目录下
		$info = $file->validate(['size'=>$size,'ext'=>'jpg,jiff,bmp,jpeg,png,gif'])->move($path);
		// $info = $this->uploadImage($size, $path);
		if($info){
			// 成功上传后 获取上传信息
			$fileFath = $path.'/'.$info->getSaveName();
			$fileName = explode('.', $info->getSaveName())[0];
			$image = Image::open($fileFath);
			$image->thumb(50, 50,Image::THUMB_CENTER)->save($path.'/'.$fileName.'_small.'.$info->getExtension());
			$image = Image::open($fileFath);
			$image->thumb(100, 100,Image::THUMB_CENTER)->save($path.'/'.$fileName.'_middle.'.$info->getExtension());
			$image = Image::open($fileFath);
			$image->thumb(200, 200,Image::THUMB_CENTER)->save($path.'/'.$fileName.'_big.'.$info->getExtension());
			$data['media_info'] = '/'.$path.'/'.$fileName;
			$data['media_type'] = $info->getExtension();
			$data['media_size'] = $info->getSize();
			$data['small'] = '/'.$path.'/'.$fileName.'_small.'.$info->getExtension();
			$data['middle'] = '/'.$path.'/'.$fileName.'_middle.'.$info->getExtension();
			$data['big'] = '/'.$path.'/'.$fileName.'_big.'.$info->getExtension();
			$data['media_name'] = $info->getInfo()['name'];
			$data['media_mime'] = $info->getInfo()['type'];
			\app\common\libs\FileLog::add(getLoginUid(), 8, $info->getExtension(), $data);
			return json(['status'=>1, 'msg'=>'上传成功','data'=>$data]);
		}else{
			// 上传失败获取错误信息
			return json(['status'=>0, 'msg'=>$file->getError()]);
		}
	}

	public function saveUpload()
	{
		$saveUpload = input('get.avatarVal');
		// dump($saveUpload);die;
		$saveUpload = json_decode($saveUpload, true);
		// dump($saveUpload);die;
		$info['media_info'] = $saveUpload['media_info'];
		$info['media_type'] = $saveUpload['media_type'];
		if (!isset($saveUpload['middle']) || !isset($saveUpload['media_info'])|| !isset($saveUpload['media_type'])) return json(['status'=>0, 'msg'=>'无数据']);
		
		$userid = getLoginUid();
		$result = UserModel::where('uid', $userid)->update([
			'head_image'=>$saveUpload['middle'],
			'head_image_info'=>json_encode($info),
		]);
		if (!$result) return json(['status'=>0, 'msg'=>'保存失败']);
		return json(['status'=>1, 'msg'=>'保存成功']);
	}

	public function saveTheme()
	{
		$backgroundImg = input('get.backgroundVal');
		$backgroundTheme = input('get.backgroundTheme');
		// dump($saveUpload);die;
		// // $saveUpload = json_decode($saveUpload, true);	
		// if (!$saveUpload || !isset($saveUpload['image_path'])) {
		// 	$saveUpload['image_path'] = '';
		// }
		$userid = getLoginUid();
		$result = UserModel::where('uid', $userid)->update([
			'theme'=>$backgroundTheme.';'.$backgroundImg,
		]);
		if (!$result) return json(['status'=>0, 'msg'=>'保存失败']);
		return json(['status'=>1, 'msg'=>'保存成功']);
	}
	
	public function msgInputImg()
	{
		$path = 'uploads/'.getLoginMd5Uid().'/message';
		// 4m
		$size = sysConfig('app.fileUploadSize', 62914561);
		$file = request()->file('file');
		// 移动到框架应用根目录/uploads/ 目录下
		$info = $file->validate(['size'=>$size,'ext'=>'mp4,mp3,jiff,jpg,bmp,jpeg,png,gif'])->move($path);
		// $info = $this->uploadImage($size, $path);
		if($info){
			// 成功上传后 获取上传信息
			$fileName = explode('.', $info->getSaveName())[0];
			$data['media_info'] = '/'.$path.'/'.$fileName;
			$data['media_type'] = $info->getExtension();
			$data['media_size'] = $info->getSize();
			$data['media_name'] = $info->getInfo()['name'];
			$data['media_mime'] = $info->getInfo()['type'];
			\app\common\libs\FileLog::add(getLoginUid(), 2, $info->getExtension(), $data);
			return json(['status'=>1, 'msg'=>'上传成功','data'=>$data]);
		}else{
			// 上传失败获取错误信息
			return json(['status'=>0, 'msg'=>$file->getError()]);
		}
	}

	public function chatMessage()
	{
		$path = 'uploads/'.getLoginMd5Uid().'/chatMessage';
		// 4m
		$size = sysConfig('app.fileUploadSize', 62914561);
		$file = request()->file('file');
		// 移动到框架应用根目录/uploads/ 目录下
		$info = $file->validate(['size'=>$size,'ext'=>'mp4,mp3,jiff,jpg,bmp,jpeg,png,gif'])->move($path);
		// $info = $this->uploadImage($size, $path);
		if($info){
			// 成功上传后 获取上传信息
			$fileName = explode('.', $info->getSaveName())[0];
			$data['media_info'] = '/'.$path.'/'.$fileName;
			$data['media_type'] = $info->getExtension();
			$data['media_size'] = $info->getSize();
			$data['media_name'] = $info->getInfo()['name'];
			$data['media_mime'] = $info->getInfo()['type'];
			\app\common\libs\FileLog::add(getLoginUid(), 3, $info->getExtension(), $data);
			return json(['status'=>1, 'msg'=>'上传成功','data'=>$data]);
		}else{
			// 上传失败获取错误信息
			return json(['status'=>0, 'msg'=>$file->getError()]);
		}
	}

	public function sourcematerial()
	{
		$path = 'uploads/'.getLoginMd5Uid().'/sourcematerial';
		// 4m
		$size = sysConfig('app.fileUploadSize', 62914561);
		$file = request()->file('file');
		// 移动到框架应用根目录/uploads/ 目录下
		$info = $file->validate(['size'=>$size,'ext'=>'mp4,mp3,jiff,jpg,bmp,jpeg,png,gif,pdf,zip,rar,7z,gz,tar.gz,xls,xlsx,txt,doc,docx,ppt,pptx'])->move($path);
		// $info = $file->validate(['size'=>$size,'ext'=>'mp4,jiff,jpg,bmp,jpeg,png,gif'])->move($path);
		// $info = $this->uploadImage($size, $path);
		if($info){
			// 成功上传后 获取上传信息
			$fileName = explode('.', $info->getSaveName())[0];
			$data['media_info'] = '/'.$path.'/'.$fileName;
			$data['media_type'] = $info->getExtension();
			$data['media_size'] = $info->getSize();
			$data['media_name'] = $info->getInfo()['name'];
			$data['media_mime'] = $info->getInfo()['type'];
			\app\common\libs\FileLog::add(getLoginUid(), 4, $info->getExtension(), $data);
			return json(['status'=>1, 'msg'=>'上传成功','data'=>$data]);
		}else{
			// 上传失败获取错误信息
			return json(['status'=>0, 'msg'=>$file->getError()]);
		}
	}
	

	public function uploadImage()
	{
		$path = 'uploads/'.getLoginMd5Uid().'/theme';
		// 4m
		$size = sysConfig('app.picUploadSize', 4194304);
		$file = request()->file('file');
		// 移动到框架应用根目录/uploads/ 目录下
		$info = $file->validate(['size'=>$size,'ext'=>'jpg,jiff,bmp,jpeg,png,gif'])->move($path);
		// $info = $this->uploadImage($size, $path);
		if($info){
			// 成功上传后 获取上传信息
			$fileFath = $path.'/'.$info->getSaveName();
			$fileName = explode('.', $info->getSaveName())[0];
			$data['image_path'] = '/'.$fileFath;
			$data['media_info'] = '/'.$path.'/'.$fileName;
			$data['media_type'] = $info->getExtension();
			$data['media_size'] = $info->getSize();
			$data['media_name'] = $info->getInfo()['name'];
			$data['media_mime'] = $info->getInfo()['type'];
			\app\common\libs\FileLog::add(getLoginUid(), 5, $info->getExtension(), $data);
			return json(['status'=>1, 'msg'=>'上传成功','data'=>$data]);
		}else{
			// 上传失败获取错误信息
			return json(['status'=>0, 'msg'=>$file->getError()]);
		}
		// // 获取表单上传文件 例如上传了001.jpg
		// $file = request()->file('file');
		// // 移动到框架应用根目录/uploads/ 目录下
		// $info = $file->validate(['size'=>$size,'ext'=>'jpg,bmp,jpeg,png,gif'])->move($path);
		// if (!$info) $file->getError();
	}
	public function passwd()
	{
		$info = input('get.info');
		$oldpw = trim($info['oldpw']);
		$newpw = trim($info['newpw']);
		$oldPassword = UserModel::where('uid', getLoginUid())->field('password')->find();
		if ($oldPassword['password'] != encryptionPass($oldpw)) return json(['status'=>0, 'msg'=>'旧密码不正确']);
		$result = UserModel::where('uid', getLoginUid())->update(['password'=>encryptionPass($newpw)]);
		if (!$result) return json(['status'=>0, 'msg'=>'修改密码失败']);
		return json(['status'=>1, 'msg'=>'修改密码成功']);
	}

	public function upNavIcon()
	{
		$path = 'uploads/'.getLoginMd5Uid().'/icon';
		// 4m
		$size = 1194304;
		$file = request()->file('file');
		// 移动到框架应用根目录/uploads/ 目录下
		$info = $file->validate(['size'=>$size,'ext'=>'jpg,jiff,bmp,jpeg,png,gif'])->move($path);
		// $info = $this->uploadImage($size, $path);
		if($info){
			// 成功上传后 获取上传信息
			$fileName = explode('.', $info->getSaveName())[0];
			$data['media_info'] = '/'.$path.'/'.$fileName;
			$data['media_type'] = $info->getExtension();
			$data['media_size'] = $info->getSize();
			$data['media_name'] = $info->getInfo()['name'];
			$data['media_mime'] = $info->getInfo()['type'];
			\app\common\libs\FileLog::add(getLoginUid(), 7, $info->getExtension(), $data);
			return json(['status'=>1, 'msg'=>'上传成功','data'=>$data]);
		}else{
			// 上传失败获取错误信息
			return json(['status'=>0, 'msg'=>$file->getError()]);
		}
	}

}