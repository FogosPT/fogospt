$(document).ready(function () {

    var id = $('#myChart').data('id');

    plot(id);

    function plot(id) {
        var url = 'https://api-dev.fogos.pt/fires/data?id=' + id
        $.ajax({
            url: url,
            method: 'GET',
            success: function (data) {
                if (data.success && data.data.length) {
                    labels = []
                    var man = []
                    var terrain = []
                    var aerial = []
                    for (d in data.data) {
                        labels.push(data.data[d].label)
                        man.push(data.data[d].man)
                        terrain.push(data.data[d].terrain)
                        aerial.push(data.data[d].aerial)
                    }

                    var ctx = document.getElementById('myChart')
                    var myLineChart = new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Operacionais',
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
                                }, {
                                    label: 'Aéreos',
                                    data: aerial,
                                    fill: false,
                                    backgroundColor: '#4E88B2',
                                    borderColor: '#4E88B2'
                                }
                            ]
                        },
                        options: {
                            elements: {
                                line: {
                                    tension: 0 // disables bezier curves
                                }
                            },
                            scales: {
                                yAxes: [{
                                    ticks: {}
                                }]
                            }
                        }
                    })
                } else {
                    $('#info').find('canvas').remove()
                    $('#info').append('<p>Não há dados disponiveis</p> ')
                }
            }
        })

    }
})