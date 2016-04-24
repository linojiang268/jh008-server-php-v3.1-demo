<?php
namespace Jihe\Domain\User;

use Jihe\Events\Dispatcher;
use Jihe\Domain\User\Events\UserRegisteredEvent;
use Prophecy\Argument;
use Test\Jihe\App\DomainServiceTest;

class UserServiceServiceTest extends DomainServiceTest
{
    public function testRegisterWithoutProfile_Success()
    {
        $userRepository = $this->prophesize(UserRepository::class);
        $userRepository->existsByMobile('13800138000')->shouldBeCalled()->willReturn(false);
        $userRepository->store(Argument::that(function (User $user) {
            return $this->isUserEqual('13800138000', User::STATUS_INCOMPLETE, User::REGISTERED_BY_SELF, $user);
        }))->shouldBeCalled();

        $eventDispatcher = $this->prophesize(Dispatcher::class);
        $eventDispatcher->dispatch(Argument::that(function (array $events) {
            return count($events) == 1 &&
                   $events[0] instanceof UserRegisteredEvent &&
                   $this->isUserEqual('13800138000',
                                      User::STATUS_INCOMPLETE,
                                      User::REGISTERED_BY_SELF,
                                      $events[0]->getUser());
        }))->shouldBeCalled();

        $userService = $this->makeService($userRepository->reveal(), $eventDispatcher->reveal());
        $userService->registerWithoutProfile('13800138000', '123456');
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 该用户已注册
     */
    public function testRegisterWithoutProfile_UserAlreadyExists()
    {
        $userRepository = $this->prophesize(UserRepository::class);
        $userRepository->existsByMobile('13800138000')->shouldBeCalled()->willReturn(true);

        $userService = $this->makeService($userRepository->reveal());
        $userService->registerWithoutProfile('13800138000', '123456');
    }

    private function isUserEqual($mobile, $status, $registeredBy, User $user)
    {
        return $user->getMobile() == $mobile &&
               $user->getStatus() == $status &&
               $user->getRegisteredBy() == $registeredBy;
    }

    private function makeService(UserRepository $userRepository, Dispatcher $eventDispatcher = null)
    {
        if (is_null($eventDispatcher)) {
            $eventDispatcher = $this->prophesize(Dispatcher::class)->reveal();
        }

        return new UserService($userRepository, new PasswordHasher(), $eventDispatcher);
    }
}