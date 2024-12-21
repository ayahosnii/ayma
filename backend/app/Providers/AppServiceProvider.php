<?php

namespace App\Providers;

use App\Models\Order;
use App\Observers\OrderObserver;
use App\Repositories\Dashboard\Contracts\ProductRepositoryInterface;
use App\Repositories\Dashboard\ProductRepository;
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
        $this->app->bind(ProductRepositoryInterface::class, ProductRepository::class);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Order::observe(OrderObserver::class);
    }
}
