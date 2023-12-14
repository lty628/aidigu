<?php
namespace app\common\libs;

class Remind
{
    protected static $prefix = 'remind_';

    protected static function getName($uid)
    {
        return self::$prefix . 'last_request_time_' .$uid;
    }

    public static function check($uid)
    {
        return cache(self::getName($uid));
    }
    

    public static function open($uid)
    {
        cache(self::getName($uid), 1);
    }

    public static function clean($uid)
    {
        return cache(self::getName($uid), 0);
    }
}