<?php
namespace Jihe\Domain;

use Jihe\Exceptions\ExceptionCode;

class DomainException extends \DomainException
{
    public function __construct($message, $code = ExceptionCode::GENERAL)
    {
        parent::__construct($message, $code);
    }
}