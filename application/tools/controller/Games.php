<?php
namespace app\tools\controller;
use app\common\controller\Base;
use app\common\model\GameConfig;
use think\Request;


// -- ----------------------------
// -- Table structure for wb_game_config
// -- ----------------------------
// DROP TABLE IF EXISTS `wb_game_config`;
// CREATE TABLE `wb_game_config`  (
//   `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
//   `game_name` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '游戏名称',
//   `game_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '游戏标识符',
//   `game_desc` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '' COMMENT '游戏描述',
//   `config_data` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL COMMENT '配置数据（JSON格式）',
//   `config_type` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '配置类型：1-系统默认，2-用户自定义',
//   `uid` int(10) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户ID（系统默认配置为0）',
//   `status` tinyint(3) UNSIGNED NOT NULL DEFAULT 1 COMMENT '状态：0-禁用，1-启用',
//   `sort_order` int(11) NOT NULL DEFAULT 0 COMMENT '排序',
//   `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
//   `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
//   PRIMARY KEY (`id`) USING BTREE,
//   INDEX `idx_game_key`(`game_key`) USING BTREE,
//   INDEX `idx_uid`(`uid`) USING BTREE,
//   INDEX `idx_config_type`(`config_type`) USING BTREE
// ) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_general_ci ROW_FORMAT = DYNAMIC;

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
            $configData = $gameConfig->getConfigDataArrayAttr(null, $gameConfig->getData());
            $this->assign('config_data', json_encode($configData));
        } else {
            // 默认配置将由前端处理
            $this->assign('config_data', json_encode([]));
        }
        
        // 传递游戏标识符到模板
        $this->assign('game_key', $gameKey);
        
        return $this->fetch('game');
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
            $configData = $gameConfig->getConfigDataArrayAttr(null, $gameConfig->getData());
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
                $gameConfig->config_data = json_encode($configData, JSON_UNESCAPED_UNICODE);
                $gameConfig->config_type = 2; // 用户自定义
                $gameConfig->status = 1;
                $gameConfig->save();
            } else {
                // 创建新配置
                GameConfig::create([
                    'game_name' => $gameName ?: $gameKey,
                    'game_key' => $gameKey,
                    'game_desc' => $gameDesc ?: $gameName ?: $gameKey,
                    'config_data' => json_encode($configData, JSON_UNESCAPED_UNICODE),
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
                $configData = $gameConfig->getConfigDataArrayAttr(null, $gameConfig->getData());
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
    
    
}