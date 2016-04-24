<?php
namespace Jihe\Domain\User;

use Event;
use Jihe\Domain\DomainException;
use Jihe\Domain\User\Events\UserRegisteredEvent;
use Jihe\Events\Dispatcher;

class UserService
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var PasswordHasher
     */
    private $hasher;

    /**
     * @var Dispatcher
     */
    private $eventDispatcher;

    public function __construct(UserRepository $userRepository, PasswordHasher $hasher, Dispatcher $eventDispatcher = null)
    {
        $this->userRepository = $userRepository;
        $this->hasher = $hasher;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * user registration
     *
     * @param string $mobile
     * @param string $password length between 6 and 32
     * @return int                  id for the registered user
     * @throws DomainException
     */
    public function registerWithoutProfile($mobile, $password)
    {
        // user's mobile is supposed to be unique
        if (!$this->isMobileUnique($mobile)) {
            throw new DomainException('该用户已注册');
        }

        // encrypt user's password, to keep back-compatibility,
        // user's password will be encrypted with a randomly generated salt
        $salt = $this->generateSalt();
        $password = $this->hasher->make($password, $salt);

        $user = new User();
        $user->setMobile($mobile);
        $user->setPassword($password);
        $user->setStatus(User::STATUS_INCOMPLETE);
        $user->setSalt($salt);
        $user->setRegisteredBy(User::REGISTERED_BY_SELF);

        $this->userRepository->store($user);

        $this->eventDispatcher->dispatch([new UserRegisteredEvent($user)]);

        return $user;
    }


    /**
     * check whether given mobile number is unique in the system or not
     *
     * @param $mobile
     * @return bool true if unique. false otherwise.
     */
    private function isMobileUnique($mobile)
    {
        return !$this->userRepository->existsByMobile($mobile);
    }

    /**
     * generate salt for hashing password
     *
     * @return string
     */
    private function generateSalt()
    {
        return str_random(16);
    }

}