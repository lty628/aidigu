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



Route::get('/article/$', 'cms/Article/list');
Route::get('/article/:page$', 'cms/Article/list')->pattern(['page'=>'[0-9]+']);
Route::get('/article/category/:category_name/$', 'cms/Article/list');
Route::get('/article/category/:category_name/:page$', 'cms/Article/list')->pattern(['page'=>'[0-9]+']);
Route::get('/article/add$', 'cms/Article/edit');
Route::get('/article/edit/:id$', 'cms/Article/edit')->pattern(['id' => '[0-9]+']);;
Route::get('/article/detail/:id$', 'cms/Article/detail')->pattern(['id' => '[0-9]+']);
Route::get('/article/link/$', 'cms/Article/link');


// 推广页
Route::domain(env('app.urlDomainRoot', 'aidigu.cn'), function () {
    Route::get('/', 'tools/home/index');
});

// 网盘
Route::any('files', 'upload/upload/index');
Route::any('files/:key', 'upload/upload/index');
Route::get('/cloud/$','upload/Index/index');
Route::get('/cloud/show/$','upload/Index/show');
// Route::get('/cloud/show/:page$','upload/Index/show')->pattern(['page'=>'[0-9]+']);
Route::get('/cloud/share/$','upload/Index/share');
// Route::get('/cloud/share/:page$','upload/Index/share')->pattern(['page'=>'[0-9]+']);
Route::get('/cloud/collection/$','upload/Index/collection');

// 聊天
Route::get('chat', 'chat/index/index');
Route::get('chat/private/:uid$', 'chat/index/index')->pattern(['uid'=>'[0-9]+']);
Route::get('chat/messageChatId/:msgid$', 'chat/index/index')->pattern(['msgid'=>'[0-9]+']);
Route::get('chat/channelMessageChatId/:chatmsgid$', 'chat/index/index')->pattern(['chatmsgid'=>'[0-9]+']);



Route::get('/admin/$', '/admin/admin/index');
// Route::get('/tools/$', 'index/index/tools');
// Route::get('/tools/movie/$', 'tools/movie/index');
// Route::get('/tools/onlinecar/$', 'tools/Onlinecar/index');

// 微博热门新闻页面
Route::get('/hotnews/$', 'index/Index/hotnews');

// 微博
if (isMobile()) {
	$module = 'm';
} else {
	$module = 'index';
}
Route::get('/tools/$',  $module . '/index/tools');
Route::get('/search/$',  $module . '/index/search');
Route::get('/search/:keyword/$',  $module . '/index/search')->pattern(['keyword'=>'.+']);
Route::get('/tools/in/$',  $module . '/index/tools?appType=1');
Route::get('/tools/out/$',  $module . '/index/tools?appType=2');

// 收藏
Route::get('/collect/$',$module . '/Index/collect');
Route::get('/collect/:page$', $module . '/Index/collect')->pattern(['page'=>'[0-9]+']);
Route::get('/:name/collect/$',$module . '/Index/collect');
Route::get('/:name/collect/:page$', $module . '/Index/collect')->pattern(['page'=>'[0-9]+']);

Route::get('/mytopic/$',$module . '/Index/myTopicList');
Route::get('/mytopic/:page$', $module . '/Index/myTopicList')->pattern(['page'=>'[0-9]+']);
Route::get('/topic/$',$module . '/Index/topicList');
Route::get('/topic/:page$', $module . '/Index/topicList')->pattern(['page'=>'[0-9]+']);
Route::get('/topic/:topic_id/$',$module . '/Index/topic')->pattern(['topic_id' => '[0-9]+']);
Route::get('/channel/$',$module . '/Index/channelList');
Route::get('/channel/myjoined/$',$module . '/Index/channelList');
Route::get('/channel/mycreated/$',$module . '/Index/channelList');
Route::get('/channel/:page$', $module . '/Index/channelList')->pattern(['page'=>'[0-9]+']);
Route::get('/channel/:base64$', $module . '/Index/channel')->pattern(['base64' => '.+']);
Route::get('/square/$',$module . '/Index/blog');
Route::get('/square/:page$',$module . '/Index/blog')->pattern(['page'=>'[0-9]+']);
Route::get('/remind/$',$module . '/Index/remind');
Route::get('/remind/:page$',$module . '/Index/remind')->pattern(['page'=>'[0-9]+']);
Route::get('/$',$module . '/Index/index');
Route::get('/:page$',$module . '/Index/index')->pattern(['page'=>'[0-9]+']);
// Route::get('ajax', '/index/ajax/ajax/');
// Route::get(':name',$module . '/Ajax')->pattern(['name' => 'ajax']);
// Route::get('/:name/$', $module . '/Index')->pattern(['name' => '\w+(?!ajax)']);
// Route::get('/index/ajax$', $module . '/Ajax/index');
Route::get('/login/$','index/User/login');
Route::get('/wxlogin/$','index/User/wxlogin');
Route::get('/register/$','index/User/register');
Route::get('/invite/$','index/User/invite');
Route::get('/forgot/$','index/User/forgot');
Route::get('/logout/$','index/User/logout');

Route::get('/setting/$', $module . '/Index/setting')->pattern(['name' => '\w+']);
Route::get('/setting/avatar/$', $module . '/Index/avatar')->pattern(['name' => '\w+']);
Route::get('/setting/passwd/$', $module . '/Index/passwd')->pattern(['name' => '\w+']);
Route::get('/setting/background/$', $module . '/Index/background')->pattern(['name' => '\w+']);

Route::get('/:name/info/$', $module . '/Index/info')->pattern(['name' => '\w+']);

Route::get('/:name/$', $module . '/Index/home')->pattern(['name' => '\w+']);
Route::get('/:name/:page$', $module . '/Index/home')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);

Route::get('/:name/fans/$', $module . '/Index/fans')->pattern(['name' => '\w+']);
Route::get('/:name/fans/:page$', $module . '/Index/fans')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
Route::get('/:name/concern/$', $module . '/Index/concern')->pattern(['name' => '\w+']);
Route::get('/:name/concern/:page$', $module . '/Index/concern')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
Route::get('/:name/own/$', $module . '/Index/own')->pattern(['name' => '\w+']);
Route::get('/:name/own/:page$', $module . '/Index/own')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
// Route::get('/:name/newrepeat/$', $module . '/Index/newrepeat')->pattern(['name' => '\w+']);
// Route::get('/:name/newcomment/$', $module . '/Index/newcomment')->pattern(['name' => '\w+']);
// Route::get('/:name/newreply/$', $module . '/Index/newreply')->pattern(['name' => '\w+']);
// Route::get('/:name/newreply/:page$', $module . '/Index/newreply')->pattern(['name' => '\w+', 'page'=>'[0-9]+']);
Route::get('/:name/message/:base64', $module . '/Index/messageInfo')->pattern(['name' => '\w+', 'base64' => '.+']);
Route::get('/:name/del/message/:msg_id', $module . '/Ajax/delMessage')->pattern(['name' => '\w+', 'msg_id' => '[0-9]+']);	
Route::get('/:name/del/channel_message/:msg_id', $module . '/Ajax/delChannelMessage')->pattern(['name' => '\w+', 'msg_id' => '[0-9]+']);	
// 绑定域名方式
// Route::domain($url, function () {

// });