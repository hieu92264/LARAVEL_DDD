<?php

namespace App\Modules\Identity\Infrastructure\Providers;

use Illuminate\Support\ServiceProvider;

final class IdentityServiceProvider extends ServiceProvider
{
    public function register(): void
    {

    }

    public function boot(): void
    {
        $this->loadTranslationsFrom(
            __DIR__ . '/../../Presentation/Resources/lang',
            'identity'
        );
    }
}
