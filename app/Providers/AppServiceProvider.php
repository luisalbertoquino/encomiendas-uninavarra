<?php

namespace App\Providers;

use App\Models\Ajuste;
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
        View::composer('layouts.encomiendas', function ($view) {
            if (! $view->offsetExists('ajuste')) {
                $view->with('ajuste', Ajuste::actual());
            }
        });
    }
}
