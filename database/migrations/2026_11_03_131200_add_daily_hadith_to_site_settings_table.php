<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('show_daily_hadith')->default(true)->after('hero_image');
            $table->foreignId('daily_hadith_id')
                ->nullable()
                ->after('show_daily_hadith')
                ->constrained('hadiths')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropConstrainedForeignId('daily_hadith_id');
            $table->dropColumn('show_daily_hadith');
        });
    }
};
