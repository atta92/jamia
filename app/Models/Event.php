<?php

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'body',
        'cover_image',
        'location',
        'starts_at',
        'ends_at',
        'audience',
        'highlights',
        'is_published',
        'show_on_home',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_published' => 'boolean',
            'show_on_home' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Event $event) {
            if (blank($event->slug) && filled($event->title)) {
                $event->slug = static::uniqueSlug($event->title, $event->id);
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
        return $query->where('is_published', true);
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->published()
            ->where(function (Builder $q) {
                $q->whereNull('ends_at')
                    ->where(function (Builder $inner) {
                        $inner->whereNull('starts_at')
                            ->orWhere('starts_at', '>=', now()->startOfDay());
                    })
                    ->orWhere('ends_at', '>=', now()->startOfDay());
            });
    }

    public function scopeForHome(Builder $query): Builder
    {
        return $query->upcoming()
            ->where('show_on_home', true)
            ->orderBy('sort_order')
            ->orderBy('starts_at')
            ->orderBy('id');
    }

    public function coverUrl(): ?string
    {
        return $this->cover_image ? asset('storage/'.$this->cover_image) : null;
    }

    public function durationDays(): int
    {
        if (! $this->starts_at) {
            return 1;
        }

        if (! $this->ends_at || $this->ends_at->lessThanOrEqualTo($this->starts_at)) {
            return 1;
        }

        return max(1, $this->starts_at->copy()->startOfDay()->diffInDays($this->ends_at->copy()->startOfDay()) + 1);
    }

    public function durationLabel(): string
    {
        $days = $this->durationDays();

        return str_pad((string) $days, 2, '0', STR_PAD_LEFT).' دن';
    }

    public function dateLabel(): string
    {
        if (! $this->starts_at) {
            return '';
        }

        $start = $this->starts_at;
        $end = $this->ends_at;

        if ($end && ! $end->isSameDay($start)) {
            if ($start->isSameMonth($end) && $start->isSameYear($end)) {
                return $this->urDay($start).'-'.$this->urDay($end).' '.$this->urMonth($start).' '.$start->year;
            }

            return $this->formatUrDate($start).' - '.$this->formatUrDate($end);
        }

        return $this->formatUrDate($start);
    }

    protected function formatUrDate(CarbonInterface $date): string
    {
        return $this->urDay($date).' '.$this->urMonth($date).' '.$date->year;
    }

    protected function urDay(CarbonInterface $date): string
    {
        return (string) $date->day;
    }

    protected function urMonth(CarbonInterface $date): string
    {
        return match ((int) $date->month) {
            1 => 'جنوری',
            2 => 'فروری',
            3 => 'مارچ',
            4 => 'اپریل',
            5 => 'مئی',
            6 => 'جون',
            7 => 'جولائی',
            8 => 'اگست',
            9 => 'ستمبر',
            10 => 'اکتوبر',
            11 => 'نومبر',
            12 => 'دسمبر',
            default => $date->format('M'),
        };
    }
}
