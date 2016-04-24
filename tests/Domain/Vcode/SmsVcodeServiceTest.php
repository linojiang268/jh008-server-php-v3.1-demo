<?php
namespace Test\Jihe\Domain\Vcode;

use App\Dispatchers\SmsSendingJobDispatcher;
use Jihe\Domain\Vcode\SmsVcode;
use Jihe\Domain\Vcode\SmsVcodeRepository;
use Jihe\Domain\Vcode\SmsVcodeService;
use Prophecy\Argument;
use Test\Jihe\App\DomainServiceTest;

class SmsVcodeServiceServiceTest extends DomainServiceTest
{
    public function testSendForRegistration_Success()
    {
        $repository = $this->prophesize(SmsVcodeRepository::class);
        $repository->countByMobile('13800138000', $this->getNow()->subSeconds(60))->shouldBeCalled()->willReturn(0);
        $repository->countByMobile('13800138000', $this->getNow()->subSeconds(300))->shouldBeCalled()->willReturn(4);
        $repository->findLastValid('13800138000', null)->shouldBeCalled()->willReturn(null);
        $repository->store(Argument::that(function (SmsVcode $code) {
            return $code->getMobile()    == '13800138000' &&
                   $code->getExpiredAt() == $this->getNow()->addSeconds(60);
        }))->shouldBeCalled();

        $smsSendingJobDispatcher = $this->prophesize(SmsSendingJobDispatcher::class);
        $smsSendingJobDispatcher->sendSms('13800138000', Argument::any())->shouldBeCalled();

        $smsVcodeService = $this->makeService($repository->reveal(), $smsSendingJobDispatcher->reveal());
        $smsVcodeService->sendForRegistration('13800138000');
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 短信发送频率太高
     */
    public function testSendForRegistration_SendTooFrequently()
    {
        $repository = $this->prophesize(SmsVcodeRepository::class);
        $repository->countByMobile('13800138000', $this->getNow()->subSeconds(60))->shouldBeCalled()->willReturn(1);

        $smsVcodeService = $this->makeService($repository->reveal());
        $smsVcodeService->sendForRegistration('13800138000');
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 短信发送次数过多, 请稍后再试
     */
    public function testSendForRegistration_SendTooMany()
    {
        $repository = $this->prophesize(SmsVcodeRepository::class);
        $repository->countByMobile('13800138000', $this->getNow()->subSeconds(60))->shouldBeCalled()->willReturn(0);
        $repository->countByMobile('13800138000', $this->getNow()->subSeconds(300))->shouldBeCalled()->willReturn(5);

        $smsVcodeService = $this->makeService($repository->reveal());
        $smsVcodeService->sendForRegistration('13800138000');
    }


    public function testVerify_VerifySuccessful()
    {
        $existCode = new SmsVcode();
        $existCode->setMobile('13800138000');
        $existCode->setCode('1234');
        $existCode->setExpiredAt($this->getNow()->addSeconds(30));

        $repository = $this->prophesize(SmsVcodeRepository::class);
        $repository->findLastValid('13800138000')->shouldBeCalled()->willReturn($existCode);

        $repository->remove(Argument::that(function (SmsVcode $code) use ($existCode) {
            return $code == $existCode;
        }))->shouldBeCalled();

        $smsVcodeService = $this->makeService($repository->reveal());
        $smsVcodeService->verify('13800138000', '1234');
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 验证码错误
     */
    public function testVerify_VcodeNotExists()
    {
        $repository = $this->prophesize(SmsVcodeRepository::class);
        $repository->findLastValid('13800138000')->shouldBeCalled()->willReturn(null);

        $smsVcodeService = $this->makeService($repository->reveal());
        $smsVcodeService->verify('13800138000', '1234');
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 验证码已过期
     */
    public function testVerify_VcodeExpired()
    {
        $existCode = new SmsVcode();
        $existCode->setMobile('13800138000');
        $existCode->setCode('1234');
        $existCode->setExpiredAt($this->getNow()->subSeconds(30));

        $repository = $this->prophesize(SmsVcodeRepository::class);
        $repository->findLastValid('13800138000')->shouldBeCalled()->willReturn($existCode);

        $smsVcodeService = $this->makeService($repository->reveal());
        $smsVcodeService->verify('13800138000', '1234');
    }

    /**
     * @expectedException \Jihe\Domain\DomainException
     * @expectedExceptionMessage 验证码错误
     */
    public function testVerify_VcodeNotMatch()
    {
        $existCode = new SmsVcode();
        $existCode->setMobile('13800138000');
        $existCode->setCode('1234');
        $existCode->setExpiredAt($this->getNow()->addSeconds(30));

        $repository = $this->prophesize(SmsVcodeRepository::class);
        $repository->findLastValid('13800138000')->shouldBeCalled()->willReturn($existCode);

        $smsVcodeService = $this->makeService($repository->reveal());
        $smsVcodeService->verify('13800138000', '4321');
    }


    /**
     *
     * @param SmsVcodeRepository $smsVcodeRepository
     * @param SmsSendingJobDispatcher $smsSendingJobDispatcher
     * @param array $config
     * @return SmsVcodeService
     */
    private function makeService(SmsVcodeRepository $smsVcodeRepository,
                                 SmsSendingJobDispatcher $smsSendingJobDispatcher = null,
                                 array $config = [])
    {
        $config = array_merge($config, [
            'sms_send_interval'      => 60,
            'sms_survival_time'      => 60,
            'sms_count_limit'        => 5,
            'sms_count_limit_period' => 300
        ]);

        if (is_null($smsSendingJobDispatcher)) {
            $smsSendingJobDispatcher = $this->prophesize(SmsSendingJobDispatcher::class)->reveal();
        }

        return new SmsVcodeService($smsVcodeRepository, $smsSendingJobDispatcher, $config);
    }
}