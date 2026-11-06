<?php

namespace Database\Seeders;

use App\Models\Teacher;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        Storage::disk('public')->makeDirectory('teachers');

        $teachers = [
            [
                'name' => 'مولانا عبدالکریم صاحب',
                'title' => 'استادِ حدیث',
                'description' => 'صحیح بخاری و دیگر کتبِ حدیث کی تدریس میں مہارت رکھنے والے فاضل استاد۔',
                'url' => 'https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=640&q=80',
            ],
            [
                'name' => 'مولانا محمد ادریس صاحب',
                'title' => 'استادِ فقہ',
                'description' => 'فقہ حنفی اور اصولِ فقہ کی تعلیم میں طلبہ کی رہنمائی کرنے والے استاد۔',
                'url' => 'https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?auto=format&fit=crop&w=640&q=80',
            ],
            [
                'name' => 'قاری محمد سلیم صاحب',
                'title' => 'استادِ تجوید',
                'description' => 'قرآنِ کریم کے مخارج و صفات اور صحیح تلاوت کی عملی تربیت دینے والے استاد۔',
                'url' => 'https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?auto=format&fit=crop&w=640&q=80',
            ],
        ];

        foreach ($teachers as $index => $teacher) {
            $existing = Teacher::query()->where('name', $teacher['name'])->first();

            if ($existing) {
                $existing->update([
                    'title' => $teacher['title'],
                    'description' => $teacher['description'],
                    'sort_order' => $index + 1,
                    'is_published' => true,
                ]);

                continue;
            }

            Teacher::query()->create([
                'name' => $teacher['name'],
                'title' => $teacher['title'],
                'description' => $teacher['description'],
                'photo' => $this->storeRemoteImage($teacher['url']),
                'sort_order' => $index + 1,
                'is_published' => true,
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

            $filename = 'teachers/'.Str::uuid().'.jpg';
            Storage::disk('public')->put($filename, $response->body());

            return $filename;
        } catch (\Throwable) {
            return null;
        }
    }
}
