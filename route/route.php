<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
$url = config('app.url_domain_root');


Route::any('files', 'upload/upload/index');
Route::any('files/:key', 'upload/upload/index');
Route::get('/cloud/$','upload/Index/index');
Route::get('/cloud/show/$','upload/Index/show');
Route::get('/cloud/show/:page$','upload/Index/show')->pattern(['page'=>'[0-9]+']);
Route::get('/cloud/collection/$','upload/Index/collection');
Route::domain($url, function () {
	Route::get('/square/$','index/Index/blog');
	Route::get('/square/:page$','index/Index/blog')->pattern(['page'=>'[0-9]+']);
	Route::get('/$','index/Index/blog');
	Route::get('/:page$','index/Index/blog')->pattern(['page'=>'[0-9]+']);
	// Route::get('ajax', '/index/ajax/ajax/');
	// Route::get(':name','index/Ajax')->pattern(['name' => 'ajax']);
	// Route::get('/:name/$', 'index/Index')->pattern(['name' => '\w+(?!ajax)']);
	// Route::get('/index/ajax$', 'index/Ajax/index');
	Route::get('/login/$','index/User/login');
	Route::get('/register/$','index/User/register');
	Route::get('/forgot/$','index/User/forgot');
	Route::get('/logout/$','index/User/logout');

	Route::get('/:name/$', 'index/Index/index')->pattern(['name' => '\w+']);
	Route::get('/:name/:page$', 'index/Index/index')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
	Route::get('/:name/setting/$', 'index/Index/setting')->pattern(['name' => '\w+']);
	Route::get('/:name/setting/avatar/$', 'index/Index/avatar')->pattern(['name' => '\w+']);
	Route::get('/:name/setting/passwd/$', 'index/Index/passwd')->pattern(['name' => '\w+']);
	Route::get('/:name/setting/background/$', 'index/Index/background')->pattern(['name' => '\w+']);
	Route::get('/:name/fans/$', 'index/Index/fans')->pattern(['name' => '\w+']);
	Route::get('/:name/fans/:page$', 'index/Index/fans')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
	Route::get('/:name/concern/$', 'index/Index/concern')->pattern(['name' => '\w+']);
	Route::get('/:name/concern/:page$', 'index/Index/concern')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
	Route::get('/:name/own/$', 'index/Index/own')->pattern(['name' => '\w+']);
	Route::get('/:name/own/:page$', 'index/Index/own')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
	// Route::get('/:name/newrepeat/$', 'index/Index/newrepeat')->pattern(['name' => '\w+']);
	// Route::get('/:name/newcomment/$', 'index/Index/newcomment')->pattern(['name' => '\w+']);
	Route::get('/:name/newreply/$', 'index/Index/newreply')->pattern(['name' => '\w+']);
	Route::get('/:name/newreply/:page$', 'index/Index/newreply')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
	Route::get('/:name/message/:msg_id$', 'index/Index/messageInfo')->pattern(['name' => '\w+', 'msg_id' => '[0-9]+']);
	Route::get('/:name/del/message/:msg_id$', 'index/Ajax/delMessage')->pattern(['name' => '\w+', 'msg_id' => '[0-9]+']);	
});
Route::domain('aidigu.cn', function () {
	Route::get('/square/$','index/Index/blog');
	Route::get('/square/:page$','index/Index/blog')->pattern(['page'=>'[0-9]+']);
	Route::get('/$','index/Index/blog');
	Route::get('/:page$','index/Index/blog')->pattern(['page'=>'[0-9]+']);
	// Route::get('ajax', '/index/ajax/ajax/');
	// Route::get(':name','index/Ajax')->pattern(['name' => 'ajax']);
	// Route::get('/:name/$', 'index/Index')->pattern(['name' => '\w+(?!ajax)']);
	// Route::get('/index/ajax$', 'index/Ajax/index');
	Route::get('/login/$','index/User/login');
	Route::get('/register/$','index/User/register');
	Route::get('/forgot/$','index/User/forgot');
	Route::get('/logout/$','index/User/logout');

	Route::get('/:name/$', 'index/Index/index')->pattern(['name' => '\w+']);
	Route::get('/:name/:page$', 'index/Index/index')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
	Route::get('/:name/setting/$', 'index/Index/setting')->pattern(['name' => '\w+']);
	Route::get('/:name/setting/avatar/$', 'index/Index/avatar')->pattern(['name' => '\w+']);
	Route::get('/:name/setting/passwd/$', 'index/Index/passwd')->pattern(['name' => '\w+']);
	Route::get('/:name/setting/background/$', 'index/Index/background')->pattern(['name' => '\w+']);
	Route::get('/:name/fans/$', 'index/Index/fans')->pattern(['name' => '\w+']);
	Route::get('/:name/fans/:page$', 'index/Index/fans')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
	Route::get('/:name/concern/$', 'index/Index/concern')->pattern(['name' => '\w+']);
	Route::get('/:name/concern/:page$', 'index/Index/concern')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
	Route::get('/:name/own/$', 'index/Index/own')->pattern(['name' => '\w+']);
	Route::get('/:name/own/:page$', 'index/Index/own')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
	// Route::get('/:name/newrepeat/$', 'index/Index/newrepeat')->pattern(['name' => '\w+']);
	// Route::get('/:name/newcomment/$', 'index/Index/newcomment')->pattern(['name' => '\w+']);
	Route::get('/:name/newreply/$', 'index/Index/newreply')->pattern(['name' => '\w+']);
	Route::get('/:name/newreply/:page$', 'index/Index/newreply')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
	Route::get('/:name/message/:msg_id$', 'index/Index/messageInfo')->pattern(['name' => '\w+', 'msg_id' => '[0-9]+']);
	Route::get('/:name/del/message/:msg_id$', 'index/Ajax/delMessage')->pattern(['name' => '\w+', 'msg_id' => '[0-9]+']);	
});
Route::domain('m', function () {
	Route::get('/square/$','index/Index/blog');
	Route::get('/square/:page$','index/Index/blog')->pattern(['page'=>'[0-9]+']);
	Route::get('/$','wap/Index/blog');
	Route::get('/:page$','wap/Index/blog')->pattern(['page'=>'[0-9]+']);
	// Route::get('ajax', '/index/ajax/ajax/');
	// Route::get(':name','wap/Ajax')->pattern(['name' => 'ajax']);
	// Route::get('/:name/$', 'wap/Index')->pattern(['name' => '\w+(?!ajax)']);
	// Route::get('/index/ajax$', 'wap/Ajax/index');
	Route::get('/login/$','wap/User/login');
	Route::get('/register/$','wap/User/register');
	Route::get('/forgot/$','wap/User/forgot');
	Route::get('/logout/$','wap/User/logout');

	Route::get('/:name/$', 'wap/Index/index')->pattern(['name' => '\w+']);
	Route::get('/:name/:page$', 'wap/Index/index')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
	Route::get('/:name/setting/$', 'wap/Index/setting')->pattern(['name' => '\w+']);
	Route::get('/:name/setting/avatar/$', 'wap/Index/avatar')->pattern(['name' => '\w+']);
	Route::get('/:name/setting/passwd/$', 'wap/Index/passwd')->pattern(['name' => '\w+']);
	Route::get('/:name/fans/$', 'wap/Index/fans')->pattern(['name' => '\w+']);
	Route::get('/:name/fans/:page$', 'wap/Index/fans')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
	Route::get('/:name/concern/$', 'wap/Index/concern')->pattern(['name' => '\w+']);
	Route::get('/:name/concern/:page$', 'wap/Index/concern')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
	Route::get('/:name/own/$', 'wap/Index/own')->pattern(['name' => '\w+']);
	Route::get('/:name/own/:page$', 'wap/Index/own')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
	// Route::get('/:name/newrepeat/$', 'wap/Index/newrepeat')->pattern(['name' => '\w+']);
	// Route::get('/:name/newcomment/$', 'wap/Index/newcomment')->pattern(['name' => '\w+']);
	Route::get('/:name/newreply/$', 'wap/Index/newreply')->pattern(['name' => '\w+']);
	Route::get('/:name/newreply/:page$', 'wap/Index/newreply')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
	Route::get('/:name/message/:msg_id$', 'wap/Index/messageInfo')->pattern(['name' => '\w+', 'msg_id' => '[0-9]+']);
	Route::get('/:name/del/message/:msg_id$', 'wap/Ajax/delMessage')->pattern(['name' => '\w+', 'msg_id' => '[0-9]+']);	
});