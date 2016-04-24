<?php
namespace App\Providers;

use Doctrine\ORM\Mapping\ClassMetadata;
use Illuminate\Support\ServiceProvider;

class UserServiceProvider extends ServiceProvider
{
    /**
     * @inheritdoc
     */
    public function register()
    {
        $this->app->singleton(\Jihe\Domain\User\UserRepository::class, function ($app) {
            return new \Jihe\Infrastructure\Repository\User\UserRepository(
                $app['em'],
                new ClassMetadata(\Jihe\Domain\User\User::class)
            );
        });

        $this->app->singleton(\Jihe\Domain\User\Handlers\UserRegisterHandler::class);

        $this->app->singleton(\Jihe\Domain\User\UserService::class, function ($app) {
            return new \Jihe\Domain\User\UserService(
                $app[\Jihe\Domain\User\UserRepository::class],
                new \Jihe\Domain\User\PasswordHasher(),
                $app[\Jihe\Events\Dispatcher::class]
            );
        });
    }

    /**
     * @inheritdoc
     */
    public function provides()
    {
        return [
            \Jihe\Domain\User\UserRepository::class,
            \Jihe\Domain\User\Handlers\UserRegisterHandler::class,
            \Jihe\Domain\User\UserService::class
        ];
    }


}
