/**
 * Cookie banner, and the trackers it gates.
 *
 * The page this runs in is very often served from a full-page cache, so it says
 * the same thing to every visitor: the consent defaults deny everything, and
 * the trackers waiting on an answer sit inert in a JSON island. Reading the
 * decision is this file's job, because the browser is the only place it can be
 * read without freezing one visitor's answer into the HTML served to everybody
 * else.
 *
 * Wrapped in an IIFE and exporting nothing: JS-concatenating optimisers such as
 * Autoptimize can end up loading the file twice, and a top-level const would
 * throw on the second pass and take the banner down with it.
 */
(function () {
    'use strict';

    // Optimisers may evaluate this file twice; share the guard across copies.
    if (window.seopressConsentInitialized) {
        return;
    }
    window.seopressConsentInitialized = true;

    const CONSENT_COOKIE = 'seopress-user-consent';
    // Written before categories existed, and still honoured until they expire.
    const ACCEPT_COOKIE = 'seopress-user-consent-accept';
    const DECLINE_COOKIE = 'seopress-user-consent-close';
    const CATEGORIES = ['necessary', 'analytics', 'advertising'];
    const OPTIONAL = ['analytics', 'advertising'];
    // Each Consent Mode v2 signal, and the category that grants it.
    const SIGNALS = {
        analytics_storage: 'analytics',
        ad_storage: 'advertising',
        ad_user_data: 'advertising',
        ad_personalization: 'advertising'
    };
    // Mirrors seopress_user_consent_snippet_categories(), for a page that
    // somehow lost the localized settings: each tracker lists alternatives,
    // each a set of categories that must all be granted.
    const EVERYTHING = [['analytics', 'advertising']];
    const DEFAULT_REQUIREMENTS = {
        gtag_js: [['analytics'], ['advertising']],
        matomo_js: [['analytics']],
        matomo_body_js: [['analytics']],
        clarity_js: [['analytics']],
        custom: EVERYTHING,
        head_js: EVERYTHING,
        body_js: EVERYTHING,
        footer_js: EVERYTHING
    };
    const BANNER_SELECTOR = '.seopress-user-consent.seopress-user-message, .seopress-user-consent-backdrop';
    const HIDE_CLASS = 'seopress-user-consent-hide';

    /**
     * Read one cookie straight from document.cookie.
     *
     * Not through js-cookie: a WordPress dependency only fixes the order of the
     * tags in the HTML, and an optimiser that combines, defers or delays scripts
     * can run this file before the library exists, or never load it before the
     * visitor clicks. Neither reads nor writes may need it.
     *
     * @param {string} name The cookie name, free of regex metacharacters.
     * @return {string|undefined} The raw value, or undefined when absent.
     */
    const readCookie = function (name) {
        const match = document.cookie.match(new RegExp('(?:^|;\\s*)' + name + '=([^;]*)'));
        return match ? match[1] : undefined;
    };

    /**
     * Write one cookie for the whole site, as js-cookie used to.
     *
     * @param {string} name  The cookie name.
     * @param {string} value The value, already safe for a cookie.
     * @param {number} days  Days before it expires; negative to delete it.
     */
    const writeCookie = function (name, value, days) {
        let cookie = name + '=' + value + '; path=/; SameSite=Lax';
        // A missing setting leaves a session cookie, as js-cookie did.
        if (Number.isFinite(days)) {
            cookie += '; expires=' + new Date(Date.now() + days * 864e5).toUTCString();
        }
        document.cookie = cookie;
    };

    /**
     * Delete one cookie written by writeCookie().
     *
     * @param {string} name The cookie name.
     */
    const removeCookie = function (name) {
        writeCookie(name, '', -1);
    };

    /**
     * The categories this browser granted.
     *
     * A visitor who answered before categories existed keeps their answer:
     * the old acceptance grants everything, the old refusal nothing.
     *
     * @return {string[]|null} The granted categories, or null before any answer.
     */
    const readGranted = function () {
        const value = readCookie(CONSENT_COOKIE);

        if (value !== undefined) {
            const granted = value.split(/\||%7C/i);
            return CATEGORIES.filter(function (category) {
                return category === 'necessary' || granted.indexOf(category) > -1;
            });
        }

        if (readCookie(ACCEPT_COOKIE) === '1') {
            return CATEGORIES.slice();
        }

        if (readCookie(DECLINE_COOKIE) === '1') {
            return ['necessary'];
        }

        return null;
    };

    /**
     * Summarise a set of granted categories as one word.
     *
     * @param {string[]} granted The granted categories.
     * @return {string} 'accept', 'decline' or 'partial'.
     */
    const summarize = function (granted) {
        const given = OPTIONAL.filter(function (category) {
            return granted.indexOf(category) > -1;
        });

        if (!given.length) {
            return 'decline';
        }

        return given.length === OPTIONAL.length ? 'accept' : 'partial';
    };

    /**
     * Tell the trackers already running about a decision.
     *
     * An update rather than a default: Consent Mode ignores a second default
     * for a signal it already knows. Both trackers are optional, since a site
     * may well run only one of them.
     *
     * @param {string[]} granted The granted categories.
     */
    const updateConsent = function (granted) {
        const state = function (category) {
            return granted.indexOf(category) > -1 ? 'granted' : 'denied';
        };

        if (typeof window.gtag === 'function') {
            const signals = {};
            Object.keys(SIGNALS).forEach(function (signal) {
                signals[signal] = state(SIGNALS[signal]);
            });
            window.gtag('consent', 'update', signals);
        }

        if (typeof window.clarity === 'function') {
            window.clarity('consentv2', {
                ad_Storage: state('advertising'),
                analytics_Storage: state('analytics')
            });
        }
    };

    // Nodes waiting to be inserted, in document order across every snippet.
    const pending = [];

    /**
     * Insert one node, recreating scripts so the browser runs them.
     *
     * innerHTML parses script tags but never executes them. A recreated
     * external script is also async by default, whereas printed inline it
     * would block what follows: a custom snippet loading a library and then
     * calling it depends on that. Such a script pauses the queue until it has
     * loaded or failed.
     *
     * @param {Object} item The node and where it belongs.
     * @return {boolean} True when the queue has to wait for this script.
     */
    const insertNode = function (item) {
        const node = item.node;

        if (node.nodeName !== 'SCRIPT') {
            item.target.appendChild(node.cloneNode(true));
            return false;
        }

        const script = document.createElement('script');
        Array.from(node.attributes).forEach(function (attr) {
            script.setAttribute(attr.name, attr.value);
        });
        script.textContent = node.textContent;

        const blocking = node.hasAttribute('src') && !node.hasAttribute('async') &&
            !node.hasAttribute('defer') && node.getAttribute('type') !== 'module';

        if (blocking) {
            script.async = false;
            script.onload = script.onerror = drainQueue;
        }

        item.target.appendChild(script);

        return blocking;
    };

    /**
     * Insert the pending nodes, synchronously until a blocking script.
     */
    const drainQueue = function () {
        while (pending.length) {
            if (insertNode(pending.shift())) {
                return;
            }
        }
    };

    /**
     * Queue a snippet of HTML to run the way the browser would have run it inline.
     *
     * @param {string}  html   The snippet.
     * @param {Element} target Where to append it.
     */
    const executeSnippet = function (html, target) {
        if (!html || !target) {
            return;
        }

        const holder = document.createElement('div');
        holder.innerHTML = html;

        Array.from(holder.childNodes).forEach(function (node) {
            pending.push({ node: node, target: target });
        });
    };

    /**
     * Read the trackers the page held back.
     *
     * Absent when nothing was deferred: no tracker configured, an excluded
     * role, or the auto-accept mode, which prints everything up front.
     *
     * Only the JSON script block counts. Post content can carry an element with
     * the same id (a <code> tag passes the content filters for authors), and
     * whatever it holds would be run as trackers. Script tags do not survive
     * those filters, so an author cannot plant a match.
     *
     * @return {Object|null} The snippets, or null when there are none.
     */
    const readPayload = function () {
        const island = document.querySelector('script#seopress-user-consent-payload[type="application/json"]');
        if (!island) {
            return null;
        }

        try {
            return JSON.parse(island.textContent);
        } catch (error) {
            return null;
        }
    };

    /**
     * The settings the page localized, if any.
     *
     * @return {Object} The settings, or an empty object.
     */
    const settings = function () {
        return window.seopressAjaxGAUserConsent || {};
    };

    /**
     * Whether a deferred tracker may start for a set of granted categories.
     *
     * @param {string}   key     The payload key.
     * @param {string[]} granted The granted categories.
     * @return {boolean} True when one of its alternatives is fully granted.
     */
    const isAllowed = function (key, granted) {
        const requirements = settings().requirements || DEFAULT_REQUIREMENTS;
        const alternatives = requirements[key] || EVERYTHING;

        return alternatives.some(function (alternative) {
            return alternative.every(function (category) {
                return granted.indexOf(category) > -1;
            });
        });
    };

    // Payload keys in the order the page would have printed them, with where
    // each one belongs.
    const SNIPPETS = [
        ['gtag_js', 'head'],
        ['matomo_js', 'head'],
        ['clarity_js', 'head'],
        ['custom', 'head'],
        ['head_js', 'head'],
        ['body_js', 'body'],
        ['matomo_body_js', 'body'],
        ['footer_js', 'body']
    ];

    // Trackers already running. A script cannot be taken back out of a page,
    // so a later refusal only reaches them through the consent signals.
    const started = {};

    /**
     * Start the deferred trackers the granted categories allow, each once.
     *
     * @param {string[]} granted The granted categories.
     */
    const startTrackers = function (granted) {
        // An optimiser may have moved this script into the head, ahead of the
        // body the snippets are appended to.
        if (!document.body) {
            document.addEventListener('DOMContentLoaded', function () {
                startTrackers(granted);
            }, { once: true });
            return;
        }

        const payload = readPayload();
        if (!payload) {
            return;
        }

        SNIPPETS.forEach(function (snippet) {
            const key = snippet[0];

            if (started[key] || !isAllowed(key, granted)) {
                return;
            }

            started[key] = true;
            executeSnippet(payload[key], document[snippet[1]]);
        });

        drainQueue();
    };

    /**
     * Notify third-party listeners (e.g. Google Tag Manager) of the decision.
     *
     * @param {string[]} granted The granted categories.
     */
    const notifyConsent = function (granted) {
        const consent = summarize(granted);

        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({
            event: 'seopress_consent_updated',
            seopress_consent: consent,
            seopress_consent_categories: granted.slice()
        });
        document.dispatchEvent(new CustomEvent('seopress.consent', {
            detail: { consent, categories: granted.slice() }
        }));
    };

    // For trackers and integrations of the site's own: the same answer this
    // file acts on, legacy cookies included.
    window.seopressConsent = {
        /**
         * Whether the visitor granted a category.
         *
         * @param {string} category 'necessary', 'analytics' or 'advertising'.
         * @return {boolean} True when granted.
         */
        has(category) {
            const granted = readGranted();
            return !!granted && granted.indexOf(category) > -1;
        },

        /**
         * The categories the visitor granted.
         *
         * @return {string[]|null} The categories, or null before any answer.
         */
        categories: readGranted
    };

    /**
     * Show or hide the banner and its backdrop.
     *
     * @param {boolean} visible Whether the banner should be on screen.
     */
    const toggleBanner = function (visible) {
        document.querySelectorAll(BANNER_SELECTOR).forEach(function (element) {
            element.classList.toggle(HIDE_CLASS, !visible);
        });

        if (!visible) {
            togglePanel(false);
        }
    };

    /**
     * Unfold or fold the per-category choices.
     *
     * Unfolding checks the boxes of what the visitor granted so far, so
     * reopening their choices starts from their answer, not from scratch.
     *
     * @param {boolean} open Whether the choices should be shown.
     */
    const togglePanel = function (open) {
        const panel = document.getElementById('seopress-user-consent-categories');
        if (!panel) {
            return;
        }

        const customizeBtn = document.getElementById('seopress-user-consent-customize');
        const saveBtn = document.getElementById('seopress-user-consent-save');
        const banner = panel.closest('.seopress-user-consent');

        panel.classList.toggle(HIDE_CLASS, !open);
        if (banner) {
            banner.classList.toggle('seopress-user-consent-expanded', open);
        }
        if (saveBtn) {
            saveBtn.classList.toggle(HIDE_CLASS, !open);
        }
        if (customizeBtn) {
            customizeBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
            customizeBtn.classList.toggle(HIDE_CLASS, open);
        }

        if (!open) {
            return;
        }

        const granted = readGranted() || [];
        const boxes = panel.querySelectorAll('input[type="checkbox"]');

        boxes.forEach(function (box) {
            box.checked = granted.indexOf(box.value) > -1;
        });

        // The button that opened the panel is now hidden: move focus into it.
        if (boxes.length) {
            boxes[0].focus();
        }
    };

    /**
     * The categories ticked in the panel.
     *
     * @return {string[]} Necessary, plus every ticked category.
     */
    const checkedCategories = function () {
        const granted = ['necessary'];

        document.querySelectorAll('#seopress-user-consent-categories input[type="checkbox"]').forEach(function (box) {
            if (box.checked && OPTIONAL.indexOf(box.value) > -1) {
                granted.push(box.value);
            }
        });

        return granted;
    };

    /**
     * How long a decision is remembered, in days.
     *
     * @return {number} Days before the visitor is asked again.
     */
    const expiry = function () {
        return Number(settings().seopress_cookies_expiration_days);
    };

    /**
     * Record a decision and act on it.
     *
     * @param {string[]} granted The granted categories, Necessary included.
     */
    const saveChoice = function (granted) {
        toggleBanner(false);

        // The old cookies would otherwise outlive the new answer.
        removeCookie(ACCEPT_COOKIE);
        removeCookie(DECLINE_COOKIE);
        writeCookie(CONSENT_COOKIE, granted.join('|'), expiry());

        // Ahead of starting anything, so a tracker the auto-accept mode printed
        // up front hears the decision too, and one already running hears a
        // refusal: a visitor who changes their mind without reloading.
        updateConsent(granted);
        startTrackers(granted);

        notifyConsent(granted);
    };

    // A returning visitor is served the same cached HTML as everyone else, so
    // the trackers they accepted have to be started here rather than by the
    // click handler, which ran on a page they have since left.
    const answered = readGranted();
    if (answered) {
        startTrackers(answered);
    }

    /**
     * Wire the banner up.
     */
    const init = function () {
        if (readGranted() === null) {
            toggleBanner(true);
        }

        const acceptBtn = document.getElementById('seopress-user-consent-accept');
        if (acceptBtn) {
            acceptBtn.addEventListener('click', function () {
                saveChoice(CATEGORIES.slice());
            });
        }

        const declineBtn = document.getElementById('seopress-user-consent-close');
        if (declineBtn) {
            declineBtn.addEventListener('click', function () {
                saveChoice(['necessary']);
            });
        }

        const customizeBtn = document.getElementById('seopress-user-consent-customize');
        if (customizeBtn) {
            customizeBtn.addEventListener('click', function () {
                togglePanel(true);
            });
        }

        const saveBtn = document.getElementById('seopress-user-consent-save');
        if (saveBtn) {
            saveBtn.addEventListener('click', function () {
                saveChoice(checkedCategories());
            });
        }

        const editBtn = document.getElementById('seopress-user-consent-edit');
        if (editBtn) {
            editBtn.addEventListener('click', function () {
                toggleBanner(true);
                // Straight to the choices, when the banner offers them.
                togglePanel(true);
            });
        }
    };

    // A delaying optimiser can run this file after DOMContentLoaded has fired,
    // and a listener added then would never be called.
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
