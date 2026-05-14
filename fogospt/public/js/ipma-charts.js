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

    var t = (window.trans && window.trans.chartIpma) || {};
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
                 { key: 'gust', label: t.gust, color: '#EFC800' }]);
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

    function renderHourly(canvasId, hourly, title, series, opts) {
        opts = opts || {};
        var canvas = document.getElementById(canvasId);
        if (!canvas || !hourly || !hourly.length) {
            if (canvas) canvas.parentElement.style.display = 'none';
            return;
        }
        var labels = hourly.map(function (r) { return shortHour(r.datetime); });
        var datasets = series.map(function (s) {
            return {
                label: s.label,
                data: hourly.map(function (r) { return r[s.key]; }),
                backgroundColor: s.color,
                borderColor: s.color,
                fill: false,
                yAxisID: s.axis === 'R' ? 'right' : 'left',
                pointRadius: 0,
                borderWidth: 1.5
            };
        });
        var hasDualAxis = series.some(function (s) { return s.axis === 'R'; });
        new Chart(canvas, {
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
        });
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
            return {
                label: s.label,
                data: dates.map(function (d) { return byDate[d] == null ? null : byDate[d]; }),
                backgroundColor: s.color,
                borderColor: s.color,
                fill: false,
                pointRadius: 3,
                borderWidth: 1.5
            };
        });

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
    }
})();
