<?php

namespace Database\Seeders;

use App\Models\HifzActivity;
use App\Models\HifzSetting;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HifzSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('hifz');
        Storage::disk('public')->makeDirectory('hifz/activities');

        if (! HifzSetting::query()->exists()) {
            $featured = $this->storeRemoteImage(
                'https://images.unsplash.com/photo-1609599006353-e629aaabfeae?auto=format&fit=crop&w=1000&q=80',
                'hifz'
            );

            HifzSetting::query()->create([
                ...HifzSetting::defaults(),
                'image' => $featured,
            ]);
        }

        if (HifzActivity::query()->exists()) {
            return;
        }

        $activities = [
            [
                'title' => 'تسمیع کا منظر',
                'caption' => 'طلبہ کا دور اور تسمیع',
                'url' => 'https://images.unsplash.com/photo-1585036156171-384164a8c870?auto=format&fit=crop&w=1000&q=80',
            ],
            [
                'title' => 'حفظ کی کلاس',
                'caption' => 'روزانہ سبق اور تکرار',
                'url' => 'https://images.unsplash.com/photo-1542816417-0983c9c9ad53?auto=format&fit=crop&w=1000&q=80',
            ],
            [
                'title' => 'اجتماعِ حفاظ',
                'caption' => 'تربیتی اور دعوتی نشست',
                'url' => 'https://images.unsplash.com/photo-1564769625905-50e93615e769?auto=format&fit=crop&w=1000&q=80',
            ],
        ];

        foreach ($activities as $index => $item) {
            $path = $this->storeRemoteImage($item['url'], 'hifz/activities');

            if (! $path) {
                continue;
            }

            HifzActivity::query()->create([
                'title' => $item['title'],
                'caption' => $item['caption'],
                'image' => $path,
                'sort_order' => $index + 1,
                'is_published' => true,
            ]);
        }
    }

    protected function storeRemoteImage(string $url, string $directory): ?string
    {
        try {
            $response = Http::timeout(20)->get($url);
            if (! $response->successful()) {
                return null;
            }

            $filename = $directory.'/'.Str::uuid().'.jpg';
            Storage::disk('public')->put($filename, $response->body());

            return $filename;
        } catch (\Throwable) {
            return null;
        }
    }
}
