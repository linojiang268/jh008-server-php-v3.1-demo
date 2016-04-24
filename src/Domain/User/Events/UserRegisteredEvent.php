<?php
namespace Jihe\Domain\User\Events;

use Jihe\Domain\User\User;
use Jihe\Events\Event;

class UserRegisteredEvent implements Event
{
    private $user;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}