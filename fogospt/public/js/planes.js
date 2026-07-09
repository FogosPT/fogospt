/**
 * Aerial firefighting assets layer (DECIR planes & helicopters).
 *
 * Source: https://api.fogos.pt/v2/planes/recent?hours=6
 * Refresh: every 60s on the client (backend caches 60s and polls FR24 every
 * 3 min, so anything more aggressive is wasted). The backend only ingests
 * between sunrise+1h and sunset-1h Lisbon time and when there are active
 * aerial fires, so empty payloads are expected outside that window.
 *
 * Renders one rotated plane marker per aircraft (at the latest known
 * position) plus a polyline for the full track. Aircraft whose last
 * position is older than 10 min are drawn dimmed and grey.
 */
(function () {
    var ENDPOINT = 'https://api.fogos.pt/v2/planes/recent?hours=6';
    var REFRESH_MS = 60000;
    var STALE_MIN = 10;
    var COLOR_ACTIVE = '#ff512f';
    var COLOR_STALE  = '#9aa0a6';
    var ICON_SIZE = 40;
    var ATTRIBUTION = 'Aeronaves: Powered by '
        + '<a href="https://www.flightradar24.com" target="_blank" rel="noopener">Flightradar24</a>'
        + ', complementado por '
        + '<a href="https://airplanes.live" target="_blank" rel="noopener">airplanes.live</a>'
        + ' e <a href="https://adsb.fi" target="_blank" rel="noopener">adsb.fi</a>';

    function tr(key, fallback) {
        var t = window.trans && window.trans.planes;
        return (t && t[key]) || fallback;
    }

    function fmtTime(iso) {
        if (!iso) return '';
        if (typeof moment !== 'undefined') {
            return moment.utc(iso).local().format('DD-MM-YYYY HH:mm');
        }
        return iso;
    }

    function ageMinutes(iso) {
        if (!iso) return Infinity;
        var t = new Date(iso).getTime();
        if (isNaN(t)) return Infinity;
        return (Date.now() - t) / 60000;
    }

    // Deterministic per-aircraft hue so each track keeps the same colour
    // across refreshes. Fixed saturation/lightness keeps every colour
    // legible against both light and dark map tiles. Cascades icao ->
    // registration -> name because a big chunk of the fleet still comes
    // through with icao=null and would otherwise collapse to a single hue.
    function trackColor(plane) {
        var s = String(plane.icao || plane.registration || plane.name || '');
        var h = 0;
        for (var i = 0; i < s.length; i++) {
            h = ((h << 5) - h + s.charCodeAt(i)) | 0;
        }
        return 'hsl(' + (Math.abs(h) % 360) + ', 72%, 42%)';
    }

    // Top-down silhouettes, nose at the top (north). Rotating the wrapper by
    // `track` degrees (0 = north, clockwise) gives a heading-correct icon.
    function planeSvg(color) {
        return ''
            + '<svg viewBox="0 0 24 24" width="' + ICON_SIZE + '" height="' + ICON_SIZE + '" aria-hidden="true">'
            +   '<path fill="' + color + '" stroke="#ffffff" stroke-width="0.9" stroke-linejoin="round"'
            +     ' d="M12 1.5 L13.3 9.6 L22 12 L22 14 L13.3 12.8 L13 19 L15.2 20.3 L15.2 21.6 L12 20.5 L8.8 21.6 L8.8 20.3 L11 19 L10.7 12.8 L2 14 L2 12 L10.7 9.6 Z"/>'
            + '</svg>';
    }

    function helicopterSvg(color) {
        // Cabin (ellipse) + tail boom pointing south + tail rotor, with a
        // faint rotor disc and rotor-blade cross to read as a helicopter
        // at small sizes even before rotation.
        return ''
            + '<svg viewBox="0 0 24 24" width="' + ICON_SIZE + '" height="' + ICON_SIZE + '" aria-hidden="true">'
            +   '<circle cx="12" cy="10" r="10.5" fill="' + color + '" opacity="0.15"/>'
            +   '<g stroke="' + color + '" stroke-width="1.4" stroke-linecap="round" opacity="0.85">'
            +     '<line x1="2" y1="10" x2="22" y2="10"/>'
            +     '<line x1="12" y1="0" x2="12" y2="20"/>'
            +   '</g>'
            +   '<ellipse cx="12" cy="10" rx="2.6" ry="4.2" fill="' + color + '" stroke="#ffffff" stroke-width="0.6"/>'
            +   '<path fill="' + color + '" stroke="#ffffff" stroke-width="0.4" stroke-linejoin="round"'
            +     ' d="M10.8 13.4 L13.2 13.4 L12.7 22 L11.3 22 Z"/>'
            +   '<line x1="10.5" y1="22.5" x2="13.5" y2="22.5" stroke="' + color + '" stroke-width="1.2" stroke-linecap="round"/>'
            + '</svg>';
    }

    // Backend authoritative field is `plane.kind` ("airplane"|"helicopter"),
    // but until it ships everywhere we fall back to a heuristic on the free-
    // form `aircraft_type` / DECIR callsign. Covers the DECIR fleet: rotor
    // types (AS3xx/AS5xx, EC13x/EC14x/EC22x, KA-32, Bell 212/412, MI-8,
    // Sikorsky S-6x) plus the HOTEL / HELI / H0-H9 callsign prefixes.
    function inferKind(plane) {
        var explicit = (plane && plane.kind || '').toString().toLowerCase();
        if (explicit === 'helicopter' || explicit === 'airplane') return explicit;

        var t = (plane && plane.aircraft_type || '').toString().toUpperCase().replace(/[\s-]/g, '');
        var n = (plane && plane.name || '').toString().toUpperCase();

        if (/^(AS3|AS5|EC1|EC2|KA3|B21|B41|BELL|MI8|S6|H500|R44|R66|EH10|AW1|H1|H2)/.test(t)) return 'helicopter';
        if (/HELI|HELIBOMBEIRO/.test(t) || /HELI|HELIBOMBEIRO/.test(n)) return 'helicopter';
        if (/^HOTEL|^HELI|^H\d/.test(n)) return 'helicopter';

        return 'airplane';
    }

    function iconHtml(kind, track, stale) {
        var rotation = (typeof track === 'number' && !isNaN(track)) ? track : 0;
        var color = stale ? COLOR_STALE : COLOR_ACTIVE;
        var svg = (kind === 'helicopter') ? helicopterSvg(color) : planeSvg(color);
        var cls = (kind === 'helicopter') ? 'fogos-heli-marker' : 'fogos-plane-marker';
        return ''
            + '<div class="' + cls + '" style="'
            +   'width:' + ICON_SIZE + 'px;height:' + ICON_SIZE + 'px;'
            +   'display:flex;align-items:center;justify-content:center;'
            +   'transform:rotate(' + rotation + 'deg);'
            +   'opacity:' + (stale ? 0.55 : 1) + ';'
            +   'filter:drop-shadow(0 1px 2px rgba(0,0,0,0.45));'
            + '">' + svg + '</div>';
    }

    function buildPopup(plane, last) {
        var name = plane.name || plane.registration || plane.icao;
        var line2 = [];
        if (plane.registration) line2.push(plane.registration);
        if (plane.aircraft_type) line2.push(plane.aircraft_type);

        var parts = ['<strong>' + name + '</strong>'];
        if (line2.length) parts.push(line2.join(' · '));
        if (plane.operator) parts.push(plane.operator);
        parts.push('');
        if (typeof last.altitude === 'number') {
            parts.push(tr('altitude', 'Altitude') + ': ' + last.altitude + ' ft');
        }
        if (typeof last.ground_speed === 'number') {
            parts.push(tr('speed', 'Velocidade') + ': ' + last.ground_speed + ' kt');
        }
        if (typeof last.track === 'number') {
            parts.push(tr('heading', 'Direção') + ': ' + last.track + '°');
        }
        if (last.on_ground) {
            parts.push('<em>' + tr('onGround', 'No solo') + '</em>');
        }
        if (last.sampled_at) {
            parts.push(tr('lastSeen', 'Última posição') + ': ' + fmtTime(last.sampled_at));
        }
        parts.push('');
        parts.push('<small><em>' + tr('source', 'Dados via subscrição da API do FlightRadar24') + '</em></small>');
        return parts.join('<br>');
    }

    window.createPlanesLayer = function (mymap, opts) {
        opts = opts || {};
        // showAllTracks=true → draw every aircraft's polyline (default legacy
        // behaviour). false → only draw the track for the aircraft whose
        // popup is currently open (single-selection, driven by popup events).
        var showAllTracks = !!opts.showAllTracks;

        var group = L.layerGroup();
        var markers = {};      // icao -> L.marker
        var tracks  = {};      // icao -> L.polyline (currently drawn)
        var cache   = {};      // icao -> { pts, color, stale } cached path
        var pollTimer = null;
        var inFlight = false;

        function clearAll() {
            for (var k in markers) {
                group.removeLayer(markers[k]);
                delete markers[k];
            }
            for (var k2 in tracks) {
                group.removeLayer(tracks[k2]);
                delete tracks[k2];
            }
            cache = {};
        }

        function drawTrack(icao) {
            var c = cache[icao];
            if (!c || !c.pts || c.pts.length < 2) return;
            if (tracks[icao]) {
                tracks[icao].setLatLngs(c.pts);
                tracks[icao].setStyle({ color: c.color, opacity: c.stale ? 0.35 : 0.75 });
                return;
            }
            tracks[icao] = L.polyline(c.pts, {
                color: c.color,
                weight: 2.5,
                opacity: c.stale ? 0.35 : 0.75,
                dashArray: '4 4'
            }).addTo(group);
        }

        function removeTrack(icao) {
            if (!tracks[icao]) return;
            group.removeLayer(tracks[icao]);
            delete tracks[icao];
        }

        function refresh() {
            if (inFlight) return;
            inFlight = true;
            fetch(ENDPOINT, { credentials: 'omit' })
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (body) {
                    inFlight = false;
                    if (!body || !body.success || !Array.isArray(body.data)) return;
                    var seen = {};
                    body.data.forEach(function (plane) {
                        if (!plane || !plane.positions || !plane.positions.length) return;
                        seen[plane.icao] = true;

                        var positions = plane.positions;
                        var last = positions[positions.length - 1];
                        var stale = ageMinutes(last.created) > STALE_MIN;
                        var pts = positions.map(function (p) { return [p.lat, p.lon]; });

                        cache[plane.icao] = {
                            pts: pts,
                            color: trackColor(plane),
                            stale: stale
                        };

                        // Track polyline (re-set on each refresh — payload is at
                        // most ~6h of points, cheap to rewrite).
                        if (showAllTracks) {
                            if (pts.length > 1) drawTrack(plane.icao);
                        } else if (tracks[plane.icao]) {
                            // Currently visible (popup open) — keep it fresh.
                            drawTrack(plane.icao);
                        }

                        var kind = inferKind(plane);
                        var icon = L.divIcon({
                            className: 'fogos-plane-icon',
                            html: iconHtml(kind, last.track, stale),
                            iconSize: [ICON_SIZE, ICON_SIZE],
                            iconAnchor: [ICON_SIZE / 2, ICON_SIZE / 2]
                        });

                        if (markers[plane.icao]) {
                            markers[plane.icao].setLatLng([last.lat, last.lon]);
                            markers[plane.icao].setIcon(icon);
                            markers[plane.icao].setPopupContent(buildPopup(plane, last));
                        } else {
                            var icao = plane.icao;
                            var m = L.marker([last.lat, last.lon], { icon: icon });
                            m.bindPopup(buildPopup(plane, last));
                            if (!showAllTracks) {
                                // Popup-driven single selection: opening a popup
                                // reveals that aircraft's track; closing hides
                                // it. Leaflet auto-closes any other popup when a
                                // new one opens, giving the "click another to
                                // switch" behaviour for free.
                                m.on('popupopen',  function () { drawTrack(icao); });
                                m.on('popupclose', function () { removeTrack(icao); });
                            }
                            m.addTo(group);
                            markers[icao] = m;
                        }
                    });

                    // Drop aircraft that dropped out of the response (e.g.
                    // window slid past their last position).
                    for (var k in markers) {
                        if (!seen[k]) {
                            group.removeLayer(markers[k]);
                            delete markers[k];
                            removeTrack(k);
                            delete cache[k];
                        }
                    }
                })
                .catch(function () { inFlight = false; });
        }

        group.onAdd = function (map) {
            L.LayerGroup.prototype.onAdd.call(this, map);
            if (map.attributionControl) map.attributionControl.addAttribution(ATTRIBUTION);
            refresh();
            if (!pollTimer) pollTimer = setInterval(refresh, REFRESH_MS);
        };
        group.onRemove = function (map) {
            if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
            if (map.attributionControl) map.attributionControl.removeAttribution(ATTRIBUTION);
            clearAll();
            L.LayerGroup.prototype.onRemove.call(this, map);
        };

        return group;
    };
})();
