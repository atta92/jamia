@if ($hadiths->isEmpty())
    <div class="hadith-empty">
        <p class="font-display text-2xl font-bold text-teal-800">اس باب میں کوئی حدیث نہیں ملی</p>
        <p class="mt-2 text-muted">دوسرا لفظ آزمائیں یا پچھلے صفحے پر جائیں۔</p>
    </div>
@else
    <div class="hadith-entry-list">
        @foreach ($hadiths as $item)
            @php
                $shareTitle = trim(
                    ($book->book_name ?? $book->titleUr() ?? 'Hadith').
                    ' · Hadith '.($item->hadith_number ?: $item->id)
                );
                $shareBody = collect([
                    $shareTitle,
                    'Hadith International Number: '.($item->hadith_number ?: '—'),
                    $item->status ? 'Status: '.$item->status : null,
                    $item->heading_urdu ?: $item->heading_arabic ?: $item->heading_english,
                    $item->english_narrator ?: $item->urdu_narrator,
                    $item->hadith_arabic,
                    $item->hadith_urdu ?: $item->hadith_english,
                ])->filter(fn ($part) => filled($part))->implode("\n\n");
                $shareUrl = url()->current().'#hadith-'.($item->hadith_number ?: $item->id);
            @endphp

            <article
                class="hadith-entry"
                id="hadith-{{ $item->hadith_number ?: $item->id }}"
                x-data="hadithCard({
                    title: @js($shareTitle),
                    text: @js($shareBody),
                    url: @js($shareUrl),
                    hadithId: {{ (int) $item->id }},
                    reportEndpoint: @js(route('hadith.error-reports.store')),
                })"
            >
                @if ($item->heading_urdu || $item->heading_arabic)
                    <h2 class="hadith-entry__heading font-display">
                        {{ $item->heading_urdu ?: $item->heading_arabic }}
                    </h2>
                @endif

                <div class="hadith-entry__bilingual">
                    <div class="hadith-entry__col hadith-entry__col--ar" dir="rtl" lang="ar">
                        @if ($item->hadith_arabic)
                            <p class="hadith-entry__matan hadith-entry__ar">{{ $item->hadith_arabic }}</p>
                        @else
                            <p class="hadith-entry__missing">عربی متن دستیاب نہیں</p>
                        @endif
                    </div>

                    <div class="hadith-entry__col hadith-entry__col--ur">
                        @if ($item->urdu_narrator || $item->english_narrator)
                            <p class="hadith-entry__narrator">
                                {{ $item->urdu_narrator ?: $item->english_narrator }}
                            </p>
                        @endif

                        @if ($item->hadith_urdu)
                            <p class="hadith-entry__matan hadith-entry__ur">{{ $item->hadith_urdu }}</p>
                        @elseif ($item->hadith_english)
                            <p class="hadith-entry__matan hadith-entry__en" dir="ltr" lang="en">{{ $item->hadith_english }}</p>
                        @else
                            <p class="hadith-entry__missing">اردو متن دستیاب نہیں</p>
                        @endif
                    </div>
                </div>

                <div class="hadith-entry__foot" dir="ltr">
                    <div class="hadith-entry__meta">
                        <span>
                            <em>Hadith International Number:</em>
                            {{ $item->hadith_number ?: '—' }}
                        </span>
                        <span>
                            <em>Status:</em>
                            {{ $item->status ?: '—' }}
                        </span>
                    </div>

                    <div class="hadith-entry__actions">
                        <button
                            type="button"
                            class="hadith-entry__action"
                            :class="{ 'is-active': reportOpen }"
                            @click="reportOpen ? closeReport() : openReport()"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M10.29 3.86 1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path>
                                <path d="M12 9v4"></path>
                                <path d="M12 17h.01"></path>
                            </svg>
                            <span>Report Error</span>
                        </button>
                        <span class="hadith-entry__sep" aria-hidden="true">|</span>
                        <button
                            type="button"
                            class="hadith-entry__action"
                            @click="share"
                        >
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <circle cx="18" cy="5" r="3"></circle>
                                <circle cx="6" cy="12" r="3"></circle>
                                <circle cx="18" cy="19" r="3"></circle>
                                <path d="m8.59 13.51 6.83 3.98M15.41 6.51l-6.82 3.98"></path>
                            </svg>
                            <span>Share</span>
                        </button>
                        <span class="hadith-entry__sep" aria-hidden="true">|</span>
                        <button
                            type="button"
                            class="hadith-entry__action"
                            :class="{ 'is-done': copied }"
                            @click="copy"
                        >
                            <svg x-show="!copied" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="9" y="9" width="13" height="13" rx="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                            <svg x-cloak x-show="copied" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M20 6 9 17l-5-5"></path>
                            </svg>
                            <span x-text="copied ? 'Copied' : 'Copy'"></span>
                        </button>
                    </div>
                </div>

                <div
                    class="hadith-share"
                    x-cloak
                    x-show="open"
                    x-transition.opacity
                    @keydown.escape.window="close"
                >
                    <div class="hadith-share__backdrop" @click="close"></div>

                    <div
                        class="hadith-share__panel"
                        role="dialog"
                        aria-modal="true"
                        aria-label="Share Hadith"
                        @click.stop
                        x-transition
                    >
                        <div class="hadith-share__head">
                            <h3 class="hadith-share__title">Share Hadith</h3>
                            <button type="button" class="hadith-share__close" @click="close" aria-label="Close">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M18 6 6 18M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="hadith-share__preview" dir="auto" x-text="payload"></div>

                        <div class="hadith-share__social">
                            <a class="hadith-share__social-btn hadith-share__social-btn--wa" :href="whatsappUrl" target="_blank" rel="noopener noreferrer" title="WhatsApp">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.435 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                                <span>WhatsApp</span>
                            </a>
                            <a class="hadith-share__social-btn hadith-share__social-btn--fb" :href="facebookUrl" target="_blank" rel="noopener noreferrer" title="Facebook">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M24 12.073C24 5.446 18.627.073 12 .073S0 5.446 0 12.073C0 18.063 4.388 23.027 10.125 23.927v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.063 24 12.073z"/></svg>
                                <span>Facebook</span>
                            </a>
                            <a class="hadith-share__social-btn hadith-share__social-btn--x" :href="twitterUrl" target="_blank" rel="noopener noreferrer" title="X">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.744l7.727-8.835L1.254 2.25H8.08l4.253 5.622L18.244 2.25zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77z"/></svg>
                                <span>X</span>
                            </a>
                            <a class="hadith-share__social-btn hadith-share__social-btn--tg" :href="telegramUrl" target="_blank" rel="noopener noreferrer" title="Telegram">
                                <svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                                <span>Telegram</span>
                            </a>
                            <a class="hadith-share__social-btn hadith-share__social-btn--mail" :href="emailUrl" title="Email">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                    <path d="m22 7-10 7L2 7"></path>
                                </svg>
                                <span>Email</span>
                            </a>
                        </div>

                        <button
                            type="button"
                            class="hadith-share__copy"
                            :class="{ 'is-done': copied }"
                            @click="copy"
                        >
                            <svg x-show="!copied" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <rect x="9" y="9" width="13" height="13" rx="2"></rect>
                                <path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"></path>
                            </svg>
                            <svg x-cloak x-show="copied" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                <path d="M20 6 9 17l-5-5"></path>
                            </svg>
                            <span x-text="copied ? 'Copied' : 'Copy text'"></span>
                        </button>
                    </div>
                </div>

                <div
                    class="hadith-report"
                    x-ref="reportPanel"
                    x-cloak
                    x-show="reportOpen"
                    x-transition.opacity.duration.200ms
                    dir="ltr"
                >
                    <div class="hadith-report__inner">
                        <div class="hadith-report__head">
                            <h3 class="hadith-report__title">Report Error</h3>
                            <button type="button" class="hadith-report__close" @click="closeReport" aria-label="Close">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                                    <path d="M18 6 6 18M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <template x-if="reportSuccess">
                            <div class="hadith-report__success">
                                Thank you. Your report has been submitted and will be reviewed.
                            </div>
                        </template>

                        <form
                            class="hadith-report__form"
                            x-show="!reportSuccess"
                            @submit.prevent="submitReport"
                        >
                            <div class="hadith-report__grid">
                                <fieldset class="hadith-report__fieldset">
                                    <legend>Type of error: <span aria-hidden="true">*</span></legend>

                                    <label class="hadith-report__radio">
                                        <input type="radio" name="error_type_{{ $item->id }}" value="mismatched_translation" x-model="report.error_type">
                                        <span>Mismatched translation</span>
                                    </label>
                                    <label class="hadith-report__radio">
                                        <input type="radio" name="error_type_{{ $item->id }}" value="spelling_mistake" x-model="report.error_type">
                                        <span>Spelling mistake</span>
                                    </label>
                                    <label class="hadith-report__radio">
                                        <input type="radio" name="error_type_{{ $item->id }}" value="incomplete_text" x-model="report.error_type">
                                        <span>Incomplete text</span>
                                    </label>
                                    <label class="hadith-report__radio">
                                        <input type="radio" name="error_type_{{ $item->id }}" value="mistranslation" x-model="report.error_type">
                                        <span>Mistranslation</span>
                                    </label>
                                    <label class="hadith-report__radio">
                                        <input type="radio" name="error_type_{{ $item->id }}" value="wrong_number" x-model="report.error_type">
                                        <span>Wrong numbering</span>
                                    </label>
                                    <label class="hadith-report__radio">
                                        <input type="radio" name="error_type_{{ $item->id }}" value="other" x-model="report.error_type">
                                        <span>Other (please specify)</span>
                                    </label>
                                    <input
                                        type="text"
                                        class="hadith-report__input"
                                        placeholder="Please specify"
                                        x-show="report.error_type === 'other'"
                                        x-model="report.other_detail"
                                        maxlength="255"
                                    >
                                </fieldset>

                                <div class="hadith-report__details">
                                    <label class="hadith-report__label" for="hadith-report-details-{{ $item->id }}">Additional details:</label>
                                    <textarea
                                        id="hadith-report-details-{{ $item->id }}"
                                        class="hadith-report__textarea"
                                        rows="5"
                                        maxlength="5000"
                                        x-model="report.details"
                                        placeholder="Describe the mistake so we can correct it…"
                                    ></textarea>

                                    <label class="hadith-report__check">
                                        <input type="checkbox" x-model="report.notify_email">
                                        <span>Yes, email me when the error is corrected</span>
                                    </label>

                                    <input
                                        type="email"
                                        class="hadith-report__input"
                                        placeholder="Email address"
                                        x-show="report.notify_email"
                                        x-model="report.email"
                                        maxlength="255"
                                    >
                                </div>
                            </div>

                            <input
                                type="text"
                                name="website"
                                class="hadith-report__honeypot"
                                tabindex="-1"
                                autocomplete="off"
                                x-model="report.website"
                                aria-hidden="true"
                            >

                            <p class="hadith-report__error" x-cloak x-show="reportError" x-text="reportError"></p>

                            <button
                                type="submit"
                                class="hadith-report__submit"
                                :disabled="reportSubmitting"
                            >
                                <span x-text="reportSubmitting ? 'Submitting…' : 'Submit'"></span>
                            </button>
                        </form>
                    </div>
                </div>
            </article>
        @endforeach
    </div>

    <div class="mt-10 hadith-pagination">
        {{ $hadiths->links() }}
    </div>
@endif
