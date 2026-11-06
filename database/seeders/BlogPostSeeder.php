<?php

namespace Database\Seeders;

use App\Models\BlogPost;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogPostSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('blog');

        $posts = [
            [
                'title' => 'علم کا سفر ایمان سے شروع ہوتا ہے',
                'category' => 'تعلیم',
                'excerpt' => 'طالبِ علم جب نیت درست رکھے تو ہر سبق عبادت بن جاتا ہے — جامعہ کی تعلیم کا پہلا سبق یہی ہے۔',
                'body' => '<p>علم صرف معلومات کا ڈھیر نہیں، بلکہ دل کی اصلاح اور عمل کی راہ ہے۔ ہماری جامعہ میں طالبِ علم کو قرآن، سنت اور اخلاق کی روشنی میں آگے بڑھنے کی دعوت دی جاتی ہے۔</p><p>جب نیت خالص ہو، تو درس بھی عبادت بن جاتا ہے اور استاد بھی رہنما۔</p>',
                'url' => 'https://images.unsplash.com/photo-1609599006353-e629aaabfeae?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'نماز: دن کا سب سے خوبصورت وقفہ',
                'category' => 'عبادت',
                'excerpt' => 'پانچ وقت کی نماز زندگی کو ترتیب دیتی ہے — وقت کی پابندی ایمان کی پہچان ہے۔',
                'body' => '<p>نماز بندے کو اپنے رب سے جوڑتی ہے اور دن بھر کی بے چینی کو سکون میں بدل دیتی ہے۔</p><p>جامعہ میں اوقاتِ نماز کی پابندی طلبہ کی تربیت کا اہم حصہ ہے۔</p>',
                'url' => 'https://images.unsplash.com/photo-1564769625905-50e93615e769?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'خدمتِ خلق: ایمان کا عملی پہلو',
                'category' => 'خدمت',
                'excerpt' => 'علم تب مکمل ہوتا ہے جب دوسروں کے کام آئے — خدمتِ خلق سنتِ نبوی کا حصہ ہے۔',
                'body' => '<p>ہماری کوشش ہے کہ طالبِ علم صرف کتابی علم نہ سیکھے بلکہ معاشرے کا مفید فرد بھی بنے۔</p><p>خدمتِ خلق، اخلاق اور تعاون — یہی جامعہ کی روح ہے۔</p>',
                'url' => 'https://images.unsplash.com/photo-1542816417-0983c9c9ad53?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'حفظِ قرآن: صبر اور استقامت کا راستہ',
                'category' => 'حفظ',
                'excerpt' => 'حفظ صرف یادداشت نہیں، بلکہ دل کی صفائی، وقت کی پابندی اور استاد کے ادب کا نام ہے۔',
                'body' => '<p>حفظِ قرآن کا سفر قدم بہ قدم طے ہوتا ہے۔ روزانہ کا سبق، تکرار اور دور — یہی تین ستون طالبِ علم کو منزل تک پہنچاتے ہیں۔</p><p>جامعہ میں حفاظ کی تربیت نیت، اخلاق اور مسلسل محنت پر رکھی جاتی ہے تاکہ قرآن سینے میں بھی ہو اور عمل میں بھی۔</p>',
                'url' => 'https://images.unsplash.com/photo-1585036156171-384164a8c870?auto=format&fit=crop&w=1200&q=80',
            ],
            [
                'title' => 'استاد کا ادب: علم کی کنجی',
                'category' => 'اخلاق',
                'excerpt' => 'علم اس وقت برکت بنتا ہے جب طالبِ علم استاد کا احترام کرے اور درس کو امانت سمجھے۔',
                'body' => '<p>بزرگانِ دین نے ہمیشہ ادب کو علم سے پہلے رکھا۔ ادب نہ ہو تو معلومات دل میں نہیں اترتیں۔</p><p>ہماری جامعہ میں طلبہ کو یہ سکھایا جاتا ہے کہ استاد کی خدمت، درس کی پابندی اور اخلاص — یہی کامیابی کی بنیاد ہے۔</p>',
                'url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=1200&q=80',
            ],
        ];

        foreach ($posts as $index => $post) {
            if (BlogPost::query()->where('title', $post['title'])->exists()) {
                continue;
            }

            $cover = $this->storeRemoteImage($post['url']);

            BlogPost::query()->create([
                'title' => $post['title'],
                'slug' => Str::slug($post['title']).'-'.($index + 1),
                'excerpt' => $post['excerpt'],
                'body' => $post['body'],
                'cover_image' => $cover,
                'category' => $post['category'],
                'author_name' => 'ادارہ',
                'published_at' => now()->subDays($index),
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

            $filename = 'blog/'.Str::uuid().'.jpg';
            Storage::disk('public')->put($filename, $response->body());

            return $filename;
        } catch (\Throwable) {
            return null;
        }
    }
}
