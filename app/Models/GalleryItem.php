<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    protected $fillable = [
        'title',
        'image',
        'type',
        'video_url',
        'year',
        'category',
        'sort_order',
        'is_published',
        'show_on_home',
    ];

    protected function casts(): array
    {
        return [
            'year' => 'integer',
            'sort_order' => 'integer',
            'is_published' => 'boolean',
            'show_on_home' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeForHome(Builder $query): Builder
    {
        return $query->published()
            ->where('show_on_home', true)
            ->orderBy('sort_order')
            ->orderByDesc('id');
    }

    public function imageUrl(): string
    {
        return asset('storage/'.$this->image);
    }

    public function isVideo(): bool
    {
        return $this->type === 'video';
    }
}
