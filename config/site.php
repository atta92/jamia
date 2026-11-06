<?php

return [

    'name' => env('SITE_NAME', 'دارالعلوم پیر شہانی'),
    'tagline' => env('SITE_TAGLINE', 'ایمان • اتحاد • ترقی'),
    'location_label' => env('SITE_LOCATION_LABEL', 'اسلامک سینٹر اینڈ مدرسہ'),
    'short_description' => env(
        'SITE_DESCRIPTION',
        'عبادت، علم اور خدمت کا مرکز — اپنے ورثے کو سنبھالتے ہوئے نئی نسل کو ایمان اور علم کی روشنی سے آراستہ کرنا ہمارا مشن ہے۔'
    ),
    'hero_line' => env('SITE_HERO_LINE', 'آئیے ہم سب مل کر'),

    'prayer' => [
        'city' => env('PRAYER_CITY', 'Chakwal'),
        'city_ur' => env('PRAYER_CITY_UR', 'چکوال'),
        'country' => env('PRAYER_COUNTRY', 'Pakistan'),
        'country_ur' => env('PRAYER_COUNTRY_UR', 'پاکستان'),
        'timezone' => env('PRAYER_TIMEZONE', 'Asia/Karachi'),
        // 1 = University of Islamic Sciences, Karachi (common in South Asia)
        'method' => (int) env('PRAYER_METHOD', 1),
        // 1 = Hanafi (affects Asr)
        'school' => 1,
        // Minutes after Azan for Iqamah (admin/CMS can replace later)
        'iqamah_offsets' => [
            'Fajr' => 20,
            'Dhuhr' => 15,
            'Asr' => 15,
            'Maghrib' => 10,
            'Isha' => 15,
            'Jummah' => 30,
        ],
        'cache_hours' => 6,
        'cities' => [
            ['city' => 'Chakwal', 'city_ur' => 'چکوال', 'country' => 'Pakistan', 'country_ur' => 'پاکستان', 'timezone' => 'Asia/Karachi', 'lat' => 32.9328, 'lng' => 72.8630],
            ['city' => 'Islamabad', 'city_ur' => 'اسلام آباد', 'country' => 'Pakistan', 'country_ur' => 'پاکستان', 'timezone' => 'Asia/Karachi', 'lat' => 33.6844, 'lng' => 73.0479],
            ['city' => 'Rawalpindi', 'city_ur' => 'راولپنڈی', 'country' => 'Pakistan', 'country_ur' => 'پاکستان', 'timezone' => 'Asia/Karachi', 'lat' => 33.5651, 'lng' => 73.0169],
            ['city' => 'Lahore', 'city_ur' => 'لاہور', 'country' => 'Pakistan', 'country_ur' => 'پاکستان', 'timezone' => 'Asia/Karachi', 'lat' => 31.5204, 'lng' => 74.3587],
            ['city' => 'Karachi', 'city_ur' => 'کراچی', 'country' => 'Pakistan', 'country_ur' => 'پاکستان', 'timezone' => 'Asia/Karachi', 'lat' => 24.8607, 'lng' => 67.0011],
            ['city' => 'Faisalabad', 'city_ur' => 'فیصل آباد', 'country' => 'Pakistan', 'country_ur' => 'پاکستان', 'timezone' => 'Asia/Karachi', 'lat' => 31.4504, 'lng' => 73.1350],
            ['city' => 'Multan', 'city_ur' => 'ملتان', 'country' => 'Pakistan', 'country_ur' => 'پاکستان', 'timezone' => 'Asia/Karachi', 'lat' => 30.1575, 'lng' => 71.5249],
            ['city' => 'Peshawar', 'city_ur' => 'پشاور', 'country' => 'Pakistan', 'country_ur' => 'پاکستان', 'timezone' => 'Asia/Karachi', 'lat' => 34.0151, 'lng' => 71.5249],
            ['city' => 'Quetta', 'city_ur' => 'کوئٹہ', 'country' => 'Pakistan', 'country_ur' => 'پاکستان', 'timezone' => 'Asia/Karachi', 'lat' => 30.1798, 'lng' => 66.9750],
            ['city' => 'Sialkot', 'city_ur' => 'سیالکوٹ', 'country' => 'Pakistan', 'country_ur' => 'پاکستان', 'timezone' => 'Asia/Karachi', 'lat' => 32.4945, 'lng' => 74.5229],
            ['city' => 'Gujranwala', 'city_ur' => 'گوجرانوالہ', 'country' => 'Pakistan', 'country_ur' => 'پاکستان', 'timezone' => 'Asia/Karachi', 'lat' => 32.1877, 'lng' => 74.1945],
            ['city' => 'Hyderabad', 'city_ur' => 'حیدرآباد', 'country' => 'Pakistan', 'country_ur' => 'پاکستان', 'timezone' => 'Asia/Karachi', 'lat' => 25.3960, 'lng' => 68.3578],
            ['city' => 'Bahawalpur', 'city_ur' => 'بہاولپور', 'country' => 'Pakistan', 'country_ur' => 'پاکستان', 'timezone' => 'Asia/Karachi', 'lat' => 29.3956, 'lng' => 71.6836],
            ['city' => 'Sargodha', 'city_ur' => 'سرگودھا', 'country' => 'Pakistan', 'country_ur' => 'پاکستان', 'timezone' => 'Asia/Karachi', 'lat' => 32.0836, 'lng' => 72.6711],
            ['city' => 'Abbottabad', 'city_ur' => 'ایبٹ آباد', 'country' => 'Pakistan', 'country_ur' => 'پاکستان', 'timezone' => 'Asia/Karachi', 'lat' => 34.1688, 'lng' => 73.2215],
        ],
    ],

];
