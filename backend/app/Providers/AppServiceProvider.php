<?php

namespace App\Providers;

use App\Services\KafkaConsumerService;
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
        $this->app->singleton(KafkaConsumerService::class, function ($app) {
            return new KafkaConsumerService(config('kafka.topics.orders-topic'));
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
