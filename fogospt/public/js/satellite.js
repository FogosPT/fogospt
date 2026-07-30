/**
 * Satellite overlays — fire perimeters and 6h propagation isochrones,
 * fed by the api.fogos.pt v2 MTG FRP endpoints.
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
    var API = 'https://api.fogos.pt';

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

    function bindIncidentPopup(feature, layer) {
        var p = feature && feature.properties;
        var inc = p && p.fogospt_incident;
        if (!inc) return;
        var line = [inc.concelho, inc.freguesia].filter(function (v) { return !!v; }).map(escapeHtml).join(' — ');
        var url = inc.url || '/fogo/' + encodeURIComponent(inc.id);
        var html = ''
            + '<div style="min-width:160px">'
            + '<b>' + escapeHtml(inc.natureza || 'Incêndio') + '</b>'
            + (line ? '<br>' + line : '')
            + (inc.status ? '<br><small>' + escapeHtml(inc.status) + '</small>' : '')
            + '<br><a href="' + escapeHtml(url) + '">' + escapeHtml((window.trans && window.trans.fire && window.trans.fire.moreDetails) || 'Detalhes') + '</a>'
            + '</div>';
        layer.bindPopup(html);
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
        var conc = {}, freg = {}, ord = [];
        features.forEach(function (f) {
            var inc = f && f.properties && f.properties.fogospt_incident;
            if (!inc) return;
            if (inc.concelho && !conc[inc.concelho]) { conc[inc.concelho] = true; ord.push(inc.concelho); }
            if (inc.freguesia) {
                var key = (inc.concelho || '') + '|' + inc.freguesia;
                if (!freg[key]) freg[key] = inc.freguesia;
            }
        });
        var concList = ord.map(escapeHtml).join(', ');
        var fregList = Object.keys(freg).map(function (k) { return escapeHtml(freg[k]); }).join(', ');
        var none = escapeHtml(t.none || '—');
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

        var perimLayer = L.geoJSON(null, { style: perimeterStyle, onEachFeature: bindIncidentPopup });

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
            $.ajax({ url: API + '/v2/fire/perimeters', method: 'GET', cache: false })
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

        var perimLayer = L.geoJSON(null, { style: perimeterStyle, onEachFeature: bindIncidentPopup }).addTo(map);
        var simLayer = L.geoJSON(null, { style: styleForHour, onEachFeature: bindIsochroneTooltip }).addTo(map);

        var $ts = $('.js-sat-ts');
        var $empty = $('.js-sat-empty');
        var $areas = $('.js-sat-areas');
        var hasFitted = false;

        function fitOnce(layer) {
            if (hasFitted) return;
            try {
                var b = layer.getBounds();
                if (b && b.isValid()) { map.fitBounds(b.pad(0.2)); hasFitted = true; }
            } catch (e) {}
        }

        function refresh() {
            var pReq = $.ajax({ url: API + '/v2/incidents/' + encodeURIComponent(fireId) + '/perimeter', method: 'GET', cache: false });
            var sReq = $.ajax({ url: API + '/v2/incidents/' + encodeURIComponent(fireId) + '/simulation', method: 'GET', cache: false });

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
        return { map: map, perimeter: perimLayer, simulation: simLayer, stop: poll.stop };
    }

    window.FogosSat = { installMain: installMain, installDetail: installDetail };
})();
