/**
 * Satellite overlays — fire perimeters and 6h propagation isochrones,
 * fed by the source.fogos.pt v2 MTG FRP endpoints. All requests go
 * through the internal source host with the FPTSC header, so this
 * module works even on pages (e.g. detail) that never call the global
 * $.ajaxSetup wiring from main.js.
 *
 * Public API:
 *   FogosSat.installMain(map, panel, opts)
 *     Adds a toggle-able perimeter overlay to the main map. When `panel`
 *     is a FogosPanel instance it registers a checkbox; when null it
 *     falls back to a small L.control.layers overlay.
 *
 *   FogosSat.installDetail({ mapEl, fireId, lat, lng })
 *     Boots a dedicated Leaflet map inside the given element and wires
 *     the per-incident perimeter + simulation endpoints.
 *
 * The module polls every 120s (main) / 60s (detail) while the tab is
 * visible; it suspends automatically when the tab is hidden. Empty
 * responses (`features: []`) are silent. Network failures keep the last
 * known layer visible.
 */
(function () {
    var API = 'https://source.fogos.pt';
    var FPTSC = 'xw2gfca9l7';

    function apiGet(path) {
        return $.ajax({
            url: API + path,
            method: 'GET',
            cache: false,
            headers: { FPTSC: FPTSC }
        });
    }

    var HOUR_RAMP = {
        1: '#7f0000',
        2: '#b71c1c',
        3: '#d32f2f',
        4: '#e53935',
        5: '#ef5350',
        6: '#ffcdd2'
    };

    function perimeterStyle() {
        return { color: '#c62828', weight: 2, opacity: 0.9, fillColor: '#e53935', fillOpacity: 0.35 };
    }

    function styleForHour(feature) {
        var h = feature && feature.properties && feature.properties.hour;
        var c = HOUR_RAMP[h] || '#e53935';
        return { color: c, weight: 1, opacity: 0.9, fillColor: c, fillOpacity: 0.28, dashArray: '4,3' };
    }

    function escapeHtml(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    function bindIsochroneTooltip(feature, layer) {
        var p = feature && feature.properties;
        if (!p) return;
        var area = (typeof p.area_km2 === 'number') ? p.area_km2.toFixed(1) + ' km²' : '';
        layer.bindTooltip('+' + p.hour + 'h ' + area, { sticky: true, direction: 'top' });
    }

    function fmtHHMM(iso) {
        if (!iso) return '';
        var d = new Date(iso);
        if (isNaN(d.getTime())) return '';
        var hh = String(d.getHours()).padStart(2, '0');
        var mm = String(d.getMinutes()).padStart(2, '0');
        return hh + ':' + mm;
    }

    function startPoll(fn, ms) {
        var t = null;
        function tick() { if (!document.hidden) fn(); }
        function schedule() { if (t) clearInterval(t); t = setInterval(tick, ms); }
        function onVisibility() {
            if (!document.hidden) { fn(); schedule(); }
        }
        document.addEventListener('visibilitychange', onVisibility);
        fn();
        schedule();
        return {
            stop: function () {
                if (t) clearInterval(t);
                t = null;
                document.removeEventListener('visibilitychange', onVisibility);
            }
        };
    }

    function renderAreasList(features, $mount) {
        var t = (window.trans && window.trans.sat) || {};
        var conc = {}, concOrd = [];
        var freg = {}, fregOrd = [];

        function addConcelho(name) {
            if (!name || conc[name]) return;
            conc[name] = true; concOrd.push(name);
        }
        function addFreguesia(name, municipio) {
            if (!name) return;
            var key = (municipio || '') + '|' + name;
            if (freg[key]) return;
            freg[key] = municipio ? (name + ' (' + municipio + ')') : name;
            fregOrd.push(key);
        }

        features.forEach(function (f) {
            var p = f && f.properties;
            if (!p) return;

            if (Array.isArray(p.concelhos)) {
                p.concelhos.forEach(function (c) { addConcelho(c && (c.municipio || c.concelho)); });
            }
            if (Array.isArray(p.freguesias)) {
                p.freguesias.forEach(function (fr) { addFreguesia(fr && fr.freguesia, fr && fr.municipio); });
            }

            // Fallback: aggregated /v2/fire/perimeters exposes only fogospt_incident.
            if (!Array.isArray(p.concelhos) && !Array.isArray(p.freguesias)) {
                var inc = p.fogospt_incident;
                if (inc) {
                    addConcelho(inc.concelho);
                    addFreguesia(inc.freguesia, inc.concelho);
                }
            }
        });

        var none = escapeHtml(t.none || '—');
        var concList = concOrd.map(escapeHtml).join(', ');
        var fregList = fregOrd.map(function (k) { return escapeHtml(freg[k]); }).join(', ');
        var html = ''
            + '<h5>' + escapeHtml(t.affectedConcelhos || 'Concelhos afetados') + '</h5>'
            + '<p class="mb-2">' + (concList || none) + '</p>'
            + '<h5>' + escapeHtml(t.affectedFreguesias || 'Freguesias afetadas') + '</h5>'
            + '<p class="mb-0">' + (fregList || none) + '</p>';
        $mount.html(html);
    }

    // ---- Main map ----
    function installMain(map, panel, opts) {
        opts = opts || {};
        var intervalMs = opts.intervalMs || 120000;
        var defaultOn = !!opts.defaultOn;
        var tp = (window.trans && window.trans.panel) || {};
        var label = tp.perimeters || 'Perímetros satélite';
        var itemLabel = tp.perimetersActive || label;

        var perimLayer = L.geoJSON(null, { style: perimeterStyle, interactive: false });

        // Small floating timestamp badge — only mounted while the layer is on.
        var TsControl = L.Control.extend({
            options: { position: 'bottomleft' },
            onAdd: function () {
                var el = L.DomUtil.create('div', 'fogos-sat-badge');
                el.setAttribute('aria-live', 'polite');
                this._el = el;
                return el;
            },
            setText: function (txt) { if (this._el) this._el.textContent = txt; }
        });
        var ts = new TsControl();

        var poll = null;
        function refresh() {
            apiGet('/v2/fire/perimeters')
                .done(function (fc, _s, xhr) {
                    if (!fc || !fc.features || !fc.features.length) {
                        perimLayer.clearLayers();
                        ts.setText('');
                        return;
                    }
                    perimLayer.clearLayers();
                    perimLayer.addData(fc);
                    var fetched = xhr.getResponseHeader('X-Fetched-At');
                    var hhmm = fmtHHMM(fetched);
                    ts.setText(hhmm ? ('Satélite: ' + hhmm) : '');
                })
                .fail(function () { /* silent — keep last-known layer */ });
        }

        perimLayer.on('add', function () {
            ts.addTo(map);
            if (!poll) poll = startPoll(refresh, intervalMs);
        });
        perimLayer.on('remove', function () {
            if (poll) { poll.stop(); poll = null; }
            if (ts._map) map.removeControl(ts);
        });

        if (panel && typeof panel.registerSection === 'function') {
            // Section may already be registered by main.js; addItem creates the
            // toggle. Default-off; user's choice persists in localStorage.
            panel.registerSection('perimeters', label, 'checkbox');
            panel.addItem('perimeters', 'active', itemLabel, perimLayer, defaultOn);
        } else {
            // Madeira fallback — plain L.control.layers overlay.
            var overlays = {};
            overlays[label] = perimLayer;
            L.control.layers(null, overlays, { position: 'topright' }).addTo(map);
            if (defaultOn) perimLayer.addTo(map);
        }

        return { layer: perimLayer };
    }

    // ---- Detail page ----
    function installDetail(opts) {
        opts = opts || {};
        var mapEl = opts.mapEl;
        var fireId = opts.fireId;
        var lat = opts.lat, lng = opts.lng;
        var intervalMs = opts.intervalMs || 60000;
        if (!mapEl || !fireId) return null;

        var el = (typeof mapEl === 'string') ? document.getElementById(mapEl) : mapEl;
        if (!el) return null;

        var map = L.map(el, { center: [lat, lng], zoom: 12 });
        L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors'
        }).addTo(map);

        var perimLayer = L.geoJSON(null, { style: perimeterStyle, interactive: false }).addTo(map);
        var simLayer = L.geoJSON(null, { style: styleForHour, onEachFeature: bindIsochroneTooltip }).addTo(map);

        var $ts = $('.js-sat-ts');
        var $empty = $('.js-sat-empty');
        var $areas = $('.js-sat-areas');
        var hasFitted = false;

        // Animation state — bloom hour-by-hour, hold, loop.
        var animTimer = null;
        var animStep = 0;
        var animBadgeControl = L.Control.extend({
            options: { position: 'bottomright' },
            onAdd: function () {
                var el = L.DomUtil.create('div', 'fogos-sat-badge fogos-sat-anim');
                this._el = el;
                return el;
            },
            setText: function (txt) { if (this._el) this._el.textContent = txt; }
        });
        var animBadge = new animBadgeControl();

        function hideAllIsochrones() {
            simLayer.eachLayer(function (l) {
                if (l.setStyle) l.setStyle({ opacity: 0, fillOpacity: 0 });
            });
        }
        function showIsochronesUpTo(hour) {
            simLayer.eachLayer(function (l) {
                var h = l.feature && l.feature.properties && l.feature.properties.hour;
                if (h && h <= hour && l.setStyle) l.setStyle(styleForHour(l.feature));
                else if (l.setStyle) l.setStyle({ opacity: 0, fillOpacity: 0 });
            });
        }
        function stopAnimation() {
            if (animTimer) { clearTimeout(animTimer); animTimer = null; }
            if (animBadge._map) map.removeControl(animBadge);
        }
        function startAnimation() {
            stopAnimation();
            if (simLayer.getLayers().length === 0) return;
            animBadge.addTo(map);
            animStep = 1;
            function tick() {
                if (animStep <= 6) {
                    showIsochronesUpTo(animStep);
                    animBadge.setText('+' + animStep + 'h');
                    animStep++;
                    animTimer = setTimeout(tick, 700);
                } else {
                    // Hold the full state, then reset.
                    animBadge.setText('+6h');
                    animTimer = setTimeout(function () {
                        animStep = 1;
                        tick();
                    }, 1800);
                }
            }
            tick();
        }

        function fitOnce(layer) {
            if (hasFitted) return;
            try {
                var b = layer.getBounds();
                if (b && b.isValid()) { map.fitBounds(b.pad(0.2)); hasFitted = true; }
            } catch (e) {}
        }

        function refresh() {
            var pReq = apiGet('/v2/incidents/' + encodeURIComponent(fireId) + '/perimeter');
            var sReq = apiGet('/v2/incidents/' + encodeURIComponent(fireId) + '/simulation');

            pReq.done(function (fc) {
                perimLayer.clearLayers();
                var hasFeatures = fc && fc.features && fc.features.length > 0;
                if (hasFeatures) perimLayer.addData(fc);
                renderAreasList(hasFeatures ? fc.features : [], $areas);
                if (hasFeatures) fitOnce(perimLayer);
                var stamp = hasFeatures ? fc.fetched_at : null;
                updateTimestamp(stamp);
                updateEmpty();
            }).fail(function () { /* silent */ });

            sReq.done(function (fc) {
                stopAnimation();
                simLayer.clearLayers();
                if (!fc || !fc.features || !fc.features.length) { updateEmpty(); return; }
                // Draw largest → smallest so hour 1 sits on top.
                fc.features.sort(function (a, b) {
                    return (b.properties.hour || 0) - (a.properties.hour || 0);
                });
                simLayer.addData(fc);
                if (fc.fetched_at) updateTimestamp(fc.fetched_at);
                if (!hasFitted) fitOnce(simLayer);
                updateEmpty();
                hideAllIsochrones();
                startAnimation();
            }).fail(function () { /* silent */ });
        }

        var lastTs = '';
        function updateTimestamp(iso) {
            var hhmm = fmtHHMM(iso);
            if (!hhmm || hhmm === lastTs) return;
            lastTs = hhmm;
            var t = (window.trans && window.trans.sat) || {};
            var prefix = t.lastUpdate || 'Satélite';
            $ts.text(prefix + ': ' + hhmm);
        }

        function updateEmpty() {
            var isEmpty = (perimLayer.getLayers().length === 0) && (simLayer.getLayers().length === 0);
            if (isEmpty) { $empty.removeClass('d-none'); $ts.text(''); }
            else $empty.addClass('d-none');
        }

        var poll = startPoll(refresh, intervalMs);

        // Pause animation while the tab is hidden — restart on refocus.
        document.addEventListener('visibilitychange', function () {
            if (document.hidden) stopAnimation();
            else if (simLayer.getLayers().length) startAnimation();
        });

        return {
            map: map,
            perimeter: perimLayer,
            simulation: simLayer,
            stop: function () { poll.stop(); stopAnimation(); }
        };
    }

    window.FogosSat = { installMain: installMain, installDetail: installDetail };
})();
