<?php

namespace App\Providers;

use App\TencentIM\TLSSigAPIv2;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(TLSSigAPIv2::class, function () {
            return new TLSSigAPIv2(config('im.appid'), config('im.app_secret'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();


        Passport::personalAccessTokensExpireIn(now()->addDays(2));
    }
}
