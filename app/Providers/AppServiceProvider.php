<?php

namespace App\Providers;

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
        View::composer('layouts.app', function ($view) {
            $unreadMessageCount = 0;

            if (auth()->check() && in_array(auth()->user()->role, ['student', 'supervisor'], true)) {
                $unreadMessageCount = auth()->user()->unreadMessagesCount();
            }

            $view->with('unreadMessageCount', $unreadMessageCount);
        });
    }
}
