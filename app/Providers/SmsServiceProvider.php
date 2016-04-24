<?php
namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class SmsServiceProvider extends ServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->app->singleton(\Jihe\Domain\Vcode\Handlers\SendVcodeForRegistrationHandler::class);

        $this->app->singleton(\Jihe\Infrastructure\Sms\SmsService::class,
                              \Jihe\Infrastructure\Sms\LogSmsService::class);
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return [
            \Jihe\Domain\Vcode\Handlers\SendVcodeForRegistrationHandler::class,
            \Jihe\Infrastructure\Sms\SmsService::class
        ];
    }


}
