<?php
namespace app\common\libs;

class Irrigation
{
    protected static $prefix = 'irrigation_';
    protected static $limitRequest = 10;

    public static function check($uid)
    {
        $time = time();
        $lastTimeKey = self::$prefix . 'last_request_time_' .$uid;
        $requestCount = self::$prefix . 'last_request_count_' .$uid;
        $lastRequestTime = cache($lastTimeKey);
        $userRequestCount = cache($requestCount);
        if ($time - $lastRequestTime > 5) {
            cache(self::$prefix . 'last_request_time_' .$uid, $time);
            cache($requestCount, 0);
            return true;
        } else {
            $nowCount = $userRequestCount+1;
            cache($requestCount, $nowCount);
            if ($nowCount > self::$limitRequest) return false;
        }
        return true;
    }
}