<?php
namespace Jihe\Exceptions;

class RuntimeException extends \RuntimeException
{
    public function __construct($message)
    {
        parent::__construct($message, ExceptionCode::GENERAL);
    }
}