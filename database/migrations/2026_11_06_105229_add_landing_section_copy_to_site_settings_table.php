<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->string('hero_title')->nullable()->after('hero_line');
            $table->string('hero_hadith_heading')->nullable()->after('show_daily_hadith');
            $table->string('faiza_kicker')->nullable()->after('hero_hadith_heading');

            $table->boolean('show_prayer_section')->default(true)->after('durood_fazilat');
            $table->string('prayer_kicker')->nullable()->after('show_prayer_section');
            $table->string('prayer_title')->nullable()->after('prayer_kicker');

            $table->boolean('show_gallery_section')->default(true)->after('prayer_title');
            $table->string('gallery_kicker')->nullable()->after('show_gallery_section');
            $table->string('gallery_title')->nullable()->after('gallery_kicker');
            $table->text('gallery_lead')->nullable()->after('gallery_title');
            $table->string('gallery_cta')->nullable()->after('gallery_lead');

            $table->boolean('show_blog_section')->default(true)->after('gallery_cta');
            $table->string('blog_kicker')->nullable()->after('show_blog_section');
            $table->string('blog_title')->nullable()->after('blog_kicker');
            $table->text('blog_lead')->nullable()->after('blog_title');
            $table->string('blog_cta')->nullable()->after('blog_lead');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'hero_title',
                'hero_hadith_heading',
                'faiza_kicker',
                'show_prayer_section',
                'prayer_kicker',
                'prayer_title',
                'show_gallery_section',
                'gallery_kicker',
                'gallery_title',
                'gallery_lead',
                'gallery_cta',
                'show_blog_section',
                'blog_kicker',
                'blog_title',
                'blog_lead',
                'blog_cta',
            ]);
        });
    }
};
