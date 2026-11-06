<?php

namespace App\Providers;

use App\Models\SiteSetting;
use App\Services\PrayerTimesService;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        View::composer(['layouts.public', 'partials.header', 'partials.footer'], function ($view) {
            if (! isset($view->getData()['site'])) {
                $view->with('site', SiteSetting::current()->toSiteArray());
            }
        });

        View::composer('partials.header', function ($view) {
            if (isset($view->getData()['prayerCountdown'])) {
                return;
            }

            try {
                $view->with('prayerCountdown', app(PrayerTimesService::class)->countdownSchedule());
            } catch (\Throwable) {
                $view->with('prayerCountdown', [
                    'timezone' => config('app.timezone', 'UTC'),
                    'prayers' => [],
                ]);
            }
        });
    }
}
