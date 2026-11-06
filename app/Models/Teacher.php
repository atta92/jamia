<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'name',
        'title',
        'description',
        'photo',
        'sort_order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
            'is_published' => 'boolean',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function scopeForAbout(Builder $query): Builder
    {
        return $query->published()
            ->orderBy('sort_order')
            ->orderByDesc('id');
    }

    public function photoUrl(): ?string
    {
        return $this->photo ? asset('storage/'.$this->photo) : null;
    }
}
