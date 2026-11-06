<?php

namespace Database\Seeders;

use App\Models\FaizaENazar;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FaizaENazarSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('faiza-e-nazar');

        $people = [
            [
                'name' => 'حضرت مولانا عبدالرحمن صاحبؒ',
                'title' => 'شیخ الحدیث',
                'description' => 'علمِ حدیث اور تربیتِ طلاب میں برسوں فیض پہنچانے والے بزرگ، جن کی نظرِ کرم سے جامعہ کے کئی طلبہ مستفید ہوئے۔',
                'url' => 'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?auto=format&fit=crop&w=640&q=80',
            ],
            [
                'name' => 'حضرت مولانا محمد یوسف صاحبؒ',
                'title' => 'سابق مہتمم',
                'description' => 'جامعہ کی بنیاد اور نظامِ تعلیم کی ترتیب میں اہم کردار ادا کرنے والے بزرگ، جن کی دعاؤں اور رہنمائی کا فیض آج بھی جاری ہے۔',
                'url' => 'https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?auto=format&fit=crop&w=640&q=80',
            ],
            [
                'name' => 'حضرت مولانا قاری احمد صاحبؒ',
                'title' => 'مرشدِ کریم',
                'description' => 'سلوک و تزکیہ کی راہ پر رہنمائی فرمانے والے مرشدِ کریم، جن کی صحبت سے قلب و روح کو سکون اور ہدایت ملتی رہی۔',
                'url' => 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=640&q=80',
            ],
            [
                'name' => 'حضرت مولانا سید حسن صاحبؒ',
                'title' => 'مرشدِ کریم',
                'description' => 'ذکر و فکر اور اصلاحِ نفس کی تعلیم دینے والے مرشدِ کریم، جن کی دعاؤں اور رہنمائی سے جامعہ فیضیاب ہوا۔',
                'url' => 'https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=640&q=80',
            ],
        ];

        foreach ($people as $index => $person) {
            $existing = FaizaENazar::query()->where('name', $person['name'])->first();

            if ($existing) {
                $existing->update([
                    'title' => $person['title'],
                    'description' => $person['description'],
                    'sort_order' => $index + 1,
                    'is_published' => true,
                    'show_on_home' => true,
                ]);

                continue;
            }

            FaizaENazar::query()->create([
                'name' => $person['name'],
                'title' => $person['title'],
                'description' => $person['description'],
                'photo' => $this->storeRemoteImage($person['url']),
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

            $filename = 'faiza-e-nazar/'.Str::uuid().'.jpg';
            Storage::disk('public')->put($filename, $response->body());

            return $filename;
        } catch (\Throwable) {
            return null;
        }
    }
}
