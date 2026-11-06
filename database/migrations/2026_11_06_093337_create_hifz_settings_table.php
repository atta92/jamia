<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hifz_settings', function (Blueprint $table) {
            $table->id();
            $table->boolean('show_on_home')->default(true);
            $table->string('kicker')->nullable();
            $table->string('title')->nullable();
            $table->text('excerpt')->nullable();
            $table->string('image')->nullable();
            $table->string('page_title')->nullable();
            $table->string('page_lead')->nullable();
            $table->longText('body')->nullable();
            $table->string('cta_label')->nullable();
            $table->string('counter_1_value')->nullable();
            $table->string('counter_1_label')->nullable();
            $table->string('counter_2_value')->nullable();
            $table->string('counter_2_label')->nullable();
            $table->string('counter_3_value')->nullable();
            $table->string('counter_3_label')->nullable();
            $table->string('counter_4_value')->nullable();
            $table->string('counter_4_label')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hifz_settings');
    }
};
