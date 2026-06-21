<?php

namespace App\Providers;

use App\Models\MapLocation;
use App\Observers\MapLocationObserver;
use App\View\Composers\CareLayoutComposer;
use App\View\Composers\VetLayoutComposer;
use Illuminate\Support\Facades\View;
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
        MapLocation::observe(MapLocationObserver::class);

        View::composer('vet.layout', VetLayoutComposer::class);
        View::composer('care.layout', CareLayoutComposer::class);
    }
}
