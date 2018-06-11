$(document).ready(function () {
    plot();
});

function plot() {
    var url = 'https://fogos.pt/v1/now/data';
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
                    terrain.push(data.data[d].terrain);
                    aerial.push(data.data[d].aerial);
                    total.push(data.data[d].total);
                }

                var ctx = document.getElementById("myChart");
                var myLineChart = new Chart(ctx, {
                    type: 'line',
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
                                label: 'Aéreos',
                                data: aerial,
                                fill: false,
                                backgroundColor: '#4E88B2',
                                borderColor: '#4E88B2'
                            },
                            {
                                label: 'Terrestres',
                                data: terrain,
                                fill: false,
                                backgroundColor: '#6D720B',
                                borderColor: '#6D720B'
                            },
                            {
                                label: 'Incêndio ativos',
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
                            }
                        },
                        scales: {
                            yAxes: [{
                                ticks: {}
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