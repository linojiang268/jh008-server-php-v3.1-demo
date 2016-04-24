<?php
namespace Jihe\Domain\User\Listeners;

use Jihe\Domain\User\Events\UserRegisteredEvent;
use Jihe\Infrastructure\Sms\SmsService;

class UserRegisteredListener
{
    private $smsService;

    public function __construct(SmsService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function handle(UserRegisteredEvent $event)
    {
        $user = $event->getUser();
        $this->smsService->send($user->getMobile(), 'Welcome, ' . $user->getNickName());
    }
}