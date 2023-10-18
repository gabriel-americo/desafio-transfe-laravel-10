<?php

namespace App\Providers;

use App\Models\Retailer;
use App\Models\User;
use App\Observers\RetailerObserver;
use App\Observers\UserObserver;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        Passport::ignoreMigrations();
    }


    public function boot(): void
    {
        User::observe(UserObserver::class);
        Retailer::observe(RetailerObserver::class);
    }
}
