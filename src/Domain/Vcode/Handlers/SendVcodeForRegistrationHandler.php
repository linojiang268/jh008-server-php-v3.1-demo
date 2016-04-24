<?php
namespace Jihe\Domain\Vcode\Handlers;

use Jihe\Domain\ActionHandler;
use Jihe\Domain\Vcode\SmsVcodeSendForRegistrationResult;
use Jihe\Domain\Vcode\Validators\SendVcodeForRegistrationValidator;
use Jihe\Domain\Vcode\SmsVcodeService;

class SendVcodeForRegistrationHandler implements ActionHandler
{

    /**
     * @var SendVcodeForRegistrationValidator
     */
    private $validator;

    /**
     * @var SmsVcodeService
     */
    private $smsVcodeService;

    function __construct(SendVcodeForRegistrationValidator $validator,
                         SmsVcodeService $smsVcodeService)
    {
        $this->validator = $validator;
        $this->smsVcodeService = $smsVcodeService;
    }

    /**
     * @param array $request  [mobile] the mobile which will be registered with
     * @return SmsVcodeSendForRegistrationResult
     */
    public function handle(array $request = [])
    {
        $this->validator->validate($request);

        return $this->smsVcodeService->sendForRegistration($request['mobile']);
    }

}