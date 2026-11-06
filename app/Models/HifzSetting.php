<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class HifzSetting extends Model
{
    protected $fillable = [
        'show_on_home',
        'kicker',
        'title',
        'excerpt',
        'image',
        'page_title',
        'page_lead',
        'body',
        'about_heading',
        'activities_heading',
        'cta_label',
        'contact_cta_label',
        'counter_1_value',
        'counter_1_label',
        'counter_2_value',
        'counter_2_label',
        'counter_3_value',
        'counter_3_label',
        'counter_4_value',
        'counter_4_label',
    ];

    protected function casts(): array
    {
        return [
            'show_on_home' => 'boolean',
        ];
    }

    public static function defaults(): array
    {
        return [
            'show_on_home' => true,
            'kicker' => 'حفظ القرآن',
            'title' => 'مدرسہ حفظ القرآن',
            'excerpt' => 'جامعہ میں حفظِ قرآن کا خصوصی شعبہ طلبہ کو صحیح تلفظ، تکرار اور دور کے ساتھ قرآنِ مجید حفظ کرانے کا اہتمام کرتا ہے۔',
            'page_title' => 'مدرسہ حفظ القرآن',
            'page_lead' => 'حفظ، تکرار اور تربیت — ایک مکمل ماحول',
            'body' => "الحمد للہ، ہمارے مدرسہ حفظ القرآن میں طلبہ کو منظم نظام کے تحت قرآن مجید حفظ کروایا جاتا ہے۔\n\nروزانہ سبق، دور، اور تسمیع کے ساتھ اخلاقی تربیت بھی دی جاتی ہے تاکہ طالبِ علم صرف حافظ نہ بنے بلکہ عامل بھی بنے۔",
            'about_heading' => 'مدرسہ حفظ القرآن کے بارے میں',
            'activities_heading' => 'مدرسہ کی سرگرمیاں',
            'cta_label' => 'مزید دیکھیں',
            'contact_cta_label' => 'مزید معلومات کے لیے رابطہ',
            'counter_1_value' => '120+',
            'counter_1_label' => 'طلبہ',
            'counter_2_value' => '45+',
            'counter_2_label' => 'حفاظ',
            'counter_3_value' => '8',
            'counter_3_label' => 'اساتذہ',
            'counter_4_value' => '15+',
            'counter_4_label' => 'سال',
        ];
    }

    public static function current(): self
    {
        return Cache::remember('hifz_setting_current', 300, function () {
            $row = static::query()->first();

            if ($row) {
                return $row;
            }

            return static::query()->create(static::defaults());
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('hifz_setting_current');
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::clearCache());
        static::deleted(fn () => static::clearCache());
    }

    public function imageUrl(): ?string
    {
        return $this->image ? asset('storage/'.$this->image) : null;
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public function counters(): array
    {
        $items = [];

        for ($i = 1; $i <= 4; $i++) {
            $value = trim((string) $this->{"counter_{$i}_value"});
            $label = trim((string) $this->{"counter_{$i}_label"});

            if ($value === '' && $label === '') {
                continue;
            }

            $items[] = [
                'value' => $value !== '' ? $value : '—',
                'label' => $label !== '' ? $label : '',
            ];
        }

        return $items;
    }
}
