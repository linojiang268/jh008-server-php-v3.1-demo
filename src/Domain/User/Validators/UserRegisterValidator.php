<?php
namespace Jihe\Domain\User\Validators;

use Jihe\Validation\Validator as ValidatorContract;

class UserRegisterValidator extends ValidatorContract
{
    protected function getRules()
    {
        return [
            'vcode'    => 'required|string|size:4',  // verification is a 4-digit string
            'mobile'   => 'required|mobile',
            'password' => 'required|between:6,32',   // length of password should range from 6 to 32
        ];
    }

    protected function getMessages()
    {
        return [
            'mobile.required'   => '手机号未填写',
            'mobile.size'       => '手机号格式错误',
            'vcode.required'    => '验证码未填写',
            'vcode.size'        => '验证码错误',
            'password.required' => '密码未填写',
            'password.between'  => '密码错误',
        ];
    }
}