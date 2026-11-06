<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BlogPost extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image',
        'category',
        'author_name',
        'published_at',
        'is_published',
        'show_on_home',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'published_at' => 'datetime',
            'is_published' => 'boolean',
            'show_on_home' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (BlogPost $post) {
            if (blank($post->slug) && filled($post->title)) {
                $post->slug = static::uniqueSlug($post->title, $post->id);
            }
        });
    }

    public static function uniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $base = Str::slug($title) ?: Str::random(8);
        $slug = $base;
        $i = 2;

        while (
            static::query()
                ->when($ignoreId, fn (Builder $q) => $q->where('id', '!=', $ignoreId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $base.'-'.$i;
            $i++;
        }

        return $slug;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true)
            ->where(function (Builder $q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function scopeForHome(Builder $query): Builder
    {
        return $query->published()
            ->where('show_on_home', true)
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->orderByDesc('id');
    }

    public function coverUrl(): ?string
    {
        return $this->cover_image ? asset('storage/'.$this->cover_image) : null;
    }

    public function readingLabel(): string
    {
        $date = $this->published_at ?: $this->created_at;

        return $date?->translatedFormat('d M Y') ?? '';
    }
}
