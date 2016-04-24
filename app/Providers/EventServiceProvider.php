<?php
namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        \Jihe\Domain\User\Events\UserRegisteredEvent::class => [
            \Jihe\Domain\User\Listeners\UserRegisteredListener::class
        ],
    ];

    public function register()
    {
        $this->app->singleton(\Jihe\Events\Dispatcher::class, function ($app) {
            return new \Jihe\Events\Dispatcher(
                $app['events']
            );
        });
    }


    public function provides()
    {
        return [
            \Jihe\Events\Dispatcher::class
        ];
    }
}
