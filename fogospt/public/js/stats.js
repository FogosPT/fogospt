$(document).ready(function () {
    plot();
    plotWeekStats();
    plotStats8hours();
    plotStats8hoursYesterday();
    plotStatsYesterdayDistricts();
    plotStatsLastNight();
    plotStatsDistricts();

    if (getParameterByName('phantom')) {
        $('.phantom-hide').hide();
    }
});

var dColors = {
    'Aveiro': '#77CBCF',
    'Beja': '#7D9BD3',
    'Braga': '#9782D7',
    'Bragança': '#31987E',
    'Castelo Branco': '#C86195',
    'Coimbra': '#CD7066',
    'Évora': '#CF9269',
    'Faro': '#56C3A1',
    'Guarda': '#CD9C68',
    'Leiria': '#CEB76A',
    'Lisboa': '#CED06D',
    'Portalegre': '#653522',
    'Porto': '#612422',
    'Santarém': '#442A7C',
    'Setúbal': '#2F2A78',
    'Viana do Castelo': '#882B82',
    'Vila Real': '#4A6223',
    'Viseu': '#57852F',
};

function plot() {
    var url = 'https://api-lb.fogos.pt/v1/now/data';
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            data = JSON.parse(data);
            if (data.success && data.data.length) {
                labels = [];
                var man = [];
                var terrain = [];
                var aerial = [];
                var total = [];
                for (d in data.data) {
                    labels.push(data.data[d].label);
                    man.push(data.data[d].man);
                    terrain.push(data.data[d].cars);
                    aerial.push(data.data[d].aerial);
                    total.push(data.data[d].total);
                }

                var ctx = document.getElementById("myChart");
                var myLineChart = new Chart(ctx, {
                    type: 'line',
                    omitXLabels: true,
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Humanos',
                            data: man,
                            fill: false,
                            backgroundColor: '#EFC800',
                            borderColor: '#EFC800'
                        },
                            {
                                label: 'Terrestres',
                                data: terrain,
                                fill: false,
                                backgroundColor: '#6D720B',
                                borderColor: '#6D720B'
                            },
                            {
                                label: 'Aéreos',
                                data: aerial,
                                fill: false,
                                backgroundColor: '#4E88B2',
                                borderColor: '#4E88B2'
                            },
                            {
                                label: 'Incêndios ativos',
                                data: total,
                                fill: false,
                                backgroundColor: '#ff512f',
                                borderColor: '#ff512f'
                            }]
                    },
                    options: {
                        elements: {
                            line: {
                                tension: 0, // disables bezier curves
                                showXLabels: 5,
                            }
                        },
                        scales: {
                            xAxes: [{
                                ticks: {
                                    stepSize: 20
                                }
                            }]
                        }
                    }
                });
            } else {
                $('#info').find('canvas').remove();
                $('#info').append('<p>Não há dados disponiveis</p> ');
            }
        }
    });
}

function plotWeekStats() {
    var url = 'https://api-lb.fogos.pt/v1/stats/week';
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            data = JSON.parse(data);
            if (data.success && data.data.length) {
                labels = [];
                var total = [];
                var falseFires = [];
                for (d in data.data) {
                    labels.push(data.data[d].label);
                    falseFires.push(data.data[d].false);
                    total.push(data.data[d].total);
                }

                var ctx = document.getElementById("myChartStatsWeek");
                var myLineChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total',
                            data: total,
                            fill: false,
                            backgroundColor: '#fe5130',
                            borderColor: '#fe5130'
                        },
                            {
                                label: 'Falsos Alarmes',
                                data: falseFires,
                                fill: false,
                                backgroundColor: '#000000',
                                borderColor: '#000000'
                            },
                        ]
                    },
                    options: {
                        elements: {
                            line: {
                                tension: 0, // disables bezier curves
                                showXLabels: 5,
                            }
                        },
                        responsive: true,
                        scales: {
                            xAxes: [{
                                stacked: true,
                            }],
                            yAxes: [{
                                stacked: true
                            }]
                        }
                    }
                });
            } else {
                $('#info').find('canvas').remove();
                $('#info').append('<p>Não há dados disponiveis</p> ');
            }
        }
    });
}

function plotStats8hours() {
    var url = 'https://api-lb.fogos.pt/v1/stats/8hours';
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            data = JSON.parse(data);
            if (data.success && data.data) {
                var labels = [];
                var total = [];

                for (d in data.data) {
                    labels.push(d);
                    total.push(data.data[d].total);
                }

                var ctx = document.getElementById("myChartStats8hours");
                var myLineChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total',
                            data: total,
                            fill: false,
                            backgroundColor: '#fe5130',
                            borderColor: '#fe5130'
                        },
                        ]
                    },
                    options: {
                        elements: {
                            line: {
                                tension: 0, // disables bezier curves
                                showXLabels: 5,
                            }
                        },
                        responsive: true,
                        scales: {
                            xAxes: [{
                                stacked: true,
                            }],
                            yAxes: [{
                                stacked: true
                            }]
                        }
                    }
                });
            } else {
                $('#info').find('canvas').remove();
                $('#info').append('<p>Não há dados disponiveis</p> ');
            }
        }
    });
}

function plotStatsLastNight() {
    var url = 'https://api-lb.fogos.pt/v1/stats/last-night';

    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            data = JSON.parse(data);
            if (data.success && data.data) {
                var labels = [];
                var total = [];
                var colors = [];

                for (d in data.data.distritos) {
                    labels.push(d);
                    total.push(data.data.distritos[d]);
                    colors.push(stringToColour(d));
                }

                var ctx = document.getElementById("myChartStatsLastNight");
                var myLineChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Incêndios',
                            data: total,
                            backgroundColor: colors,
                        },
                        ]
                    },
                });

            } else {
                $('#info').find('canvas').remove();
                $('#info').append('<p>Não há dados disponiveis</p> ');
            }
        }
    });
}

function plotStats8hoursYesterday() {
    var url = 'https://api-lb.fogos.pt/v1/stats/8hours/yesterday';
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            data = JSON.parse(data);
            if (data.success && data.data) {
                var labels = [];
                var total = [];

                for (d in data.data) {
                    labels.push(d);
                    total.push(data.data[d].total);
                }

                var ctx = document.getElementById("myChartStats8hoursYesterday");
                var myLineChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total',
                            data: total,
                            fill: false,
                            backgroundColor: '#fe5130',
                            borderColor: '#fe5130'
                        },
                        ]
                    },
                    options: {
                        elements: {
                            line: {
                                tension: 0, // disables bezier curves
                                showXLabels: 5,
                            }
                        },
                        responsive: true,
                        scales: {
                            xAxes: [{
                                stacked: true,
                            }],
                            yAxes: [{
                                stacked: true
                            }]
                        }
                    }
                });
            } else {
                $('#info').find('canvas').remove();
                $('#info').append('<p>Não há dados disponiveis</p> ');
            }
        }
    });
}

function plotStatsYesterdayDistricts() {
    var url = 'https://api-lb.fogos.pt/v1/stats/yesterday';

    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            data = JSON.parse(data);
            if (data.success && data.data) {
                var labels = [];
                var total = [];
                var colors = [];

                for (d in data.data.distritos) {
                    labels.push(d);
                    total.push(data.data.distritos[d]);
                    colors.push(stringToColour(d));
                }

                var ctx = document.getElementById("myChartStatsYesterday");
                var myLineChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total',
                            data: total,
                            backgroundColor: colors,
                        },
                        ]
                    },
                });

            } else {
                $('#info').find('canvas').remove();
                $('#info').append('<p>Não há dados disponiveis</p> ');
            }
        }
    });
}

function plotStatsDistricts() {
    var url = 'https://api-lb.fogos.pt/v1/stats/today';

    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            data = JSON.parse(data);
            if (data.success && data.data) {
                var labels = [];
                var total = [];
                var colors = [];

                for (d in data.data.distritos) {
                    labels.push(d);
                    total.push(data.data.distritos[d]);
                    colors.push(stringToColour(d));
                }

                var ctx = document.getElementById("myChartStatsToday");
                var myLineChart = new Chart(ctx, {
                    type: 'doughnut',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: 'Total',
                            data: total,
                            backgroundColor: colors,
                        },
                        ]
                    },
                });

            } else {
                $('#info').find('canvas').remove();
                $('#info').append('<p>Não há dados disponiveis</p> ');
            }
        }
    });
}

var stringToColour = function (str) {
    var hash = 0;
    for (var i = 0; i < str.length; i++) {
        hash = str.charCodeAt(i) + ((hash << 5) - hash);
    }
    var colour = '#';
    for (var i = 0; i < 3; i++) {
        var value = (hash >> (i * 7)) & 0xFF;
        colour += ('00' + value.toString(16)).substr(-2);
    }
    return colour;
}


function getParameterByName(name, url) {
    if (!url) url = window.location.href;
    name = name.replace(/[\[\]]/g, "\\$&");
    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
        results = regex.exec(url);
    if (!results) return null;
    if (!results[2]) return '';
    return decodeURIComponent(results[2].replace(/\+/g, " "));
}