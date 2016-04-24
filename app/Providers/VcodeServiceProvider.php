<?php
namespace App\Providers;

use Doctrine\ORM\Mapping\ClassMetadata;
use Illuminate\Support\ServiceProvider;
use App\Dispatchers\SmsSendingJobDispatcher;

class VcodeServiceProvider extends ServiceProvider
{

    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->app->singleton(\Jihe\Domain\Vcode\SmsVcodeRepository::class, function ($app) {
            return new \Jihe\Infrastructure\Repository\Vcode\SmsVcodeRepository(
                $app['em'],
                new ClassMetadata(\Jihe\Domain\Vcode\SmsVcode::class)
            );
        });

        $this->app->singleton(\Jihe\Domain\Vcode\Handlers\SendVcodeForRegistrationHandler::class);

        $this->app->singleton(\Jihe\Domain\Vcode\SmsVcodeService::class, function ($app) {
            return new \Jihe\Domain\Vcode\SmsVcodeService(
                $app[\Jihe\Domain\Vcode\SmsVcodeRepository::class],
                new SmsSendingJobDispatcher(),
                $app['config']['verification']
            );
        });
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return [
            \Jihe\Domain\Vcode\SmsVcodeRepository::class,
            \Jihe\Domain\Vcode\Handlers\SendVcodeForRegistrationHandler::class,
            \Jihe\Domain\Vcode\SmsVcodeService::class
        ];
    }


}
