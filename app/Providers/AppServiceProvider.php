<?php

namespace App\Providers;

use App\AboutUser;
use App\Photo;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('App\Photo', function(){
            return new Photo();
        });
        $this->app->bind('App\AboutUser', function(){
            return new AboutUser();
        });
    }
}
