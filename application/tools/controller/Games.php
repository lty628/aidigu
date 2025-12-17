<?php
namespace app\tools\controller;
use app\common\controller\Base;
use app\common\model\GameConfig;
use think\Request;


class Games extends Base
{	
	public function index()
    {
        return $this->fetch();
    }
    
    /**
     * 通用游戏页面
     */
    public function game($gameKey)
    {
        // 获取当前用户ID（假设从session中获取）
        $uid = getLoginUid();
        
        // 获取游戏的配置
        $gameConfig = GameConfig::getConfigByGameKey($gameKey, $uid);
        
        // 如果有配置，解析配置数据
        if ($gameConfig) {
            $configData = $gameConfig->config_data_array;
            $this->assign('config_data', json_encode($configData));
        } else {
            // 默认配置将由前端处理
            $this->assign('config_data', json_encode([]));
        }
        
        // 传递游戏标识符到模板
        $this->assign('game_key', $gameKey);
        
        return $this->fetch('game');
    }

    public function whopays()
    {
        return $this->fetch('whopays');
    }

    public function spinwheel()
    {
        return $this->fetch('spinwheel');
    }
    
    /**
     * 真心话大冒险游戏页面（保持兼容性）
     */
    public function truthordare()
    {
        // 获取当前用户ID（假设从session中获取）
        $uid = getLoginUid();
        
        // 获取真心话大冒险的配置
        $gameConfig = GameConfig::getConfigByGameKey('truthordare', $uid);
        
        // 如果有配置，解析配置数据
        if ($gameConfig) {
            $configData = $gameConfig->config_data_array;
            $this->assign('truths', isset($configData['truths']) ? json_encode($configData['truths']) : '[]');
            $this->assign('dares', isset($configData['dares']) ? json_encode($configData['dares']) : '[]');
        } else {
            // 默认配置
            $defaultTruths = [
                "你最近一次撒谎是什么时候？",
                "你最尴尬的经历是什么？",
                "如果你可以拥有任何超能力，你会选择什么？",
                "你最害怕的事情是什么？",
                "你最喜欢的童年记忆是什么？",
                "如果你能成为任何人，你会选择谁？",
                "你最大的秘密是什么？",
                "你最想去哪里旅行？",
                "你最讨厌的食物是什么？",
                "你做过最疯狂的事情是什么？"
            ];
            
            $defaultDares = [
                "大声唱一首歌",
                "做10个俯卧撑",
                "给你的朋友发一条奇怪的信息",
                "模仿一位名人说话",
                "用方言讲一个笑话",
                "闭眼单脚站立30秒",
                "向陌生人问好",
                "做一个搞笑的表情并拍照",
                "说出你最近做的梦",
                "跳一段舞"
            ];
            
            $this->assign('truths', json_encode($defaultTruths));
            $this->assign('dares', json_encode($defaultDares));
        }
        
        return $this->fetch();
    }
    
    /**
     * 获取用户的所有游戏配置列表
     */
    public function getUserGameConfigs($gameKey)
    {
        try {
            // 获取当前用户ID
            $uid = getLoginUid();
            
            if ($uid == 0) {
                return json(['code' => 0, 'msg' => '请先登录']);
            }
            
            // 获取用户的所有配置
            $configs = GameConfig::where('game_key', $gameKey)
                ->where('uid', $uid)
                ->where('config_type', 2) // 只获取用户自定义配置
                ->select();
            
            $result = [];
            foreach ($configs as $config) {
                $result[] = [
                    'id' => $config->id,
                    'config_name' => $config->config_name,
                    'game_name' => $config->game_name,
                    'game_desc' => $config->game_desc,
                    'created_at' => $config->created_at,
                    'updated_at' => $config->updated_at
                ];
            }
            
            return json(['code' => 1, 'data' => $result]);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '获取配置列表失败：' . $e->getMessage()]);
        }
    }
    
    /**
     * 保存带自定义名称的游戏配置
     */
    public function saveNamedGameConfig(Request $request)
    {
        try {
            // 获取当前用户ID
            $uid = getLoginUid();
            
            if ($uid == 0) {
                return json(['code' => 0, 'msg' => '请先登录']);
            }
            
            // 获取参数
            $gameKey = $request->param('game_key', '');
            $configName = $request->param('config_name', '');
            $gameName = $request->param('game_name', '');
            $gameDesc = $request->param('game_desc', '');
            $configData = $request->param('config_data', []);
            
            if (empty($gameKey)) {
                return json(['code' => 0, 'msg' => '游戏标识符不能为空']);
            }
            
            if (empty($configName)) {
                return json(['code' => 0, 'msg' => '配置名称不能为空']);
            }
            
            if (empty($configData)) {
                return json(['code' => 0, 'msg' => '配置数据不能为空']);
            }
            
            // 创建新配置
            GameConfig::create([
                'config_name' => $configName,
                'game_name' => $gameName ?: $gameKey,
                'game_key' => $gameKey,
                'game_desc' => $gameDesc ?: $gameName ?: $gameKey,
                'config_data' => $configData,
                'config_type' => 2, // 用户自定义
                'uid' => $uid,
                'status' => 1
            ]);
            
            return json(['code' => 1, 'msg' => '配置保存成功']);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '保存失败：' . $e->getMessage()]);
        }
    }
    
    /**
     * 更新指定ID的游戏配置
     */
    public function updateGameConfig(Request $request)
    {
        try {
            // 获取当前用户ID
            $uid = getLoginUid();
            
            if ($uid == 0) {
                return json(['code' => 0, 'msg' => '请先登录']);
            }
            
            // 获取参数
            $id = $request->param('id', 0);
            $configName = $request->param('config_name', '');
            $gameName = $request->param('game_name', '');
            $gameDesc = $request->param('game_desc', '');
            $configData = $request->param('config_data', []);
            
            if (empty($id)) {
                return json(['code' => 0, 'msg' => '配置ID不能为空']);
            }
            
            if (empty($configName)) {
                return json(['code' => 0, 'msg' => '配置名称不能为空']);
            }
            
            if (empty($configData)) {
                return json(['code' => 0, 'msg' => '配置数据不能为空']);
            }
            
            // 查找配置
            $gameConfig = GameConfig::where('id', $id)
                ->where('uid', $uid)
                ->find();
            
            if (!$gameConfig) {
                return json(['code' => 0, 'msg' => '配置不存在或无权限修改']);
            }
            
            // 更新配置
            $gameConfig->config_name = $configName;
            $gameConfig->game_name = $gameName ?: $gameConfig->game_key;
            $gameConfig->game_desc = $gameDesc ?: $gameName ?: $gameConfig->game_key;
            $gameConfig->config_data = $configData;
            $gameConfig->save();
            
            return json(['code' => 1, 'msg' => '配置更新成功']);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '更新失败：' . $e->getMessage()]);
        }
    }
    
    /**
     * 删除指定ID的游戏配置
     */
    public function deleteNamedGameConfig($id)
    {
        try {
            // 获取当前用户ID
            $uid = getLoginUid();
            
            if ($uid == 0) {
                return json(['code' => 0, 'msg' => '请先登录']);
            }
            
            if (empty($id)) {
                return json(['code' => 0, 'msg' => '配置ID不能为空']);
            }
            
            // 删除配置
            $result = GameConfig::where('id', $id)
                ->where('uid', $uid)
                ->where('config_type', 2) // 只能删除用户自定义配置
                ->delete();
            
            if ($result) {
                return json(['code' => 1, 'msg' => '配置删除成功']);
            } else {
                return json(['code' => 0, 'msg' => '删除失败或配置不存在']);
            }
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '删除失败：' . $e->getMessage()]);
        }
    }
    
    /**
     * 保存游戏配置（通用版本）
     */
    public function saveGameConfig(Request $request)
    {
        try {
            // 获取当前用户ID
            $uid = getLoginUid();
            
            if ($uid == 0) {
                return json(['code' => 0, 'msg' => '请先登录']);
            }
            
            // 获取参数
            $gameKey = $request->param('game_key', '');
            $gameName = $request->param('game_name', '');
            $gameDesc = $request->param('game_desc', '');
            $configData = $request->param('config_data', []);
            
            if (empty($gameKey)) {
                return json(['code' => 0, 'msg' => '游戏标识符不能为空']);
            }
            
            if (empty($configData)) {
                return json(['code' => 0, 'msg' => '配置数据不能为空']);
            }
            
            // 查找用户是否已有该游戏操作
            $gameConfig = GameConfig::where('game_key', $gameKey)
                ->where('uid', $uid)
                ->find();
            
            if ($gameConfig) {
                // 更新配置
                $gameConfig->config_data = $configData;
                $gameConfig->config_type = 2; // 用户自定义
                $gameConfig->status = 1;
                $gameConfig->save();
            } else {
                // 创建新配置
                GameConfig::create([
                    'game_name' => $gameName ?: $gameKey,
                    'game_key' => $gameKey,
                    'game_desc' => $gameDesc ?: $gameName ?: $gameKey,
                    'config_data' => $configData,
                    'config_type' => 2, // 用户自定义
                    'uid' => $uid,
                    'status' => 1
                ]);
            }
            
            return json(['code' => 1, 'msg' => '配置保存成功']);
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '保存失败：' . $e->getMessage()]);
        }
    }
    
    /**
     * 获取游戏配置（通用版本）
     */
    public function getGameConfig($gameKey)
    {
        try {
            // 获取当前用户ID
            $uid = getLoginUid();
            
            if ($uid == 0) {
                return json(['code' => 0, 'msg' => '请先登录']);
            }
            
            // 获取配置
            $gameConfig = GameConfig::getConfigByGameKey($gameKey, $uid);
            
            if ($gameConfig) {
                $configData = $gameConfig->config_data_array;
                return json(['code' => 1, 'data' => $configData]);
            } else {
                return json(['code' => 0, 'msg' => '暂无配置']);
            }
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '获取配置失败：' . $e->getMessage()]);
        }
    }
    
    /**
     * 删除游戏配置（通用版本）
     */
    public function deleteGameConfig($gameKey)
    {
        try {
            // 获取当前用户ID
            $uid = getLoginUid();
            
            if ($uid == 0) {
                return json(['code' => 0, 'msg' => '请先登录']);
            }
            
            // 删除配置
            $result = GameConfig::where('game_key', $gameKey)
                ->where('uid', $uid)
                ->delete();
            
            if ($result) {
                return json(['code' => 1, 'msg' => '配置删除成功']);
            } else {
                return json(['code' => 0, 'msg' => '删除失败或配置不存在']);
            }
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '删除失败：' . $e->getMessage()]);
        }
    }
    
    /**
     * 根据配置ID获取游戏配置
     */
    public function getGameConfigById($id)
    {
        try {
            // 获取当前用户ID
            $uid = getLoginUid();
            
            if ($uid == 0) {
                return json(['code' => 0, 'msg' => '请先登录']);
            }
            
            if (empty($id)) {
                return json(['code' => 0, 'msg' => '配置ID不能为空']);
            }
            
            // 获取配置
            $gameConfig = GameConfig::where('id', $id)
                ->where('uid', $uid)
                ->find();
            
            if ($gameConfig) {
                // 正确的方式获取配置数据数组
                $configData = $gameConfig->config_data_array;
                
                // 确保configData是数组
                if (!is_array($configData)) {
                    $configData = [];
                }
                
                // 添加配置名称到返回数据中
                $configData['config_name'] = $gameConfig->config_name;
                
                return json(['code' => 1, 'data' => $configData]);
            } else {
                return json(['code' => 0, 'msg' => '配置不存在或无权限查看']);
            }
        } catch (\Exception $e) {
            return json(['code' => 0, 'msg' => '获取配置失败：' . $e->getMessage()]);
        }
    }
    
}