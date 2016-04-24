<?php
namespace Jihe\Validation;

use Illuminate\Support\MessageBag;
use Jihe\Exceptions\RuntimeException;

/**
 * Exception that can be thrown during validation
 */
class ValidationException extends RuntimeException
{
    private $errors;

    /**
     * @param array|\Illuminate\Support\MessageBag $errors
     */
    public function __construct($errors)
    {
        $this->errors = $this->morphErrors($errors);

        parent::__construct('参数校验错误');
    }

    /**
     * get validation errors
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    //
    /**
     * morph errors to array
     * @param array|\Illuminate\Support\MessageBag $errors
     * @return array
     */
    private function morphErrors($errors)
    {
        if ($errors instanceof MessageBag) {
            return $errors->getMessages();
        }

        return $errors;
    }
}