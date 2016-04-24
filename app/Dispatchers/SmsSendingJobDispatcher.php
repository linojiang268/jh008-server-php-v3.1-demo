<?php
namespace App\Dispatchers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Jobs\SmsSendingJob;

class SmsSendingJobDispatcher
{
    use DispatchesJobs;

    /**
     * send message
     * @param array|string  $subscriber   subscriber to send message to
     * @param string        $message      message to send
     * @param boolean|null  $pretending   whether pretend to send or not
     *
     * @return boolean      true if message will be sent （whether it really success is not known）, false if message
     *                      sending will be skipped.
     *
     * @throws \Exception   exception will be thrown if sending fails.
     */
    public function sendSms($subscriber, $message, $pretending = null)
    {
        if (is_null($pretending)) {
            $config = app('config')['sms.config'];
            $pretending = !array_get($config, 'pretending', true);
        }

        if (!$pretending) {
            $this->dispatch(new SmsSendingJob($subscriber, $message));
        }

        return !$pretending;
    }
}