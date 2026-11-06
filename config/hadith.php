<?php

return [
    'api_key' => env('HADITH_API_KEY'),
    'base_url' => env('HADITH_API_BASE', 'https://hadithapi.com/api'),
    'paginate' => (int) env('HADITH_API_PAGINATE', 200),
    'request_delay_ms' => (int) env('HADITH_API_DELAY_MS', 150),
];
