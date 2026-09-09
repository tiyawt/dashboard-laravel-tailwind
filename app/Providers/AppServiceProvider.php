<?php

namespace App\Providers;
use App\Models\PengajuanBarang;
use App\Observers\PengajuanBarangObserver;
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
        PengajuanBarang::observe(PengajuanBarangObserver::class);
    }
}
