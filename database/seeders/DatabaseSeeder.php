<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::query()->updateOrCreate(
            ['email' => 'admin@jamia.test'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ]
        );

        if (! SiteSetting::query()->exists()) {
            SiteSetting::query()->create(SiteSetting::defaults());
        }

        $this->call(GalleryItemSeeder::class);
        $this->call(BlogPostSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(FaizaENazarSeeder::class);
        $this->call(TeacherSeeder::class);
        $this->call(HifzSeeder::class);
    }
}
