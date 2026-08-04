/**
 * Fogos.pt — first-party cookie consent (GDPR-compliant, no dependencies).
 *
 * Storage: localStorage under "fogos:cookie-consent" as
 *   { v: <version>, ts: <ISO>, categories: { analytics: bool } }
 *
 * GA (gtag) is loaded on demand — only when the "analytics" category is
 * granted. The GA property ID is read from window.__FOGOS_GA_ID__ set in
 * scripts.blade.php.
 *
 * Public API:
 *   FogosConsent.hasConsent('analytics')  → bool, for other modules to gate
 *   FogosConsent.get()                    → current categories
 *   FogosConsent.open()                   → re-open the preferences modal
 *   FogosConsent.set({ analytics: bool }) → save + apply
 */
(function () {
    var STORAGE_KEY = 'fogos:cookie-consent';
    // Bump when the disclosed purposes change — forces re-consent.
    var CONSENT_VERSION = 1;
    var DEFAULT_CATEGORIES = { analytics: false };

    // ---------- storage ----------
    function loadConsent() {
        try {
            var raw = localStorage.getItem(STORAGE_KEY);
            if (!raw) return null;
            var o = JSON.parse(raw);
            if (!o || o.v !== CONSENT_VERSION) return null;
            return o;
        } catch (e) { return null; }
    }
    function saveConsent(categories) {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify({
                v: CONSENT_VERSION,
                ts: new Date().toISOString(),
                categories: categories
            }));
        } catch (e) { /* private mode etc. — silent */ }
    }
    function currentCategories() {
        var c = loadConsent();
        return c ? extend({}, DEFAULT_CATEGORIES, c.categories) : extend({}, DEFAULT_CATEGORIES);
    }
    function extend() {
        var out = arguments[0] || {};
        for (var i = 1; i < arguments.length; i++) {
            var src = arguments[i]; if (!src) continue;
            for (var k in src) if (Object.prototype.hasOwnProperty.call(src, k)) out[k] = src[k];
        }
        return out;
    }

    // ---------- gtag (Google Analytics) ----------
    function loadGA() {
        var id = window.__FOGOS_GA_ID__;
        if (!id || window.__FOGOS_GA_LOADED__) return;
        window.__FOGOS_GA_LOADED__ = true;
        var s = document.createElement('script');
        s.async = true;
        s.src = 'https://www.googletagmanager.com/gtag/js?id=' + encodeURIComponent(id);
        document.head.appendChild(s);
        window.dataLayer = window.dataLayer || [];
        window.gtag = function () { window.dataLayer.push(arguments); };
        window.gtag('js', new Date());
        window.gtag('config', id, { anonymize_ip: true });
    }

    function purgeCookies(prefixes) {
        // Walk document.cookie and clear anything whose name matches a prefix
        // on every parent domain (needed because gtag sets cookies on the
        // registrable domain, not necessarily the current subdomain).
        var domains = [''];
        var parts = location.hostname.split('.');
        for (var i = 0; i < parts.length - 1; i++) {
            domains.push('.' + parts.slice(i).join('.'));
        }
        domains.push(location.hostname);
        document.cookie.split(';').forEach(function (raw) {
            var name = raw.split('=')[0].trim();
            if (!name) return;
            for (var j = 0; j < prefixes.length; j++) {
                if (name === prefixes[j] || name.indexOf(prefixes[j]) === 0) {
                    domains.forEach(function (d) {
                        var dom = d ? '; domain=' + d : '';
                        document.cookie = name + '=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/' + dom;
                    });
                    break;
                }
            }
        });
    }
    function purgeAnalytics() {
        // gtag drops _ga, _gid, _gat, _ga_<container>
        purgeCookies(['_ga', '_gid', '_gat']);
        // If GA had already booted this page, best-effort disable further hits.
        var id = window.__FOGOS_GA_ID__;
        if (id) window['ga-disable-' + id] = true;
    }
    function apply(categories) {
        if (categories.analytics) loadGA();
        else purgeAnalytics();
    }

    // ---------- i18n ----------
    function t(key, fallback) {
        var c = window.trans && window.trans.consent;
        return (c && typeof c[key] === 'string') ? c[key] : fallback;
    }
    function esc(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }
    function policyHref() {
        var lang = document.documentElement.getAttribute('lang') || 'pt';
        return '/' + encodeURIComponent(lang) + '/privacy-policy';
    }

    // ---------- DOM ----------
    var currentBanner = null;
    var currentModal = null;

    function renderBanner() {
        removeBanner();
        var wrap = document.createElement('div');
        wrap.className = 'fogos-consent-banner';
        wrap.setAttribute('role', 'dialog');
        wrap.setAttribute('aria-live', 'polite');
        wrap.setAttribute('aria-label', t('title', 'Preferências de cookies'));
        wrap.innerHTML = ''
            + '<div class="fogos-consent-banner__body">'
            +   '<h5 class="fogos-consent-banner__title">' + esc(t('title', 'Preferências de cookies')) + '</h5>'
            +   '<p class="fogos-consent-banner__text">' + esc(t('intro', 'Este site usa cookies necessários e, com o seu consentimento, cookies de análise (Google Analytics) para melhorar a experiência.')) + ' '
            +     '<a href="' + esc(policyHref()) + '" target="_blank" rel="noopener">' + esc(t('policyLink', 'Política de privacidade')) + '</a>'
            +   '</p>'
            + '</div>'
            + '<div class="fogos-consent-banner__actions">'
            +   '<button type="button" class="fogos-consent-btn fogos-consent-btn--ghost" data-fc="preferences">' + esc(t('preferences', 'Preferências')) + '</button>'
            +   '<button type="button" class="fogos-consent-btn fogos-consent-btn--outline" data-fc="reject">' + esc(t('reject', 'Rejeitar tudo')) + '</button>'
            +   '<button type="button" class="fogos-consent-btn fogos-consent-btn--primary" data-fc="accept">' + esc(t('accept', 'Aceitar tudo')) + '</button>'
            + '</div>';
        document.body.appendChild(wrap);
        wrap.addEventListener('click', onAction);
        currentBanner = wrap;
    }
    function removeBanner() {
        if (currentBanner && currentBanner.parentNode) currentBanner.parentNode.removeChild(currentBanner);
        currentBanner = null;
    }

    function renderModal() {
        closeModal();
        var current = currentCategories();
        var wrap = document.createElement('div');
        wrap.className = 'fogos-consent-modal';
        wrap.setAttribute('role', 'dialog');
        wrap.setAttribute('aria-modal', 'true');
        wrap.setAttribute('aria-label', t('preferencesTitle', 'Gerir preferências de cookies'));
        wrap.innerHTML = ''
            + '<div class="fogos-consent-modal__panel" role="document">'
            +   '<button type="button" class="fogos-consent-modal__close" aria-label="' + esc(t('close', 'Fechar')) + '" data-fc="close">&times;</button>'
            +   '<h4 class="fogos-consent-modal__title">' + esc(t('preferencesTitle', 'Gerir preferências de cookies')) + '</h4>'
            +   '<p class="fogos-consent-modal__intro">' + esc(t('preferencesIntro', 'Escolha que categorias de cookies quer permitir. Pode alterar esta escolha mais tarde no menu.')) + '</p>'
            +   '<div class="fogos-consent-cat">'
            +     '<label class="fogos-consent-cat__row">'
            +       '<span class="fogos-consent-cat__label">'
            +         '<strong>' + esc(t('catNecessaryTitle', 'Necessários')) + '</strong>'
            +         '<span class="fogos-consent-cat__desc">' + esc(t('catNecessaryDesc', 'Guardam as suas preferências locais (idioma, camadas do mapa, filtros) no navegador. Sem estes o site não funciona correctamente.')) + '</span>'
            +       '</span>'
            +       '<input type="checkbox" checked disabled aria-label="' + esc(t('catNecessaryTitle', 'Necessários')) + '">'
            +     '</label>'
            +   '</div>'
            +   '<div class="fogos-consent-cat">'
            +     '<label class="fogos-consent-cat__row">'
            +       '<span class="fogos-consent-cat__label">'
            +         '<strong>' + esc(t('catAnalyticsTitle', 'Análise')) + '</strong>'
            +         '<span class="fogos-consent-cat__desc">' + esc(t('catAnalyticsDesc', 'Google Analytics — mede o tráfego e ajuda-nos a perceber o que funciona. IP anonimizado. Cookies: _ga, _gid, _gat.')) + '</span>'
            +       '</span>'
            +       '<input type="checkbox" data-fc-cat="analytics"' + (current.analytics ? ' checked' : '') + ' aria-label="' + esc(t('catAnalyticsTitle', 'Análise')) + '">'
            +     '</label>'
            +   '</div>'
            +   '<div class="fogos-consent-modal__actions">'
            +     '<button type="button" class="fogos-consent-btn fogos-consent-btn--outline" data-fc="reject">' + esc(t('reject', 'Rejeitar tudo')) + '</button>'
            +     '<button type="button" class="fogos-consent-btn fogos-consent-btn--outline" data-fc="accept">' + esc(t('accept', 'Aceitar tudo')) + '</button>'
            +     '<button type="button" class="fogos-consent-btn fogos-consent-btn--primary" data-fc="save">' + esc(t('save', 'Guardar preferências')) + '</button>'
            +   '</div>'
            + '</div>';
        document.body.appendChild(wrap);
        wrap.addEventListener('click', function (e) {
            if (e.target === wrap) { closeModal(); return; }
            onAction(e);
        });
        document.addEventListener('keydown', onEscape);
        currentModal = wrap;
    }
    function closeModal() {
        if (currentModal && currentModal.parentNode) currentModal.parentNode.removeChild(currentModal);
        currentModal = null;
        document.removeEventListener('keydown', onEscape);
    }
    function onEscape(e) { if (e.key === 'Escape') closeModal(); }

    function onAction(e) {
        var el = e.target;
        while (el && el !== document && !el.getAttribute('data-fc')) el = el.parentNode;
        if (!el || el === document) return;
        var action = el.getAttribute('data-fc');
        if (action === 'preferences') { renderModal(); }
        else if (action === 'reject')  { commit({ analytics: false }); }
        else if (action === 'accept')  { commit({ analytics: true }); }
        else if (action === 'save')    {
            var chosen = {};
            var boxes = (currentModal || document).querySelectorAll('[data-fc-cat]');
            for (var i = 0; i < boxes.length; i++) chosen[boxes[i].getAttribute('data-fc-cat')] = boxes[i].checked;
            commit(chosen);
        }
        else if (action === 'close')   { closeModal(); }
    }

    function commit(categories) {
        saveConsent(categories);
        apply(categories);
        removeBanner();
        closeModal();
    }

    // ---------- boot ----------
    function boot() {
        var existing = loadConsent();
        if (existing) {
            apply(existing.categories);
            return;
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', renderBanner);
        } else {
            renderBanner();
        }
    }

    window.FogosConsent = {
        get: currentCategories,
        set: function (cats) { commit(extend({}, DEFAULT_CATEGORIES, cats)); },
        open: function () { renderModal(); },
        hasConsent: function (cat) { return !!currentCategories()[cat]; }
    };

    boot();
})();
