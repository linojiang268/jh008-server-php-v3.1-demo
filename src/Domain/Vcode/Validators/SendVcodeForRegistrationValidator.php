<?php
namespace Jihe\Domain\Vcode\Validators;

use Jihe\Validation\Validator as ValidatorContract;

class SendVcodeForRegistrationValidator extends ValidatorContract
{
    protected function getRules()
    {
        return [
            'mobile' => 'required|size:11',
        ];
    }

    protected function getMessages()
    {
        return [
            'mobile.required'   => '手机号未填写',
            'mobile.size'       => '手机号格式错误',
        ];
    }
}