<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('hifz_settings', function (Blueprint $table) {
            $table->string('about_heading')->nullable()->after('body');
            $table->string('activities_heading')->nullable()->after('about_heading');
            $table->string('contact_cta_label')->nullable()->after('cta_label');
        });
    }

    public function down(): void
    {
        Schema::table('hifz_settings', function (Blueprint $table) {
            $table->dropColumn(['about_heading', 'activities_heading', 'contact_cta_label']);
        });
    }
};
