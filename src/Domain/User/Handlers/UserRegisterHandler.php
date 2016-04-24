<?php
namespace Jihe\Domain\User\Handlers;

use Jihe\Domain\ActionHandler;
use Jihe\Domain\User\Validators\UserRegisterValidator;
use Jihe\Domain\User\UserService;
use Jihe\Domain\Vcode\SmsVcodeService;

class UserRegisterHandler implements ActionHandler
{
    /**
     * @var UserRegisterValidator
     */
    private $validator;

    /**
     * @var SmsVcodeService
     */
    private $smsVcodeService;

    /**
     * @var UserService
     */
    private $userService;

    public function __construct(UserRegisterValidator $validator,
                                SmsVcodeService $smsVcodeService,
                                UserService $userService)
    {
        $this->validator = $validator;
        $this->smsVcodeService = $smsVcodeService;
        $this->userService = $userService;
    }


    public function handle(array $request = [])
    {
        $this->validator->validate($request);

        $this->smsVcodeService->verify($request['mobile'], $request['vcode']);

        $this->userService->registerWithoutProfile($request['mobile'], $request['password']);
    }
}