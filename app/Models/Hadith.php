<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Hadith extends Model
{
    protected $fillable = [
        'api_id',
        'hadith_book_id',
        'hadith_chapter_id',
        'hadith_number',
        'english_narrator',
        'hadith_english',
        'hadith_urdu',
        'urdu_narrator',
        'hadith_arabic',
        'heading_arabic',
        'heading_urdu',
        'heading_english',
        'book_slug',
        'chapter_api_id',
        'volume',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'api_id' => 'integer',
            'chapter_api_id' => 'integer',
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(HadithBook::class, 'hadith_book_id');
    }

    public function chapter(): BelongsTo
    {
        return $this->belongsTo(HadithChapter::class, 'hadith_chapter_id');
    }
}
