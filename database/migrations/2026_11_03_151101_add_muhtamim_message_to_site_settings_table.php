<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->boolean('show_muhtamim_message')->default(true)->after('about_text');
            $table->string('muhtamim_heading')->nullable()->after('show_muhtamim_message');
            $table->string('muhtamim_name')->nullable()->after('muhtamim_heading');
            $table->string('muhtamim_role')->nullable()->after('muhtamim_name');
            $table->text('muhtamim_message')->nullable()->after('muhtamim_role');
            $table->string('muhtamim_photo')->nullable()->after('muhtamim_message');
        });
    }

    public function down(): void
    {
        Schema::table('site_settings', function (Blueprint $table) {
            $table->dropColumn([
                'show_muhtamim_message',
                'muhtamim_heading',
                'muhtamim_name',
                'muhtamim_role',
                'muhtamim_message',
                'muhtamim_photo',
            ]);
        });
    }
};
