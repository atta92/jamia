<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'tagline',
        'location_label',
        'hero_line',
        'hero_title',
        'short_description',
        'about_heading',
        'about_text',
        'show_muhtamim_message',
        'muhtamim_heading',
        'muhtamim_name',
        'muhtamim_role',
        'muhtamim_message',
        'muhtamim_photo',
        'muhtamim_facebook_url',
        'muhtamim_whatsapp',
        'show_durood_section',
        'durood_kicker',
        'durood_text',
        'durood_fazilat',
        'hero_image',
        'show_daily_hadith',
        'hero_hadith_heading',
        'faiza_kicker',
        'daily_hadith_id',
        'show_prayer_section',
        'prayer_kicker',
        'prayer_title',
        'show_gallery_section',
        'gallery_kicker',
        'gallery_title',
        'gallery_lead',
        'gallery_cta',
        'show_blog_section',
        'blog_kicker',
        'blog_title',
        'blog_lead',
        'blog_cta',
        'prayer_city',
        'prayer_city_ur',
        'prayer_country',
        'prayer_country_ur',
        'prayer_timezone',
        'prayer_method',
        'iqamah_fajr',
        'iqamah_dhuhr',
        'iqamah_asr',
        'iqamah_maghrib',
        'iqamah_isha',
        'iqamah_jummah',
        'phone',
        'email',
        'address',
    ];

    protected function casts(): array
    {
        return [
            'show_daily_hadith' => 'boolean',
            'show_muhtamim_message' => 'boolean',
            'show_durood_section' => 'boolean',
            'show_prayer_section' => 'boolean',
            'show_gallery_section' => 'boolean',
            'show_blog_section' => 'boolean',
            'prayer_method' => 'integer',
            'iqamah_fajr' => 'integer',
            'iqamah_dhuhr' => 'integer',
            'iqamah_asr' => 'integer',
            'iqamah_maghrib' => 'integer',
            'iqamah_isha' => 'integer',
            'iqamah_jummah' => 'integer',
        ];
    }

    public function dailyHadith()
    {
        return $this->belongsTo(Hadith::class, 'daily_hadith_id');
    }

    public static function current(): self
    {
        return Cache::remember('site_settings.current', now()->addHour(), function () {
            return static::query()->with('dailyHadith.book')->first()
                ?? static::query()->create(static::defaults());
        });
    }

    public static function clearCache(): void
    {
        Cache::forget('site_settings.current');
    }

    /**
     * @return array<string, mixed>
     */
    public static function defaults(): array
    {
        return [
            'name' => config('site.name'),
            'tagline' => config('site.tagline'),
            'location_label' => config('site.location_label'),
            'hero_line' => config('site.hero_line'),
            'hero_title' => (config('site.name') ?: 'دارالعلوم').' کا مختصر تعارف',
            'short_description' => config('site.short_description'),
            'about_heading' => 'بسم اللہ الرحمن الرحیم',
            'about_text' => 'ہماری جامعہ علم و عمل کی روایت پر قائم ہے۔ قرآن، سنت اور خدمتِ خلق کے ذریعے ہر طالبِ علم کو ایمان، اخلاق اور تعلیم میں آگے بڑھنے کی دعوت دیتے ہیں۔',
            'show_muhtamim_message' => true,
            'muhtamim_heading' => 'مہتمم صاحب کا پیغام',
            'muhtamim_name' => 'مہتمم صاحب',
            'muhtamim_role' => 'مہتمم',
            'muhtamim_message' => 'الحمد للہ، ہماری جامعہ کا مقصد صرف تعلیم نہیں بلکہ تربیتِ نفس، خدمتِ دین اور کردار سازی ہے۔ طلبہ سے التماس ہے کہ علم کو عمل سے جوڑیں، اخلاق کو اپنی پہچان بنائیں، اور اس ادارے کو دعاؤں میں یاد رکھیں۔',
            'muhtamim_facebook_url' => 'https://www.facebook.com/',
            'muhtamim_whatsapp' => '923001234567',
            'show_durood_section' => true,
            'durood_kicker' => 'درود پڑھیے',
            'durood_text' => 'صلّی اللہ علیہ وآلہ وسلّم',
            'durood_fazilat' => 'فرمانِ مصطفیٰ ﷺ: جس نے مجھ پر دن میں صبح و شام درود پڑھا، اس کی شفاعت میرے لیے واجب ہو گئی۔',
            'show_daily_hadith' => true,
            'hero_hadith_heading' => 'آج کی حدیثِ پاک',
            'faiza_kicker' => 'فیضانِ نظر',
            'show_prayer_section' => true,
            'prayer_kicker' => 'آج کے اوقات',
            'prayer_title' => 'اوقاتِ نماز',
            'show_gallery_section' => true,
            'gallery_kicker' => 'یادگار',
            'gallery_title' => 'علم، تربیت اور کامیابی کے مناظر',
            'gallery_lead' => 'جامعہ کی زندگی، تعلیم، عبادت اور اجتماعات کے منتخب مناظر — علم، ایمان اور خدمت کے سفر کی جھلک۔',
            'gallery_cta' => 'مزید دیکھیں',
            'show_blog_section' => true,
            'blog_kicker' => 'بلاگ',
            'blog_title' => 'علمی مباحث اور شرعی رہنمائی',
            'blog_lead' => 'تعلیم، اخلاق، عبادت اور جامعہ کی زندگی سے متعلق تازہ مضامین۔',
            'blog_cta' => 'تمام مضامین',
            'prayer_city' => config('site.prayer.city'),
            'prayer_city_ur' => config('site.prayer.city_ur'),
            'prayer_country' => config('site.prayer.country'),
            'prayer_country_ur' => config('site.prayer.country_ur'),
            'prayer_timezone' => config('site.prayer.timezone'),
            'prayer_method' => config('site.prayer.method'),
            'iqamah_fajr' => config('site.prayer.iqamah_offsets.Fajr'),
            'iqamah_dhuhr' => config('site.prayer.iqamah_offsets.Dhuhr'),
            'iqamah_asr' => config('site.prayer.iqamah_offsets.Asr'),
            'iqamah_maghrib' => config('site.prayer.iqamah_offsets.Maghrib'),
            'iqamah_isha' => config('site.prayer.iqamah_offsets.Isha'),
            'iqamah_jummah' => config('site.prayer.iqamah_offsets.Jummah'),
        ];
    }

    /**
     * Shape used by the public frontend (compatible with old config('site')).
     *
     * @return array<string, mixed>
     */
    public function toSiteArray(): array
    {
        return [
            'name' => $this->name,
            'logo' => $this->logo,
            'tagline' => $this->tagline,
            'location_label' => $this->location_label,
            'hero_line' => $this->hero_line,
            'hero_title' => $this->hero_title,
            'hero_image' => $this->hero_image,
            'short_description' => $this->short_description,
            'about_heading' => $this->about_heading,
            'about_text' => $this->about_text,
            'show_muhtamim_message' => (bool) $this->show_muhtamim_message,
            'muhtamim_heading' => $this->muhtamim_heading,
            'muhtamim_name' => $this->muhtamim_name,
            'muhtamim_role' => $this->muhtamim_role,
            'muhtamim_message' => $this->muhtamim_message,
            'muhtamim_photo' => $this->muhtamim_photo,
            'muhtamim_facebook_url' => $this->muhtamim_facebook_url,
            'muhtamim_whatsapp' => $this->muhtamim_whatsapp,
            'show_durood_section' => (bool) ($this->show_durood_section ?? true),
            'durood_kicker' => $this->durood_kicker,
            'durood_text' => $this->durood_text,
            'durood_fazilat' => $this->durood_fazilat,
            'show_daily_hadith' => (bool) $this->show_daily_hadith,
            'hero_hadith_heading' => $this->hero_hadith_heading,
            'faiza_kicker' => $this->faiza_kicker,
            'show_prayer_section' => (bool) ($this->show_prayer_section ?? true),
            'prayer_kicker' => $this->prayer_kicker,
            'prayer_title' => $this->prayer_title,
            'show_gallery_section' => (bool) ($this->show_gallery_section ?? true),
            'gallery_kicker' => $this->gallery_kicker,
            'gallery_title' => $this->gallery_title,
            'gallery_lead' => $this->gallery_lead,
            'gallery_cta' => $this->gallery_cta,
            'show_blog_section' => (bool) ($this->show_blog_section ?? true),
            'blog_kicker' => $this->blog_kicker,
            'blog_title' => $this->blog_title,
            'blog_lead' => $this->blog_lead,
            'blog_cta' => $this->blog_cta,
            'daily_hadith_id' => $this->daily_hadith_id,
            'phone' => $this->phone,
            'email' => $this->email,
            'address' => $this->address,
            'prayer' => [
                'city' => $this->prayer_city,
                'city_ur' => $this->prayer_city_ur,
                'country' => $this->prayer_country,
                'country_ur' => $this->prayer_country_ur,
                'timezone' => $this->prayer_timezone,
                'method' => $this->prayer_method,
                'school' => 1,
                'iqamah_offsets' => [
                    'Fajr' => $this->iqamah_fajr,
                    'Dhuhr' => $this->iqamah_dhuhr,
                    'Asr' => $this->iqamah_asr,
                    'Maghrib' => $this->iqamah_maghrib,
                    'Isha' => $this->iqamah_isha,
                    'Jummah' => $this->iqamah_jummah,
                ],
                'cache_hours' => 6,
            ],
        ];
    }

    protected static function booted(): void
    {
        static::saved(fn () => static::clearCache());
        static::deleted(fn () => static::clearCache());
    }
}
