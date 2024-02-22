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

// 网盘
Route::any('files', 'upload/upload/index');
Route::any('files/:key', 'upload/upload/index');
Route::get('/cloud/$','upload/Index/index');
Route::get('/cloud/show/$','upload/Index/show');
Route::get('/cloud/show/:page$','upload/Index/show')->pattern(['page'=>'[0-9]+']);
Route::get('/cloud/collection/$','upload/Index/collection');

// 聊天
Route::get('chat', 'chat/index/index');
Route::get('chat/private/:uid$', 'chat/index/index')->pattern(['uid'=>'[0-9]+']);

// Route::get('/tools/$', 'index/index/tools');
// Route::get('/tools/movie/$', 'tools/movie/index');
// Route::get('/tools/onlinecar/$', 'tools/Onlinecar/index');

// 微博
if (isMobile()) {
	$module = 'm';
} else {
	$module = 'index';
}
Route::get('/tools/$',  $module . '/index/tools');
Route::get('/mytopic/$',$module . '/Index/myTopicList');
Route::get('/mytopic/:page$', $module . '/Index/myTopicList')->pattern(['page'=>'[0-9]+']);
Route::get('/topic/$',$module . '/Index/topicList');
Route::get('/topic/:page$', $module . '/Index/topicList')->pattern(['page'=>'[0-9]+']);
Route::get('/topic/:topic_id/$',$module . '/Index/topic')->pattern(['topic_id' => '[0-9]+']);
Route::get('/square/$',$module . '/Index/blog');
Route::get('/square/:page$',$module . '/Index/blog')->pattern(['page'=>'[0-9]+']);
Route::get('/$',$module . '/Index/blog');
Route::get('/:page$',$module . '/Index/blog')->pattern(['page'=>'[0-9]+']);
// Route::get('ajax', '/index/ajax/ajax/');
// Route::get(':name',$module . '/Ajax')->pattern(['name' => 'ajax']);
// Route::get('/:name/$', $module . '/Index')->pattern(['name' => '\w+(?!ajax)']);
// Route::get('/index/ajax$', $module . '/Ajax/index');
Route::get('/login/$','index/User/login');
Route::get('/register/$','index/User/register');
Route::get('/invite/$','index/User/invite');
Route::get('/forgot/$','index/User/forgot');
Route::get('/logout/$','index/User/logout');

Route::get('/setting/$', $module . '/Index/setting')->pattern(['name' => '\w+']);
Route::get('/setting/avatar/$', $module . '/Index/avatar')->pattern(['name' => '\w+']);
Route::get('/setting/passwd/$', $module . '/Index/passwd')->pattern(['name' => '\w+']);
Route::get('/setting/background/$', $module . '/Index/background')->pattern(['name' => '\w+']);

Route::get('/:name/info/$', $module . '/Index/info')->pattern(['name' => '\w+']);

Route::get('/:name/$', $module . '/Index/index')->pattern(['name' => '\w+']);
Route::get('/:name/:page$', $module . '/Index/index')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);

Route::get('/:name/fans/$', $module . '/Index/fans')->pattern(['name' => '\w+']);
Route::get('/:name/fans/:page$', $module . '/Index/fans')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
Route::get('/:name/concern/$', $module . '/Index/concern')->pattern(['name' => '\w+']);
Route::get('/:name/concern/:page$', $module . '/Index/concern')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
Route::get('/:name/own/$', $module . '/Index/own')->pattern(['name' => '\w+']);
Route::get('/:name/own/:page$', $module . '/Index/own')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
// Route::get('/:name/newrepeat/$', $module . '/Index/newrepeat')->pattern(['name' => '\w+']);
// Route::get('/:name/newcomment/$', $module . '/Index/newcomment')->pattern(['name' => '\w+']);
Route::get('/:name/newreply/$', $module . '/Index/newreply')->pattern(['name' => '\w+']);
Route::get('/:name/newreply/:page$', $module . '/Index/newreply')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
Route::get('/:name/message/:msg_id', $module . '/Index/messageInfo')->pattern(['name' => '\w+', 'msg_id' => '[0-9]+']);
Route::get('/:name/del/message/:msg_id', $module . '/Ajax/delMessage')->pattern(['name' => '\w+', 'msg_id' => '[0-9]+']);	

// 绑定域名方式
// Route::domain($url, function () {

// });
