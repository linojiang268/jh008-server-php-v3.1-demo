<?php
namespace Jihe\Domain\Template;

abstract class SmsTemplate
{
    //=============================================================================================
    //                                     用户：账号消息
    //=============================================================================================
    // 短信验证码
    const SMS_VERIFICATION_CODE = '本次验证码%s，%s分钟有效。';
    
    // 用户注册成功
    const USER_REGISTERED_SUCCESSFUL = '亲爱的用户，恭喜您注册成功。集合兴趣，争当团长，客服热线400-XXX-XXXX。';
}
