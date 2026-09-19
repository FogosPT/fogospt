(function () {
    'use strict';

    // Selection state built from user input; serialised into the URL every
    // time the DOM changes. Keys are compact so the generated URL stays short
    // (dico codes are already 4-char INE codes; statuses are one digit each).
    var state = {
        dicos: new Set(),      // e.g. "0603"
        statuses: new Set(),   // e.g. 5 (int)
        kind: 'fogos',         // "fogos" (default) or "todos"
        base: 'normal',        // "normal" (default) or "satellite"
        layers: new Set()      // alias strings, see LAYER_DEFS
    };

    // Must match LAYER_ALIASES in main.js. The wizard exposes each alias as a
    // checkbox; the main map resolves the alias to sectionKey:itemId.
    var LAYER_DEFS = [
        { key: 'modis',               label: 'MODIS' },
        { key: 'viirs',               label: 'VIIRS' },
        { key: 'frp',                 label: 'IPMA FRP' },
        { key: 'lightning',           labelFrom: 'panel.lightningLabel',   fallback: 'Descargas elétricas' },
        { key: 'planes',              labelFrom: 'panel.planes',           fallback: 'Aviões e helicópteros' },
        { key: 'planesTracks',        labelFrom: 'panel.planesTracks',     fallback: 'Aviões e helicópteros com trajetos' },
        { key: 'risk-today',          labelFrom: 'risk.today',             fallback: 'Perigo hoje',       prefix: 'RCM: ' },
        { key: 'risk-tomorrow',       labelFrom: 'risk.tomorrow',          fallback: 'Perigo amanhã',     prefix: 'RCM: ' },
        { key: 'risk-after',          labelFrom: 'risk.after',             fallback: 'Perigo depois',     prefix: 'RCM: ' },
        { key: 'ipma-temperature',    labelFrom: 'map.temperature',        fallback: 'Temperatura',       prefix: 'IPMA: ' },
        { key: 'ipma-wind',           labelFrom: 'map.wind',               fallback: 'Vento',             prefix: 'IPMA: ' },
        { key: 'ipma-wind-direction', labelFrom: 'map.windDirection',      fallback: 'Direção do vento',  prefix: 'IPMA: ' },
        { key: 'ipma-wind-animated',  labelFrom: 'map.windAnimated',       fallback: 'Vento animado',     prefix: 'IPMA: ' },
        { key: 'ipma-precipitation',  labelFrom: 'map.precipitation',      fallback: 'Precipitação',      prefix: 'IPMA: ' },
        { key: 'ipma-humidity',       labelFrom: 'map.humidity',           fallback: 'Humidade',          prefix: 'IPMA: ' },
        { key: 'perimeters',          labelFrom: 'panel.perimetersActive', fallback: 'Perímetros satélite' }
    ];

    // statusCode -> label key in window.trans.status. Order mirrors the panel
    // in main.js so the wizard's UI reads left-to-right the same way.
    var STATUS_DEFS = [
        { code: 3,  key: 'dispatch' },
        { code: 4,  key: 'firstAlertDispatch' },
        { code: 6,  key: 'arrival' },
        { code: 5,  key: 'ongoing' },
        { code: 7,  key: 'inResolution' },
        { code: 8,  key: 'conclusion' },
        { code: 9,  key: 'surveillance' },
        { code: 10, key: 'closed' },
        { code: 11, key: 'falseAlarm' },
        { code: 12, key: 'falseAlert' }
    ];

    function trans(path, fallback) {
        var parts = path.split('.');
        var cur = window.trans;
        for (var i = 0; i < parts.length; i++) {
            if (cur == null) return fallback;
            cur = cur[parts[i]];
        }
        return (cur == null) ? fallback : cur;
    }

    function toTitle(s) {
        return String(s || '').toLowerCase();
    }

    // ---- DOM helpers ----
    function el(tag, attrs, children) {
        var e = document.createElement(tag);
        if (attrs) {
            for (var k in attrs) {
                if (k === 'class') e.className = attrs[k];
                else if (k === 'text') e.textContent = attrs[k];
                else if (k === 'html') e.innerHTML = attrs[k];
                else e.setAttribute(k, attrs[k]);
            }
        }
        if (children) children.forEach(function (c) { if (c) e.appendChild(c); });
        return e;
    }

    // ---- Concelho grouping (by district, alphabetic) ----
    function buildDistrictGroups() {
        var groups = {};
        (concelhos.features || []).forEach(function (f) {
            var p = f.properties || {};
            var dName = p.Distrito || '—';
            if (!groups[dName]) groups[dName] = [];
            groups[dName].push({ dico: p.DICO, name: p.Concelho, feature: f });
        });
        Object.keys(groups).forEach(function (d) {
            groups[d].sort(function (a, b) { return a.name.localeCompare(b.name, 'pt'); });
        });
        return groups;
    }

    // ---- Render sections ----
    function renderDistricts(container) {
        var groups = buildDistrictGroups();
        var districtNames = Object.keys(groups).sort(function (a, b) {
            return a.localeCompare(b, 'pt');
        });
        districtNames.forEach(function (dName) {
            var items = groups[dName];
            var head = el('div', { class: 'mc-district__head' });
            var title = el('div', { class: 'mc-district__title' }, [
                el('i', { class: 'fas fa-chevron-right mc-district__chevron' }),
                el('span', { text: toTitle(dName) }),
                el('span', { class: 'mc-district__count', 'data-district-count': dName })
            ]);
            var actions = el('div', { class: 'mc-district__actions' }, [
                (function () {
                    var b = el('button', { type: 'button', text: (window.trans && window.trans.mapConfig && window.trans.mapConfig.selectAll) || 'All' });
                    b.addEventListener('click', function (e) {
                        e.stopPropagation();
                        items.forEach(function (it) { state.dicos.add(it.dico); });
                        wrap.classList.add('is-open');
                        sync();
                    });
                    return b;
                })(),
                (function () {
                    var b = el('button', { type: 'button', text: (window.trans && window.trans.mapConfig && window.trans.mapConfig.clearAll) || 'Clear' });
                    b.addEventListener('click', function (e) {
                        e.stopPropagation();
                        items.forEach(function (it) { state.dicos.delete(it.dico); });
                        sync();
                    });
                    return b;
                })()
            ]);
            head.appendChild(title);
            head.appendChild(actions);

            var body = el('div', { class: 'mc-district__body' });
            items.forEach(function (it) {
                var cb = el('input', { type: 'checkbox', value: it.dico, 'data-dico': it.dico });
                cb.addEventListener('change', function () {
                    if (cb.checked) state.dicos.add(it.dico);
                    else state.dicos.delete(it.dico);
                    sync();
                });
                var label = el('label', { class: 'mc-concelho', 'data-concelho-name': it.name.toLowerCase() }, [
                    cb,
                    el('span', { text: toTitle(it.name) })
                ]);
                body.appendChild(label);
            });

            var wrap = el('div', { class: 'mc-district', 'data-district': dName }, [head, body]);
            head.addEventListener('click', function () {
                wrap.classList.toggle('is-open');
            });
            container.appendChild(wrap);
        });
    }

    function renderStatus(container) {
        var labels = (window.trans && window.trans.status) || {};
        STATUS_DEFS.forEach(function (def) {
            var cb = el('input', { type: 'checkbox', value: def.code });
            cb.addEventListener('change', function () {
                if (cb.checked) state.statuses.add(def.code);
                else state.statuses.delete(def.code);
                sync();
            });
            container.appendChild(el('label', {}, [ cb, el('span', { text: labels[def.key] || def.key }) ]));
        });
    }

    function renderKind(container) {
        var opts = [
            { v: 'fogos', label: trans('mapConfig.kindFires', 'Só incêndios') },
            { v: 'todos', label: trans('mapConfig.kindAll', 'Todos os incidentes') }
        ];
        opts.forEach(function (o) {
            var r = el('input', { type: 'radio', name: 'mc-kind', value: o.v });
            if (o.v === state.kind) r.checked = true;
            r.addEventListener('change', function () {
                if (r.checked) { state.kind = o.v; sync(); }
            });
            container.appendChild(el('label', {}, [ r, el('span', { text: o.label }) ]));
        });
    }

    function renderBase(container) {
        var opts = [
            { v: 'normal',    label: trans('map.normal', 'Normal') },
            { v: 'satellite', label: trans('map.satellite', 'Satélite') }
        ];
        opts.forEach(function (o) {
            var r = el('input', { type: 'radio', name: 'mc-base', value: o.v });
            if (o.v === state.base) r.checked = true;
            r.addEventListener('change', function () {
                if (r.checked) { state.base = o.v; sync(); }
            });
            container.appendChild(el('label', {}, [ r, el('span', { text: o.label }) ]));
        });
    }

    function renderLayers(container) {
        LAYER_DEFS.forEach(function (def) {
            var text = def.label
                || ((def.prefix || '') + (def.labelFrom ? trans(def.labelFrom, def.fallback) : def.fallback));
            var cb = el('input', { type: 'checkbox', value: def.key });
            cb.addEventListener('change', function () {
                if (cb.checked) state.layers.add(def.key);
                else state.layers.delete(def.key);
                sync();
            });
            container.appendChild(el('label', {}, [ cb, el('span', { text: text }) ]));
        });
    }

    // ---- Preview map ----
    var previewMap = null;
    var previewLayer = null;
    var previewInitialBounds = null;

    function initPreview(node) {
        previewMap = L.map(node, { zoomControl: true, attributionControl: false })
            .setView([39.5, -8.0], 6);
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(previewMap);
        // Cache the "fit to all of Portugal" bounds by fitting the full
        // GeoJSON once — we use it as the fallback view when nothing's picked.
        try {
            var allLayer = L.geoJson(concelhos);
            previewInitialBounds = allLayer.getBounds();
            if (previewInitialBounds.isValid()) previewMap.fitBounds(previewInitialBounds);
        } catch (e) {}
    }

    function refreshPreview() {
        if (!previewMap) return;
        if (previewLayer) {
            previewMap.removeLayer(previewLayer);
            previewLayer = null;
        }
        if (state.dicos.size === 0) {
            if (previewInitialBounds && previewInitialBounds.isValid()) {
                previewMap.fitBounds(previewInitialBounds);
            }
            return;
        }
        var subset = concelhos.features.filter(function (f) {
            return state.dicos.has(f.properties.DICO);
        });
        if (!subset.length) return;
        previewLayer = L.geoJson({ type: 'FeatureCollection', features: subset }, {
            style: { color: '#b81e1f', weight: 2, fillColor: '#b81e1f', fillOpacity: 0.25 }
        }).addTo(previewMap);
        var b = previewLayer.getBounds();
        if (b.isValid()) previewMap.fitBounds(b, { padding: [20, 20] });
    }

    // ---- URL building ----
    function buildUrl() {
        var base = window.fogosMapConfigMapCustomUrl || '/pt/mapa';
        var params = [];
        if (state.dicos.size) {
            params.push('c=' + Array.from(state.dicos).sort().join(','));
        }
        if (state.statuses.size) {
            params.push('s=' + Array.from(state.statuses).sort(function (a, b) { return a - b; }).join(','));
        }
        if (state.kind !== 'fogos') {
            params.push('k=' + encodeURIComponent(state.kind));
        }
        if (state.base !== 'normal') {
            params.push('b=' + encodeURIComponent(state.base));
        }
        if (state.layers.size) {
            params.push('l=' + Array.from(state.layers).join(','));
        }
        var qs = params.join('&');
        // Absolute URL so copy-to-clipboard produces something shareable
        // outside the current tab.
        var absolute = base;
        if (base.indexOf('http') !== 0) {
            absolute = window.location.protocol + '//' + window.location.host + base;
        }
        return absolute + (qs ? '?' + qs : '');
    }

    // ---- Sync UI after any state change ----
    var urlInput, openLink, countEl;
    function sync() {
        var url = buildUrl();
        if (urlInput) urlInput.value = url;
        if (openLink) openLink.setAttribute('href', url);
        if (countEl) {
            var tmpl = trans('mapConfig.selectedCount', '{n} concelho(s) selecionado(s)');
            countEl.textContent = tmpl.replace('{n}', state.dicos.size);
        }
        // Per-district counters
        var groups = {};
        state.dicos.forEach(function (dico) {
            var f = concelhos.features.find(function (f) { return f.properties.DICO === dico; });
            if (!f) return;
            var d = f.properties.Distrito;
            groups[d] = (groups[d] || 0) + 1;
        });
        document.querySelectorAll('[data-district-count]').forEach(function (el) {
            var d = el.getAttribute('data-district-count');
            var n = groups[d] || 0;
            el.textContent = n ? '(' + n + ')' : '';
        });
        refreshPreview();
    }

    // ---- Search filter ----
    function bindSearch(input) {
        input.addEventListener('input', function () {
            var q = input.value.trim().toLowerCase();
            document.querySelectorAll('.mc-concelho').forEach(function (label) {
                var name = label.getAttribute('data-concelho-name') || '';
                label.classList.toggle('is-hidden', !!q && name.indexOf(q) === -1);
            });
            if (q) {
                document.querySelectorAll('.mc-district').forEach(function (d) { d.classList.add('is-open'); });
            }
        });
    }

    // ---- Copy button ----
    function bindCopy(btn, input, feedback) {
        btn.addEventListener('click', function () {
            input.select();
            var done = false;
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(input.value).then(function () {
                    done = true;
                    feedback.classList.add('is-visible');
                    setTimeout(function () { feedback.classList.remove('is-visible'); }, 1500);
                });
            }
            if (!done) {
                try { document.execCommand('copy'); feedback.classList.add('is-visible');
                    setTimeout(function () { feedback.classList.remove('is-visible'); }, 1500);
                } catch (e) {}
            }
        });
    }

    // ---- Restore state from URL (used when the /mapa/configurar page is
    // opened via the "Edit options" link from an existing custom map). Only
    // seeds the internal state — the DOM sync happens after each render.
    function readStateFromLocation() {
        var qs = window.location.search;
        if (!qs || qs.length < 2) return;
        var params = new URLSearchParams(qs);
        var c = params.get('c');
        if (c) c.split(',').forEach(function (d) { if (d) state.dicos.add(d); });
        var s = params.get('s');
        if (s) s.split(',').forEach(function (v) {
            var n = parseInt(v, 10);
            if (!isNaN(n)) state.statuses.add(n);
        });
        var k = params.get('k');
        if (k === 'todos') state.kind = 'todos';
        var b = params.get('b');
        if (b === 'satellite') state.base = 'satellite';
        var l = params.get('l');
        if (l) l.split(',').forEach(function (a) { if (a) state.layers.add(a); });
    }

    // Apply the internal state onto the just-rendered form controls so the
    // wizard opens with everything ticked as it was in the URL.
    function applyStateToDom() {
        state.dicos.forEach(function (dico) {
            var cb = document.querySelector('input[data-dico="' + dico + '"]');
            if (cb) {
                cb.checked = true;
                var group = cb.closest('.mc-district');
                if (group) group.classList.add('is-open');
            }
        });
        state.statuses.forEach(function (code) {
            var cb = document.querySelector('[data-mc-status] input[value="' + code + '"]');
            if (cb) cb.checked = true;
        });
        var kindRadio = document.querySelector('[data-mc-kind] input[value="' + state.kind + '"]');
        if (kindRadio) kindRadio.checked = true;
        var baseRadio = document.querySelector('[data-mc-base] input[value="' + state.base + '"]');
        if (baseRadio) baseRadio.checked = true;
        state.layers.forEach(function (key) {
            var cb = document.querySelector('[data-mc-layers] input[value="' + key + '"]');
            if (cb) cb.checked = true;
        });
    }

    // ---- Boot ----
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof concelhos === 'undefined' || !concelhos.features) return;

        readStateFromLocation();

        renderDistricts(document.querySelector('[data-mc-districts]'));
        renderStatus(document.querySelector('[data-mc-status]'));
        renderKind(document.querySelector('[data-mc-kind]'));
        renderBase(document.querySelector('[data-mc-base]'));
        renderLayers(document.querySelector('[data-mc-layers]'));

        applyStateToDom();

        // Strip the query string from the address bar. The wizard is
        // stateful via its own DOM; keeping params in the URL would
        // encourage users to bookmark /mapa/configurar?… when they meant
        // to bookmark the generated /mapa?… link.
        if (window.history && window.history.replaceState && window.location.search) {
            window.history.replaceState('', document.title, window.location.pathname);
        }

        urlInput  = document.querySelector('[data-mc-url]');
        openLink  = document.querySelector('[data-mc-open]');
        countEl   = document.querySelector('[data-mc-count]');
        var searchEl = document.querySelector('[data-mc-search]');
        var copyBtn  = document.querySelector('[data-mc-copy]');
        var copied   = document.querySelector('[data-mc-copied]');
        var clearAllBtn = document.querySelector('[data-mc-clear-all]');

        if (searchEl) bindSearch(searchEl);
        if (copyBtn && urlInput && copied) bindCopy(copyBtn, urlInput, copied);
        if (clearAllBtn) clearAllBtn.addEventListener('click', function () {
            state.dicos.clear();
            document.querySelectorAll('input[data-dico]').forEach(function (cb) { cb.checked = false; });
            sync();
        });

        initPreview(document.getElementById('mc-map'));
        sync();
    });
})();
