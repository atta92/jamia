import './bootstrap';
import Alpine from 'alpinejs';

Alpine.data('hadithCard', (config = {}) => {
    const options = config || {};

    return {
        title: options.title || '',
        text: options.text || '',
        url: options.url || window.location.href,
        hadithId: options.hadithId || null,
        reportEndpoint: options.reportEndpoint || '',
        open: false,
        reportOpen: false,
        copied: false,
        copyTimer: null,
        reportSubmitting: false,
        reportSuccess: false,
        reportError: '',
        report: {
            error_type: 'mismatched_translation',
            other_detail: '',
            details: '',
            notify_email: false,
            email: '',
            website: '',
        },
        get payload() {
            return [this.text, this.url].filter(Boolean).join('\n\n');
        },
        get whatsappUrl() {
            return `https://wa.me/?text=${encodeURIComponent(this.payload)}`;
        },
        get facebookUrl() {
            return `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(this.url)}`;
        },
        get twitterUrl() {
            return `https://twitter.com/intent/tweet?text=${encodeURIComponent(this.text)}&url=${encodeURIComponent(this.url)}`;
        },
        get telegramUrl() {
            return `https://t.me/share/url?url=${encodeURIComponent(this.url)}&text=${encodeURIComponent(this.text)}`;
        },
        get emailUrl() {
            return `mailto:?subject=${encodeURIComponent(this.title)}&body=${encodeURIComponent(this.payload)}`;
        },
        share() {
            this.reportOpen = false;
            this.open = true;
            document.documentElement.classList.add('overflow-hidden');
        },
        close() {
            this.open = false;
            document.documentElement.classList.remove('overflow-hidden');
        },
        openReport() {
            this.open = false;
            document.documentElement.classList.remove('overflow-hidden');
            this.reportOpen = true;
            this.reportSuccess = false;
            this.reportError = '';
            this.$nextTick(() => {
                this.$refs.reportPanel?.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            });
        },
        closeReport() {
            this.reportOpen = false;
        },
        async copy() {
            try {
                await navigator.clipboard.writeText(this.payload);
            } catch (error) {
                const area = document.createElement('textarea');
                area.value = this.payload;
                area.setAttribute('readonly', '');
                area.style.position = 'fixed';
                area.style.opacity = '0';
                document.body.appendChild(area);
                area.select();
                document.execCommand('copy');
                document.body.removeChild(area);
            }

            this.copied = true;
            clearTimeout(this.copyTimer);
            this.copyTimer = setTimeout(() => {
                this.copied = false;
            }, 1800);
        },
        async submitReport() {
            if (this.reportSubmitting || !this.hadithId || !this.reportEndpoint) {
                return;
            }

            this.reportSubmitting = true;
            this.reportError = '';
            this.reportSuccess = false;

            try {
                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const response = await fetch(this.reportEndpoint, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': token,
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: JSON.stringify({
                        hadith_id: this.hadithId,
                        error_type: this.report.error_type,
                        other_detail: this.report.other_detail,
                        details: this.report.details,
                        notify_email: Boolean(this.report.notify_email),
                        email: this.report.email,
                        website: this.report.website,
                        page_url: this.url,
                    }),
                });

                const data = await response.json().catch(() => ({}));

                if (!response.ok) {
                    const firstError = data?.errors
                        ? Object.values(data.errors).flat()[0]
                        : null;
                    throw new Error(firstError || data?.message || 'Could not submit report.');
                }

                this.reportSuccess = true;
                this.report = {
                    error_type: 'mismatched_translation',
                    other_detail: '',
                    details: '',
                    notify_email: false,
                    email: '',
                    website: '',
                };
            } catch (error) {
                this.reportError = error?.message || 'Could not submit report.';
            } finally {
                this.reportSubmitting = false;
            }
        },
    };
});

Alpine.data('hadithSearch', (config = {}) => {
    const options = config || {};

    return {
        query: options.query || '',
        mode: options.mode || 'urdu',
        typed: Boolean(options.typed),
        loading: false,
        timer: null,
        endpoint: options.endpoint || '',
        defaultPlaceholder: options.placeholder || 'تلاش کریں…',
        placeholders: {
            urdu: 'اردو متن لکھیں…',
            arabic: 'عربی متن لکھیں…',
            number: 'حدیث نمبر…',
        },
        get placeholder() {
            if (!this.typed) {
                return this.defaultPlaceholder;
            }

            return this.placeholders[this.mode] || this.placeholders.urdu;
        },
        init() {
            this.$watch('query', () => this.scheduleSearch());
            this.$watch('mode', () => {
                if (this.query.trim() !== '') {
                    this.scheduleSearch(120);
                }
            });

            this.$refs.results?.addEventListener('click', (event) => {
                const link = event.target.closest('a[href]');
                if (!link || !this.$refs.results.contains(link)) {
                    return;
                }

                const isPagination = Boolean(
                    link.closest('.hadith-pagination, nav[role="navigation"], .pagination')
                    || link.href.includes('page=')
                );

                if (!isPagination) {
                    return;
                }

                event.preventDefault();
                this.fetchUrl(link.href);
            });
        },
        scheduleSearch(delay = 350) {
            clearTimeout(this.timer);
            this.timer = setTimeout(() => this.search(this.query), delay);
        },
        onSubmit(event) {
            event.preventDefault();
            clearTimeout(this.timer);
            this.search(this.query);
        },
        clear() {
            this.query = '';
            clearTimeout(this.timer);
            this.search('');
        },
        search(value) {
            const url = new URL(this.endpoint || window.location.pathname, window.location.origin);
            const q = String(value ?? '').trim();

            if (q !== '') {
                url.searchParams.set('q', q);
            } else {
                url.searchParams.delete('q');
            }

            if (this.typed) {
                url.searchParams.set('type', this.mode);
            } else {
                url.searchParams.delete('type');
            }

            url.searchParams.delete('page');
            this.fetchUrl(url.toString(), { syncHistory: true, query: q });
        },
        async fetchUrl(targetUrl, { syncHistory = true, query = null } = {}) {
            const url = new URL(targetUrl, window.location.origin);
            url.searchParams.set('partial', '1');

            if (this.typed) {
                url.searchParams.set('type', this.mode);
            }

            this.loading = true;

            try {
                const response = await fetch(url.toString(), {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-Hadith-Search': '1',
                        Accept: 'text/html',
                    },
                });

                if (!response.ok) {
                    throw new Error('Search request failed');
                }

                const html = await response.text();
                if (this.$refs.results) {
                    this.$refs.results.innerHTML = html;
                    if (window.Alpine?.initTree) {
                        window.Alpine.initTree(this.$refs.results);
                    }
                    applyHadithPageHighlight();
                }

                if (syncHistory) {
                    const historyUrl = new URL(url.toString());
                    historyUrl.searchParams.delete('partial');

                    if (query !== null) {
                        if (query) {
                            historyUrl.searchParams.set('q', query);
                        } else {
                            historyUrl.searchParams.delete('q');
                        }
                    }

                    if (this.typed) {
                        historyUrl.searchParams.set('type', this.mode);
                    }

                    window.history.replaceState({}, '', historyUrl.toString());
                }
            } catch (error) {
                console.error(error);
            } finally {
                this.loading = false;
            }
        },
    };
});

window.Alpine = Alpine;
function escapeHtmlText(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function escapeRegExp(value) {
    return String(value).replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
}

function highlightPlainText(text, query, markClass = 'hadith-search-mark') {
    const raw = String(text ?? '');
    const q = String(query ?? '').trim();
    if (!raw) {
        return '';
    }

    const safe = escapeHtmlText(raw);
    if (!q) {
        return safe;
    }

    const safeQuery = escapeHtmlText(q);
    if (!safeQuery) {
        return safe;
    }

    const diacritics = '[\\u064B-\\u065F\\u0670\\u06D6-\\u06ED\\u0640\\u00A0\\s]*';
    const flexible = escapeRegExp(safeQuery)
        .split('')
        .map((ch) => (ch.trim() === '' ? diacritics : `${ch}${diacritics}`))
        .join('');

    try {
        const next = safe.replace(
            new RegExp(flexible, 'giu'),
            (match) => `<mark class="${markClass}">${match}</mark>`
        );
        if (next !== safe) {
            return next;
        }

        const plain = escapeRegExp(safeQuery);
        return safe.replace(
            new RegExp(plain, 'giu'),
            (match) => `<mark class="${markClass}">${match}</mark>`
        );
    } catch {
        return safe;
    }
}

function applyHadithPageHighlight() {
    const params = new URLSearchParams(window.location.search);
    const query = (params.get('hq') || params.get('q') || '').trim();
    if (!query) {
        return false;
    }

    const hash = window.location.hash || '';
    const hn = (params.get('hn') || '').trim();
    let target = null;

    if (hash.startsWith('#hadith-')) {
        target = document.querySelector(hash);
    }
    if (!target && hn) {
        target = document.getElementById(`hadith-${hn}`);
    }

    const entries = target
        ? [target]
        : [
            ...document.querySelectorAll('.hadith-entry'),
            ...document.querySelectorAll('.hadith-hit'),
        ];

    if (!entries.length) {
        return false;
    }

    const selectors = [
        '.hadith-entry__heading-text',
        '.hadith-entry__narrator',
        '.hadith-entry__matan',
        '.hadith-entry__meta',
        '.hadith-hit__snippet',
        '.hadith-hit__chapter',
    ];

    let anyMatch = false;

    entries.forEach((entry) => {
        let matched = false;

        selectors.forEach((selector) => {
            entry.querySelectorAll(selector).forEach((node) => {
                if (node.dataset.hqHighlighted === '1') {
                    return;
                }

                const original = node.textContent || '';
                if (!original.trim()) {
                    return;
                }

                const highlighted = highlightPlainText(original, query, 'hadith-search-mark');
                if (highlighted === escapeHtmlText(original)) {
                    return;
                }

                node.innerHTML = highlighted;
                node.dataset.hqHighlighted = '1';
                matched = true;
                anyMatch = true;
            });
        });

        if (matched || target === entry) {
            entry.classList.add('is-search-hit');
        }
    });

    const focusEntry = target || document.querySelector('.hadith-entry.is-search-hit');
    if (focusEntry) {
        window.requestAnimationFrame(() => {
            focusEntry.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    }

    return anyMatch || Boolean(focusEntry);
}

Alpine.data('hadithBot', (config = {}) => {
    const options = config || {};
    const dockKey = 'jamia.hadithBot.docked';
    const bookKey = 'jamia.hadithBot.book';
    const modeKey = 'jamia.hadithBot.mode';
    const books = Array.isArray(options.books) ? options.books : [];
    const modeLabels = {
        urdu: 'اردو',
        arabic: 'عربی',
        number: 'نمبر',
    };
    const modePlaceholders = {
        urdu: 'اردو متن لکھیں…',
        arabic: 'عربی متن لکھیں…',
        number: 'حدیث نمبر…',
    };

    return {
        endpoint: options.endpoint || '',
        books,
        selectedBook: null,
        mode: 'urdu',
        open: false,
        docked: false,
        query: '',
        loading: false,
        messages: [],
        get modeLabel() {
            return modeLabels[this.mode] || modeLabels.urdu;
        },
        get inputPlaceholder() {
            if (!this.selectedBook) {
                return 'پہلے کتاب منتخب کریں';
            }

            return modePlaceholders[this.mode] || modePlaceholders.urdu;
        },
        init() {
            try {
                this.docked = localStorage.getItem(dockKey) === '1';
            } catch {
                this.docked = false;
            }

            let savedMode = 'urdu';
            try {
                savedMode = localStorage.getItem(modeKey) || 'urdu';
            } catch {
                savedMode = 'urdu';
            }
            this.mode = ['urdu', 'arabic', 'number'].includes(savedMode) ? savedMode : 'urdu';

            let savedSlug = '';
            try {
                savedSlug = localStorage.getItem(bookKey) || '';
            } catch {
                savedSlug = '';
            }

            const saved = books.find((book) => book.slug === savedSlug) || null;
            if (saved) {
                this.selectedBook = saved;
                this.messages = [
                    {
                        role: 'bot',
                        kind: 'text',
                        text: `کتاب منتخب ہے: ${saved.title}۔ اب ${this.modeLabel} تلاش کریں۔`,
                    },
                ];
            } else {
                this.resetToBookPicker();
            }
        },
        setMode(mode) {
            if (!['urdu', 'arabic', 'number'].includes(mode)) {
                return;
            }
            this.mode = mode;
            try {
                localStorage.setItem(modeKey, mode);
            } catch {
                // ignore
            }
            this.$nextTick(() => this.$refs.input?.focus());
        },
        resetToBookPicker() {
            this.selectedBook = null;
            this.query = '';
            this.messages = [
                {
                    role: 'bot',
                    kind: 'books',
                    text: 'السلام علیکم! پہلے کتاب منتخب کریں، پھر اردو / عربی / نمبر سے تلاش کریں۔',
                },
            ];
        },
        selectBook(book) {
            if (!book?.slug) {
                return;
            }

            this.selectedBook = book;
            try {
                localStorage.setItem(bookKey, book.slug);
            } catch {
                // ignore
            }

            this.messages.push({
                role: 'user',
                kind: 'text',
                text: book.title,
            });
            this.messages.push({
                role: 'bot',
                kind: 'text',
                text: `${book.title} منتخب ہو گئی۔ اردو، عربی یا نمبر چنیں اور تلاش لکھیں۔`,
            });
            this.scrollToBottom();
            this.$nextTick(() => this.$refs.input?.focus());
        },
        clearBook() {
            try {
                localStorage.removeItem(bookKey);
            } catch {
                // ignore
            }
            this.resetToBookPicker();
            this.scrollToBottom();
        },
        toggleDock() {
            this.docked = !this.docked;
            if (this.docked) {
                this.close();
            }
            try {
                localStorage.setItem(dockKey, this.docked ? '1' : '0');
            } catch {
                // ignore
            }
        },
        toggle() {
            if (this.docked) {
                return;
            }
            if (this.open) {
                this.close();
            } else {
                this.open = true;
                this.$nextTick(() => {
                    if (this.selectedBook) {
                        this.$refs.input?.focus();
                    }
                });
            }
        },
        close() {
            this.open = false;
        },
        scrollToBottom() {
            this.$nextTick(() => {
                const box = this.$refs.messages;
                if (box) {
                    box.scrollTop = box.scrollHeight;
                }
            });
        },
        escapeHtml(value) {
            return escapeHtmlText(value);
        },
        highlightText(text, query) {
            return highlightPlainText(text, query, 'hadith-bot__mark') || '-';
        },
        resultHref(item) {
            return String(item?.url || '');
        },
        async submit() {
            const q = this.query.trim();
            if (!q || this.loading || !this.endpoint) {
                return;
            }

            if (!this.selectedBook?.slug) {
                this.messages.push({
                    role: 'bot',
                    kind: 'books',
                    text: 'تلاش سے پہلے کتاب منتخب کریں۔',
                });
                this.scrollToBottom();
                return;
            }

            this.messages.push({
                role: 'user',
                kind: 'text',
                text: `${this.modeLabel}: ${q}`,
            });
            this.query = '';
            this.loading = true;
            this.scrollToBottom();

            try {
                const url = new URL(this.endpoint, window.location.origin);
                url.searchParams.set('q', q);
                url.searchParams.set('book', this.selectedBook.slug);
                url.searchParams.set('type', this.mode);
                url.searchParams.set('limit', '5');

                const response = await fetch(url.toString(), {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (!response.ok) {
                    throw new Error('search failed');
                }

                const data = await response.json();
                const results = Array.isArray(data.results) ? data.results : [];
                const usedQuery = String(data.q || q).trim();
                const total = Number(data.total || results.length) || results.length;
                const moreUrl = String(data.more_url || '');

                if (!results.length) {
                    this.messages.push({
                        role: 'bot',
                        kind: 'text',
                        text: `${this.selectedBook.title} میں کوئی حدیث نہیں ملی۔ دوسرا لفظ یا نمبر آزمائیں۔`,
                    });
                } else {
                    const shown = results.length;
                    const label = total > shown
                        ? `${this.selectedBook.title} میں کل ${total} نتیجہ - پہلے ${shown}:`
                        : `${this.selectedBook.title} میں ${shown} نتیجہ ملا:`;

                    this.messages.push({
                        role: 'bot',
                        kind: 'results',
                        text: label,
                        query: usedQuery,
                        results,
                        total,
                        moreUrl: total > shown ? moreUrl : '',
                    });
                }
            } catch (error) {
                this.messages.push({
                    role: 'bot',
                    kind: 'text',
                    text: 'تلاش ابھی ممکن نہیں۔ تھوڑی دیر بعد کوشش کریں۔',
                });
            } finally {
                this.loading = false;
                this.scrollToBottom();
            }
        },
    };
});

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    applyHadithPageHighlight();
    window.setTimeout(() => applyHadithPageHighlight(), 120);
    window.setTimeout(() => applyHadithPageHighlight(), 450);

    const header = document.getElementById('site-header');
    const menuBtn = document.getElementById('mobile-menu-btn');
    const mobileNav = document.getElementById('mobile-nav');

    const revealEls = document.querySelectorAll('.reveal');
    if (revealEls.length) {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (reduceMotion || !('IntersectionObserver' in window)) {
            revealEls.forEach((el) => el.classList.add('is-visible'));
        } else {
            const revealObserver = new IntersectionObserver(
                (entries, observer) => {
                    entries.forEach((entry) => {
                        if (!entry.isIntersecting) {
                            return;
                        }

                        entry.target.classList.add('is-visible');
                        observer.unobserve(entry.target);
                    });
                },
                {
                    root: null,
                    rootMargin: '0px 0px -8% 0px',
                    threshold: 0.12,
                }
            );

            revealEls.forEach((el) => revealObserver.observe(el));
        }
    }

    // Highlighter sweep on مہتمم "پیغام" title
    document.querySelectorAll('[data-highlight]').forEach((el) => {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
        if (reduceMotion || !('IntersectionObserver' in window)) {
            el.classList.add('is-highlighted');
            return;
        }

        const highlightObserver = new IntersectionObserver(
            (entries, observer) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) {
                        return;
                    }
                    entry.target.classList.add('is-highlighted');
                    observer.unobserve(entry.target);
                });
            },
            {
                root: null,
                rootMargin: '0px 0px -10% 0px',
                threshold: 0.55,
            }
        );

        highlightObserver.observe(el);
    });

    if (header) {
        const isSolidMode = header.dataset.headerMode === 'solid'
            || document.body.classList.contains('page-solid-header');

        const syncHeader = () => {
            if (isSolidMode) {
                header.dataset.scrolled = 'true';
                return;
            }

            header.dataset.scrolled = window.scrollY > 24 ? 'true' : 'false';
        };

        syncHeader();
        window.addEventListener('scroll', syncHeader, { passive: true });
    }

    if (menuBtn && mobileNav) {
        const setMobileMenuOpen = (open) => {
            mobileNav.classList.toggle('hidden', !open);
            menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            menuBtn.setAttribute('aria-label', open ? 'مینو بند کریں' : 'مینو کھولیں');
            document.body.classList.toggle('mobile-menu-open', open);
            if (header) {
                header.classList.toggle('is-menu-open', open);
            }
        };

        menuBtn.addEventListener('click', () => {
            const open = mobileNav.classList.contains('hidden');
            setMobileMenuOpen(open);
        });

        mobileNav.querySelectorAll('a').forEach((link) => {
            link.addEventListener('click', () => setMobileMenuOpen(false));
        });

        window.addEventListener('resize', () => {
            if (window.matchMedia('(min-width: 1024px)').matches) {
                setMobileMenuOpen(false);
            }
        });
    }

    document.querySelectorAll('[data-nav-dropdown-btn]').forEach((btn) => {
        const root = btn.closest('.site-nav-dropdown');
        const menu = root?.querySelector('[data-nav-dropdown-menu]');
        if (!root || !menu) {
            return;
        }

        let closeTimer = null;

        const clearCloseTimer = () => {
            if (closeTimer) {
                window.clearTimeout(closeTimer);
                closeTimer = null;
            }
        };

        const close = () => {
            clearCloseTimer();
            root.classList.remove('is-open');
            menu.hidden = true;
            btn.setAttribute('aria-expanded', 'false');
        };

        const open = () => {
            clearCloseTimer();
            root.classList.add('is-open');
            menu.hidden = false;
            btn.setAttribute('aria-expanded', 'true');
        };

        const scheduleClose = () => {
            clearCloseTimer();
            closeTimer = window.setTimeout(close, 180);
        };

        btn.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            if (menu.hidden) {
                open();
            } else {
                close();
            }
        });

        root.addEventListener('mouseenter', open);
        root.addEventListener('mouseleave', scheduleClose);
        menu.addEventListener('mouseenter', open);
        menu.addEventListener('mouseleave', scheduleClose);

        document.addEventListener('click', (event) => {
            if (!root.contains(event.target)) {
                close();
            }
        });
    });

    const countdownEl = document.getElementById('prayer-countdown');
    if (countdownEl) {
        const labelEl = countdownEl.querySelector('[data-countdown-label]');
        const timeEl = countdownEl.querySelector('[data-countdown-time]');
        let schedule = { timezone: 'UTC', prayers: [] };
        let loading = false;

        try {
            schedule = JSON.parse(countdownEl.dataset.prayerCountdown || '{}');
        } catch (error) {
            schedule = { timezone: 'UTC', prayers: [] };
        }

        const pad = (value) => String(value).padStart(2, '0');
        const dayMs = 24 * 60 * 60 * 1000;

        const setCountdownLoading = (isLoading) => {
            loading = Boolean(isLoading);
            countdownEl.classList.toggle('is-loading', loading);
            countdownEl.setAttribute('aria-busy', loading ? 'true' : 'false');
        };

        // Current prayer window + countdown to the next adhan
        const currentPrayerWindow = () => {
            const prayers = Array.isArray(schedule.prayers) ? schedule.prayers : [];
            if (!prayers.length) {
                return null;
            }

            const now = Date.now();
            const sorted = prayers
                .map((prayer) => ({
                    name: prayer.name,
                    at: new Date(prayer.at).getTime(),
                }))
                .filter((prayer) => Number.isFinite(prayer.at))
                .sort((a, b) => a.at - b.at);

            if (!sorted.length) {
                return null;
            }

            let current = null;
            for (const prayer of sorted) {
                if (prayer.at <= now) {
                    current = prayer;
                }
            }

            let next = sorted.find((prayer) => prayer.at > now) || null;

            // Before Fajr: still in previous night's Isha window
            if (!current) {
                current = sorted[sorted.length - 1];
                next = sorted[0];
            } else if (!next) {
                // After Isha: remaining Isha until tomorrow's Fajr
                next = {
                    name: sorted[0].name,
                    at: sorted[0].at + dayMs,
                };
            }

            return { current, next };
        };

        const tick = () => {
            if (loading) {
                return;
            }

            const window = currentPrayerWindow();
            if (!window) {
                if (labelEl) {
                    labelEl.textContent = 'اوقاتِ نماز';
                }
                if (timeEl) {
                    timeEl.textContent = '--:--:--';
                }
                return;
            }

            const diff = Math.max(0, window.next.at - Date.now());
            const totalSeconds = Math.floor(diff / 1000);
            const hours = Math.floor(totalSeconds / 3600);
            const minutes = Math.floor((totalSeconds % 3600) / 60);
            const seconds = totalSeconds % 60;

            if (labelEl) {
                labelEl.textContent = `${window.current.name} کا باقی وقت`;
            }
            if (timeEl) {
                timeEl.textContent = `${pad(hours)}:${pad(minutes)}:${pad(seconds)}`;
            }
        };

        countdownEl.setPrayerLoading = setCountdownLoading;

        countdownEl.setPrayerSchedule = (next) => {
            if (!next || !Array.isArray(next.prayers)) {
                return;
            }
            schedule = next;
            setCountdownLoading(false);
            tick();
        };

        // Home prayer section auto-locates on boot - show timer skeleton immediately.
        const prayerSectionEl = document.querySelector('[data-prayer-section]');
        if (prayerSectionEl?.dataset.autoLocate === '1') {
            const savedSource = localStorage.getItem('jamia.prayer.citySource') || 'auto';
            const savedCity = localStorage.getItem('jamia.prayer.city');
            const defaultCity = prayerSectionEl.dataset.defaultCity || '';
            const needsLocate = savedSource !== 'manual'
                || (savedCity && savedCity !== defaultCity);

            if (needsLocate) {
                setCountdownLoading(true);
            }
        }

        tick();
        setInterval(tick, 1000);
    }

    const hadithBody = document.getElementById('hadith-scroll-body');
    const hadithUp = document.getElementById('hadith-scroll-up');
    const hadithDown = document.getElementById('hadith-scroll-down');
    const hadithTabs = document.querySelectorAll('[data-hadith-tab]');
    const hadithPanels = document.querySelectorAll('[data-hadith-panel]');

    if (hadithBody && hadithUp && hadithDown) {
        const step = 120;

        const syncHadithArrows = () => {
            const maxScroll = hadithBody.scrollHeight - hadithBody.clientHeight;
            const canScroll = maxScroll > 4;
            const atTop = hadithBody.scrollTop <= 2;
            const atBottom = hadithBody.scrollTop >= maxScroll - 2;

            // Only show arrows when text is longer than the card.
            hadithUp.hidden = !canScroll;
            hadithDown.hidden = !canScroll;
            hadithUp.disabled = !canScroll || atTop;
            hadithDown.disabled = !canScroll || atBottom;
        };

        const activateHadithTab = (tabName) => {
            hadithTabs.forEach((tab) => {
                const active = tab.dataset.hadithTab === tabName;
                tab.setAttribute('aria-selected', active ? 'true' : 'false');
            });

            hadithPanels.forEach((panel) => {
                const active = panel.dataset.hadithPanel === tabName;
                panel.classList.toggle('hidden', !active);
                panel.hidden = !active;
            });

            hadithBody.scrollTop = 0;
            syncHadithArrows();
        };

        hadithTabs.forEach((tab) => {
            tab.addEventListener('click', () => {
                activateHadithTab(tab.dataset.hadithTab);
            });
        });

        hadithUp.addEventListener('click', () => {
            hadithBody.scrollBy({ top: -step, behavior: 'smooth' });
        });

        hadithDown.addEventListener('click', () => {
            hadithBody.scrollBy({ top: step, behavior: 'smooth' });
        });

        hadithBody.addEventListener('scroll', syncHadithArrows, { passive: true });
        window.addEventListener('resize', () => {
            requestAnimationFrame(syncHadithArrows);
        });
        // After fonts/layout settle so height is accurate
        requestAnimationFrame(() => requestAnimationFrame(syncHadithArrows));
        window.setTimeout(syncHadithArrows, 300);
    }

    const galleryStage = document.querySelector('[data-gallery-stage]');
    if (galleryStage) {
        const allSlides = [...galleryStage.querySelectorAll('[data-gallery-slide]')];
        const prevBtn = galleryStage.querySelector('[data-gallery-prev]');
        const nextBtn = galleryStage.querySelector('[data-gallery-next]');
        const filters = document.querySelectorAll('[data-gallery-filter]');
        let activeFilter = 'all';
        let activeIndex = 0;
        let autoplayTimer = null;
        const autoplayMs = 4000;

        const visibleSlides = () => allSlides.filter((slide) => {
            if (activeFilter === 'all') {
                return true;
            }

            return String(slide.dataset.year || '') === String(activeFilter);
        });

        const layoutGallery = () => {
            const slides = visibleSlides();

            allSlides.forEach((slide) => {
                slide.classList.add('is-hidden');
                slide.classList.remove('is-active', 'is-near');
                slide.style.visibility = 'hidden';
                slide.style.opacity = '0';
                slide.style.pointerEvents = 'none';
                slide.style.zIndex = '0';
                slide.style.transform = 'translate(-50%, -50%) translateX(0%) scale(0.62) translateZ(-280px)';
            });

            if (slides.length === 0) {
                return;
            }

            activeIndex = ((activeIndex % slides.length) + slides.length) % slides.length;

            slides.forEach((slide, index) => {
                let offset = index - activeIndex;
                const half = Math.floor(slides.length / 2);

                if (offset > half) {
                    offset -= slides.length;
                } else if (offset < -half) {
                    offset += slides.length;
                }

                const abs = Math.abs(offset);
                const x = offset * 42;
                let scale = 0.62;
                let depth = -abs * 70;
                let opacity = 0;

                if (abs === 0) {
                    scale = 1;
                    depth = 0;
                    opacity = 1;
                } else if (abs === 1) {
                    scale = 0.84;
                    depth = -70;
                    opacity = 0.78;
                } else if (abs === 2) {
                    scale = 0.68;
                    depth = -140;
                    opacity = 0.56;
                }

                const show = abs <= 2;
                slide.classList.toggle('is-hidden', !show);
                slide.classList.toggle('is-active', abs === 0);
                slide.classList.toggle('is-near', abs === 1);
                slide.style.visibility = show ? 'visible' : 'hidden';
                slide.style.opacity = show ? String(opacity) : '0';
                slide.style.zIndex = String(30 - abs);
                slide.style.pointerEvents = abs <= 1 ? 'auto' : 'none';
                slide.style.transform = `translate(-50%, -50%) translateX(${x}%) scale(${scale}) translateZ(${depth}px)`;
            });
        };

        const stopAutoplay = () => {
            if (autoplayTimer) {
                clearInterval(autoplayTimer);
                autoplayTimer = null;
            }
        };

        const startAutoplay = () => {
            stopAutoplay();

            if (visibleSlides().length < 2) {
                return;
            }

            autoplayTimer = setInterval(() => {
                if (document.hidden) {
                    return;
                }

                activeIndex += 1;
                layoutGallery();
            }, autoplayMs);
        };

        const restartAutoplay = () => {
            startAutoplay();
        };

        filters.forEach((chip) => {
            chip.addEventListener('click', () => {
                activeFilter = chip.dataset.galleryFilter || 'all';
                activeIndex = 0;

                filters.forEach((item) => {
                    const on = item === chip;
                    item.classList.toggle('is-active', on);
                    item.setAttribute('aria-pressed', on ? 'true' : 'false');
                });

                layoutGallery();
                restartAutoplay();
            });
        });

        prevBtn?.addEventListener('click', () => {
            activeIndex -= 1;
            layoutGallery();
            restartAutoplay();
        });

        nextBtn?.addEventListener('click', () => {
            activeIndex += 1;
            layoutGallery();
            restartAutoplay();
        });

        allSlides.forEach((slide) => {
            slide.addEventListener('click', () => {
                const slides = visibleSlides();
                const index = slides.indexOf(slide);
                if (index >= 0) {
                    activeIndex = index;
                    layoutGallery();
                    restartAutoplay();
                }
            });
        });

        galleryStage.addEventListener('mouseenter', stopAutoplay);
        galleryStage.addEventListener('mouseleave', startAutoplay);
        document.addEventListener('visibilitychange', () => {
            if (document.hidden) {
                stopAutoplay();
            } else {
                startAutoplay();
            }
        });

        window.addEventListener('resize', layoutGallery);
        layoutGallery();
        startAutoplay();
    }

    const photoGallery = document.querySelector('[data-photo-gallery]');
    const lightbox = document.getElementById('gallery-lightbox');

    if (photoGallery) {
        const yearFilters = document.querySelectorAll('[data-year-filter]');
        const yearBlocks = [...photoGallery.querySelectorAll('[data-year-block]')];
        const items = [...photoGallery.querySelectorAll('[data-gallery-item]')];

        const applyYearFilter = (filter) => {
            yearBlocks.forEach((block) => {
                const year = String(block.dataset.yearBlock || '');
                const show = filter === 'all' || year === filter;
                block.classList.toggle('is-hidden', !show);
            });
        };

        yearFilters.forEach((chip) => {
            chip.addEventListener('click', () => {
                const filter = chip.dataset.yearFilter || 'all';

                yearFilters.forEach((item) => {
                    const on = item === chip;
                    item.classList.toggle('is-active', on);
                    item.setAttribute('aria-pressed', on ? 'true' : 'false');
                });

                applyYearFilter(filter);
            });
        });

        if (lightbox) {
            const imageEl = lightbox.querySelector('[data-lightbox-image]');
            const captionEl = lightbox.querySelector('.plightbox__caption');
            const titleEl = lightbox.querySelector('[data-lightbox-title]');
            const descEl = lightbox.querySelector('[data-lightbox-desc]');
            const counterEl = lightbox.querySelector('[data-lightbox-counter]');
            const closeBtns = lightbox.querySelectorAll('[data-lightbox-close]');
            const prevBtn = lightbox.querySelector('[data-lightbox-prev]');
            const nextBtn = lightbox.querySelector('[data-lightbox-next]');
            let activeItems = [];
            let activeIndex = 0;

            const visibleItems = () => items.filter((item) => !item.hidden && item.offsetParent !== null);

            const openLightbox = (index) => {
                activeItems = visibleItems();
                if (!activeItems.length) {
                    return;
                }

                activeIndex = ((index % activeItems.length) + activeItems.length) % activeItems.length;
                const item = activeItems[activeIndex];
                const src = item.dataset.src || '';
                const title = item.dataset.title || '';
                const video = item.dataset.video || '';

                if (item.dataset.type === 'video' && video) {
                    window.open(video, '_blank', 'noopener');
                    return;
                }

                imageEl.src = src;
                imageEl.alt = title || 'یادگار';

                if (titleEl) {
                    titleEl.textContent = title;
                }

                if (descEl) {
                    descEl.textContent = '';
                }

                captionEl?.classList.toggle('is-empty', !title);

                if (counterEl) {
                    counterEl.textContent = `${activeIndex + 1} / ${activeItems.length}`;
                }

                lightbox.hidden = false;
                lightbox.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            };

            const closeLightbox = () => {
                lightbox.hidden = true;
                lightbox.setAttribute('aria-hidden', 'true');
                imageEl.removeAttribute('src');
                document.body.style.overflow = '';
            };

            items.forEach((item) => {
                item.addEventListener('click', () => {
                    const list = visibleItems();
                    openLightbox(list.indexOf(item));
                });
            });

            closeBtns.forEach((btn) => btn.addEventListener('click', closeLightbox));
            prevBtn?.addEventListener('click', () => openLightbox(activeIndex - 1));
            nextBtn?.addEventListener('click', () => openLightbox(activeIndex + 1));

            document.addEventListener('keydown', (event) => {
                if (lightbox.hidden) {
                    return;
                }

                if (event.key === 'Escape') {
                    closeLightbox();
                } else if (event.key === 'ArrowLeft') {
                    openLightbox(activeIndex - 1);
                } else if (event.key === 'ArrowRight') {
                    openLightbox(activeIndex + 1);
                }
            });
        }
    }

    const prayerSection = document.querySelector('[data-prayer-section]');
    if (prayerSection) {
        const cityPicker = prayerSection.querySelector('[data-prayer-city-picker]');
        const cityBtn = prayerSection.querySelector('[data-prayer-city-btn]');
        const cityMenu = prayerSection.querySelector('[data-prayer-city-menu]');
        const cityLabel = prayerSection.querySelector('[data-prayer-city-label]');
        const cityOptions = [...prayerSection.querySelectorAll('[data-prayer-city-option]')];
        const endpoint = prayerSection.dataset.prayerEndpoint;
        const storageKey = 'jamia.prayer.city';
        const sourceKey = 'jamia.prayer.citySource';
        const locationEl = prayerSection.querySelector('[data-prayer-location]');
        const dateEl = prayerSection.querySelector('[data-prayer-date]');
        const hijriEl = prayerSection.querySelector('[data-prayer-hijri]');
        const hijriWrap = prayerSection.querySelector('[data-prayer-hijri-wrap]');
        const errorEl = prayerSection.querySelector('[data-prayer-error]');
        const loadingEl = prayerSection.querySelector('[data-prayer-loading]');
        const locateBtn = prayerSection.querySelector('[data-prayer-locate]');
        let selectedCity = prayerSection.dataset.defaultCity || '';

        const setLoading = (loading) => {
            prayerSection.classList.toggle('is-loading', loading);
            if (loadingEl) {
                loadingEl.hidden = !loading;
            }
            if (cityBtn) {
                cityBtn.disabled = loading;
            }
            if (locateBtn) {
                locateBtn.disabled = loading;
            }

            const headerCountdown = document.getElementById('prayer-countdown');
            if (headerCountdown && typeof headerCountdown.setPrayerLoading === 'function') {
                headerCountdown.setPrayerLoading(loading);
            }
        };

        const closeCityMenu = () => {
            if (!cityPicker || !cityMenu || !cityBtn) {
                return;
            }
            cityPicker.classList.remove('is-open');
            cityMenu.hidden = true;
            cityBtn.setAttribute('aria-expanded', 'false');
        };

        const openCityMenu = () => {
            if (!cityPicker || !cityMenu || !cityBtn) {
                return;
            }
            cityPicker.classList.add('is-open');
            cityMenu.hidden = false;
            cityBtn.setAttribute('aria-expanded', 'true');
        };

        const setSelectedOption = (city) => {
            cityOptions.forEach((option) => {
                const active = option.dataset.city === city;
                option.classList.toggle('is-active', active);
                option.setAttribute('aria-selected', active ? 'true' : 'false');
                if (active) {
                    selectedCity = city;
                    if (cityLabel) {
                        cityLabel.textContent = option.dataset.cityUr || city;
                    }
                }
            });
        };

        const applyPayload = (payload, option) => {
            const meta = payload.meta || {};
            const cityUr = option?.dataset.cityUr || meta.city_ur || meta.city || '';
            const countryUr = option?.dataset.countryUr || meta.country_ur || meta.country || '';

            if (locationEl) {
                locationEl.textContent = `${cityUr}، ${countryUr}`;
            }
            if (dateEl && meta.date) {
                dateEl.textContent = meta.date;
            }
            if (hijriEl) {
                hijriEl.textContent = meta.hijri || '';
            }
            if (hijriWrap) {
                hijriWrap.hidden = !meta.hijri;
            }
            if (errorEl) {
                errorEl.hidden = !payload.error;
            }

            (payload.timings || []).forEach((prayer) => {
                const card = prayerSection.querySelector(`[data-prayer-card][data-prayer-key="${prayer.key}"]`);
                if (!card) {
                    return;
                }

                const azan = card.querySelector('[data-prayer-azan]');
                const iqamah = card.querySelector('[data-prayer-iqamah]');
                const badge = card.querySelector('[data-prayer-badge]');

                if (azan) {
                    azan.textContent = prayer.azan;
                }
                if (iqamah) {
                    iqamah.textContent = prayer.iqamah;
                }

                card.classList.toggle('prayer-card--active', !!prayer.active);
                if (badge) {
                    badge.hidden = !prayer.active;
                }
            });

            const countdownEl = document.getElementById('prayer-countdown');
            if (countdownEl && typeof countdownEl.setPrayerSchedule === 'function' && payload.countdown) {
                countdownEl.setPrayerSchedule(payload.countdown);
            }
        };

        const defaultErrorText = errorEl?.textContent?.trim() || 'اوقاتِ نماز ابھی دستیاب نہیں۔';

        const showPrayerError = (message) => {
            if (!errorEl) {
                return;
            }
            errorEl.textContent = message || defaultErrorText;
            errorEl.hidden = false;
        };

        const hidePrayerError = () => {
            if (!errorEl) {
                return;
            }
            errorEl.textContent = defaultErrorText;
            errorEl.hidden = true;
        };

        const fetchPrayerPayload = async (query) => {
            const response = await fetch(`${endpoint}?${query}`, {
                headers: { Accept: 'application/json' },
            });
            if (!response.ok) {
                throw new Error('Failed to load prayer times');
            }
            return response.json();
        };

        const loadCity = async (city, { persist = true, source = 'manual' } = {}) => {
            if (!endpoint || !city) {
                return false;
            }

            const option = cityOptions.find((item) => item.dataset.city === city);
            setSelectedOption(city);
            setLoading(true);
            try {
                const payload = await fetchPrayerPayload(`city=${encodeURIComponent(city)}`);
                applyPayload(payload, option);
                hidePrayerError();
                if (persist) {
                    localStorage.setItem(storageKey, city);
                    localStorage.setItem(sourceKey, source);
                }
                return true;
            } catch (error) {
                showPrayerError(defaultErrorText);
                return false;
            } finally {
                setLoading(false);
            }
        };

        const applyLocatedPayload = (payload, { persist = true, source = 'auto' } = {}) => {
            const city = payload?.meta?.city;
            if (!city) {
                return false;
            }
            const option = cityOptions.find((item) => item.dataset.city === city);
            setSelectedOption(city);
            applyPayload(payload, option);
            hidePrayerError();
            if (persist) {
                localStorage.setItem(storageKey, city);
                localStorage.setItem(sourceKey, source);
            }
            return true;
        };

        const readGpsPosition = ({ force = false } = {}) => new Promise((resolve, reject) => {
            if (!navigator.geolocation || prayerSection.dataset.autoLocate !== '1') {
                reject(new Error('unsupported'));
                return;
            }

            navigator.geolocation.getCurrentPosition(
                (position) => resolve(position),
                (error) => reject(error),
                {
                    enableHighAccuracy: force,
                    timeout: force ? 15000 : 10000,
                    maximumAge: force ? 0 : 60 * 1000,
                },
            );
        });

        const applyDetectedCity = async ({ force = false } = {}) => {
            if (!endpoint) {
                return false;
            }

            setLoading(true);
            try {
                // 1) Browser GPS → server resolves Rawalpindi/etc via reverse geocode + nearest city
                try {
                    const position = await readGpsPosition({ force });
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;
                    const payload = await fetchPrayerPayload(
                        `lat=${encodeURIComponent(lat)}&lng=${encodeURIComponent(lng)}`,
                    );
                    const city = payload?.meta?.city;
                    if (!force && city && city === selectedCity) {
                        localStorage.setItem(storageKey, city);
                        localStorage.setItem(sourceKey, 'auto');
                        hidePrayerError();
                        return true;
                    }
                    return applyLocatedPayload(payload, { persist: true, source: 'auto' });
                } catch (geoError) {
                    // 2) Fallback: server IP lookup (works on public hosts, not localhost)
                    try {
                        const payload = await fetchPrayerPayload('locate=1');
                        return applyLocatedPayload(payload, { persist: true, source: 'auto' });
                    } catch (ipError) {
                        if (force) {
                            const denied = geoError && geoError.code === 1;
                            showPrayerError(
                                denied
                                    ? 'براؤزر نے مقام کی اجازت نہیں دی۔ Address bar میں Location Allow کریں، یا شہر خود منتخب کریں۔'
                                    : 'مقام معلوم نہیں ہو سکا۔ براہِ کرم Location Allow کریں یا راولپنڈی منتخب کریں۔',
                            );
                        }
                        return false;
                    }
                }
            } finally {
                setLoading(false);
            }
        };

        if (cityBtn && cityMenu) {
            cityBtn.addEventListener('click', (event) => {
                event.stopPropagation();
                if (cityMenu.hidden) {
                    openCityMenu();
                } else {
                    closeCityMenu();
                }
            });

            cityOptions.forEach((option) => {
                option.addEventListener('click', () => {
                    const city = option.dataset.city;
                    closeCityMenu();
                    if (city && city !== selectedCity) {
                        loadCity(city, { persist: true, source: 'manual' });
                    } else if (city) {
                        localStorage.setItem(sourceKey, 'manual');
                    }
                });
            });

            document.addEventListener('click', (event) => {
                if (cityPicker && !cityPicker.contains(event.target)) {
                    closeCityMenu();
                }
            });

            document.addEventListener('keydown', (event) => {
                if (event.key === 'Escape') {
                    closeCityMenu();
                }
            });
        }

        locateBtn?.addEventListener('click', async () => {
            locateBtn.disabled = true;
            hidePrayerError();
            try {
                await applyDetectedCity({ force: true });
            } finally {
                locateBtn.disabled = false;
            }
        });

        const bootCity = async () => {
            const savedCity = localStorage.getItem(storageKey);
            const savedSource = localStorage.getItem(sourceKey) || 'auto';
            const hasSaved = savedCity && cityOptions.some((option) => option.dataset.city === savedCity);

            // Manual choice: keep the user's selected city.
            if (hasSaved && savedSource === 'manual') {
                if (savedCity !== selectedCity) {
                    await loadCity(savedCity, { persist: false, source: 'manual' });
                }
                return;
            }

            // Auto mode: GPS (or IP fallback) for this visitor.
            const detected = await applyDetectedCity({ force: false });
            if (detected) {
                return;
            }

            if (hasSaved && savedCity !== selectedCity) {
                await loadCity(savedCity, { persist: false, source: 'auto' });
                return;
            }

            setLoading(false);
        };

        bootCity();
    }

    // WebPhase-style sticky horizontal text (durood section)
    const duroodSection = document.querySelector('[data-durood-scroll]');
    const duroodText = document.querySelector('[data-durood-scroll-text]');
    if (duroodSection && duroodText) {
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        if (!reduceMotion) {
            let ticking = false;

            const updateDuroodScroll = () => {
                const rect = duroodSection.getBoundingClientRect();
                const travel = Math.max(1, duroodSection.offsetHeight - window.innerHeight);
                const progress = Math.min(1, Math.max(0, -rect.top / travel));
                const textWidth = duroodText.scrollWidth;
                const viewWidth = window.innerWidth;
                // Enter from left, travel fully across to the right (not stop mid-screen)
                const startX = -(textWidth - viewWidth * 0.15);
                const endX = viewWidth * 0.75;
                const x = startX + (endX - startX) * progress;
                duroodText.style.transform = `translate3d(${x}px, 0, 0)`;
                ticking = false;
            };

            const onScrollOrResize = () => {
                if (ticking) {
                    return;
                }
                ticking = true;
                window.requestAnimationFrame(updateDuroodScroll);
            };

            window.addEventListener('scroll', onScrollOrResize, { passive: true });
            window.addEventListener('resize', onScrollOrResize, { passive: true });
            updateDuroodScroll();
        }
    }

    const scrollTopBtn = document.querySelector('[data-scroll-top]');
    if (scrollTopBtn) {
        const syncScrollTop = () => {
            const show = window.scrollY > 420;
            scrollTopBtn.hidden = !show;
        };

        scrollTopBtn.addEventListener('click', () => {
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });

        window.addEventListener('scroll', syncScrollTop, { passive: true });
        syncScrollTop();
    }

    // Home blog horizontal scroller
    document.querySelectorAll('[data-blog-scroller]').forEach((root) => {
        const track = root.querySelector('[data-blog-track]');
        const section = root.closest('.home-blog') || root.parentElement;
        const prevBtn = section?.querySelector('[data-blog-prev]');
        const nextBtn = section?.querySelector('[data-blog-next]');
        if (!track) {
            return;
        }

        const step = () => {
            const item = track.querySelector('.home-blog__item');
            if (!item) {
                return Math.max(180, track.clientWidth * 0.7);
            }
            const styles = window.getComputedStyle(track);
            const gap = Number.parseFloat(styles.columnGap || styles.gap || '0') || 0;
            return item.getBoundingClientRect().width + gap;
        };

        const maxScroll = () => Math.max(0, track.scrollWidth - track.clientWidth);

        const syncButtons = () => {
            const max = maxScroll();
            const left = Math.abs(track.scrollLeft);
            const atStart = left <= 2;
            const atEnd = left >= max - 2;
            if (prevBtn) {
                prevBtn.disabled = atStart;
            }
            if (nextBtn) {
                nextBtn.disabled = atEnd || max <= 0;
            }
        };

        const scrollByDir = (direction) => {
            const amount = step();
            const rtl = window.getComputedStyle(track).direction === 'rtl';
            // direction: -1 = toward start (prev), +1 = toward end (next)
            const delta = rtl ? -direction * amount : direction * amount;
            track.scrollBy({ left: delta, behavior: 'smooth' });
        };

        prevBtn?.addEventListener('click', () => scrollByDir(-1));
        nextBtn?.addEventListener('click', () => scrollByDir(1));
        track.addEventListener('scroll', syncButtons, { passive: true });
        window.addEventListener('resize', syncButtons, { passive: true });
        syncButtons();
    });

    // Cognition-style programme slider
    document.querySelectorAll('[data-programme-slider]').forEach((root) => {
        const slides = [...root.querySelectorAll('[data-programme-slide]')];
        const dots = [...root.querySelectorAll('[data-programme-dot]')];
        if (slides.length < 2) {
            return;
        }

        let current = Math.max(0, slides.findIndex((slide) => slide.classList.contains('is-active')));
        let timer = null;
        const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

        const go = (index) => {
            current = (index + slides.length) % slides.length;
            slides.forEach((slide, i) => {
                const active = i === current;
                slide.classList.toggle('is-active', active);
                slide.hidden = !active;
            });
            dots.forEach((dot, i) => {
                const active = i === current;
                dot.classList.toggle('is-active', active);
                dot.setAttribute('aria-selected', active ? 'true' : 'false');
            });
        };

        const startAutoplay = () => {
            if (reduceMotion) {
                return;
            }
            stopAutoplay();
            timer = window.setInterval(() => go(current + 1), 6500);
        };

        const stopAutoplay = () => {
            if (timer) {
                window.clearInterval(timer);
                timer = null;
            }
        };

        dots.forEach((dot) => {
            dot.addEventListener('click', () => {
                go(Number(dot.dataset.programmeDot) || 0);
                startAutoplay();
            });
        });

        root.addEventListener('mouseenter', stopAutoplay);
        root.addEventListener('mouseleave', startAutoplay);
        startAutoplay();
    });
});
