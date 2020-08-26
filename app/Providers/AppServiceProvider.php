<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // \Illuminate\Support\Str::macro('isLength', function ($str, $length) {
        //     return static::length($str) == $length;
        // });
        \Illuminate\Support\Str::mixin(new \App\Mixins\TestMixin);
    }
}
