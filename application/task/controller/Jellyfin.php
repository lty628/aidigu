<?php
namespace app\task\controller;

use app\common\libs\CurlHelpers;
use think\Db;

class Jellyfin
{
    private $apiKey;

    public function __construct()
    {
        // 从环境变量获取 Jellyfin API Key
        $this->apiKey = env('jellyfin.api_key');
    }

    /**
     * 处理 Jellyfin 视频链接
     * @param string $videoUrl Jellyfin 播放视频链接
     * @return array|bool 存储结果或 false
     */
    public function processVideoUrl($videoUrl)
    {
        // 解析链接获取服务器地址和视频 ID
        $parsedUrl = parse_url($videoUrl);
        if (!$parsedUrl || !isset($parsedUrl['host']) || !isset($parsedUrl['query'])) {
            return false;
        }

        parse_str($parsedUrl['query'], $queryParams);
        if (!isset($queryParams['Id'])) {
            return false;
        }

        $serverUrl = $parsedUrl['scheme'] . '://' . $parsedUrl['host'];
        $itemId = $queryParams['Id'];

        // 调用 Jellyfin API 获取视频信息
        $videoInfo = $this->getVideoInfo($serverUrl, $itemId);
        if (!$videoInfo) {
            return false;
        }

        // 存储视频信息到数据库
        return $this->saveVideoInfo($videoInfo);
    }

    /**
     * 调用 Jellyfin API 获取视频信息
     * @param string $serverUrl Jellyfin 服务器地址
     * @param string $itemId 视频 ID
     * @return array|bool 视频信息或 false
     */
    private function getVideoInfo($serverUrl, $itemId)
    {
        $apiUrl = $serverUrl . "/Items/{$itemId}?api_key={$this->apiKey}";
        $result = CurlHelpers::getInstance()->curlApiGet($apiUrl);
        $videoInfo = json_decode($result, true);

        if ($videoInfo && isset($videoInfo['Id'])) {
            return $videoInfo;
        }

        return false;
    }

    /**
     * 存储视频信息到数据库
     * @param array $videoInfo 视频信息
     * @return array|bool 存储结果或 false
     */
    private function saveVideoInfo($videoInfo)
    {
        $data = [
            'jellyfin_id' => $videoInfo['Id'],
            'name' => $videoInfo['Name'],
            'overview' => $videoInfo['Overview'],
            'runtime' => $videoInfo['RunTimeTicks'] / 10000000, // 转换为秒
            'created_at' => date('Y-m-d H:i:s'),
        ];

        Db::startTrans();
        try {
            $data['id'] = Db::name('jellyfin_videos')->insertGetId($data);
            Db::commit();
            return $data;
        } catch (\Exception $e) {
            Db::rollback();
            return false;
        }
    }
}