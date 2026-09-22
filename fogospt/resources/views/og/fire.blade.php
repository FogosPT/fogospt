@php
    // Standalone 1200x630 data-first card for the headless-Chrome sidecar.
    // No map, no tiles, no async loads — the screenshot is stable within
    // one frame of DOMContentLoaded. This baseline earns us a working
    // pipeline end-to-end; a map variant can come back as an iteration
    // once we know the sidecar path is solid.

    $status   = trim((string) ($fire['status'] ?? ''));
    $location = trim((string) ($fire['location'] ?? ''));
    $concelho = trim((string) ($fire['concelho'] ?? ''));
    $distrito = trim((string) ($fire['district'] ?? $fire['distrito'] ?? ''));

    $man     = (int) ($fire['man']     ?? 0);
    $terrain = (int) ($fire['terrain'] ?? 0);
    $aerial  = (int) ($fire['aerial']  ?? 0);

    // Upstream sometimes sends -1 to mean "not confirmed yet" — surface
    // that explicitly instead of "-1 operacionais".
    $unconfirmed = ($man < 0 || $terrain < 0 || $aerial < 0);

    $statusColors = [
        'Em Curso'              => '#dc2626',
        'Em Resolução'          => '#ea580c',
        'Conclusão'             => '#d97706',
        'Vigilância'            => '#ca8a04',
        'Chegada ao TO'         => '#e11d48',
        'Despacho'              => '#b45309',
        'Despacho de 1º Alerta' => '#b45309',
        'Encerrada'             => '#16a34a',
        'Falso Alarme'          => '#6b7280',
        'Falso Alerta'          => '#6b7280',
    ];
    $statusColor = $statusColors[$status] ?? '#dc2626';

    $region = trim(implode(' · ', array_filter([$concelho, $distrito])));
@endphp
<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=1200,initial-scale=1">
    <title>Fogos.pt — Cartão</title>
    <style>
        * { box-sizing: border-box; }
        html, body {
            margin: 0; padding: 0;
            width: 1200px; height: 630px;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            color: #fff;
            background: #0f172a;
            overflow: hidden;
        }

        .card {
            position: relative;
            width: 1200px; height: 630px;
            background:
                radial-gradient(ellipse at top left, rgba(255,255,255,0.06) 0%, transparent 55%),
                linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            padding: 60px 64px;
            display: flex; flex-direction: column;
            gap: 32px;
        }

        /* Full-width color strip at the top — reads instantly in a feed
           even at thumbnail size before the reader parses any text. */
        .card::before {
            content: "";
            position: absolute; top: 0; left: 0; right: 0;
            height: 10px;
            background: {{ $statusColor }};
        }

        .top {
            display: flex; justify-content: space-between; align-items: center;
        }
        .brand {
            display: flex; align-items: center; gap: 14px;
        }
        .brand__logo {
            width: 48px; height: 48px;
            display: flex; align-items: center; justify-content: center;
            background: {{ $statusColor }};
            border-radius: 12px;
            font-size: 30px; line-height: 1;
        }
        .brand__name {
            font-size: 30px; font-weight: 800; letter-spacing: -0.5px;
        }

        .status {
            padding: 12px 24px;
            border-radius: 999px;
            font-size: 22px; font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.8px;
            color: #fff;
            background: {{ $statusColor }};
            box-shadow: 0 8px 24px rgba(0,0,0,0.35);
        }

        .location {
            font-size: 78px; font-weight: 800; line-height: 1.02;
            letter-spacing: -2px;
            margin: 0;
            text-wrap: balance;
        }
        .region {
            font-size: 34px; font-weight: 500;
            margin: 0;
            opacity: 0.75;
        }

        .meios {
            margin-top: auto;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .stat {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 18px;
            padding: 24px 28px;
            display: flex; flex-direction: column;
            gap: 4px;
        }
        .stat__label {
            font-size: 20px; font-weight: 600;
            opacity: 0.7;
            display: flex; align-items: center; gap: 10px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
        }
        .stat__icon {
            width: 26px; height: 26px;
            filter: brightness(0) invert(1);
            opacity: 0.85;
        }
        .stat__value {
            font-size: 58px; font-weight: 800;
            letter-spacing: -1.5px;
            line-height: 1;
        }
        .stat__value--unconfirmed {
            font-size: 26px;
            font-weight: 700;
            opacity: 0.6;
            padding-top: 20px;
            letter-spacing: 0;
        }

        .footer {
            display: flex; justify-content: space-between; align-items: center;
            font-size: 22px;
            opacity: 0.55;
        }
        .footer__url {
            font-weight: 600;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="top">
            <div class="brand">
                <div class="brand__logo">🔥</div>
                <span class="brand__name">Fogos.pt</span>
            </div>
            @if($status !== '')
                <div class="status">{{ $status }}</div>
            @endif
        </div>

        <div>
            <h1 class="location">{{ $location !== '' ? $location : '—' }}</h1>
            @if($region !== '')
                <p class="region">{{ $region }}</p>
            @endif
        </div>

        <div class="meios">
            <div class="stat">
                <div class="stat__label">
                    <img class="stat__icon" src="/img/fireman.svg" alt="">
                    Operacionais
                </div>
                @if($man < 0)
                    <div class="stat__value stat__value--unconfirmed">por confirmar</div>
                @else
                    <div class="stat__value">{{ $man }}</div>
                @endif
            </div>
            <div class="stat">
                <div class="stat__label">
                    <img class="stat__icon" src="/img/firetruck.svg" alt="">
                    Terrestres
                </div>
                @if($terrain < 0)
                    <div class="stat__value stat__value--unconfirmed">por confirmar</div>
                @else
                    <div class="stat__value">{{ $terrain }}</div>
                @endif
            </div>
            <div class="stat">
                <div class="stat__label">
                    <img class="stat__icon" src="/img/plane.svg" alt="">
                    Aéreos
                </div>
                @if($aerial < 0)
                    <div class="stat__value stat__value--unconfirmed">por confirmar</div>
                @else
                    <div class="stat__value">{{ $aerial }}</div>
                @endif
            </div>
        </div>

        <div class="footer">
            <span>{{ date('H:i') }} · {{ date('d-m-Y') }}</span>
            <span class="footer__url">fogos.pt</span>
        </div>
    </div>

    <script>
        // No async work in this build — flip the ready flag as soon as
        // the layout has been painted. requestAnimationFrame ensures we
        // don't screenshot before styles apply on very fast machines.
        requestAnimationFrame(function () {
            requestAnimationFrame(function () { window.__ogReady = true; });
        });
    </script>
</body>
</html>
