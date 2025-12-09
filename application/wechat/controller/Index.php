<?php
namespace app\wechat\controller;

class Index
{
    // 服务器接入（域名不能被举报）
    public function index()
    {  
        // 获取微信服务器发送的参数
        $signature = isset($_GET["signature"]) ? $_GET["signature"] : '';
        $timestamp = isset($_GET["timestamp"]) ? $_GET["timestamp"] : '';
        $nonce = isset($_GET["nonce"]) ? $_GET["nonce"] : '';
        $echostr = isset($_GET["echostr"]) ? $_GET["echostr"] : '';
        
        // 微信Token，需要与微信公众平台配置一致
        $token = config('wechat.token');
        
        // 验证签名
        if ($this->checkSignature($signature, $timestamp, $nonce, $token)) {
            // 验证成功，原样返回echostr参数内容
            echo $echostr;
            exit;
        } else {
            // 验证失败
            echo '验证失败';
            exit;
        }
    }
    
    /**
     * 验证微信签名
     * @param string $signature 微信加密签名
     * @param string $timestamp 时间戳
     * @param string $nonce 随机数
     * @param string $token Token
     * @return bool 验证结果
     */
    private function checkSignature($signature, $timestamp, $nonce, $token)
    {
        // 将token、timestamp、nonce三个参数进行字典序排序
        $tmpArr = array($token, $timestamp, $nonce);
        sort($tmpArr, SORT_STRING);
        
        // 将三个参数字符串拼接成一个字符串进行sha1加密
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);
        
        // 开发者获得加密后的字符串可与signature对比，标识该请求来源于微信
        return $tmpStr === $signature;
    }

    // 处理微信推送的消息
    public function handleMessage()
    {
        // 获取微信推送的数据
        $postStr = file_get_contents("php://input");
        
        if (!empty($postStr)) {
            // 解析XML数据
            $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
            
            // 转换为数组格式供MediaMessageHandler使用
            $message = json_decode(json_encode($postObj), true);
            
            // 创建MediaMessageHandler实例并处理消息
            $handler = new \app\wechat\libs\officialAccount\MediaMessageHandler();
            $response = $handler->handle($message);
            
            // 如果返回的是字符串，则按文本消息回复
            if (is_string($response)) {
                return $this->responseText($postObj, $response);
            }
            
            // 如果返回空字符串或null，则不回复
            if ($response === '' || $response === null) {
                exit;
            }
            
            // 其他情况原样输出
            echo $response;
            exit;
        } else {
            echo 'Hello World!';
            exit;
        }
    }
    
    /**
     * 回复文本消息
     */
    private function responseText($postObj, $content)
    {
        $fromUsername = $postObj->FromUserName;
        $toUsername = $postObj->ToUserName;
        $time = time();
        
        $textTpl = "<xml>
                    <ToUserName><![CDATA[%s]]></ToUserName>
                    <FromUserName><![CDATA[%s]]></FromUserName>
                    <CreateTime>%s</CreateTime>
                    <MsgType><![CDATA[%s]]></MsgType>
                    <Content><![CDATA[%s]]></Content>
                    </xml>";
                    
        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $content);
        echo $resultStr;
        exit;
    }

    // 需做缓存处理（未完成）
    public function qrcodeLogin()
    {
        $cache = session('qrSceneStr');
        if ($cache) {
            $returnData = $cache;
        } else {
            // 生成带参数的二维码用于公众号登录
            $accessToken = $this->getAccessToken();
            if (!$accessToken) {
                return ajaxJson(0, '获取access_token失败');
            }
            
            // 生成场景值ID
            $sceneId = time() . rand(1000, 9999);
            
            // 调用微信API创建二维码ticket
            $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=' . $accessToken;
            
            // 构造POST数据
            $postData = [
                'expire_seconds' => 604800, // 7天有效
                'action_name' => 'QR_STR_SCENE',
                'action_info' => [
                    'scene' => [
                        'scene_str' => 'login_' . $sceneId
                    ]
                ]
            ];
            
            $result = $this->httpRequest($url, json_encode($postData));
            $qrResult = json_decode($result, true);
            
            if (isset($qrResult['ticket'])) {
                // 获取二维码图片URL
                $qrCodeUrl = 'https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=' . urlencode($qrResult['ticket']);
                
                $returnData = [
                    'sceneId' => $sceneId,
                    'qrCodeUrl' => $qrCodeUrl,
                    'img' => '<img src="' . $qrCodeUrl . '" height="150" />',
                    'expireTime' => time() + 604800 // 过期时间
                ];
                
                // 存储到session中，用于后续验证
                session('qrSceneStr', $returnData);
            } else {
                return ajaxJson(0, '生成二维码失败: ' . (isset($qrResult['errmsg']) ? $qrResult['errmsg'] : '未知错误'));
            }
        }
        return ajaxJson(1, 'ok', $returnData); 
    }
    
    /**
     * 获取微信access_token
     * @return string|bool access_token或false
     */
    private function getAccessToken()
    {
        // 先尝试从缓存获取
        $cacheKey = 'wechat_access_token';
        $cachedToken = cache($cacheKey);
        
        if ($cachedToken) {
            return $cachedToken;
        }
        
        // 从配置获取appid和secret
        $appId = config('wechat.app_id');
        $appSecret = config('wechat.secret');
        
        if (!$appId || !$appSecret) {
            return false;
        }
        
        // 调用微信API获取access_token
        $url = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=' . $appId . '&secret=' . $appSecret;
        $result = $this->httpRequest($url);
        $tokenData = json_decode($result, true);
        
        if (isset($tokenData['access_token'])) {
            // 缓存access_token，提前10分钟过期
            $expireTime = isset($tokenData['expires_in']) ? $tokenData['expires_in'] - 600 : 6600;
            cache($cacheKey, $tokenData['access_token'], $expireTime);
            return $tokenData['access_token'];
        }
        
        return false;
    }
    
    /**
     * 发送HTTP请求
     * @param string $url 请求URL
     * @param string $data POST数据
     * @return string 响应内容
     */
    private function httpRequest($url, $data = null)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data)
            ]);
        }
        
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $output = curl_exec($curl);
        curl_close($curl);
        
        return $output;
    }
    
    /**
     * 检查二维码扫描状态
     */
    public function checkScanStatus()
    {
        $sceneId = input('sceneId');
        if (!$sceneId) {
            return ajaxJson(0, '参数错误');
        }
        
        // 这里应该检查用户是否已经扫描二维码并确认登录
        // 实际项目中需要结合微信推送事件来实现
        // 此处仅为示例逻辑
        
        // 模拟检查逻辑
        $scanStatus = session('scan_status_' . $sceneId);
        
        if ($scanStatus && $scanStatus['scanned']) {
            // 用户已扫描并确认登录
            $userData = [
                'openid' => $scanStatus['openid'],
                'nickname' => $scanStatus['nickname'] ?? '微信用户',
                'headimgurl' => $scanStatus['headimgurl'] ?? ''
            ];
            
            // 清除临时状态
            session('scan_status_' . $sceneId, null);
            
            // 设置登录状态
            // 这里需要根据您的用户系统实现具体的登录逻辑
            // 示例：
            // setLoginUid($userId);
            
            return ajaxJson(1, '登录成功', [
                'userInfo' => $userData,
                'redirectUrl' => '/' // 登录成功后跳转地址
            ]);
        } else {
            // 仍在等待扫描
            return ajaxJson(2, '等待扫描');
        }
    }
}