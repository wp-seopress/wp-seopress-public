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

    const ACCEPT_COOKIE = 'seopress-user-consent-accept';
    const DECLINE_COOKIE = 'seopress-user-consent-close';
    const SIGNALS = ['ad_storage', 'ad_user_data', 'ad_personalization', 'analytics_storage'];
    const BANNER_SELECTOR = '.seopress-user-consent.seopress-user-message, .seopress-user-consent-backdrop';

    /**
     * Whether this browser holds the acceptance cookie.
     *
     * @return {boolean} True when the visitor accepted.
     */
    const hasConsent = function () {
        return Cookies.get(ACCEPT_COOKIE) === '1';
    };

    /**
     * Tell the trackers already running about a decision.
     *
     * An update rather than a default: Consent Mode ignores a second default
     * for a signal it already knows. Both trackers are optional, since a site
     * may well run only one of them.
     *
     * @param {boolean} granted Whether the visitor accepted.
     */
    const updateConsent = function (granted) {
        if (typeof window.gtag === 'function') {
            const signals = {};
            SIGNALS.forEach(function (signal) {
                signals[signal] = granted ? 'granted' : 'denied';
            });
            window.gtag('consent', 'update', signals);
        }

        if (typeof window.clarity === 'function') {
            window.clarity('consent', granted);
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

    let started = false;

    /**
     * Start the deferred trackers, once.
     */
    const startTrackers = function () {
        if (started) {
            return;
        }

        // An optimiser may have moved this script into the head, ahead of the
        // body the snippets are appended to.
        if (!document.body) {
            document.addEventListener('DOMContentLoaded', startTrackers, { once: true });
            return;
        }

        const payload = readPayload();
        if (!payload) {
            return;
        }

        started = true;

        const head = document.head;
        const body = document.body;

        executeSnippet(payload.gtag_js, head);
        executeSnippet(payload.matomo_js, head);
        executeSnippet(payload.clarity_js, head);
        executeSnippet(payload.custom, head);
        executeSnippet(payload.head_js, head);
        executeSnippet(payload.body_js, body);
        executeSnippet(payload.matomo_body_js, body);
        executeSnippet(payload.footer_js, body);
        drainQueue();
    };

    /**
     * Notify third-party listeners (e.g. Google Tag Manager) of the decision.
     *
     * @param {string} consent 'accept' or 'decline'.
     */
    const notifyConsent = function (consent) {
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push({ event: 'seopress_consent_updated', seopress_consent: consent });
        document.dispatchEvent(new CustomEvent('seopress.consent', { detail: { consent: consent } }));
    };

    /**
     * Show or hide the banner and its backdrop.
     *
     * @param {boolean} visible Whether the banner should be on screen.
     */
    const toggleBanner = function (visible) {
        document.querySelectorAll(BANNER_SELECTOR).forEach(function (element) {
            element.classList.toggle('seopress-user-consent-hide', !visible);
        });
    };

    /**
     * How long a decision is remembered, in days.
     *
     * @return {number} Days before the visitor is asked again.
     */
    const expiry = function () {
        return Number(seopressAjaxGAUserConsent.seopress_cookies_expiration_days);
    };

    // A returning visitor is served the same cached HTML as everyone else, so
    // the trackers they accepted have to be started here rather than by the
    // click handler, which ran on a page they have since left.
    if (hasConsent()) {
        startTrackers();
    }

    document.addEventListener('DOMContentLoaded', function () {
        if (Cookies.get(DECLINE_COOKIE) === undefined && Cookies.get(ACCEPT_COOKIE) === undefined) {
            toggleBanner(true);
        }

        const acceptBtn = document.getElementById('seopress-user-consent-accept');
        if (acceptBtn) {
            acceptBtn.addEventListener('click', function () {
                toggleBanner(false);

                Cookies.remove(DECLINE_COOKIE);
                Cookies.set(ACCEPT_COOKIE, '1', { expires: expiry() });

                // Ahead of starting anything, so a tracker the auto-accept mode
                // printed up front hears the decision too.
                updateConsent(true);
                startTrackers();

                notifyConsent('accept');
            });
        }

        const declineBtn = document.getElementById('seopress-user-consent-close');
        if (declineBtn) {
            declineBtn.addEventListener('click', function () {
                toggleBanner(false);

                Cookies.remove(ACCEPT_COOKIE);
                Cookies.set(DECLINE_COOKIE, '1', { expires: expiry() });

                // Covers the visitor who accepted and changed their mind
                // without reloading: those trackers are running and have to be
                // told.
                updateConsent(false);

                notifyConsent('decline');
            });
        }

        const editBtn = document.getElementById('seopress-user-consent-edit');
        if (editBtn) {
            editBtn.addEventListener('click', function () {
                toggleBanner(true);
            });
        }
    });
})();
