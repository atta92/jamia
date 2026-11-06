<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\Event;
use App\Models\FaizaENazar;
use App\Models\GalleryItem;
use App\Models\Hadith;
use App\Models\HifzSetting;
use App\Models\SiteSetting;
use App\Services\PrayerTimesService;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(PrayerTimesService $prayerTimes): View
    {
        $settings = SiteSetting::current();
        $site = $settings->toSiteArray();
        $prayer = $prayerTimes->forToday();

        $dailyHadith = null;
        if ($settings->show_daily_hadith) {
            $dailyHadith = $settings->dailyHadith()
                ->with('book')
                ->first();

            if (! $dailyHadith) {
                $dailyHadith = Hadith::query()
                    ->with('book')
                    ->where('book_slug', 'sahih-bukhari')
                    ->orderBy('id')
                    ->first();
            }
        }

        $galleryItems = GalleryItem::query()
            ->forHome()
            ->limit(24)
            ->get();

        $blogPosts = BlogPost::query()
            ->forHome()
            ->limit(8)
            ->get();

        $upcomingEvents = Event::query()
            ->forHome()
            ->limit(3)
            ->get();

        $faizaENazars = FaizaENazar::query()
            ->forHome()
            ->limit(4)
            ->get();

        $hifz = HifzSetting::current();

        return view('home', [
            'site' => $site,
            'dailyHadith' => $dailyHadith,
            'prayerTimings' => $prayer['timings'],
            'prayerMeta' => $prayer['meta'],
            'prayerError' => $prayer['error'],
            'prayerCities' => config('site.prayer.cities', []),
            'galleryItems' => $galleryItems,
            'blogPosts' => $blogPosts,
            'upcomingEvents' => $upcomingEvents,
            'faizaENazars' => $faizaENazars,
            'hifz' => $hifz,
        ]);
    }
}
