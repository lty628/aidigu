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
// Route::get('think', function () {
//     return 'hello,ThinkPHP5!';
// });
Route::get('/$','index/Index/blog');
Route::get('/page/:page$','index/Index/blog')->pattern(['page'=>'[0-9]+']);
// Route::get('ajax', '/index/ajax/ajax/');
// Route::get(':name','index/Ajax')->pattern(['name' => 'ajax']);
// Route::get('/:name/$', 'index/Index')->pattern(['name' => '\w+(?!ajax)']);
// Route::get('/index/ajax$', 'index/Ajax/index');
Route::get('/login/$','index/User/login');
Route::get('/register/$','index/User/register');
Route::get('/forgot/$','index/User/forgot');
Route::get('/logout/$','index/User/logout');

Route::get('/:name/$', 'index/Index/index')->pattern(['name' => '\w+']);
Route::get('/:name/page/:page$', 'index/Index/index')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
Route::get('/:name/setting/$', 'index/Index/setting')->pattern(['name' => '\w+']);
Route::get('/:name/setting/avatar/$', 'index/Index/avatar')->pattern(['name' => '\w+']);
Route::get('/:name/setting/passwd/$', 'index/Index/passwd')->pattern(['name' => '\w+']);
Route::get('/:name/fans/$', 'index/Index/fans')->pattern(['name' => '\w+']);
Route::get('/:name/fans/page/:page$', 'index/Index/fans')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
Route::get('/:name/concern/$', 'index/Index/concern')->pattern(['name' => '\w+']);
Route::get('/:name/concern/page/:page$', 'index/Index/concern')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
Route::get('/:name/own/$', 'index/Index/own')->pattern(['name' => '\w+']);
Route::get('/:name/own/page/:page$', 'index/Index/own')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
// Route::get('/:name/newrepeat/$', 'index/Index/newrepeat')->pattern(['name' => '\w+']);
// Route::get('/:name/newcomment/$', 'index/Index/newcomment')->pattern(['name' => '\w+']);
Route::get('/:name/newreply/$', 'index/Index/newreply')->pattern(['name' => '\w+']);
Route::get('/:name/newreply/page/:page$', 'index/Index/newreply')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
Route::get('/:name/message/:msg_id$', 'index/Index/messageInfo')->pattern(['name' => '\w+', 'msg_id' => '[0-9]+']);
Route::get('/:name/del/message/:msg_id$', 'index/Ajax/delMessage')->pattern(['name' => '\w+', 'msg_id' => '[0-9]+']);

// Route::get('hello/:name', 'index/hello');

// return [

// ];
