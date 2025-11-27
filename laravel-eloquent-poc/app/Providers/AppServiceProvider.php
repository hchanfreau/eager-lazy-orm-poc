<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB; // Import DB facade
use Illuminate\Support\Facades\Log; // Import Log facade

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
        if ($this->app->environment('local')) { // Check if application is in local environment
            DB::listen(function ($query) {
                $sql = $query->sql;
                $bindings = $query->bindings;
                $time = $query->time;

                // Format the log message
                $logMessage = "SQL: {$sql} - Bindings: " . json_encode($bindings) . " - Time: {$time}ms\n";

                // Output to stderr, which docker logs will capture
                error_log($logMessage);
            });
        }
    }
}
