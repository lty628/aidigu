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
        if (!$loginUserInfo) {
            $server->disconnect($request->fd);
            return;
        }
        unset($loginUserInfo['password']);
        // dump($loginUserInfo);
        $uid = $loginUserInfo['uid'];
        \app\common\libs\Remind::clean($uid, 'chat');
        // 好友列表
        // $friendList = \app\chat\libs\TagInfo::getTagInfo($uid);
        $data['fd'] = $request->fd;
        $data['uid'] = $uid;
        $data['online_time'] = time();
        \app\chat\libs\ChatDbHelper::upOnlineUserStatus(['uid' => $uid], $data);
        $server->push($request->fd, json_encode([
            'code' => 1,
            'msg' => 'success',
            'data' => $loginUserInfo
        ], 320));
        // $server->push($request->fd, json_encode($friendList, 320));
    }

    public function onMessage($server, $frame)
    {
        if ($frame->data == 'ping') {
            $server->push($frame->fd, 'pong');
        } else {
            $frameData = json_decode($frame->data, true);
            $funcArr = [
                // type 0 默认方法
                'index',
                'none',
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

    }

    public function onClose($server, $fd)
    {

        \app\chat\libs\ChatDbHelper::upOnlineUserStatus(['fd' => $fd], [
            'offline_time' => time(),
            'fd' => 0
        ]);
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

        $this->server->start();
    }
}
