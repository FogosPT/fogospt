@php
    // Standalone 1200x630 data-first card. No map, no tiles, no async I/O.
    // Renders in one animation frame so the sidecar can screenshot before
    // Chrome even finishes styling on a fast box.

    $status   = trim((string) ($fire['status'] ?? ''));
    $location = trim((string) ($fire['location'] ?? ''));
    $concelho = trim((string) ($fire['concelho'] ?? ''));
    $distrito = trim((string) ($fire['district'] ?? $fire['distrito'] ?? ''));

    $man     = (int) ($fire['man']     ?? 0);
    $terrain = (int) ($fire['terrain'] ?? 0);
    $aerial  = (int) ($fire['aerial']  ?? 0);

    // Palette lifted from public/css/app.css so the card matches marker
    // colours users already associate with each state on the live map.
    // The "brand" orange is the Em Curso colour — it is the emotional
    // anchor of the site.
    $BRAND  = '#ff6e02';
    $statusPalette = [
        3  => '#b81e1f', // Chegada ao TO
        4  => '#ff6e02', // Em Curso
        5  => '#b81e1f', // Em Resolução
        6  => '#b81e1f', // Conclusão
        7  => '#65c4ed', // Vigilância
        8  => '#8e7e7d',
        9  => '#65c4ed',
        10 => '#6abf59', // Encerrada
        11 => '#bdbdbd', // Falso Alarme
        12 => '#bdbdbd', // Falso Alerta
    ];
    $statusColorByName = [
        'Em Curso'              => $statusPalette[4],
        'Em Resolução'          => $statusPalette[5],
        'Conclusão'             => $statusPalette[6],
        'Vigilância'            => $statusPalette[7],
        'Chegada ao TO'         => $statusPalette[3],
        'Despacho'              => '#b45309',
        'Despacho de 1º Alerta' => '#b45309',
        'Encerrada'             => $statusPalette[10],
        'Falso Alarme'          => $statusPalette[11],
        'Falso Alerta'          => $statusPalette[12],
    ];
    $statusColor = $statusColorByName[$status] ?? $BRAND;

    $region = trim(implode(' · ', array_filter([$concelho, $distrito])));

    // Meios bars: shared scale so proportions read at a glance. Clamp
    // at max=1 so the "no meios" case still renders empty tracks
    // instead of dividing by zero. Unconfirmed (-1) shows a label
    // instead of a bar segment.
    $manUnconfirmed     = $man     < 0;
    $terrainUnconfirmed = $terrain < 0;
    $aerialUnconfirmed  = $aerial  < 0;
    $barMax = max(1, $manUnconfirmed ? 0 : $man, $terrainUnconfirmed ? 0 : $terrain, $aerialUnconfirmed ? 0 : $aerial);
    $manPct     = $manUnconfirmed     ? 0 : min(100, ($man     / $barMax) * 100);
    $terrainPct = $terrainUnconfirmed ? 0 : min(100, ($terrain / $barMax) * 100);
    $aerialPct  = $aerialUnconfirmed  ? 0 : min(100, ($aerial  / $barMax) * 100);

    // Timeline: keep the last 5 entries so labels never overlap. If the
    // upstream sends fewer, we show what we have. History is oldest-first
    // in the source payload; the "current" state sits at the right edge.
    $history = [];
    if (isset($fire['statusHistory']) && is_array($fire['statusHistory'])) {
        $history = array_values(array_filter($fire['statusHistory'], fn($e) => is_array($e)));
    }
    $historyCountTotal = count($history);
    $historyMax = 5;
    $historyOverflow = max(0, $historyCountTotal - $historyMax);
    $history = array_slice($history, -$historyMax);
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
            background: #0a0a0b;
            overflow: hidden;
        }

        .card {
            position: relative;
            width: 1200px; height: 630px;
            background:
                radial-gradient(ellipse at 20% -10%, rgba(255,110,2,0.18) 0%, transparent 55%),
                linear-gradient(135deg, #0a0a0b 0%, #1a1210 100%);
            padding: 48px 56px 40px;
            display: flex; flex-direction: column;
            gap: 22px;
        }
        .card::before {
            content: "";
            position: absolute; top: 0; left: 0; right: 0;
            height: 8px;
            background: {{ $BRAND }};
        }

        /* ── Header ──────────────────────────────────────────────────── */

        .top {
            display: flex; justify-content: space-between; align-items: center;
        }
        .brand {
            display: flex; align-items: center; gap: 14px;
        }
        .brand__flame {
            width: 46px; height: 46px;
            display: flex; align-items: center; justify-content: center;
            background: {{ $BRAND }};
            border-radius: 12px;
            font-size: 28px; line-height: 1;
            box-shadow: 0 6px 18px rgba(255,110,2,0.35);
        }
        .brand__name {
            font-size: 30px; font-weight: 800; letter-spacing: -0.5px;
        }
        .brand__name em {
            font-style: normal;
            color: {{ $BRAND }};
        }

        .status {
            padding: 12px 22px;
            border-radius: 999px;
            font-size: 20px; font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 0.7px;
            color: #fff;
            background: {{ $statusColor }};
            box-shadow: 0 8px 22px rgba(0,0,0,0.4);
        }

        /* ── Location ────────────────────────────────────────────────── */

        .location {
            font-size: 62px; font-weight: 800; line-height: 1.02;
            letter-spacing: -1.6px;
            margin: 0;
        }
        .region {
            font-size: 26px; font-weight: 500;
            margin: 4px 0 0;
            opacity: 0.72;
        }

        /* ── Section headings ────────────────────────────────────────── */

        .section-title {
            font-size: 14px; font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.6px;
            opacity: 0.55;
            margin: 0 0 12px;
        }

        /* ── Meios chart ─────────────────────────────────────────────── */

        .meios {
            display: flex; flex-direction: column;
            gap: 12px;
        }
        .bar-row {
            display: grid;
            grid-template-columns: 180px 1fr 90px;
            align-items: center;
            gap: 18px;
        }
        .bar-label {
            display: flex; align-items: center; gap: 12px;
            font-size: 18px; font-weight: 600;
            opacity: 0.85;
        }
        .bar-label__icon {
            width: 24px; height: 24px;
            filter: brightness(0) invert(1);
            opacity: 0.9;
        }
        .bar-track {
            position: relative;
            height: 22px;
            background: rgba(255,255,255,0.06);
            border-radius: 6px;
            overflow: hidden;
        }
        .bar-fill {
            position: absolute; top: 0; left: 0; bottom: 0;
            background: linear-gradient(90deg, {{ $BRAND }} 0%, #ff9147 100%);
            border-radius: 6px;
        }
        .bar-value {
            font-size: 30px; font-weight: 800;
            text-align: right;
            letter-spacing: -0.5px;
        }
        .bar-value--unconfirmed {
            font-size: 15px; font-weight: 600;
            opacity: 0.55;
            padding-top: 8px;
        }

        /* ── Timeline ────────────────────────────────────────────────── */

        .timeline-wrap {
            position: relative;
        }
        .timeline-line {
            position: absolute; left: 8px; right: 8px; top: 10px;
            height: 2px;
            background: rgba(255,255,255,0.12);
            z-index: 0;
        }
        .timeline {
            display: grid;
            grid-auto-flow: column;
            grid-auto-columns: 1fr;
            gap: 8px;
            position: relative;
            z-index: 1;
        }
        .tl-item {
            display: flex; flex-direction: column; align-items: flex-start;
            gap: 6px;
        }
        .tl-dot {
            width: 22px; height: 22px;
            border-radius: 50%;
            border: 3px solid #0a0a0b;
            box-shadow: 0 0 0 2px rgba(255,255,255,0.08);
        }
        .tl-time {
            font-size: 15px; font-weight: 700;
            letter-spacing: -0.2px;
        }
        .tl-label {
            font-size: 13px; font-weight: 500;
            opacity: 0.65;
            line-height: 1.2;
            /* Keep labels to two lines so a long name never punches
               through into the row above. */
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .tl-overflow {
            font-size: 14px; font-weight: 700;
            color: {{ $BRAND }};
            padding-top: 2px;
        }

        .timeline-empty {
            font-size: 16px;
            opacity: 0.5;
            font-style: italic;
        }

        /* ── Footer ──────────────────────────────────────────────────── */

        .footer {
            margin-top: auto;
            display: flex; justify-content: space-between; align-items: baseline;
            font-size: 18px;
            opacity: 0.55;
        }
        .footer__url {
            font-weight: 700;
            color: {{ $BRAND }};
            opacity: 0.9;
        }
    </style>
</head>
<body>
    <div class="card">
        <div class="top">
            <div class="brand">
                <div class="brand__flame">🔥</div>
                <span class="brand__name">Fogos<em>.pt</em></span>
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
            <p class="section-title">Meios no terreno</p>

            <div class="bar-row">
                <div class="bar-label">
                    <img class="bar-label__icon" src="/img/fireman.svg" alt="">
                    Operacionais
                </div>
                <div class="bar-track">
                    <div class="bar-fill" style="width: {{ number_format($manPct, 2, '.', '') }}%"></div>
                </div>
                @if($manUnconfirmed)
                    <div class="bar-value bar-value--unconfirmed">por confirmar</div>
                @else
                    <div class="bar-value">{{ $man }}</div>
                @endif
            </div>

            <div class="bar-row">
                <div class="bar-label">
                    <img class="bar-label__icon" src="/img/firetruck.svg" alt="">
                    Terrestres
                </div>
                <div class="bar-track">
                    <div class="bar-fill" style="width: {{ number_format($terrainPct, 2, '.', '') }}%"></div>
                </div>
                @if($terrainUnconfirmed)
                    <div class="bar-value bar-value--unconfirmed">por confirmar</div>
                @else
                    <div class="bar-value">{{ $terrain }}</div>
                @endif
            </div>

            <div class="bar-row">
                <div class="bar-label">
                    <img class="bar-label__icon" src="/img/plane.svg" alt="">
                    Aéreos
                </div>
                <div class="bar-track">
                    <div class="bar-fill" style="width: {{ number_format($aerialPct, 2, '.', '') }}%"></div>
                </div>
                @if($aerialUnconfirmed)
                    <div class="bar-value bar-value--unconfirmed">por confirmar</div>
                @else
                    <div class="bar-value">{{ $aerial }}</div>
                @endif
            </div>
        </div>

        <div>
            <p class="section-title">
                Evolução
                @if($historyOverflow > 0)
                    <span class="tl-overflow">· +{{ $historyOverflow }} anteriores</span>
                @endif
            </p>
            @if(count($history) === 0)
                <div class="timeline-empty">Sem histórico disponível</div>
            @else
                <div class="timeline-wrap">
                    <div class="timeline-line"></div>
                    <div class="timeline">
                        @foreach($history as $ev)
                            @php
                                $code = (int) ($ev['statusCode'] ?? 0);
                                $dotColor = $statusPalette[$code] ?? $BRAND;
                                $tLabel = trim((string) ($ev['label']  ?? ''));
                                $sLabel = trim((string) ($ev['status'] ?? ''));
                            @endphp
                            <div class="tl-item">
                                <span class="tl-dot" style="background: {{ $dotColor }};"></span>
                                <span class="tl-time">{{ $tLabel }}</span>
                                <span class="tl-label">{{ $sLabel }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <div class="footer">
            <span>{{ date('H:i') }} · {{ date('d-m-Y') }}</span>
            <span class="footer__url">fogos.pt</span>
        </div>
    </div>

    <script>
        // Two rAFs guarantee styles have committed before we let the
        // sidecar screenshot. The card has no async I/O so this fires
        // within ~10 ms on a warm browser.
        requestAnimationFrame(function () {
            requestAnimationFrame(function () { window.__ogReady = true; });
        });
    </script>
</body>
</html>
