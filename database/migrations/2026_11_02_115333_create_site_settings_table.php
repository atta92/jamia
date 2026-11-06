<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('site_settings', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('tagline')->nullable();
            $table->string('location_label')->nullable();
            $table->string('hero_line')->nullable();
            $table->text('short_description')->nullable();
            $table->string('about_heading')->nullable();
            $table->text('about_text')->nullable();
            $table->string('hero_image')->nullable();
            $table->string('prayer_city')->default('Chakwal');
            $table->string('prayer_city_ur')->default('چکوال');
            $table->string('prayer_country')->default('Pakistan');
            $table->string('prayer_country_ur')->default('پاکستان');
            $table->string('prayer_timezone')->default('Asia/Karachi');
            $table->unsignedTinyInteger('prayer_method')->default(1);
            $table->unsignedTinyInteger('iqamah_fajr')->default(20);
            $table->unsignedTinyInteger('iqamah_dhuhr')->default(15);
            $table->unsignedTinyInteger('iqamah_asr')->default(15);
            $table->unsignedTinyInteger('iqamah_maghrib')->default(10);
            $table->unsignedTinyInteger('iqamah_isha')->default(15);
            $table->unsignedTinyInteger('iqamah_jummah')->default(30);
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('address')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('site_settings');
    }
};
