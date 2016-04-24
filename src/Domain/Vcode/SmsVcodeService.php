<?php
namespace Jihe\Domain\Vcode;

use Carbon\Carbon;
use App\Dispatchers\SmsSendingJobDispatcher;
use Jihe\Domain\DomainException;
use Jihe\Utils\StringUtil;
use Jihe\Domain\Template\SmsTemplate;

class SmsVcodeService
{
    /**
     * the minimum time interval (in seconds) after which message is allowed
     * to be sent since last. 0 for no limit.
     *
     * @var int
     */
    private $intervalLimit;

    /**
     * the max survival time of one verification code (in seconds)
     *
     * @var int
     */
    private $survivalTime;

    /**
     * the maximum number of messages that can be sent within a period of time. 0 for no limit.
     *
     * @var int
     */
    private $countLimit;

    /**
     * the period of time within which maximum number of messages that can be sent. 0 for no limit.
     *
     * @var int
     */
    private $countLimitPeriod;

    /**
     * @var SmsVcodeRepository
     */
    private $smsVcodeRepository;

    private $smsSendingJobDispatcher;

    function __construct(SmsVcodeRepository $smsVcodeRepository, SmsSendingJobDispatcher $smsSendingJobDispatcher, array $config)
    {
        $this->smsVcodeRepository = $smsVcodeRepository;
        $this->smsSendingJobDispatcher = $smsSendingJobDispatcher;

        // rate limit defines two metrics
        // 1). the minimum time interval (in seconds) after which message is allowed to be sent since last
        $this->intervalLimit = array_get($config, 'sms_send_interval', SmsVcode::DEFAULT_EXPIRE_INTERVAL);
        $this->survivalTime = array_get($config, 'sms_survival_time', SmsVcode::DEFAULT_SURVIVAL_TIME);

        // 2). the maximum number of messages that can be sent within a period of time
        $this->countLimit = array_get($config, 'sms_count_limit', 0);
        $this->countLimitPeriod = array_get($config, 'sms_count_limit_period', 0);
    }

    /**
     *
     * send text message for user registration
     *
     * @param string $mobile   mobile to send message to
     *
     * @return SmsVcodeSendForRegistrationResult
     * @throws \Exception if request is too frequently, or requested too much in a period
     */
    public function sendForRegistration($mobile)
    {
        $this->ensureCanSendForRegistration($mobile);
        // fetch last valid requested
        $existVcode = $this->smsVcodeRepository->findLastValid($mobile);
        if ($existVcode) {
            // if there is a valid exist
            // make a longer life copy to avoid two valid code in a same time
            $code = $existVcode->getCode();
        } else {
            // generate verification code
            $code = $this->generateCode(4);
        }

        $vcode = new SmsVcode();
        $vcode->setMobile($mobile);
        $vcode->setCode($code);
        $vcode->setExpiredAt(Carbon::now()->addSeconds($this->survivalTime));

        $this->smsVcodeRepository->store($vcode);

        $message = sprintf(SmsTemplate::SMS_VERIFICATION_CODE, $code, ceil($this->survivalTime / 60));
        $this->smsSendingJobDispatcher->sendSms($mobile, $message);

        return new SmsVcodeSendForRegistrationResult($vcode->getCode(), $this->intervalLimit);

    }


    /**
     * generate verification code
     *
     * @param int $length
     * @return string
     */
    private function generateCode($length)
    {
        return StringUtil::quickRandom($length, '0123456789');
    }


    /**
     * ensure can send the registration verification code
     *
     * @param $mobile
     * @throws DomainException
     */
    private function ensureCanSendForRegistration($mobile)
    {
        // rule #1. the next message cannot be sent within the specified time interval
        // intervalLimit is 0 means no limit
        if ($this->intervalLimit > 0) {
            if ($this->smsVcodeRepository->countByMobile($mobile, $this->aheadOf($this->intervalLimit)) > 0) {
                // don't tell user the exact time left
                // typically human beings can read the time remaining from UI
                throw new DomainException('短信发送频率太高, 请稍后再试');
            }
        }

        // rule #2. message count limit in a period
        // any of countLimit or countLimitPeriod is 0 means no limit
        if ($this->countLimit > 0 && $this->countLimitPeriod > 0) {
            if ($this->smsVcodeRepository->countByMobile($mobile, $this->aheadOf($this->countLimitPeriod)) >= $this->countLimit) {
                throw new DomainException('短信发送次数过多, 请稍后再试');
            }
        }
    }

    /**
     * compute the time which goes ahead of given time($time) in seconds
     *
     * @param int $seconds  seconds ahead
     * @param int $time     the time base
     *
     * @return \DateTime    the computed time
     * @throws DomainException
     */
    private function aheadOf($seconds, $time = null) {
        $time = $time ? Carbon::createFromTimestamp($time) : Carbon::now();

        return $time->subSeconds($seconds);
    }

    /**
     * verify whether the sms verify code of the mobile is illegal
     *
     * @param $mobile
     * @param $vcode
     * @throws DomainException throws domain exception if the verify code is illegal
     */
    public function verify($mobile, $vcode)
    {
        $existsVcode = $this->smsVcodeRepository->findLastValid($mobile);

        if (!$existsVcode) { // no verification for this mobile, hack-ed?
            throw new DomainException('验证码错误');
        }
        if ($existsVcode == null || $existsVcode->isExpired()) {
            throw new DomainException('验证码已过期');
        }
        if ($vcode != $existsVcode->getCode()) {
            throw new DomainException('验证码错误');
        }

        // mark the verification code is used no matter this
        // verification passes or not
        $this->smsVcodeRepository->remove($existsVcode);

    }

}