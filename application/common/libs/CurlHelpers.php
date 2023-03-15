<?php
namespace app\common\libs;

use GuzzleHttp\Psr7;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Pool;
use GuzzleHttp\Psr7\Request;
use think\Log;
/** 
 * curl类
 * @author SR.李
 */

class CurlHelpers
{
    // curl实例
    protected $client;
    protected static $instance;
    protected $guzzleHeader = [
            // 'auth' => ['admin', 'admin'],
            'timeout'=> 10,
            'http_errors'=>true,
        ];


    public function __construct()
    {
        $this->header = $this->guzzleHeader;
        $this->client = new Client();
    }

    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }




    // 发送post请求
    public function curlApiPost($api, $header = [], $params = '')
    {
        $this->header['body'] = $params;
        // $this->header = array_merge($header, $this->header);
        $this->header['headers'] = $header;
        try {
            $response = $this->client->request('POST', $api, $this->header);
            if ($response->getStatusCode() != 200) Log::error($api.'状态码错误:'.$response->getStatusCode()."\n");
            $responseData = (string)$response->getBody();
            return $responseData;
        } catch (RequestException $e) {
            dump(Psr7\str($e->getResponse()));
            Log::error($api.'POST请求失败,响应结果'. Psr7\str($e->getResponse()) ."\n");
            // echo $api.'请求失败'."\n";
            return false;
        }
    }

    // 发送get请求
    public function curlApiGet(string $api, $header = [])
    {
        $this->header = array_merge($header, $this->header);
        $this->header['headers'] = $header;
        // dump($this->header);die;
        try {
            $response = $this->client->request('GET', $api, $this->header);
            // dump($response);die;
            if ($response->getStatusCode() != 200) Log::error($api.'状态码错误:'.$response->getStatusCode()."\n");
            $responseData = (string)$response->getBody();
            return $responseData;
        } catch (RequestException $e) {
            Log::error($api.'GET请求失败,响应结果'. Psr7\str($e->getResponse()) ."\n");
            // echo $api.'请求失败'."\n";
            return false;
        }
    }

    // raw 方式
    protected function initRawMultiple($url, $params, $method, $headers = [])
    {
        $headers = array_merge($headers, ['content-type' => 'application/json']);
        foreach ($params as $param) {
            yield new Request($method, $url,$headers, json_encode($param));
        }
    }

    public function raw($url, $params, $method = 'POST', $headers = [])
    {
        return $this->multiple($this->initRawMultiple($url, $params, $method, $headers));
    }

    // 并发请求
    public function multiple($request)
    {
        $pool = new Pool($this->client, $request, [
            'concurrency' => 5,
            'fulfilled' => function ($response, $index) {
                // 请求成功
                $content = $response->getBody()->getContents();
                // file_put_contents('1.txt', $content);
                $this->result[$index] = $content;
            },
            // 失败
            'rejected' => function ($reason, $index) {
                // file_put_contents('1.txt', $reason->getMessage());
                $this->result[$index] = $reason->getMessage();
            },
        ]);
        $promise = $pool->promise();
        $promise->wait();
        return $result;
    }
}
