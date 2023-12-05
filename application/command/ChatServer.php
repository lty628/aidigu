<?php

namespace app\command;

use app\chat\libs\Chat;

use Swoole\WebSocket\Server;

use think\console\Command;
use think\console\Input;
use think\console\Output;


// use think\console\input\Argument;
// use think\console\input\Option;

class ChatServer extends Command
{
    public $server;

    protected function configure()
    {
        // 指令配置
        $this->setName('chatserver');
        $this->setName('chatserver')
            // ->addArgument('name', Argument::OPTIONAL, "your name")
            // ->addOption('city', null, Option::VALUE_REQUIRED, 'city name')
            ->setDescription('聊天服务');
        // 设置参数

    }

    protected function execute(Input $input, Output $output)
    {
        $this->initServer();
    }

    public function onOpen($server, $request)
    {
        $loginUserInfo = getWsUserInfoByCookie($request->cookie['rememberMe']);
        // dump($loginUserInfo);
        $uid = $loginUserInfo['uid'];
        $friendList = \app\chat\libs\Friends::list($uid);
        $data['fd'] = $request->fd;
        $data['uid'] = $uid;
        $data['online_time'] = time();
        \app\chat\libs\Chat::upOnlineUserStatus($uid, $data);
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

    public function initMessageData($data)
    {
        $time = time();
        $data['create_time'] = date('Y-m-d H:i:s', $time);
        if (!$data['content_type']) return $data;
        if ($data['content_type'] == 'mp3') {
            $data['content'] = '<p class="massageImg clear"><audio id="music_' . (string)$time . '" class="music" controls="controls" loop="loop" onplay="stopOther(this)" preload="none" controlsList="nodownload" οncοntextmenu="return false" name="media"><source src="' . $data['content'] . '" type="audio/mpeg"></audio></p>';
        } elseif($data['content_type'] == 'mp4' || $data['content_type'] == 'm3u8') {
            $data['content'] = '<p  class="massageImg"><video width="300px"  controls=""  name="media"><source src="'.$data['content'].'" type="video/mp4"></video></p>';
        } else {
            $data['content'] = '<img width="150px" class="massageImgCommon massageImg_jpg" onclick="showMessageImg(this)" src="' . $data['content'] . '">';
        }
        return $data;
    }

    public function onMessage($server, $frame)
    {
        $frameData = json_decode($frame->data, true);
        $func = [
            'none',
            'login',
            'friendChat',
        ];

        if ($frameData['type'] == 2) {
            // dump($frameData);
            $frameData['listtagid'] = 'friends';
            $frameData = $this->initMessageData($frameData);
            $data['create_time'] = $frameData['create_time'];
            $data['fromuid'] = $frameData['fromuid'];
            $data['content'] = $frameData['content'];
            $data['content_type'] = $frameData['content_type'];
            $data['touid'] = $frameData['touid'];
            $server->push($frame->fd, json_encode([
                'code' => 2,
                'msg' => 'success',
                'data' => $frameData
            ], 320));
            $isOnline = \app\chat\libs\Chat::isOnline($data['touid']);
            if ($isOnline) {
                $server->push($isOnline['fd'], json_encode([
                    'code' => 2,
                    'msg' => 'success',
                    'data' => $frameData
                ], 320));
                $data['send_status'] = 1;
            }
            \app\chat\libs\Chat::saveFriendChat($data);
        }

        if ($frameData['type'] == 3) {
            $chatHisory = \app\chat\libs\Chat::getChatHistory($frameData);
            $server->push($frame->fd, json_encode([
                'code' => 3,
                'msg' => 'success',
                'data' => $chatHisory,
                'listtagid' => 'friends'
            ], 320));
        }

        
        // $server->push($frame->fd, '{"code":2,"msg":"","data":{"listtagid":"a","fd":2,"name":"bookapi","avatar":"/static/index/images/noavatar_small.gif","newmessage":"1111","remains":null,"time":"08:04","mine":1}}');

        // $data = json_decode($frame->data, true);
        // switch ($data['type']) {
        //     case 1: //登录
        //         $data = array(
        //             'task' => 'login',
        //             'params' => array(
        //                 'name' => $data['name'],
        //                 'email' => $data['email']
        //             ),
        //             'fd' => $frame->fd,
        //             'listtagid' => $data['listtagid']
        //         );
        //         if (!$data['params']['name'] || !$data['params']['email']) {
        //             $data['task'] = "nologin";
        //             $this->server->task(json_encode($data));
        //             break;
        //         }
        //         $this->server->task(json_encode($data));
        //         break;
        //     case 2: //新消息
        //         $data = array(
        //             'task' => 'new',
        //             'params' => array(
        //                 'name' => $data['name'],
        //                 'avatar' => $data['avatar']
        //             ),
        //             'c' => $data['c'],
        //             'message' => $data['message'],
        //             'fd' => $frame->fd,
        //             'listtagid' => $data['listtagid']
        //         );
        //         $this->server->task(json_encode($data));
        //         break;
        //     case 3: // 改变房间
        //         $data = array(
        //             'task' => 'change',
        //             'params' => array(
        //                 'name'   => $data['name'],
        //                 'avatar' => $data['avatar'],
        //             ),
        //             'fd' => $frame->fd,
        //             'oldlisttagid' => $data['oldlisttagid'],
        //             'listtagid' => $data['listtagid']
        //         );

        //         $this->server->task(json_encode($data));

        //         break;
        //     default:
        //         $this->server->push($frame->fd, json_encode(array('code' => 0, 'msg' => 'type error')));
        // }
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


    public function initServer()
    {
        $this->server = new Server("0.0.0.0", 9502);
        $this->server->set(array(
            'task_worker_num'     => 8
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
