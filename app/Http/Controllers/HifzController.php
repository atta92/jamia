<?php

namespace App\Http\Controllers;

use App\Models\HifzActivity;
use App\Models\HifzSetting;
use App\Models\SiteSetting;
use Illuminate\View\View;

class HifzController extends Controller
{
    public function __invoke(): View
    {
        $hifz = HifzSetting::current();

        $activities = HifzActivity::query()
            ->published()
            ->ordered()
            ->get();

        return view('hifz.show', [
            'site' => SiteSetting::current()->toSiteArray(),
            'hifz' => $hifz,
            'activities' => $activities,
            'solidHeader' => true,
        ]);
    }
}
