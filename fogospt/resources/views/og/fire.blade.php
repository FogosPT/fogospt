@php
    // Standalone 1200x630 card for the headless-Chrome sidecar. No site
    // chrome, no analytics, no shared layout — everything the screenshot
    // needs lives in this file so the render is fast and reproducible.

    $status   = trim((string) ($fire['status'] ?? ''));
    $location = trim((string) ($fire['location'] ?? ''));
    $concelho = trim((string) ($fire['concelho'] ?? ''));
    $distrito = trim((string) ($fire['district'] ?? $fire['distrito'] ?? ''));

    $man     = (int) ($fire['man']     ?? 0);
    $terrain = (int) ($fire['terrain'] ?? 0);
    $aerial  = (int) ($fire['aerial']  ?? 0);

    $lat = isset($fire['lat']) ? (float) $fire['lat'] : 39.5;
    $lng = isset($fire['lng']) ? (float) $fire['lng'] : -8.0;

    // Palette per operational state. Fallback keeps the card usable even if
    // upstream sends a value we don't know yet.
    $statusColors = [
        'Em Curso'              => '#dc2626', // red-600
        'Em Resolução'          => '#ea580c', // orange-600
        'Conclusão'             => '#d97706', // amber-600
        'Vigilância'            => '#ca8a04', // yellow-600
        'Chegada ao TO'         => '#e11d48', // rose-600
        'Despacho'              => '#b45309', // amber-700
        'Despacho de 1º Alerta' => '#b45309',
        'Encerrada'             => '#16a34a', // green-600
        'Falso Alarme'          => '#6b7280', // gray-500
        'Falso Alerta'          => '#6b7280',
    ];
    $statusColor = $statusColors[$status] ?? '#dc2626';
@endphp
<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1200,initial-scale=1">
    <title>Fogos.pt — Cartão OG</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.8.0/leaflet.css" integrity="sha512-hoalWLoI8r4UszCkZ5kL8vayOGVae1oxXe/2A4AO6J9+580uKHDO3JdHb7NzwwzK5xr/Fs0W40kiNHxM9vyTtQ==" crossorigin="anonymous" referrerpolicy="no-referrer">
    <style>
        html, body {
            margin: 0; padding: 0;
            width: 1200px; height: 630px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #0f172a;
            overflow: hidden;
            color: #fff;
        }
        #map {
            position: absolute; inset: 0;
            z-index: 1;
            background: #0f172a;
        }
        .leaflet-control-attribution,
        .leaflet-control-zoom { display: none !important; }

        /* Vignette so the overlays stay readable regardless of the tile luminance */
        .vignette {
            position: absolute; inset: 0;
            z-index: 5;
            background:
                linear-gradient(180deg, rgba(0,0,0,0.55) 0%, rgba(0,0,0,0) 25%, rgba(0,0,0,0) 55%, rgba(0,0,0,0.85) 100%),
                radial-gradient(ellipse at center, rgba(0,0,0,0) 45%, rgba(0,0,0,0.35) 100%);
            pointer-events: none;
        }

        .top {
            position: absolute; top: 32px; left: 40px; right: 40px;
            z-index: 10;
            display: flex; justify-content: space-between; align-items: flex-start;
        }
        .brand {
            display: flex; align-items: center; gap: 14px;
            background: rgba(15, 23, 42, 0.85);
            padding: 14px 22px;
            border-radius: 14px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.35);
        }
        .brand__logo { width: 42px; height: 42px; display: block; }
        .brand__name {
            font-size: 26px; font-weight: 800; letter-spacing: -0.5px;
            color: #fff;
        }
        .status {
            padding: 14px 24px;
            border-radius: 14px;
            font-size: 22px; font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #fff;
            box-shadow: 0 6px 20px rgba(0,0,0,0.4);
            white-space: nowrap;
        }

        .bottom {
            position: absolute; left: 0; right: 0; bottom: 0;
            z-index: 10;
            padding: 60px 48px 40px;
            display: flex; flex-direction: column; gap: 18px;
        }
        .location {
            font-size: 60px; font-weight: 800; line-height: 1.02;
            letter-spacing: -1.8px;
            margin: 0;
            text-shadow: 0 2px 12px rgba(0,0,0,0.6);
        }
        .region {
            font-size: 28px; font-weight: 500;
            margin: 0;
            opacity: 0.92;
            text-shadow: 0 1px 6px rgba(0,0,0,0.6);
        }
        .meios {
            display: flex; gap: 36px; align-items: center;
            margin-top: 8px;
        }
        .meios__item {
            display: flex; align-items: center; gap: 12px;
            font-size: 30px; font-weight: 700;
            background: rgba(15, 23, 42, 0.65);
            padding: 10px 18px;
            border-radius: 12px;
        }
        .meios__icon { width: 34px; height: 34px; display: inline-block; filter: brightness(0) invert(1); }

        .timestamp {
            position: absolute; right: 48px; bottom: 44px;
            z-index: 11;
            font-size: 22px; font-weight: 600;
            background: rgba(15, 23, 42, 0.7);
            padding: 10px 18px;
            border-radius: 10px;
            box-shadow: 0 4px 14px rgba(0,0,0,0.35);
        }
    </style>
</head>
<body>
    <div id="map"></div>
    <div class="vignette"></div>

    <div class="top">
        <div class="brand">
            <img class="brand__logo" src="/img/logo.svg" alt="">
            <span class="brand__name">Fogos.pt</span>
        </div>
        @if($status !== '')
            <div class="status" style="background: {{ $statusColor }};">{{ $status }}</div>
        @endif
    </div>

    <div class="bottom">
        <h1 class="location">{{ $location !== '' ? $location : '—' }}</h1>
        @if($concelho !== '' || $distrito !== '')
            <p class="region">
                {{ $concelho }}{{ $concelho !== '' && $distrito !== '' ? ' · ' : '' }}{{ $distrito }}
            </p>
        @endif
        <div class="meios">
            <span class="meios__item"><img class="meios__icon" src="/img/fireman.svg" alt="">{{ $man }}</span>
            <span class="meios__item"><img class="meios__icon" src="/img/firetruck.svg" alt="">{{ $terrain }}</span>
            <span class="meios__item"><img class="meios__icon" src="/img/plane.svg" alt="">{{ $aerial }}</span>
        </div>
    </div>

    <div class="timestamp">{{ date('H:i') }} · {{ date('d-m-Y') }}</div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.8.0/leaflet.js" integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="/js/vendor/L.KLM.js"></script>
    <script>
        (function () {
            var lat = {{ $lat }};
            var lng = {{ $lng }};

            var map = L.map('map', {
                zoomControl: false,
                attributionControl: false,
                zoomAnimation: false,
                fadeAnimation: false,
                markerZoomAnimation: false,
                center: [lat, lng],
                zoom: 12
            });

            var osm = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                crossOrigin: true
            });
            osm.addTo(map);

            var fireIcon = L.divIcon({
                className: 'og-fire-marker',
                html: '<div style="width:28px;height:28px;border-radius:50%;background:rgba(240,0,51,0.85);border:3px solid #fff;box-shadow:0 0 0 8px rgba(240,0,51,0.35), 0 2px 8px rgba(0,0,0,0.5);"></div>',
                iconSize: [28, 28],
                iconAnchor: [14, 14]
            });
            L.marker([lat, lng], { icon: fireIcon }).addTo(map);

            var bounds = null;
            @if(!empty($kml))
                try {
                    var kmltext = @json($kml);
                    var kmlDoc = new DOMParser().parseFromString(kmltext, 'text/xml');
                    var track = new L.KML(kmlDoc);
                    map.addLayer(track);
                    var b = track.getBounds();
                    if (b && b.isValid()) bounds = b;
                } catch (e) { console.warn('KML parse failed', e); }
            @endif
            @if(!empty($kmlVost))
                try {
                    var kmlVostText = @json($kmlVost);
                    var kmlVostDoc = new DOMParser().parseFromString(kmlVostText, 'text/xml');
                    var trackVost = new L.KML(kmlVostDoc);
                    map.addLayer(trackVost);
                    var bV = trackVost.getBounds();
                    if (bV && bV.isValid()) {
                        bounds = bounds ? bounds.extend(bV) : bV;
                    }
                } catch (e) { console.warn('KML VOST parse failed', e); }
            @endif

            if (bounds) {
                // Leave headroom for the bottom overlay so the perimeter is
                // not covered by the location text.
                map.fitBounds(bounds, { padding: [60, 60], maxZoom: 14 });
            }

            // Signal to the sidecar when to capture. `load` fires once the
            // currently-in-view tiles have all decoded; give it a small
            // safety buffer to let KML repaint on top.
            var settled = false;
            function settle(delay) {
                if (settled) return;
                settled = true;
                setTimeout(function () { window.__ogReady = true; }, delay);
            }
            osm.on('load', function () { settle(300); });

            // Belt-and-braces: if OSM tiles hang, still capture after 5s so
            // the sidecar never times out on a slow tile CDN.
            setTimeout(function () { settle(0); }, 5000);
        })();
    </script>
</body>
</html>
