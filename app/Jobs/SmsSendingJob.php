<?php
namespace App\Jobs;

use Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;
use Jihe\Infrastructure\Sms\SmsService;

class SmsSendingJob extends Job implements SelfHandling, ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * subscriber to send message to
     * @var array|string
     */
    private $subscriber;

    /**
     * message to send
     *
     * @var string
     */
    private $message;

    /**
     * send message
     * @param array|string  $subscriber   subscriber to send message to
     * @param string        $message      message to send
     *
     * @throws \Exception   exception will be thrown if sending fails.
     */
    public function __construct($subscriber, $message)
    {
        $this->subscriber = $subscriber;
        $this->message = $message;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(SmsService $service)
    {
        try {
            $service->send($this->subscriber, $this->message);
        } catch (\Exception $ex) {
            Log::error($ex->getMessage());
        }
    }

    public function failed()
    {
        // TODO: handle message sending errors
        $this->release();
    }
}
