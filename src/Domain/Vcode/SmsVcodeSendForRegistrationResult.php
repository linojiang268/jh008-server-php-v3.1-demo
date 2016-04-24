<?php
namespace Jihe\Domain\Vcode;

class SmsVcodeSendForRegistrationResult
{
    private $vcode;
    private $sendInterval;

    function __construct($vcode, $sendInterval)
    {
        $this->vcode = $vcode;
        $this->sendInterval = $sendInterval;
    }

    /**
     * @return mixed
     */
    public function getVcode()
    {
        return $this->vcode;
    }

    /**
     * @param mixed $vcode
     * @return SmsVcodeSendForRegistrationResult
     */
    public function setVcode($vcode)
    {
        $this->vcode = $vcode;
    }

    /**
     * @return mixed
     */
    public function getSendInterval()
    {
        return $this->sendInterval;
    }

    /**
     * @param mixed $sendInterval
     * @return SmsVcodeSendForRegistrationResult
     */
    public function setSendInterval($sendInterval)
    {
        $this->sendInterval = $sendInterval;
    }

}