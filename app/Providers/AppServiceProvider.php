<?php

namespace App\Providers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191); // For utf8mb4 encoding
        //     if (env('APP_DEBUG')) {
        //     DB::listen(function ($query) {
        //         Log::info($query->sql, $query->bindings, $query->time);
        //     });
        // }
    }
}
