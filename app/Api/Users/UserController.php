<?php
namespace App\Api\Users;

use Illuminate\Http\Request;
use Jihe\Http\Controllers\ApiController;
use Jihe\Domain\User\Handlers\UserRegisterHandler;

class UserController extends ApiController
{
    /**
     * handle user register
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request, UserRegisterHandler $handler)
    {
        $handler->handle($request->input());

        return $this->respondAsJson('注册成功');
    }
}
