<?php

namespace App\Providers;

use App\Integrations\Soap\EstrellaAdapter;
use App\Integrations\Soap\EstrellaGateway;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(EstrellaGateway::class, EstrellaAdapter::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();
    }
}
