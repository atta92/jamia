<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('hadith_books', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('api_id')->unique();
            $table->string('book_name');
            $table->string('book_slug')->unique();
            $table->string('writer_name')->nullable();
            $table->text('about_writer')->nullable();
            $table->string('writer_death')->nullable();
            $table->unsignedInteger('hadiths_count')->default(0);
            $table->unsignedInteger('chapters_count')->default(0);
            $table->timestamps();
        });

        Schema::create('hadith_chapters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('api_id');
            $table->foreignId('hadith_book_id')->constrained('hadith_books')->cascadeOnDelete();
            $table->unsignedInteger('chapter_number');
            $table->string('chapter_english')->nullable();
            $table->string('chapter_urdu')->nullable();
            $table->string('chapter_arabic')->nullable();
            $table->string('book_slug');
            $table->timestamps();

            $table->unique(['hadith_book_id', 'chapter_number']);
            $table->unique(['book_slug', 'api_id']);
            $table->index('book_slug');
        });

        Schema::create('hadiths', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('api_id')->unique();
            $table->foreignId('hadith_book_id')->constrained('hadith_books')->cascadeOnDelete();
            $table->foreignId('hadith_chapter_id')->nullable()->constrained('hadith_chapters')->nullOnDelete();
            $table->string('hadith_number')->nullable();
            $table->text('english_narrator')->nullable();
            $table->longText('hadith_english')->nullable();
            $table->longText('hadith_urdu')->nullable();
            $table->text('urdu_narrator')->nullable();
            $table->longText('hadith_arabic')->nullable();
            $table->text('heading_arabic')->nullable();
            $table->text('heading_urdu')->nullable();
            $table->text('heading_english')->nullable();
            $table->string('book_slug')->nullable();
            $table->unsignedInteger('chapter_api_id')->nullable();
            $table->string('volume')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();

            $table->index(['hadith_book_id', 'hadith_chapter_id']);
            $table->index('book_slug');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hadiths');
        Schema::dropIfExists('hadith_chapters');
        Schema::dropIfExists('hadith_books');
    }
};
