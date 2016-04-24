<?php
namespace App\Providers;

use Doctrine\ORM\Mapping\ClassMetadata;
use Illuminate\Support\ServiceProvider;

class TeamServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(\Jihe\Domain\Team\CertificationRepository::class, function ($app) {
            return new \Jihe\Infrastructure\Repository\Team\CertificationRepository(
                $app['em'],
                new ClassMetadata(\Jihe\Domain\Team\Certification::class)
            );
        });
        $this->app->singleton(\Jihe\Domain\Team\EnrollmentRequestRepository::class, function ($app) {
            return new \Jihe\Infrastructure\Repository\Team\EnrollmentRequestRepository(
                $app['em'],
                new ClassMetadata(\Jihe\Domain\Team\EnrollmentRequest::class)
            );
        });
        $this->app->singleton(\Jihe\Domain\Team\IdentifyRequestRepository::class, function ($app) {
            return new \Jihe\Infrastructure\Repository\Team\IdentifyRequestRepository(
                $app['em'],
                new ClassMetadata(\Jihe\Domain\Team\IdentifyRequest::class)
            );
        });
        $this->app->singleton(\Jihe\Domain\Team\UpdateRequestRepository::class, function ($app) {
            return new \Jihe\Infrastructure\Repository\Team\UpdateRequestRepository(
                $app['em'],
                new ClassMetadata(\Jihe\Domain\Team\UpdateRequest::class)
            );
        });
        $this->app->singleton(\Jihe\Domain\Team\EnrollmentRequestService::class);
        $this->app->singleton(\Jihe\Domain\Team\IdentifyRequestService::class);
        $this->app->singleton(\Jihe\Domain\Team\UpdateRequestService::class);
    }

    public function provides()
    {
        return [
            \Jihe\Domain\Team\CertificationRepository::class,
            \Jihe\Domain\Team\EnrollmentRequestRepository::class,
            \Jihe\Domain\Team\IdentifyRequestRepository::class,
            \Jihe\Domain\Team\UpdateRequestRepository::class,
            \Jihe\Domain\Team\TeamRepository::class,
            \Jihe\Domain\Team\EnrollmentRequestService::class,
            \Jihe\Domain\Team\IdentifyRequestService::class,
            \Jihe\Domain\Team\UpdateRequestService::class
        ];
    }
}