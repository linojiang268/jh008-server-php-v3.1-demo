<?php
namespace Jihe\Domain\Team\Validators;

use Jihe\Validation\Validator;

class TeamEnrollmentRequestRejectValidator extends Validator
{
    /**
     * @inheritdoc
     */
    protected function getRules()
    {
        return [
            'id'            => 'required|size:36'
        ];
    }

    /**
     * @inheritdoc
     */
    protected function getMessages()
    {
        return [
            'id.required'   => '申请未选择',
            'id.size'       => '申请未选择'
        ];
    }
}