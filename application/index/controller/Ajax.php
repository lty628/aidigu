<?php
namespace app\index\controller;
use app\common\controller\Api;
class Ajax extends Api
{	
    protected function initialize()
	{
		self::$refrom = '网站';
	}
}
