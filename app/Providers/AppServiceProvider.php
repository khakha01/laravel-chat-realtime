<?php

namespace App\Providers;

use App\Contracts\Repository\Message\IMessageRepository;
use App\Repositories\Message\MessageRepository;
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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
