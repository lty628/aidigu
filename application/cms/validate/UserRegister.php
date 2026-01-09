<?php
namespace app\cms\validate;

use think\Validate;

class UserRegister extends Validate
{
    protected $rule = [
        'nickname|昵称' => ['require', 'min' => 3, 'max' => 12, 'checkName'],
        'phone|手机号' => 'require|number|length:11',
    ];

    protected $message = [
        'nickname.require' => '昵称必须填写',
        'nickname.min' => '昵称不能少于3位',
        'nickname.max' => '昵称不多于12位',
        'nickname.checkName' => '昵称不能含有特殊字符',
    ];

    // 自定义验证规则
    protected function checkName($value,$rule, $data=[])
    {
        return preg_match('/[@#$%^&*]/', $value) ? '昵称不能还有特殊字符' : true;
    }
}