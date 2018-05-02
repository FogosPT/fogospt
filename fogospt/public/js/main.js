$(document).ready(function () {
    // mapboxgl.accessToken = 'ZdKY[PHwJ.2e@wff9>&46LD^7D&pepEKcW9J3JKgv2bMLY2c&EYAN&$4gskreqWF';
    // mapboxgl.accessToken = 'pk.eyJ1IjoidG9tYWhvY2siLCJhIjoiY2pmYmgydHJnMzMwaTJ3azduYzI2eGZteiJ9.4Z0iB0Pgbb3M_8t9VG10kQ';

    var mymap = L.map('map').setView([ 40.5050025, -7.9053189], 7);

    L.tileLayer('https://api.tiles.mapbox.com/v4/{id}/{z}/{x}/{y}.png?access_token={accessToken}', {
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
        maxZoom: 18,
        id: 'mapbox.streets',
        accessToken: 'pk.eyJ1IjoidG9tYWhvY2siLCJhIjoiY2pmYmgydHJnMzMwaTJ3azduYzI2eGZteiJ9.4Z0iB0Pgbb3M_8t9VG10kQ'
    }).addTo(mymap);


    // var map = new mapboxgl.Map({
    //     container: 'map',
    //     style: 'mapbox://styles/mapbox/streets-v9',
    //     center: {lat: 40.5050025, lng: -7.9053189},
    //     zoom: 8
    // });

    var url = 'https://fogos.pt/new/fires';
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            console.log(data);
            data = JSON.parse(data);
            if(data.success){
                for(i in data.data){
                    var item = data.data[i];
                    var coords = [ item.lat, item.lng ];

                    var el = document.createElement('div');
                    el.className = 'marker';

                    var marker = L.marker(coords);

                    marker.properties = {};
                    marker.properties.item = item;

                    marker.addTo(mymap).on('click',function (e) {
                        console.log(e);
                        console.log(e.latlng);
                        mymap.setView(e.latlng,10);
                        var item = e.sourceTarget.properties.item;
                        console.log(item);
                        $('.sidebar').css({'display':'flex'});
                        $('.f-local').text(item.location);
                        $('.f-man').text(item.man);
                        $('.f-aerial').text(item.aerial);
                        $('.f-terrain').text(item.terrain);
                        $('.f-status').text(item.status);
                        $('.f-start').text(item.date + ' ' + item.hour);

                        status(item.id);
                        plot(item.id);
                    });


                    // var marker = new mapboxgl.Marker(el)
                    //     .setLngLat(coords)
                    //     .addTo(map);


                    // var marker = new mapboxgl.Marker(el)
                    //     .setLngLat(coords)
                    //     .setPopup(new mapboxgl.Popup({ offset: 25 }) // add popups
                    //         .setHTML('<h3>title</h3><p>desc</p>'))
                    //     .addTo(map);
                    //
                    // var el = marker.getElement();
                    //     el.classList.add(item.statusColor);

                    // console.log(el);


                    // console.log(marker);
                }
            }


        }
    });
});

function plot(id) {
    var url = 'https://fogos.pt/fires/data?id=' + id;
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
                for (d in data.data) {
                    labels.push(data.data[d].label);
                    man.push(data.data[d].man);
                    terrain.push(data.data[d].terrain);
                    aerial.push(data.data[d].aerial);
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
                console.log(myLineChart);
            } else {
                $('#info').find('canvas').remove();
                $('#info').append('<p>Não há dados disponiveis</p> ');
            }
        }
    });

}


function status(id) {
    var url = 'https://fogos.pt/fires/status?id=' + id;
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            data = JSON.parse(data);
            direction = ['l', 'r'];
            if (data.success) {
                for (i in data.data) {
                    dir = direction[i % 2];
                    content = '<li>';
                    content += '<div class="direction-' + dir + '">';
                    content += '<div class="flag-wrapper">';
                    content += '<span class="flag">' + data.data[i].status + '</span>';
                    content += '<span class="time-wrapper"><span class="time">' + data.data[i].label + '</span></span>';
                    content += '</div>';
                    content += '</div>';
                    content += '</li>';
                    $('.timeline').append(content);

                }
            }
        }
    });

}