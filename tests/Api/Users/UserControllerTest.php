<?php
namespace Test\Jihe\Api\Users;

use Test\Jihe\App\ApiTest;

class UserControllerTest extends ApiTest
{
    public function testRegister_Successful()
    {
        $this->setTestNow('2015-08-24 12:47:46');
        $this->ajaxPost('/api/users', [
            'mobile'    => '13800138000',
            'password'  => '123456',
            'vcode'     => '0215',
        ]);

        $this->seeJsonContains(['code' => 0]);
    }

//    public function testRegister_VcodeExpired()
//    {
//        Carbon::setTestNow(Carbon::createFromFormat('Y-m-d H:i:s', '2015-08-24 12:49:46'));
//        $this->call('POST', 'api/users', [
//            'mobile'    => '13800138001',
//            'password'  => '123456',
//            'vcode'     => '0216',
//        ]);
//dd($this->response->getContent());
//        $this->seeJsonContains(['code' => 10000]);
//    }
}