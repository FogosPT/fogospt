// Fire resources history chart (operacionais / terrestres / aéreos), shared by
// the three maps and the fire detail page. Chart.js 4.
//
// Firefighters and ground vehicles share the left axis; aircraft — an order
// of magnitude fewer — get their own right axis so the line stays readable.
(function () {
    var COLORS = { man: '#EFC800', terrain: '#6D720B', aerial: '#4E88B2' };

    function t(key, fallback) {
        var chart = (window.trans && window.trans.chart) || {};
        return chart[key] || fallback;
    }

    function dataset(label, data, color, axis) {
        return {
            label: label,
            data: data,
            borderColor: color,
            backgroundColor: color,
            yAxisID: axis,
            tension: 0,
            fill: false,
        };
    }

    // rows: upstream /fires/data items ({label, man, terrain, aerial}).
    window.fogosResourcesChart = function (canvas, rows, opts) {
        opts = opts || {};
        var humans = t('humans', 'Operacionais');
        var terrestrial = t('terrestrial', 'Terrestres');
        var aerial = t('aerial', 'Aéreos');

        // Chart.js 3+ refuses to reuse a canvas — drop the previous fire's chart.
        var previous = window.Chart && Chart.getChart(canvas);
        if (previous) previous.destroy();

        return new Chart(canvas, {
            type: 'line',
            data: {
                labels: rows.map(function (r) { return r.label; }),
                datasets: [
                    dataset(humans, rows.map(function (r) { return r.man; }), COLORS.man, 'yLeft'),
                    dataset(terrestrial, rows.map(function (r) { return r.terrain; }), COLORS.terrain, 'yLeft'),
                    dataset(aerial, rows.map(function (r) { return r.aerial; }), COLORS.aerial, 'yRight'),
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: opts.maintainAspectRatio !== false,
                interaction: { mode: 'index', intersect: false },
                scales: {
                    yLeft: {
                        type: 'linear',
                        position: 'left',
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        title: { display: true, text: humans + ' / ' + terrestrial },
                    },
                    yRight: {
                        type: 'linear',
                        position: 'right',
                        beginAtZero: true,
                        ticks: { precision: 0 },
                        grid: { drawOnChartArea: false },
                        title: { display: true, text: aerial },
                    },
                },
            },
        });
    };

    // Replaces the canvas with a translated "no data" note.
    window.fogosResourcesChartEmpty = function (container) {
        var $c = $(container);
        $c.find('canvas').remove();
        $c.append($('<p>').text(t('noData', 'Não há dados disponíveis')));
    };
})();
