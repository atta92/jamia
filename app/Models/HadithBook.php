<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class HadithBook extends Model
{
    protected $fillable = [
        'api_id',
        'book_name',
        'book_slug',
        'writer_name',
        'about_writer',
        'writer_death',
        'hadiths_count',
        'chapters_count',
    ];

    protected function casts(): array
    {
        return [
            'api_id' => 'integer',
            'hadiths_count' => 'integer',
            'chapters_count' => 'integer',
        ];
    }

    public function chapters(): HasMany
    {
        return $this->hasMany(HadithChapter::class)->orderBy('chapter_number');
    }

    public function hadiths(): HasMany
    {
        return $this->hasMany(Hadith::class);
    }

    public const FRONTEND_HIDDEN_SLUGS = [
        'al-silsila-sahiha',
    ];

    public function scopeVisibleOnFrontend(Builder $query): Builder
    {
        return $query->whereNotIn('book_slug', self::FRONTEND_HIDDEN_SLUGS);
    }

    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        $term = trim((string) $term);

        if ($term === '') {
            return $query;
        }

        return $query->where(function (Builder $q) use ($term) {
            $q->where('book_name', 'like', "%{$term}%")
                ->orWhere('book_slug', 'like', "%{$term}%")
                ->orWhere('writer_name', 'like', "%{$term}%");
        });
    }

    public function isHiddenOnFrontend(): bool
    {
        return in_array($this->book_slug, self::FRONTEND_HIDDEN_SLUGS, true);
    }

    public function titleUr(): string
    {
        return match ($this->book_slug) {
            'sahih-bukhari' => 'صحیح بخاری',
            'sahih-muslim' => 'صحیح مسلم',
            'al-tirmidhi' => 'جامع ترمذی',
            'abu-dawood' => 'سنن ابو داؤد',
            'ibn-e-majah' => 'سنن ابن ماجہ',
            'sunan-nasai' => 'سنن نسائی',
            'mishkat' => 'مشکاة المصابیح',
            'musnad-ahmad' => 'مسند احمد',
            'al-silsila-sahiha' => 'السلسلہ الصحیحہ',
            default => $this->book_name,
        };
    }

    public function getRouteKeyName(): string
    {
        return 'book_slug';
    }
}
