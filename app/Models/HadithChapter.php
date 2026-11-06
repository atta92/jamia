<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HadithChapter extends Model
{
    protected $fillable = [
        'api_id',
        'hadith_book_id',
        'chapter_number',
        'chapter_english',
        'chapter_urdu',
        'chapter_arabic',
        'book_slug',
    ];

    protected function casts(): array
    {
        return [
            'api_id' => 'integer',
            'chapter_number' => 'integer',
        ];
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(HadithBook::class, 'hadith_book_id');
    }

    public function hadiths(): HasMany
    {
        return $this->hasMany(Hadith::class);
    }

    public function titleUr(): string
    {
        return $this->chapter_urdu
            ?: $this->chapter_arabic
            ?: $this->chapter_english
            ?: ('باب '.$this->chapter_number);
    }

    public function titleEn(): string
    {
        return $this->chapter_english
            ?: $this->chapter_urdu
            ?: ('Chapter '.$this->chapter_number);
    }
}
