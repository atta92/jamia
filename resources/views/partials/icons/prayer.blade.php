@switch($icon)
    @case('fajr')
        {{-- Sunrise --}}
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M4 18h16"/>
            <path d="M12 4v3"/>
            <path d="M5.6 10.6 7.5 12"/>
            <path d="M18.4 10.6 16.5 12"/>
            <path d="M4 15a8 8 0 0 1 16 0"/>
        </svg>
        @break

    @case('ishraq')
        {{-- Early morning sun --}}
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M4 18h16"/>
            <path d="M12 6v2"/>
            <path d="M5.6 11.2 7.3 12.4"/>
            <path d="M18.4 11.2 16.7 12.4"/>
            <path d="M4 15.5a8 8 0 0 1 16 0"/>
            <circle cx="12" cy="14" r="2.2"/>
        </svg>
        @break

    @case('chasht')
        {{-- Mid-morning sun --}}
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="11" r="3.4"/>
            <path d="M12 3.5v1.6"/>
            <path d="M5.4 6.4l1.2 1.2"/>
            <path d="M18.6 6.4l-1.2 1.2"/>
            <path d="M3.5 12h1.6"/>
            <path d="M18.9 12h1.6"/>
            <path d="M4 18h16"/>
        </svg>
        @break

    @case('zuhr')
        {{-- Midday sun --}}
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="4"/>
            <path d="M12 2v2"/>
            <path d="M12 20v2"/>
            <path d="M4.93 4.93l1.41 1.41"/>
            <path d="M17.66 17.66l1.41 1.41"/>
            <path d="M2 12h2"/>
            <path d="M20 12h2"/>
            <path d="M4.93 19.07l1.41-1.41"/>
            <path d="M17.66 6.34l1.41-1.41"/>
        </svg>
        @break

    @case('asr')
        {{-- Afternoon sun / cloud-sun --}}
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M12 4v1.5"/>
            <path d="M18.4 7.6 17.3 8.7"/>
            <path d="M20 14h-1.5"/>
            <circle cx="14" cy="10" r="3"/>
            <path d="M5 16.5a3.5 3.5 0 0 1 .4-6.9A5 5 0 0 1 14.8 9.2 3 3 0 0 1 17 16.5H5z"/>
        </svg>
        @break

    @case('maghrib')
        {{-- Sunset --}}
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M4 18h16"/>
            <path d="M12 14v-3"/>
            <path d="M5.6 12.4 7.5 11"/>
            <path d="M18.4 12.4 16.5 11"/>
            <path d="M4 15a8 8 0 0 1 16 0"/>
            <path d="M8 21h8"/>
        </svg>
        @break

    @case('isha')
        {{-- Crescent moon --}}
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M20 14.5A8.5 8.5 0 0 1 9.5 4 7 7 0 1 0 20 14.5z"/>
        </svg>
        @break

    @case('jummah')
        {{-- Mosque / dome --}}
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M4 20V11l2-1.5V20"/>
            <path d="M20 20V11l-2-1.5V20"/>
            <path d="M3 20h18"/>
            <path d="M8 20v-5h8v5"/>
            <path d="M12 4.5 8 9h8L12 4.5z"/>
            <path d="M12 3v1.5"/>
            <circle cx="12" cy="12.5" r="1"/>
        </svg>
        @break

    @default
        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <circle cx="12" cy="12" r="4"/>
        </svg>
@endswitch
