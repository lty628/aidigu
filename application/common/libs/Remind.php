<?php
namespace app\common\libs;

class Remind
{
    protected static $prefix = 'remind_';

    protected static function getName($uid)
    {
        return self::$prefix . 'last_request_time_' .$uid;
    }

    public static function checkAll($uid)
    {
        $chatRemind = cache(self::getName($uid));
        $messageRemind = \app\common\model\Reminder::where('touid', $uid)->where('status', 0)->count();
        $types = [];
        if ($chatRemind) {
            array_push($types, 'chat');
        }
        if ($messageRemind) {
            array_push($types, 'message');
        }
        
        return [
            'chatRemind' => $chatRemind,
            'messageRemind'=> $messageRemind,
            'types' => $types
        ];
    }

    public static function check($uid)
    {
        $result = cache(self::getName($uid));
        if (!$result) return [];
        return $result;
    }
    

    public static function open($uid, $appNameKey = 'chat')
    {
        $data = self::check($uid);
        if (in_array($appNameKey, $data)) {
            return true;
        }
        array_push($data, $appNameKey);
        return cache(self::getName($uid), $data);
    }

    public static function clean($uid, $appNameKey = 'chat')
    {
        $data = self::check($uid);
        if (in_array($appNameKey, $data)) {
            $data = array_values(array_diff($data, [$appNameKey]));
        }
        return cache(self::getName($uid), $data);
    }
}