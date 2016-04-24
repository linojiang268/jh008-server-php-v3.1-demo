<?php
namespace Jihe\Domain\Team\Validators;

use Jihe\Validation\Validator;

class CreateTeamEnrollmentRequestValidator extends Validator
{
    /**
     * @inheritdoc
     */
    protected function getRules()
    {
        return [
            'name'            => 'required|max:32',
            'city_id'         => 'required|size:36',
            'requester_id'    => 'required|size:36',
            'email'           => 'email',
            'logo_url'        => 'max:255',
            'contact_address' => 'max:255',
            'contact_phone'   => 'mobile',
            'contact_name'    => 'max:32',
            'introduction'    => 'max:32',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getMessages()
    {
        return [
            'name.required'         => '社团名称未填写',
            'name.max'              => '社团名称错误',
            'city_id.required'      => '城市未选择',
            'city_id.size'          => '城市错误',
            'requester_id.required' => '城市未选择',
            'requester_id.size'     => '城市错误',
            'email.email'           => '邮箱格式错误',
            'logo_url.max'          => 'logo错误',
            'contact_address.max'   => '地址错误',
            'contact_phone.mobile'  => '联系方式手机号格式错误',
            'contact_name.max'      => '联系人错误',
            'introduction.max'      => '简介错误',
        ];
    }
}