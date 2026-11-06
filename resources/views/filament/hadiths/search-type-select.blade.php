<style>
    /* Keep type + search + filters on one row and packed together */
    .fi-ta-header-toolbar > div:has(.fi-ta-hadith-search-type) {
        display: flex !important;
        align-items: center;
        justify-content: flex-end;
        flex-wrap: nowrap;
        gap: 0.5rem;
    }

    .fi-ta-hadith-search-type {
        flex: 0 0 8.75rem;
        width: 8.75rem;
        margin: 0;
    }

    .fi-ta-hadith-search-type .fi-input-wrp {
        height: 100%;
        border-start-end-radius: 0 !important;
        border-end-end-radius: 0 !important;
        border-inline-end-width: 0 !important;
    }

    /* Join directly with Filament search field that follows this select */
    .fi-ta-hadith-search-type + .fi-ta-search-field {
        flex: 0 1 16rem;
        min-width: 10rem;
        max-width: 18rem;
    }

    .fi-ta-hadith-search-type + .fi-ta-search-field .fi-input-wrp {
        border-start-start-radius: 0 !important;
        border-end-start-radius: 0 !important;
    }

    .fi-ta-hadith-search-type .fi-select-input {
        font-weight: 600;
    }
</style>

<div
    class="fi-ta-hadith-search-type"
    wire:ignore.self
    x-data="{
        placeholders: {
            urdu: 'اردو متن لکھیں…',
            arabic: 'عربی متن لکھیں…',
            number: 'حدیث نمبر…',
        },
        syncPlaceholder() {
            const input = this.$el.parentElement?.querySelector('.fi-ta-search-field input[type=search]')
            if (! input) return
            input.placeholder = this.placeholders[$wire.searchType] || this.placeholders.urdu
            input.dir = $wire.searchType === 'number' ? 'ltr' : 'rtl'
        }
    }"
    x-init="$nextTick(() => syncPlaceholder())"
    x-effect="syncPlaceholder()"
>
    <label for="hadith-admin-search-type" class="fi-sr-only">تلاش کی قسم</label>
    <x-filament::input.wrapper>
        <x-filament::input.select
            id="hadith-admin-search-type"
            wire:model.live="searchType"
        >
            <option value="urdu">اردو تلاش</option>
            <option value="arabic">عربی تلاش</option>
            <option value="number">نمبر تلاش</option>
        </x-filament::input.select>
    </x-filament::input.wrapper>
</div>
