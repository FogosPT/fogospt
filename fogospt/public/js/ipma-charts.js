/**
 * Renders the IPMA forecast charts on the fire detail page.
 * Reads lat/lng from the wrapper, fetches /v1/ipma-point/{lat}/{lng}
 * (proxied + cached by Laravel), and draws eight Chart.js panels.
 */
(function () {
    var wrap = document.querySelector('.ipma-charts');
    if (!wrap || typeof Chart === 'undefined') return;

    var lat = parseFloat(wrap.dataset.lat);
    var lng = parseFloat(wrap.dataset.lng);
    if (!isFinite(lat) || !isFinite(lng)) return;

    // Fallback labels (PT) when window.trans.chartIpma isn't available — e.g.
    // when the rendered HTML still has a window.trans payload from before the
    // chartIpma keys were added to lang/{locale}/js.php.
    var DEFAULTS = {
        temperature: 'Temperatura (°C)',
        humidity:    'Humidade (%)',
        wind:        'Vento médio (km/h)',
        gust:        'Rajada (km/h)',
        pressure:    'Pressão (hPa)',
        precipitation: 'Precipitação (mm)',
        fwi: 'FWI', isi: 'ISI', bui: 'BUI',
        dc: 'DC',   dmc: 'DMC', ffmc: 'FFMC',
        p2000:  'Probabilidade de extremos',
        p2000a: 'Anomalia',
        rcm:    'RCM (estação)',
        titleTempHum:  'Temperatura e humidade',
        titleWind:     'Vento e rajada',
        titlePressure: 'Pressão atmosférica',
        titlePrecip:   'Precipitação acumulada',
        titleFwi:      'FWI / ISI / BUI',
        titleDc:       'DC / DMC / FFMC',
        titleFrm:      'FRM — probabilidade e anomalia',
        titleRcm:      'RCM (estação)',
    };
    var fromTrans = (window.trans && window.trans.chartIpma) || {};
    var t = {};
    Object.keys(DEFAULTS).forEach(function (k) {
        t[k] = fromTrans[k] || DEFAULTS[k];
    });
    var loader = wrap.querySelector('.ipma-charts__loader');
    var grid   = wrap.querySelector('.ipma-charts__grid');
    var err    = wrap.querySelector('.ipma-charts__error');

    loader.classList.remove('d-none');

    fetch('/v1/ipma-point/' + lat + '/' + lng, { credentials: 'omit' })
        .then(function (r) { return r.ok ? r.json() : Promise.reject(r.status); })
        .then(function (data) {
            loader.classList.add('d-none');
            if (!data || (!data.hourly && !data.daily)) {
                err.classList.remove('d-none');
                return;
            }
            grid.classList.remove('d-none');
            renderHourly('ipmaTempHum',   data.hourly, t.titleTempHum,
                [{ key: 'temperature', label: t.temperature, color: '#ff512f', axis: 'L' },
                 { key: 'humidity',    label: t.humidity,    color: '#33a1fd', axis: 'R' }]);
            renderHourly('ipmaWind',      data.hourly, t.titleWind,
                [{ key: 'wind', label: t.wind, color: '#6D720B' },
                 { key: 'gust', label: t.gust, color: '#EFC800' }],
                { windArrows: true });
            renderHourly('ipmaPressure',  data.hourly, t.titlePressure,
                [{ key: 'pressure', label: t.pressure, color: '#4E88B2' }]);
            renderHourly('ipmaPrecip',    data.hourly, t.titlePrecip,
                [{ key: 'precipitation', label: t.precipitation, color: '#33a1fd' }],
                { type: 'bar' });
            renderDaily('ipmaFwiIsiBui',  data.daily, t.titleFwi,
                [{ key: 'fwi', label: t.fwi, color: '#ff512f' },
                 { key: 'isi', label: t.isi, color: '#f09819' },
                 { key: 'bui', label: t.bui, color: '#6D720B' }]);
            renderDaily('ipmaDcDmcFfmc',  data.daily, t.titleDc,
                [{ key: 'dc',   label: t.dc,   color: '#cb333b' },
                 { key: 'dmc',  label: t.dmc,  color: '#e87722' },
                 { key: 'ffmc', label: t.ffmc, color: '#509e2f' }]);
            renderDaily('ipmaFrm',        data.daily, t.titleFrm,
                [{ key: 'p2000',  label: t.p2000,  color: '#cb333b' },
                 { key: 'p2000a', label: t.p2000a, color: '#33a1fd' }]);
            renderDaily('ipmaRcm',        data.daily, t.titleRcm,
                [{ key: 'rcm', label: t.rcm, color: '#6f263d' }]);
        })
        .catch(function () {
            loader.classList.add('d-none');
            err.classList.remove('d-none');
        });

    function shortHour(iso) {
        // "2026-05-13T19:00" -> "13/05 19h"
        if (!iso) return '';
        var d = iso.slice(8, 10), m = iso.slice(5, 7), h = iso.slice(11, 13);
        return d + '/' + m + ' ' + h + 'h';
    }

    function shortDay(iso) {
        if (!iso) return '';
        return iso.slice(8, 10) + '/' + iso.slice(5, 7);
    }

    function numOrNaN(v) {
        return (v == null || isNaN(v)) ? NaN : Number(v);
    }

    function renderHourly(canvasId, hourly, title, series, opts) {
        opts = opts || {};
        var canvas = document.getElementById(canvasId);
        if (!canvas || !hourly || !hourly.length) {
            if (canvas) canvas.parentElement.style.display = 'none';
            return;
        }
        var labels = hourly.map(function (r) { return shortHour(r.datetime); });
        var hasDualAxis = series.some(function (s) { return s.axis === 'R'; });
        var datasets = series.map(function (s) {
            var values = hourly.map(function (r) { return numOrNaN(r[s.key]); });
            var ds = {
                label: s.label,
                data: values,
                _hasData: values.some(function (v) { return !isNaN(v); }),
                backgroundColor: s.color,
                borderColor: s.color,
                fill: false,
                pointRadius: 0,
                borderWidth: 1.5,
                spanGaps: true
            };
            // Only attach yAxisID when the chart has multiple y-axes; otherwise
            // Chart.js looks up a scale that doesn't exist and dies inside
            // updateElement with 'Cannot read getBasePixel of undefined'.
            if (hasDualAxis) {
                ds.yAxisID = s.axis === 'R' ? 'right' : 'left';
            }
            return ds;
        }).filter(function (ds) { return ds._hasData; });

        if (!datasets.length) { canvas.parentElement.style.display = 'none'; return; }

        var config = {
            type: opts.type || 'line',
            data: { labels: labels, datasets: datasets },
            options: {
                title: { display: !!title, text: title, fontSize: 13 },
                legend: { position: 'bottom', labels: { boxWidth: 12, fontSize: 11 } },
                elements: { line: { tension: 0.1 } },
                scales: {
                    xAxes: [{ ticks: { maxTicksLimit: 8, fontSize: 10 } }],
                    yAxes: hasDualAxis
                        ? [{ id: 'left',  position: 'left',  ticks: { fontSize: 10 } },
                           { id: 'right', position: 'right', ticks: { fontSize: 10 }, gridLines: { display: false } }]
                        : [{ ticks: { fontSize: 10 } }]
                }
            }
        };
        if (opts.windArrows) {
            config.plugins = [windArrowsPlugin(hourly)];
        }

        try {
            new Chart(canvas, config);
        } catch (e) {
            if (window.console && console.warn) console.warn('ipma chart ' + canvasId + ':', e);
            canvas.parentElement.style.display = 'none';
        }
    }

    // Plugin: draws a small arrow above each data point pointing in the
    // direction the wind is going (u,v are east/north components in m/s).
    // We draw one arrow every other point to avoid clutter at 48 hours.
    // Wrapped in try/catch so a plugin error never takes the chart with it.
    function windArrowsPlugin(hourly) {
        return {
            afterDatasetsDraw: function (chart) {
                try {
                    var ctx = (chart && chart.ctx) || (chart && chart.chart && chart.chart.ctx);
                    if (!ctx || !chart.chartArea) return;
                    var meta = chart.getDatasetMeta && chart.getDatasetMeta(0);
                    if (!meta || !meta.data || !meta.data.length) return;
                    // Draw arrows just inside the top of the chart area
                    // (below the title, above the data). Title lives above
                    // chartArea so we can't use negative offsets.
                    var top = chart.chartArea.top + 10;
                    var step = hourly.length > 24 ? 3 : 2;
                    ctx.save();
                    ctx.globalAlpha = 0.75;
                    ctx.strokeStyle = '#333';
                    ctx.fillStyle = '#333';
                    ctx.lineWidth = 1;
                    ctx.lineCap = 'round';
                    ctx.lineJoin = 'round';
                    for (var i = 0; i < hourly.length; i++) {
                        if (i % step !== 0) continue;
                        var row = hourly[i];
                        if (!row) continue;
                        var u = row.windU;
                        var v = row.windV;
                        if (u == null || v == null || isNaN(u) || isNaN(v)) continue;
                        var mag = Math.sqrt(u * u + v * v);
                        if (mag < 0.1) continue;
                        var pt = meta.data[i];
                        if (!pt || !pt._model || typeof pt._model.x !== 'number') continue;
                        // Canvas Y is flipped: wind going north (v>0) should
                        // visually point UP, so negate v.
                        var angle = Math.atan2(-v, u);
                        drawArrow(ctx, pt._model.x, top, angle, 11);
                    }
                    ctx.restore();
                } catch (err) {
                    // Don't let the plugin break the chart.
                    if (window.console && console.warn) console.warn('wind arrows plugin:', err);
                }
            }
        };
    }

    function drawArrow(ctx, cx, cy, angle, length) {
        // Stem
        var hx = length / 2;
        var x1 = cx - Math.cos(angle) * hx;
        var y1 = cy - Math.sin(angle) * hx;
        var x2 = cx + Math.cos(angle) * hx;
        var y2 = cy + Math.sin(angle) * hx;
        ctx.beginPath();
        ctx.moveTo(x1, y1);
        ctx.lineTo(x2, y2);
        ctx.stroke();
        // Head
        var head = 4;
        var leftAngle = angle + Math.PI - 0.5;
        var rightAngle = angle + Math.PI + 0.5;
        ctx.beginPath();
        ctx.moveTo(x2, y2);
        ctx.lineTo(x2 + Math.cos(leftAngle) * head, y2 + Math.sin(leftAngle) * head);
        ctx.lineTo(x2 + Math.cos(rightAngle) * head, y2 + Math.sin(rightAngle) * head);
        ctx.closePath();
        ctx.fill();
    }

    function renderDaily(canvasId, daily, title, series) {
        var canvas = document.getElementById(canvasId);
        if (!canvas || !daily) {
            if (canvas) canvas.parentElement.style.display = 'none';
            return;
        }

        // Build a unified date axis across the union of all series' datetimes
        var dateSet = {};
        series.forEach(function (s) {
            (daily[s.key] || []).forEach(function (r) { if (r.datetime) dateSet[r.datetime] = true; });
        });
        var dates = Object.keys(dateSet).sort();
        if (!dates.length) { canvas.parentElement.style.display = 'none'; return; }

        var datasets = series.map(function (s) {
            var byDate = {};
            (daily[s.key] || []).forEach(function (r) { byDate[r.datetime] = r.value; });
            var values = dates.map(function (d) { return numOrNaN(byDate[d]); });
            return {
                label: s.label,
                data: values,
                _hasData: values.some(function (v) { return !isNaN(v); }),
                backgroundColor: s.color,
                borderColor: s.color,
                fill: false,
                pointRadius: 3,
                borderWidth: 1.5,
                spanGaps: true
            };
        }).filter(function (ds) { return ds._hasData; });

        if (!datasets.length) { canvas.parentElement.style.display = 'none'; return; }

        try {
            new Chart(canvas, {
                type: 'line',
                data: { labels: dates.map(shortDay), datasets: datasets },
                options: {
                    title: { display: !!title, text: title, fontSize: 13 },
                    legend: { position: 'bottom', labels: { boxWidth: 12, fontSize: 11 } },
                    elements: { line: { tension: 0 } },
                    scales: {
                        xAxes: [{ ticks: { fontSize: 10 } }],
                        yAxes: [{ ticks: { fontSize: 10 } }]
                    },
                    spanGaps: true
                }
            });
        } catch (e) {
            canvas.parentElement.style.display = 'none';
        }
    }
})();
