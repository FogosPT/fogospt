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

    function extractLatLng(ev) {
        // GeoJSON Feature
        if (ev && ev.geometry && Array.isArray(ev.geometry.coordinates)) {
            var g = ev.geometry;
            if (g.type === 'Point') return [g.coordinates[1], g.coordinates[0]];
            // Polygon / MultiPolygon: take first coord of first ring
            if (g.type === 'Polygon' && Array.isArray(g.coordinates[0]) && Array.isArray(g.coordinates[0][0])) {
                return [g.coordinates[0][0][1], g.coordinates[0][0][0]];
            }
            if (g.type === 'MultiPolygon' && Array.isArray(g.coordinates[0]) && Array.isArray(g.coordinates[0][0]) && Array.isArray(g.coordinates[0][0][0])) {
                return [g.coordinates[0][0][0][1], g.coordinates[0][0][0][0]];
            }
        }
        // Common direct shapes
        var c = ev && (ev.centroid || (ev.properties && ev.properties.centroid));
        if (c) {
            if (Array.isArray(c.coordinates)) return [c.coordinates[1], c.coordinates[0]];
            if (Array.isArray(c) && c.length >= 2) return [c[1], c[0]]; // [lng, lat]
            if (typeof c === 'object') {
                var la = c.lat != null ? c.lat : (c.latitude  != null ? c.latitude  : null);
                var ln = c.lng != null ? c.lng : (c.lon != null ? c.lon : (c.longitude != null ? c.longitude : null));
                if (la != null && ln != null) return [la, ln];
            }
            if (typeof c === 'string') {
                // "POINT(lng lat)" or "lat,lng"
                var m = c.match(/POINT\s*\(\s*(-?\d+(?:\.\d+)?)\s+(-?\d+(?:\.\d+)?)\s*\)/i);
                if (m) return [parseFloat(m[2]), parseFloat(m[1])];
                var p = c.split(/[,\s]+/).map(parseFloat);
                if (p.length === 2 && !isNaN(p[0]) && !isNaN(p[1])) return [p[0], p[1]];
            }
        }
        var props = (ev && ev.properties) || ev || {};
        var la2 = props.latitude != null ? props.latitude : (props.lat != null ? props.lat : null);
        var ln2 = props.longitude != null ? props.longitude : (props.lng != null ? props.lng : (props.lon != null ? props.lon : null));
        if (la2 != null && ln2 != null) return [la2, ln2];
        return null;
    }

    function getEventId(ev) {
        var props = (ev && ev.properties) ? ev.properties : ev;
        if (!props) return null;
        return props.id != null ? props.id : props.event_id;
    }

    function eventMarker(ev, onClickId) {
        var coords = extractLatLng(ev);
        if (!coords) return null;
        var lat = coords[0], lng = coords[1];
        var src = (ev && ev.properties) ? ev.properties : ev;
        var active = src.is_active === true;
        var marker = L.circleMarker([lat, lng], {
            radius: 7,
            color: active ? '#d33' : '#666',
            weight: 2,
            fillColor: active ? '#f55' : '#bbb',
            fillOpacity: 0.7
        });
        marker.bindPopup(eventPopupHTML(src), { maxWidth: 320 });
        var eid = getEventId(ev);
        if (eid != null && typeof onClickId === 'function') {
            marker.on('click', function () { onClickId(eid); });
        }
        return marker;
    }

    function setup(map) {
        var detectionsLayer  = L.layerGroup();
        var delineationsLayer = L.featureGroup();
        // Footprint of the event the user clicked. One polygon at a time —
        // cleared and replaced on each click, and on every refresh.
        var selectedLayer    = L.featureGroup();

        detectionsLayer.addTo(map);
        delineationsLayer.addTo(map);
        selectedLayer.addTo(map);

        function loadEventPolygon(eventId) {
            setStatus('A carregar evento #' + eventId + '…');
            return fetchJSON('/gaia/v1/events/' + encodeURIComponent(eventId))
                .then(function (data) {
                    selectedLayer.clearLayers();
                    if (!data) return;
                    var feature;
                    if (data.type === 'Feature' && data.geometry) {
                        feature = data;
                    } else if (data.geometry) {
                        feature = { type: 'Feature', geometry: data.geometry, properties: data };
                    } else if (data.type === 'FeatureCollection') {
                        feature = data;
                    } else {
                        return;
                    }
                    var gj = L.geoJSON(feature, {
                        style: { color: '#d33', weight: 2, fillColor: '#f55', fillOpacity: 0.25 }
                    });
                    gj.addTo(selectedLayer);
                    try {
                        var b = gj.getBounds();
                        if (b.isValid()) map.fitBounds(b, { padding: [40, 40], maxZoom: 13 });
                    } catch (e) {}
                    setStatus('Evento #' + eventId + ' carregado');
                })
                .catch(function (err) {
                    console.warn('[gaia] event detail fetch failed', err);
                    setStatus('Erro ao carregar evento');
                });
        }

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
                '<label style="display:block"><input type="checkbox" id="gaia-toggle-inactive"> Incluir inactivos</label>' +
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
            if (e.target && e.target.id === 'gaia-toggle-inactive') {
                refresh();
            }
        });

        function includeInactive() {
            var el = document.getElementById('gaia-toggle-inactive');
            return !!(el && el.checked);
        }

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
            if (!includeInactive()) {
                params.is_active = 'true';
            }

            fetchJSON('/gaia/v1/events?' + qs(params))
                .then(function (data) {
                    detectionsLayer.clearLayers();
                    delineationsLayer.clearLayers();
                    // selectedLayer is user-driven (replaced on next click),
                    // so we don't clear it on bbox refresh.

                    var events = Array.isArray(data) ? data
                        : (data && Array.isArray(data.features) ? data.features
                        : (data && Array.isArray(data.events)   ? data.events
                        : (data && Array.isArray(data.items)    ? data.items
                        : (data && Array.isArray(data.data)     ? data.data : []))));

                    if (events[0]) console.log('[gaia] sample event:', events[0]);

                    var count = 0, skipped = 0;
                    events.forEach(function (ev) {
                        var m = eventMarker(ev, loadEventPolygon);
                        if (m) {
                            m.addTo(detectionsLayer);
                            count++;
                        } else {
                            skipped++;
                        }
                        var props = (ev && ev.properties) ? ev.properties : ev;
                        var eid = props && (props.id != null ? props.id : props.event_id);
                        if (props && props.has_delineation && eid != null) loadDelineation(eid);
                    });

                    setStatus(count + ' eventos' + (skipped ? ' (' + skipped + ' sem coords)' : ''));
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
