<?php

namespace App\Providers;

use App\Modules\User\UserServiceInterface;
use App\Modules\User\UserService;
use App\Repository\UserRepositoryInterface;
use App\Repository\Eloquent\UserRepository;
use Illuminate\Support\ServiceProvider;

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

        /**
         * Repositories registered
         */
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
