<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('show_durood_section')->default(true)->after('show_muhtamim_message');
            $table->string('durood_kicker')->nullable()->after('show_durood_section');
            $table->string('durood_text')->nullable()->after('durood_kicker');
            $table->text('durood_fazilat')->nullable()->after('durood_text');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'show_durood_section',
                'durood_kicker',
                'durood_text',
                'durood_fazilat',
            ]);
        });
    }
};
