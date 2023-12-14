<?php

namespace app\command;

// use app\chat\libs\Chat;

use Swoole\WebSocket\Server;

use think\console\Command;
use think\console\Input;
use think\console\Output;


use think\console\input\Argument;
// use think\console\input\Option;

class ChatServer extends Command
{
    public $server;

    protected function configure()
    {
        // 指令配置
        $this->setName('chatserver');
        $this->setName('chatserver')
            ->addArgument('worker_num', Argument::OPTIONAL, "进程数(默认： 8)")
            ->addArgument('port', Argument::OPTIONAL, "端口(默认： 9501)")
            ->addArgument('ip', Argument::OPTIONAL, "ip(默认： 127.0.0.1)")
            // ->addOption('city', null, Option::VALUE_REQUIRED, 'city name')
            ->setDescription('聊天服务');
        // 设置参数

    }

    protected function execute(Input $input, Output $output)
    {
        $port = trim($input->getArgument('port'));
        $ip = trim($input->getArgument('ip'));
        $workerNum = trim($input->getArgument('worker_num'));
      	$port = $port ?: '9501';
      	$ip = $ip ?: '127.0.0.1';
      	$workerNum = $workerNum ?: '8';
        // 初始化fd
        \app\chat\libs\ChatDbHelper::initFd();
        $this->initServer($ip, $port, $workerNum);
    }

    public function onOpen($server, $request)
    {
        $loginUserInfo = getWsUserInfoByCookie($request->cookie['rememberMe']);
        // dump($loginUserInfo);
        $uid = $loginUserInfo['uid'];
        \app\common\libs\Remind::clean($uid);
        // 好友列表
        $friendList = \app\chat\libs\TagInfo::getTagInfo($uid);
        $data['fd'] = $request->fd;
        $data['uid'] = $uid;
        $data['online_time'] = time();
        \app\chat\libs\ChatDbHelper::upOnlineUserStatus(['uid' => $uid], $data);
        $server->push($request->fd, json_encode([
            'code' => 1,
            'msg' => 'success',
            'data' => $loginUserInfo
        ], 320));
        $server->push($request->fd, json_encode($friendList, 320));

        // $server->push($request->fd, '{"code":4,"msg":"success","data":{"mine":0,"listtags":[{"listtagid":"a","listtagname":"\u5510"},{"listtagid":"b","listtagname":"\u4f2f"},{"listtagid":"c","listtagname":"\u864e"},{"listtagid":"d","listtagname":"\u70b9"},{"listtagid":"e","listtagname":"\u79cb"},{"listtagid":"f","listtagname":"\u9999"}],"users":{"a":[{"fd":7,"name":"bookapi","avatar":"http:\/\/127.0.0.1:8081\/static\/images\/avatar\/f1\/f_7.jpg","email":"3528@qq.com","time":"10:31","listtagid":"a"}],"b":[],"c":[],"d":[],"e":[],"f":[]}}}');
        // $server->push($request->fd, '{"code":4,"msg":"success","data":{"mine":0,"listtags":[{"listtagid":"1","listtagname":"我的好友"},{"listtagid":"2","listtagname":"我的群聊"}],"users":{"1":[{"fd":7,"name":"bookapi","avatar":"/uploads/c81e728d9d4c2f636f067f89cc14862c/avatar/20231121/dbdc4662fc214ba89ada1ab7088905b7_middle.jpeg","email":"3528@qq.com","time":"10:31","listtagid":"a"}],"2":[]}}}');
        // $server->push($request->fd, '{"code":1,"msg":"bookapi\u52a0\u5165\u4e86\u7fa4\u804a","data":{"listtagid":"1","fd":2,"name":"bookapi","avatar":"/static/index/images/noavatar_small.gif","time":"07:59","mine":1}}');
        // $server->push($request->fd, '{"code":1,"msg":"sasdfasf\u52a0\u5165\u4e86\u7fa4\u804a","data":{"listtagid":"1","fd":5,"name":"sasdfasf","avatar":"/static/index/images/noavatar_small.gif","time":"08:18","mine":1}}');
        // $server->push($request->fd, '{"code":2,"msg":"","data":{"listtagid":"1","fd":2,"name":"bookapi","avatar":"/static/index/images/noavatar_small.gif","newmessage":"1111","remains":null,"time":"08:04","mine":1}}');
        // $server->push($request->fd, '{"code":2,"msg":"","data":{"listtagid":"1","fd":4,"name":"sdfasdf","avatar":"/static/index/images/noavatar_small.gif","newmessage":"sdfdsaf","remains":null,"time":"08:16","mine":0}}');

        // $data = array(
        //     'task' => 'open',
        //     'fd' => $request->fd
        // );

        
        // $this->server->task(json_encode($data));
        // echo "open\n";
    }

    public function onMessage($server, $frame)
    {
        if ($frame->data == 'ping') {
            $server->push($frame->fd, 'pong');
        } else {
            $frameData = json_decode($frame->data, true);
            $funcArr = [
                'none',
                'login',
                // 在线聊天
                'chatOnline',
                // 聊天记录
                'chatHistory',
            ];
            // $frameData['listtagid'] Friends PrivateLetter Group
            $className = '\\app\\chat\\libs\\'.$frameData['listtagid'];
            $func = $funcArr[$frameData['type']];
            $className::{$func}($server,$frame, $frameData);
        }
        
    }
    public function onTask($server, $task_id, $from_id, $data)
    {
        // $pushMsg = array('code' => 0, 'msg' => '', 'data' => array());
        // $data = json_decode($data, true);
        // switch ($data['task']) {
        //     case 'open':
        //         $pushMsg = Chat::open($data);
        //         $this->server->push($data['fd'], json_encode($pushMsg));
        //         return 'Finished';
        //     case 'login':
        //         $pushMsg = Chat::doLogin($data);
        //         break;
        //     case 'new':
        //         $pushMsg = Chat::sendNewMsg($data);
        //         break;
        //     case 'logout':
        //         $pushMsg = Chat::doLogout($data);
        //         break;
        //     case 'nologin':
        //         $pushMsg = Chat::noLogin($data);
        //         $this->server->push($data['fd'], json_encode($pushMsg));
        //         return "Finished";
        //     case 'change':
        //         $pushMsg = Chat::change($data);
        //         break;
        // }
        // $this->sendMsg($pushMsg, $data['fd']);
        // return "Finished";
    }

    public function onClose($server, $fd)
    {

        \app\chat\libs\ChatDbHelper::upOnlineUserStatus(['fd' => $fd], [
            'offline_time' => time(),
            'fd' => 0
        ]);
        // $pushMsg = array('code' => 0, 'msg' => '', 'data' => array());
        // //获取用户信息
        // $user = Chat::logout("", $fd);
        // if ($user) {
        //     $data = array(
        //         'task' => 'logout',
        //         'params' => array(
        //             'name' => $user['name']
        //         ),
        //         'fd' => $fd
        //     );
        //     $this->server->task(json_encode($data));
        // }

        // echo "client {$fd} closed\n";
    }

    public function sendMsg($pushMsg, $myfd)
    {
        foreach ($this->server->connections as $fd) {
            if ($fd === $myfd) {
                $pushMsg['data']['mine'] = 1;
            } else {
                $pushMsg['data']['mine'] = 0;
            }
            $this->server->push($fd, json_encode($pushMsg));
        }
    }


    public function onFinish($server, $task_id, $data)
    {
        echo "Task {$task_id} finish\n";
        echo "Result: {$data}\n";
    }


    public function onRequest($request, $response)
    {
        foreach ($this->server->connections as $fd) {
            // 需要先判断是否是正确的websocket连接，否则有可能会push失败
            if ($this->server->isEstablished($fd)) {
                $this->server->push($fd, $request->get['message']);
            }
        }
    }


    public function initServer($ip, $port, $workerNum)
    {
        $this->server = new Server($ip, $port);
        $this->server->set(array(
            'task_worker_num'     =>  $workerNum
        ));
        $this->server->on("open", array($this, "onOpen"));
        $this->server->on("message", array($this, "onMessage"));
        $this->server->on("request", array($this, "onRequest"));
        $this->server->on("Task", array($this, "onTask"));
        $this->server->on("Finish", array($this, "onFinish"));
        $this->server->on("close", array($this, "onClose"));
        // $this->server->on('open', function (Server $server, $request) {
        //     $this->onOpen($server, $request);

        //     // 图片附件
        //     // {"type":2,"name":"hand","avatar":"http://127.0.0.1:8081/static/images/avatar/f1/f_4.jpg","message":"http://192.168.56.133/uploads/20231201081945_860.jpeg","c":"img","listtagid":"a"}
        //     // {"code":2,"msg":"","data":{"listtagid":"a","fd":3,"name":"hand","avatar":"http:\/\/127.0.0.1:8081\/static\/images\/avatar\/f1\/f_4.jpg","newmessage":"<img class=\"chat-img\" onclick=\"preview(this)\" style=\"display: block; max-width: 120px; max-height: 120px; visibility: visible;\" src=http:\/\/192.168.56.133\/uploads\/20231201081945_860.jpeg>","remains":[],"time":"08:19","mine":1}}

        //     // echo "server: handshake success with fd{$request->fd}\n";
        // });
        // $this->server->on('message', function (Server $server, $frame) {
        //     // echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
        //     // {"type":1,"name":"bookapi","email":"3528@qq.com","listtagid":"a"}
        //     // {"type":2,"name":"bookapi","avatar":"http://127.0.0.1:8081/static/images/avatar/f1/f_9.jpg","message":"1111","c":"text","listtagid":"a"}
        //     $this->onMessage($server, $frame);

        // });
        // $this->server->on('close', function ($ser, $fd) {
        //     // echo "client {$fd} closed\n";
        // });
        // $this->server->on('request', function ($request, $response) {
        //     // 接收http请求从get获取message参数的值，给用户推送
        //     // $this->server->connections 遍历所有websocket连接用户的fd，给所有用户推送
        //     $this->onRequest($request, $response);

        // });
        $this->server->start();
    }

    // protected function execute(Input $input, Output $output)
    // {
    //     $name = trim($input->getArgument('name'));
    //   	$name = $name ?: 'thinkphp';

    // 	if ($input->hasOption('city')) {
    //     	$city = PHP_EOL . 'From ' . $input->getOption('city');
    //     } else {
    //     	$city = '';
    //     }

    //     $output->writeln("Hello," . $name . '!' . $city);
    // 	// 指令输出
    // 	$output->writeln('chatserver');
    // }
}
