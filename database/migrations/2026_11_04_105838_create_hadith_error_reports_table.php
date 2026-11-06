<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hadith_error_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('hadith_id')->constrained('hadiths')->cascadeOnDelete();
            $table->string('error_type');
            $table->string('other_detail')->nullable();
            $table->text('details')->nullable();
            $table->boolean('notify_email')->default(false);
            $table->string('email')->nullable();
            $table->string('status')->default('pending');
            $table->string('page_url')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['status', 'created_at']);
            $table->index('error_type');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hadith_error_reports');
    }
};
