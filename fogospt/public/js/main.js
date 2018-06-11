$(document).ready(function () {
    const messaging = firebase.messaging();

    messaging.onMessage(function(payload) {
        toastr.warning(payload.notification.body);
    });

    var mymap = L.map('map').setView([40.5050025, -7.9053189], 7);

    L.tileLayer('https://api.mapbox.com/styles/v1/fogospt/cjgppvcdp00aa2spjclz9sjst/tiles/256/{z}/{x}/{y}?access_token=pk.eyJ1IjoiZm9nb3NwdCIsImEiOiJjamZ3Y2E5OTMyMjFnMnFxbjAxbmt3bmdtIn0.xg1X-A17WRBaDghhzsmjIA', {
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
        maxZoom: 18,
        id: 'mapbox.streets',
        accessToken: 'pk.eyJ1IjoidG9tYWhvY2siLCJhIjoiY2pmYmgydHJnMzMwaTJ3azduYzI2eGZteiJ9.4Z0iB0Pgbb3M_8t9VG10kQ'
    }).addTo(mymap);

    addRisk(mymap);
    mymap.on('click', function (e) {
        mymap.setView(e.latlng);
        window.history.pushState('obj', 'Fogos.pt', '/');
        $('.sidebar').removeClass('active');
        $('#map').find('.fa-map-marker-alt').removeClass('active').addClass('fa-map-marker').removeClass('fa-map-marker-alt');
    });

    var res = window.location.pathname.match(/^\/fogo\/(\d+)/);
    if (res && res.length === 2) {
        plot(res[1]);
    }

    window.fogosLayers = [];
    window.fogosLayers[3] = L.layerGroup();
    window.fogosLayers[4] = L.layerGroup();
    window.fogosLayers[5] = L.layerGroup();
    window.fogosLayers[6] = L.layerGroup();
    window.fogosLayers[7] = L.layerGroup();
    window.fogosLayers[8] = L.layerGroup();
    window.fogosLayers[9] = L.layerGroup();
    window.fogosLayers[10] = L.layerGroup();
    window.fogosLayers[11] = L.layerGroup();
    window.fogosLayers[12] = L.layerGroup();

    var url = 'https://fogos.pt/new/fires';
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            data = JSON.parse(data);
            if (data.success) {
                for (i in data.data) {
                    addMaker(data.data[i], mymap)
                }

                var obj = {};
                obj["Despacho"] = window.fogosLayers[3];
                obj["Despacho de 1º Alerta"] = window.fogosLayers[4];
                obj["Chegada ao TO"] = window.fogosLayers[6];
                obj["Em Curso"] = window.fogosLayers[5];
                obj["Em Resolução"] = window.fogosLayers[7];
                obj["Conclusão"] = window.fogosLayers[8];
                obj["Vigilância"] = window.fogosLayers[9];
                obj["Encerrada"] = window.fogosLayers[10];
                obj["Falso Alarme"] = window.fogosLayers[11];
                obj["Falso Alerta"] = window.fogosLayers[12];

                obj["Despacho"].addTo(mymap);
                obj["Despacho de 1º Alerta"].addTo(mymap);
                obj["Chegada ao TO"].addTo(mymap);
                obj["Em Curso"].addTo(mymap);
                obj["Em Resolução"].addTo(mymap);
                obj["Conclusão"].addTo(mymap);
                obj["Vigilância"].addTo(mymap);
                obj["Encerrada"].addTo(mymap);
                obj["Falso Alarme"].addTo(mymap);
                obj["Falso Alerta"].addTo(mymap);

                layerControl2 = L.control.layers(null, obj, {position: 'topright'});

                layerControl2.addTo(mymap);
                $controls = $(layerControl2.getContainer());
                $controls.find('a').css({'background-image': 'none', 'font-size': '33px', 'text-align': 'center', 'color':'#333333'}).append('<i class="fas fa-map-marker"></i>');

                var cloudLayer = L.OWM.cloudsClassic({legendPosition: 'bottomright',showLegend: true, opacity: 0.5, appId: '793b3a933c50946491eeb8aad4339ad2'});
                var precLayer = L.OWM.precipitationClassic({legendPosition: 'bottomright',showLegend: true, opacity: 0.5, appId: '793b3a933c50946491eeb8aad4339ad2'});
                var rainLayer = L.OWM.rainClassic({legendPosition: 'bottomright',showLegend: true, opacity: 0.5, appId: '793b3a933c50946491eeb8aad4339ad2'});
                var pressureLayer = L.OWM.pressure({legendPosition: 'bottomright',showLegend: true, opacity: 0.5, appId: '793b3a933c50946491eeb8aad4339ad2'});
                var tempLayer = L.OWM.temperature({legendPosition: 'bottomright',showLegend: true, opacity: 0.5, appId: '793b3a933c50946491eeb8aad4339ad2'});
                var windLayer = L.OWM.wind({legendPosition: 'bottomright', showLegend: true, opacity: 0.5, appId: '793b3a933c50946491eeb8aad4339ad2'});


                var baseMaps = {
                    'Temperatura': tempLayer,
                    "Precipitação": precLayer,
                    "Nuvens": cloudLayer,
                    "Pressão": pressureLayer,
                    "Vento": windLayer,
                    "Chuva" : rainLayer
                };

                // var layerControl = L.control.layers(baseMaps, overlayMaps).addTo(map);



                // //
                // // url = "https://tile.openweathermap.org/map/temp_new/{z}/{x}/{y}.png?appid=793b3a933c50946491eeb8aad4339ad2";
                // // prec_url = "https://tile.openweathermap.org/map/precipitation_new/{z}/{x}/{y}.png?appid=793b3a933c50946491eeb8aad4339ad2";
                // // clouds_url = "https://tile.openweathermap.org/map/clouds_new/{z}/{x}/{y}.png?appid=793b3a933c50946491eeb8aad4339ad2";
                // // pressure_url = "https://tile.openweathermap.org/map/pressure_new/{z}/{x}/{y}.png?appid=793b3a933c50946491eeb8aad4339ad2";
                // // wind_url = "https://tile.openweathermap.org/map/wind_new/{z}/{x}/{y}.png?appid=793b3a933c50946491eeb8aad4339ad2";
                // //
                // // var tempLayer = L.tileLayer(url, {});
                // // var precLayer = L.tileLayer(prec_url, {});
                // // var cloudLayer = L.tileLayer(clouds_url, {});
                // // var pressureLayer = L.tileLayer(pressure_url, {});
                // // var windLayer = L.tileLayer(wind_url, {});
                //
                // var baseMaps = {
                //     'Temperatura': tempLayer,
                //     "Precipitação": precLayer,
                //     "Nuvens": cloudLayer,
                //     "Pressão": pressureLayer,
                //     "Vento": windLayer,
                // };



                L.control.layers(baseMaps,null,{collapsed:false}).addTo(mymap);
            }
        }
    });


});

function addMaker(item, mymap) {
    var coords = [item.lat, item.lng];

    var el = document.createElement('div');
    el.className = 'marker';

    var marker = L.marker(coords);

    marker.properties = {};
    marker.properties.item = item;

    marker.setIcon(L.divIcon({
        className: 'count-icon-emergency',
        html: '<i class="fas fa-map-marker map-marker status-' + item.statusCode + '"></i>',
        iconSize: [40, 40]
    }));

    window.fogosLayers[item.statusCode].addLayer(marker);

    marker.addTo(mymap);

    marker.on('click', function (e) {
        $('#map').find('.fa-map-marker-alt').removeClass('active').addClass('fa-map-marker').removeClass('fa-map-marker-alt');

        mymap.setView(e.latlng, 10);

        var $icon = $(e.target._icon);
        $icon.find('i').addClass('active');
        $icon.find('i').removeClass('fa-map-marker');
        $icon.find('i').addClass('fa-map-marker-alt');

        var item = e.sourceTarget.properties.item;
        $('.sidebar').addClass('active').scrollTop(0);
        $('.f-local').text(item.location);
        $('.f-man').text(item.man);
        $('.f-aerial').text(item.aerial);
        $('.f-terrain').text(item.terrain);
        $('.f-nature').text(item.natureza);
        $('.f-start').text(item.date + ' ' + item.hour);

        window.history.pushState('obj', 'newtitle', '/fogo/' + item.id);
        status(item.id);
        plot(item.id);
        danger(item.id);
        meteo(item.id);
        addPageview();
    });

}

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
            } else {
                $('#info').find('canvas').remove();
                $('#info').append('<p>Não há dados disponiveis</p> ');
            }
        }
    });

}


function status(id) {
    $('#status').empty();
    var url = '/views/status/' + id;
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            $('.f-status').html(data);
        }
    });
}

function danger(id) {
    var url = '/views/risk/' + id;
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            $('.f-danger').html(data);
        }
    });

}

function meteo(id) {
    var url = '/views/meteo/' + id;
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            $('.f-meteo').html(data);
        }
    });

}


function getColor(d) {
    return d === 1 ? '#509e2f' :
        d === 2 ? '#ffe900' :
            d === 3 ? '#e87722' :
                d === 4 ? '#cb333b' :
                    d === 5 ? '#6f263d' :
                        'rgb(255,  255,  255)';
}


function addRisk(mymap) {
    // lel
    var url = 'https://fogos.pt/v1/risk-today';
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            data = JSON.parse(data);
            if (data.success) {
                var riscoHoje = L.geoJson(concelhos, {
                    style: function (feature) {
                        var d = data.data.local[feature.properties.DICO].data.rcm;
                        return {weight: 1.0, color: '#666', fillOpacity: 0.6, fillColor: getColor(d)};
                    },
                });

                // for phantom
                if(getParameterByName('risk')){
                    riscoHoje.addTo(mymap);
                    $('main #map .map-marker').hide();
                }

                var url = 'https://fogos.pt/v1/risk-tomorrow';
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function (data) {
                        data = JSON.parse(data);
                        if (data.success) {
                            var riscoTomorrow = L.geoJson(concelhos, {
                                style: function (feature) {
                                    var d = data.data.local[feature.properties.DICO].data.rcm;
                                    return {weight: 1.0, color: '#666', fillOpacity: 0.6, fillColor: getColor(d)};
                                },
                            });


                            // for phantom
                            if(getParameterByName('risk-tomorrow')){
                                riscoTomorrow.addTo(mymap);
                                $('main #map .map-marker').hide();
                            }

                            var url = 'https://fogos.pt/v1/risk-after';
                            $.ajax({
                                url: url,
                                method: 'GET',
                                success: function (data) {
                                    data = JSON.parse(data);
                                    if (data.success) {
                                        var riscoAfter = L.geoJson(concelhos, {
                                            style: function (feature) {
                                                var d = data.data.local[feature.properties.DICO].data.rcm;
                                                return {weight: 1.0, color: '#666', fillOpacity: 0.6, fillColor: getColor(d)};
                                            },
                                        });


                                        var baseMaps = {
                                            'Risco Hoje': riscoHoje,
                                            'Risco Amanhã': riscoTomorrow,
                                            'Risco Depois': riscoAfter,
                                        };

                                        L.control.layers(baseMaps,null,{collapsed:false}).addTo(mymap);
                                    }
                                }
                            });

                        }
                    }
                });

            }
        }
    });
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

function addPageview() {
    if (window.ga) {
        if ("ga" in window) {
            var tracker = window.ga.getAll()[0];
            if (tracker)
                tracker.send("pageview");
        }
    }
}

function extend() {
    for (var o = {}, i = 0; i < arguments.length; i++) {
        for (var k in arguments[i]) {
            if (arguments[i].hasOwnProperty(k)) {
                o[k] = arguments[i][k].constructor === Object ? extend(o[k] || {}, arguments[i][k]) : arguments[i][k];
            }
        }
    }
    return o;
}