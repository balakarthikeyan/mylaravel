<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use App\Models\Product;
use App\Observers\ProductObserver;

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

        // Model Observers
        Product::observe(ProductObserver::class);
    }
}
