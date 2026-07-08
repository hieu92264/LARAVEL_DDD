<?php

declare(strict_types=1);

namespace App\Core\Providers;

use App\Core\Application\Contracts\PasswordHasher;
use App\Core\Application\Contracts\TransactionManager;
use App\Core\Infrastructure\Persistence\LaravelTransactionManager;
use App\Core\Infrastructure\Security\LaravelPasswordHasher;
use Illuminate\Support\ServiceProvider;

final class CoreServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(PasswordHasher::class, LaravelPasswordHasher::class);

        $this->app->bind(TransactionManager::class, LaravelTransactionManager::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
