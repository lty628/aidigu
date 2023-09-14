<?php
namespace app\m\controller;
use app\common\controller\Api;
class Ajax extends Api
{	
    protected function initialize()
	{
		self::$refrom = '手机';
	}
}
