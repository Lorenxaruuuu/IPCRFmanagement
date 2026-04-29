<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use App\Services\GoogleDriveService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register Google Drive Service as a singleton
        $this->app->singleton(GoogleDriveService::class, function ($app) {
            return new GoogleDriveService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Use Tailwind for pagination links
        Paginator::useTailwind();
    }
}
