/*
 * Gaia Wildfires overlay — preview build.
 * Loaded only when the blade view receives the `gaia` flag (route /pt/gaia).
 * Adds two Leaflet layer groups (satellite detections + burn perimeters) on top of the
 * existing fogos.pt map, fed by the proxied /gaia/v1/* endpoints.
 */
(function () {
    'use strict';

    var POLL_INTERVAL_MS = 200;
    var POLL_TIMEOUT_MS  = 15000;
    var REFETCH_DEBOUNCE = 600;
    var DEFAULT_BBOX_PT  = {
        'NorthEastBoundary.Latitude':  42.2,
        'NorthEastBoundary.Longitude': -6.1,
        'SouthWestBoundary.Latitude':  36.9,
        'SouthWestBoundary.Longitude': -9.6
    };

    function waitForMap(cb) {
        var t0 = Date.now();
        (function poll() {
            var panel = window.fogosPanel;
            var map   = panel && panel._map;
            if (!map && window.fogosLayers) {
                for (var i = 0; i < window.fogosLayers.length; i++) {
                    if (window.fogosLayers[i] && window.fogosLayers[i]._map) {
                        map = window.fogosLayers[i]._map;
                        break;
                    }
                }
            }
            if (map) { cb(map); return; }
            if (Date.now() - t0 > POLL_TIMEOUT_MS) {
                console.warn('[gaia] map handle not found, giving up');
                return;
            }
            setTimeout(poll, POLL_INTERVAL_MS);
        })();
    }

    function bboxFromMap(map) {
        var b = map.getBounds();
        return {
            'NorthEastBoundary.Latitude':  b.getNorthEast().lat,
            'NorthEastBoundary.Longitude': b.getNorthEast().lng,
            'SouthWestBoundary.Latitude':  b.getSouthWest().lat,
            'SouthWestBoundary.Longitude': b.getSouthWest().lng
        };
    }

    function bboxIntersectsPT(map) {
        var b = map.getBounds();
        return b.getNorth() >= DEFAULT_BBOX_PT['SouthWestBoundary.Latitude']
            && b.getSouth() <= DEFAULT_BBOX_PT['NorthEastBoundary.Latitude']
            && b.getEast()  >= DEFAULT_BBOX_PT['SouthWestBoundary.Longitude']
            && b.getWest()  <= DEFAULT_BBOX_PT['NorthEastBoundary.Longitude'];
    }

    function qs(params) {
        return Object.keys(params)
            .filter(function (k) { return params[k] !== undefined && params[k] !== null; })
            .map(function (k) { return encodeURIComponent(k) + '=' + encodeURIComponent(params[k]); })
            .join('&');
    }

    function fetchJSON(url) {
        return fetch(url, { credentials: 'same-origin' }).then(function (r) {
            if (!r.ok) throw new Error('HTTP ' + r.status);
            return r.json();
        });
    }

    function fmtDate(s) {
        if (!s) return '';
        var d = new Date(s);
        if (isNaN(d.getTime())) return s;
        return d.toLocaleString('pt-PT');
    }

    function eventPopupHTML(ev) {
        var rows = [];
        if (ev.event_name) rows.push('<strong>' + ev.event_name + '</strong>');
        rows.push('<div style="font-size:11px;color:#888">Gaia event #' + ev.id + '</div>');
        if (ev.first_seen) rows.push('Primeiro: ' + fmtDate(ev.first_seen));
        if (ev.last_seen)  rows.push('Último: '   + fmtDate(ev.last_seen));
        if (ev.area_km2 != null) rows.push('Área: ' + (+ev.area_km2).toFixed(2) + ' km²');
        if (ev.detection_count != null) rows.push('Detecções: ' + ev.detection_count);
        if (ev.is_active === true)  rows.push('<span style="color:#c00">Activo</span>');
        if (ev.is_active === false) rows.push('<span style="color:#888">Inactivo</span>');
        if (ev.summary && ev.summary.text) {
            rows.push('<hr style="margin:4px 0">');
            rows.push('<div style="max-width:280px;font-size:12px">' + ev.summary.text + '</div>');
        }
        return rows.join('<br>');
    }

    function eventMarker(ev) {
        var lat, lng;
        if (ev.centroid && Array.isArray(ev.centroid.coordinates)) {
            lng = ev.centroid.coordinates[0];
            lat = ev.centroid.coordinates[1];
        } else {
            return null;
        }
        var active = ev.is_active === true;
        var marker = L.circleMarker([lat, lng], {
            radius: 7,
            color: active ? '#d33' : '#666',
            weight: 2,
            fillColor: active ? '#f55' : '#bbb',
            fillOpacity: 0.7
        });
        marker.bindPopup(eventPopupHTML(ev), { maxWidth: 320 });
        return marker;
    }

    function setup(map) {
        var detectionsLayer  = L.layerGroup();
        var delineationsLayer = L.featureGroup();

        detectionsLayer.addTo(map);
        delineationsLayer.addTo(map);

        var control = L.control({ position: 'topright' });
        control.onAdd = function () {
            var div = L.DomUtil.create('div', 'leaflet-bar');
            div.style.background = '#fff';
            div.style.padding = '6px 8px';
            div.style.font = '12px/1.3 sans-serif';
            div.innerHTML =
                '<div style="font-weight:bold;margin-bottom:4px">Gaia (preview)</div>' +
                '<label style="display:block"><input type="checkbox" id="gaia-toggle-detections" checked> Eventos satélite</label>' +
                '<label style="display:block"><input type="checkbox" id="gaia-toggle-delineations" checked> Perímetros</label>' +
                '<div id="gaia-status" style="margin-top:4px;color:#888;font-size:11px">A carregar…</div>';
            L.DomEvent.disableClickPropagation(div);
            return div;
        };
        control.addTo(map);

        function setStatus(text) {
            var el = document.getElementById('gaia-status');
            if (el) el.textContent = text;
        }

        document.addEventListener('change', function (e) {
            if (e.target && e.target.id === 'gaia-toggle-detections') {
                if (e.target.checked) detectionsLayer.addTo(map);
                else map.removeLayer(detectionsLayer);
            }
            if (e.target && e.target.id === 'gaia-toggle-delineations') {
                if (e.target.checked) delineationsLayer.addTo(map);
                else map.removeLayer(delineationsLayer);
            }
        });

        var inflight = 0;

        function loadDelineation(eventId) {
            return fetchJSON('/gaia/v1/delineations?event_id=' + encodeURIComponent(eventId))
                .then(function (geo) {
                    if (!geo || !geo.features) return;
                    L.geoJSON(geo, {
                        style: { color: '#a0522d', weight: 2, fillColor: '#d2691e', fillOpacity: 0.25 }
                    }).addTo(delineationsLayer);
                })
                .catch(function () { /* swallow per-event errors */ });
        }

        function refresh() {
            if (!bboxIntersectsPT(map)) {
                setStatus('Fora de Portugal — pan para PT');
                return;
            }

            inflight++;
            setStatus('A carregar…');

            var params = bboxFromMap(map);
            params.limit = 200;

            fetchJSON('/gaia/v1/events?' + qs(params))
                .then(function (data) {
                    detectionsLayer.clearLayers();
                    delineationsLayer.clearLayers();

                    var events = Array.isArray(data) ? data
                        : (data && Array.isArray(data.events) ? data.events
                        : (data && Array.isArray(data.items)  ? data.items : []));

                    var count = 0;
                    events.forEach(function (ev) {
                        var m = eventMarker(ev);
                        if (m) { m.addTo(detectionsLayer); count++; }
                        if (ev.has_delineation) loadDelineation(ev.id);
                    });

                    setStatus(count + ' eventos');
                })
                .catch(function (err) {
                    console.warn('[gaia] events fetch failed', err);
                    setStatus('Erro ao carregar');
                })
                .then(function () { inflight = Math.max(0, inflight - 1); });
        }

        var debTimer = null;
        function scheduleRefresh() {
            if (debTimer) clearTimeout(debTimer);
            debTimer = setTimeout(refresh, REFETCH_DEBOUNCE);
        }

        map.on('moveend zoomend', scheduleRefresh);
        refresh();
    }

    if (!window.fogosGaiaEnabled) return;
    waitForMap(setup);
})();
