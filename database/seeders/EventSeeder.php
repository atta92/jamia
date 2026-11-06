<?php

namespace Database\Seeders;

use App\Models\Event;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class EventSeeder extends Seeder
{
    public function run(): void
    {
        if (Event::query()->exists()) {
            return;
        }

        Storage::disk('public')->makeDirectory('events');

        $events = [
            [
                'title' => 'سالانہ جلسہ عام',
                'excerpt' => 'جامعہ کا سالانہ جلسہ عام — تلاوت، نعت، تقاریر اور طلبہ کی کامیابیوں کا اعلان۔',
                'body' => '<p>سالانہ جلسہ عام جامعہ کی سب سے اہم تقریب ہے جس میں والدین، معززین اور طلبہ شریک ہوتے ہیں۔</p><p>پروگرام میں تلاوتِ قرآن، نعت رسول ﷺ، تعلیمی رپورٹ اور طلبہ کی کارکردگی کا اعلان شامل ہوگا۔</p>',
                'location' => 'جامعہ ہال، چکوال',
                'starts_at' => now()->addMonths(1)->setTime(10, 0),
                'ends_at' => now()->addMonths(1)->setTime(14, 0),
                'audience' => 'والدین، طلبہ، اساتذہ اور علاقے کے معززین',
                'highlights' => 'تلاوت، نعت، تعلیمی رپورٹ، اسناد کی تقسیم',
                'url' => 'https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'تلاوت و تجوید ورکشاپ',
                'excerpt' => 'طلبہ کے لیے تین روزہ ورکشاپ — مخارج، صفات اور صحیح تلاوت کی عملی مشق۔',
                'body' => '<p>اس ورکشاپ میں طلبہ مخارج، صفات اور تجوید کے بنیادی اصول عملی طور پر سیکھیں گے۔</p><p>ہر شرکت کنندہ کو مختصر عملی تشخیص کے بعد سرٹیفکیٹ دیا جائے گا۔</p>',
                'location' => 'درس گاہ اول، جامعہ',
                'starts_at' => now()->addMonths(2)->setTime(9, 0),
                'ends_at' => now()->addMonths(2)->addDays(2)->setTime(13, 0),
                'audience' => 'طلبہ اور قراءت کے شوقین افراد',
                'highlights' => 'عملی مشق، انفرادی اصلاح، سرٹیفکیٹ',
                'url' => 'https://images.unsplash.com/photo-1609599006353-e629aaabfeae?auto=format&fit=crop&w=1200&q=80',
            ],
        ];

        foreach ($events as $index => $event) {
            $cover = $this->storeRemoteImage($event['url']);

            Event::query()->create([
                'title' => $event['title'],
                'slug' => Str::slug($event['title']).'-'.($index + 1),
                'excerpt' => $event['excerpt'],
                'body' => $event['body'],
                'cover_image' => $cover,
                'location' => $event['location'],
                'starts_at' => $event['starts_at'],
                'ends_at' => $event['ends_at'],
                'audience' => $event['audience'],
                'highlights' => $event['highlights'],
                'is_published' => true,
                'show_on_home' => true,
                'sort_order' => $index + 1,
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

            $filename = 'events/'.Str::uuid().'.jpg';
            Storage::disk('public')->put($filename, $response->body());

            return $filename;
        } catch (\Throwable) {
            return null;
        }
    }
}
