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
                    <svg class="bar-label__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 368 512" aria-hidden="true"><path fill="#ff512f" d="M225.3,421.8c18.2-7.5,31.5-18.7,41.1-30,.6-.3,1.2-.7,1.8-1.2.2-.2,24.8-23.5,49.1-50.9,34-38.5,50.5-65.6,50.5-82.9,0-30.2-27.1-42.7-46.5-46.1.1-7.6-.7-19.9-6-34.1-13.3-36.1-44.7-58.3-70.5-70.9v-16.9c0-4.3-3.5-7.8-7.8-7.8h-106.2c-4.3,0-7.8,3.5-7.8,7.8v16.9c-44.6,21.9-62.9,50.5-70.5,70.9-5.2,14.2-6.1,26.5-6,34.1C27.3,214.1.2,226.6.2,256.8s16.5,44.4,50.5,82.9c24.2,27.4,48.8,50.7,49.1,50.9.5.5,1.1.9,1.8,1.2,9.6,11.3,22.9,22.4,41.1,30,0,0,18.7,9.1,41,9.2,22.3.1,41.7-9.2,41.7-9.2ZM294.3,328.2v-81.7c4.6,1.5,10.1,4.5,13.6,10.7,9.2,16.7-1.1,46.1-13.6,71ZM55.5,225.3c2.2-.1,4.3-1.2,5.7-3,1.4-1.8,1.9-4,1.5-6.2-.1-.6-10.1-60.7,71.5-98.4,2.8-1.3,4.5-4,4.5-7.1v-14h90.6v14c0,3,1.8,5.8,4.5,7.1,34.2,15.8,56.6,37.1,66.6,63.5,7.4,19.6,4.9,34.8,4.9,34.9-.4,2.2.2,4.4,1.5,6.2,1.4,1.8,3.4,2.8,5.6,3,1.6.1,39.7,3,39.7,31.6s-15.4,35.3-35.3,59.3c11.6-28.9,13.1-51.2,4.4-66.7-11.1-19.7-33.9-20-34.9-20H81.3c-19.1,0-32.1,6.3-38.8,18.6-7.8,14.4-5.6,36.4,6.3,65.2-18.8-23-33-44.6-33-56.5,0-28.6,38.1-31.4,39.7-31.6ZM56.3,255.5c3-5.5,8.7-8.8,17.4-10v84.4c-14-26.7-25.7-59.1-17.4-74.4ZM105.7,371.4c-12.2-18.7-15.9-37.2-16.4-40.4v-85.9h189.4v85.9c-.5,3.2-4.1,21.3-16.1,39.9-17.4,27.1-43.7,42-77.9,44.3h-1.4c-34-2.3-60.1-17-77.6-43.8ZM173.9,150.6v17.5l9.8,11,10.4-11.2v-17.3h-20.2ZM177.7,195.8l-17.4-19.6c-1.3-1.4-2-3.3-2-5.2v-28.2c0-4.3,3.5-7.8,7.8-7.8h35.8c4.3,0,7.8,3.5,7.8,7.8v28.2c0,2-.8,3.9-2.1,5.3l-18.4,19.6c-1.5,1.6-3.5,2.5-5.7,2.5h-.1c-2.2,0-4.3-1-5.7-2.6Z"/></svg>
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
                    <svg class="bar-label__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" aria-hidden="true"><path fill="#ff512f" d="M99.8,40.2l-5.8-14.6c-1-2.5-3.5-4.2-6.2-4.2h-14.4v-3.3c0-1.8-1.5-3.3-3.3-3.3s-3.3,1.5-3.3,3.3v26.7H3.3c-1.8,0-3.3,1.5-3.3,3.3v20c0,3.7,3,6.7,6.7,6.7h7.1c1.8,7.1,9.1,11.4,16.2,9.6,4.7-1.2,8.4-4.9,9.6-9.6h20.8c1.8,7.1,9.1,11.4,16.2,9.6,4.7-1.2,8.4-4.9,9.6-9.6h7.1c3.7,0,6.7-3,6.7-6.7v-26.7c0-.4,0-.9-.2-1.2ZM26.7,78.1c-3.7,0-6.7-3-6.7-6.7s3-6.7,6.7-6.7,6.7,3,6.7,6.7-3,6.7-6.7,6.7ZM73.3,78.1c-3.7,0-6.7-3-6.7-6.7s3-6.7,6.7-6.7,6.7,3,6.7,6.7-3,6.7-6.7,6.7ZM73.3,38.1v-10h14.4l4,10h-18.4ZM3.3,31.4c-1.8,0-3.3,1.5-3.3,3.3s1.5,3.3,3.3,3.3h53.3c1.8,0,3.3-1.5,3.3-3.3s-1.5-3.3-3.3-3.3h-6.7v-10h6.7c1.8,0,3.3-1.5,3.3-3.3s-1.5-3.3-3.3-3.3H3.3c-1.8,0-3.3,1.5-3.3,3.3s1.5,3.3,3.3,3.3h6.7v10H3.3ZM33.3,21.4h10v10h-10v-10ZM16.7,21.4h10v10h-10v-10Z"/></svg>
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
                    <svg class="bar-label__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100" aria-hidden="true"><path fill="#ff512f" d="M90,83.3c0,1.8-1.5,3.3-3.3,3.3H26.7c-1.8,0-3.3-1.5-3.3-3.3s1.5-3.3,3.3-3.3h60c1.8,0,3.3,1.5,3.3,3.3ZM83.3,33.3h-25.3l-19-19c-.6-.6-1.5-1-2.4-1h-3.3c-3.7,0-6.7,3-6.7,6.7,0,.7.1,1.4.3,2.1l3.8,11.2h-9.4l-9-9c-.6-.6-1.5-1-2.4-1h-3.3C3,23.3,0,26.3,0,30c0,.6,0,1.3.3,1.9l5.9,19.5c2.1,7.1,8.6,11.9,16,11.9h74.6c1.8,0,3.3-1.5,3.3-3.3v-10c0-9.2-7.5-16.7-16.7-16.7Z"/></svg>
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
