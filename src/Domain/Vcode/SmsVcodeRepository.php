<?php
namespace Jihe\Domain\Vcode;

interface SmsVcodeRepository
{

    /**
     * get the validation code count of given mobile
     * from starting time (optional) to now
     *
     * @param string         $mobile                     mobile#
     * @param \DateTime|null $createdAtFrom  (optional)  get count after this time
     */
    public function countByMobile($mobile, $createdAtFrom = null);


    /**
     * find the verification code that is valid(not used and not expired).
     * multiple verification codes might be found,
     * what's needed is the last one.
     *
     * @param string         $mobile
     * @param \DateTime|null $expiredBehind  expiry time of the code, default to now
     * @return \Jihe\Domain\Vcode\SmsVcode|null      the last valid verification code
     */
    public function findLastValid($mobile, $expiredBehind = null);

    /**
     * save the sms verification code
     *
     * @param SmsVcode $smsVcode the saved instance
     */
    public function store(SmsVcode $smsVcode);

    /**
     * remove the sms verification code
     *
     * @param SmsVcode $smsVcode the removed instance
     */
    public function remove(SmsVcode $smsVcode);

}