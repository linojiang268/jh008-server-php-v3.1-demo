<?php
namespace Jihe\Infrastructure\Sms;

/**
 * The sms service implementation for test, not real send message
 */
class LogSmsService implements SmsService
{
    /**
     * @inheritdoc
     */
    public function send($subscriber, $message, array $options = [])
    {
        // TODO: Implement send() method.
    }

    /**
     * @inheritdoc
     */
    public function queryQuota()
    {
        // TODO: Implement queryQuota() method.
    }
}