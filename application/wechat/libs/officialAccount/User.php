<?php
namespace app\wechat\libs\officialAccount;

use EasyWeChat\Factory;

class User
{
    public static function info($openId)
    {
        $config = config('wechat.');
        // dump($config);die;
        $app = Factory::officialAccount($config);
        return $app->user->get($openId);
    }

    // {"subscribe":1,"openid":"o91z36f8umOgdZ0I5VnzKcX8sEog","nickname":"SR.\u674e","sex":0,"language":"zh_CN","city":"","province":"","country":"","headimgurl":"http:\/\/thirdwx.qlogo.cn\/mmopen\/Jj2jz2J5iaqO6n6Ffx1DS6bt9o2eVl2ezO7r5X1Dgx8MEUUGUS4qZR8JZuA5OwYMlqqCQeWlqsIgFKrJMB87htBRGKJcBiaVia1\/132","subscribe_time":1636450232,"remark":"","groupid":0,"tagid_list":[],"subscribe_scene":"ADD_SCENE_QR_CODE","qr_scene":0,"qr_scene_str":"foo"}
    public static function checkUser($wxUser, $qrSceneStr)
    {
        return true;
        $findUser = \think\Db::name('user')->where(['openid' => $wxUser['openid']])->find();
        // 存在用户
        if ($findUser) {
            return \think\Db::name('user')->where(['openid' => $wxUser['openid']])->update(['qr_scene_str' => $qrSceneStr]);
        } else {
            // file_put_contents('1.txt', json_encode($wxUser));
            $data['headimage'] = $wxUser['headimgurl'];
            $data['openid'] = $wxUser['openid'];
            $data['nickname'] = $wxUser['nickname'];
            $data['sex'] = $wxUser['sex'];
            $data['city'] = $wxUser['city'];
            $data['province'] = $wxUser['province'];
            $data['country'] = $wxUser['country'];
            $data['qr_scene_str'] = $qrSceneStr;
            $data['create_time'] = date('Y-m-d H:i:s', time());
            return \think\Db::name('user')->insert($data);
        }
        
    }
}