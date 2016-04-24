<?php
namespace Test\Jihe\Domain\User\Handlers;

use Jihe\Domain\User\Handlers\UserRegisterHandler;
use Test\Jihe\App\HandlerTest;

class UserRegisterHandlerTest extends HandlerTest
{
    public function testHandle_Success()
    {
        $userRegisterHandler = $this->prophesize(UserRegisterHandler::class);
//        $userRepository->existsByMobile('13800138000')->shouldBeCalled()->willReturn(false);
//        $userRepository->store(Argument::that(function (User $user) {
//            return $user->getMobile() == '13800138000' &&
//            $user->getStatus() == User::STATUS_INCOMPLETE &&
//            $user->getRegisteredBy() == User::REGISTERED_BY_SELF &&
//            !is_null($user->getPassword()) &&
//            !is_null($user->getSalt());
//        }))->shouldBeCalled();
//
//        $userService = $this->makeService($userRepository->reveal());
//        $userService->registerWithoutProfile('13800138000', '123456');
    }
}