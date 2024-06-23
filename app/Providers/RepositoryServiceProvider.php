<?php

namespace App\Providers;

use App\Modules\Media\MediaService;
use App\Modules\User\UserServiceInterface;
use App\Modules\User\UserService;
use App\Repository\UserRepositoryInterface;
use App\Repository\Eloquent\UserRepository;
use App\Modules\Auth\AuthServiceInterface;
use App\Modules\Auth\AuthService;
use App\Modules\Event\EventService;
use App\Modules\Event\EventServiceInterface;
use App\Repository\EventRepositoryInterface;
use App\Repository\Eloquent\EventRepository;
use Illuminate\Support\ServiceProvider;
use App\Modules\Organizer\OrganizerServiceInterface;
use App\Modules\Organizer\OrganizerService;
use App\Repository\OrganizerRepositoryInterface;
use App\Repository\Eloquent\OrganizerRepository;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        /**
         * Services registered
         */
        $this->app->bind(UserServiceInterface::class, UserService::class);
        $this->app->bind(AuthServiceInterface::class, AuthService::class);
        $this->app->bind(EventServiceInterface::class, EventService::class);
        $this->app->bind(OrganizerServiceInterface::class, OrganizerService::class);

        
        $this->app->singleton(MediaService::class, function ($app) {
            return new MediaService();
        });

        /**
         * Repositories registered
         */
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(EventRepositoryInterface::class, EventRepository::class);
        $this->app->bind(OrganizerRepositoryInterface::class, OrganizerRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
