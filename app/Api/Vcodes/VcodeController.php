<?php
namespace App\Api\Vcodes;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Jihe\Http\Controllers\ApiController;
use Jihe\Domain\Vcode\Handlers\SendVcodeForRegistrationHandler;

class VcodeController extends ApiController
{
    /**
     * send mobile verification to user
     */
    public function sendVcode(Request $request, SendVcodeForRegistrationHandler $handler)
    {
        $vcodeSendForRegistrationResult = $handler->handle($request->all());
        $response = ['sendInterval' => $vcodeSendForRegistrationResult->getSendInterval()];
        if (Config::get('app.debug')) {
            $response['vcode'] = $vcodeSendForRegistrationResult->getVcode();
        }

        return $this->respondAsJson('发送成功', $response);
    }

}