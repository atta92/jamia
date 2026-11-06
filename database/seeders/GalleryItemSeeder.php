<?php

namespace Database\Seeders;

use App\Models\GalleryItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class GalleryItemSeeder extends Seeder
{
    public function run(): void
    {
        if (GalleryItem::query()->exists()) {
            return;
        }

        Storage::disk('public')->makeDirectory('gallery');

        $samples = [
            [
                'title' => 'مسجد کا منظر',
                'url' => 'https://images.unsplash.com/photo-1564769625905-50e93615e769?auto=format&fit=crop&w=1280&q=80',
                'year' => 2026,
                'category' => 'campus',
            ],
            [
                'title' => 'نماز کی صفیں',
                'url' => 'https://images.unsplash.com/photo-1585036156171-384074a5bcbc?auto=format&fit=crop&w=1280&q=80',
                'year' => 2026,
                'category' => 'worship',
            ],
            [
                'title' => 'اسلامی فنِ تعمیر',
                'url' => 'https://images.unsplash.com/photo-1591604129939-f1efa4d9f7fa?auto=format&fit=crop&w=1280&q=80',
                'year' => 2026,
                'category' => 'campus',
            ],
            [
                'title' => 'قرآنی مطالعہ',
                'url' => 'https://images.unsplash.com/photo-1609599006353-e629aaabfeae?auto=format&fit=crop&w=1280&q=80',
                'year' => 2025,
                'category' => 'education',
            ],
            [
                'title' => 'شبِ قدر کا ماحول',
                'url' => 'https://images.unsplash.com/photo-1519817659676-abc8711faa30?auto=format&fit=crop&w=1280&q=80',
                'year' => 2025,
                'category' => 'events',
            ],
            [
                'title' => 'طلبہ کی محفل',
                'url' => 'https://images.unsplash.com/photo-1542816417-0983c9c9ad53?auto=format&fit=crop&w=1280&q=80',
                'year' => 2026,
                'category' => 'education',
            ],
            [
                'title' => 'منارہ و قبہ',
                'url' => 'https://images.unsplash.com/photo-1548013146-72479768bada?auto=format&fit=crop&w=1280&q=80',
                'year' => 2025,
                'category' => 'campus',
            ],
            [
                'title' => 'اجتماعی دعا',
                'url' => 'https://images.unsplash.com/photo-1512632576470-8a35dba6f9c2?auto=format&fit=crop&w=1280&q=80',
                'year' => 2026,
                'category' => 'events',
            ],
        ];

        foreach ($samples as $index => $sample) {
            $path = $this->storeRemoteImage($sample['url']);

            if (! $path) {
                continue;
            }

            GalleryItem::query()->create([
                'title' => $sample['title'],
                'image' => $path,
                'type' => 'photo',
                'year' => $sample['year'],
                'category' => $sample['category'],
                'sort_order' => $index + 1,
                'is_published' => true,
                'show_on_home' => true,
            ]);
        }
    }

    protected function storeRemoteImage(string $url): ?string
    {
        try {
            $response = Http::timeout(20)->get($url);

            if (! $response->successful()) {
                return null;
            }

            $filename = 'gallery/'.Str::uuid().'.jpg';
            Storage::disk('public')->put($filename, $response->body());

            return $filename;
        } catch (\Throwable) {
            return null;
        }
    }
}
