<?php

namespace App\Providers;

use App\Repositories\VisitRedisRepository;
use App\Repositories\VisitRepositoryInterface;
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
        $this->app->bind(VisitRepositoryInterface::class, VisitRedisRepository::class);
    }
}
