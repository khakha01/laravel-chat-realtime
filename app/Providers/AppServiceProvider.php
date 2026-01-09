<?php

namespace App\Providers;

use App\Contracts\Repository\Message\IMessageRepository;
use App\Contracts\Repository\User\IUserRepository;
use App\Repositories\Message\MessageRepository;
use App\Repositories\User\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        # messages
        $this->app->bind(IMessageRepository::class, MessageRepository::class);

        # users
        $this->app->bind(IUserRepository::class, UserRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
