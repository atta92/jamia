<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FaizaENazar extends Model
{
    protected $fillable = [
        'name',
        'title',
        'description',
        'photo',
        'sort_order',
        'is_published',
        'show_on_home',
    ];

    protected function casts(): array
    {
        return [
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

    public function photoUrl(): ?string
    {
        return $this->photo ? asset('storage/'.$this->photo) : null;
    }
}
