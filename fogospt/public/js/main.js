$(document).ready(function () {
    const messaging = firebase.messaging()

    messaging.onMessage(function (payload) {
        toastr.warning(payload.notification.body)
    })

    $('.click-notification').on('click', function(e){
        $that = $(e.currentTarget);

        const url = '/notifications/subscribe';

        const topic =  $that.data('value');
        const data   = {
            'token' : store.get('token'),
            'topic' : $that.data('id')
        };

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function(data){
                if(data.success){
                    toastr.success('Registado com sucesso');
                    store.set($that.data('value'), true);
                    sendEvent('notifications', 'subscribed', topic );
                } else {
                    toastr.error('Ocorreu um erro');
                    store.set($that.data('value'), false);
                    sendEvent('notifications', 'subscribed error', topic );
                }
            },
        });
    });

    var mymap = L.map('map').setView([40.5050025, -7.9053189], 7)

    var normalLayer = L.tileLayer('https://api.mapbox.com/styles/v1/fogospt/cjgppvcdp00aa2spjclz9sjst/tiles/256/{z}/{x}/{y}@2x?access_token=pk.eyJ1IjoiZm9nb3NwdCIsImEiOiJjamZ3Y2E5OTMyMjFnMnFxbjAxbmt3bmdtIn0.xg1X-A17WRBaDghhzsmjIA', {
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
        maxZoom: 18,
        id: 'mapbox.streets',
        accessToken: 'pk.eyJ1IjoidG9tYWhvY2siLCJhIjoiY2pmYmgydHJnMzMwaTJ3azduYzI2eGZteiJ9.4Z0iB0Pgbb3M_8t9VG10kQ'
    }).addTo(mymap)

    var satLayer = L.tileLayer('https://api.mapbox.com/styles/v1/fogospt/cjksgciqsctfg2rp9x9uyh37g/tiles/256/{z}/{x}/{y}@2x?access_token=pk.eyJ1IjoiZm9nb3NwdCIsImEiOiJjamZ3Y2E5OTMyMjFnMnFxbjAxbmt3bmdtIn0.xg1X-A17WRBaDghhzsmjIA', {
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
        maxZoom: 18,
        id: 'mapbox.streets',
        accessToken: 'pk.eyJ1IjoidG9tYWhvY2siLCJhIjoiY2pmYmgydHJnMzMwaTJ3azduYzI2eGZteiJ9.4Z0iB0Pgbb3M_8t9VG10kQ'
    })

    mymap.attributionControl.addAttribution('Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>')

    var xx = {
        'Normal': normalLayer,
        'Satélite': satLayer
    }

    L.control.layers(xx, {}, {collapsed: false, position: 'topleft'}).addTo(mymap)

    addRisk(mymap)
    mymap.on('click', function (e) {
        mymap.setView(e.latlng)
        window.history.pushState('obj', 'Fogos.pt', '/')

        var previouslyActive = $('#map').find('.active')
        if (previouslyActive.length) {
            changeElementSizeById(previouslyActive[0].id, (parseFloat(previouslyActive[0].style.height) - 48) * BASE_SIZE)
            previouslyActive.removeClass('active')
        }

        $('.sidebar').removeClass('active')
        // $('#map').find('.active').removeClass('active');
    })

    var res = window.location.pathname.match(/^\/fogo\/(\d+)/)
    if (res && res.length === 2) {
        plot(res[1])
    }

    window.fogosLayers = []
    window.fogosLayers[3] = L.layerGroup()
    window.fogosLayers[4] = L.layerGroup()
    window.fogosLayers[5] = L.layerGroup()
    window.fogosLayers[6] = L.layerGroup()
    window.fogosLayers[7] = L.layerGroup()
    window.fogosLayers[8] = L.layerGroup()
    window.fogosLayers[9] = L.layerGroup()
    window.fogosLayers[10] = L.layerGroup()
    window.fogosLayers[11] = L.layerGroup()
    window.fogosLayers[12] = L.layerGroup()
    window.fogosLayers[80] = L.layerGroup()

    var url = 'https://api-lb.fogos.pt/new/fires'
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            data = JSON.parse(data)
            if (data.success) {

                for (i in data.data) {
                    calculateImportanceValue(data.data[i])
                }
                for (i in data.data) {
                    addMaker(data.data[i], mymap)
                }

                var obj = {}
                obj['Despacho'] = window.fogosLayers[3]
                obj['Despacho de 1º Alerta'] = window.fogosLayers[4]
                obj['Chegada ao TO'] = window.fogosLayers[6]
                obj['Em Curso'] = window.fogosLayers[5]
                obj['Em Resolução'] = window.fogosLayers[7]
                obj['Conclusão'] = window.fogosLayers[8]
                obj['Vigilância'] = window.fogosLayers[9]
                obj['Encerrada'] = window.fogosLayers[10]
                obj['Falso Alarme'] = window.fogosLayers[11]
                obj['Falso Alerta'] = window.fogosLayers[12]

                obj['Despacho'].addTo(mymap)
                obj['Despacho de 1º Alerta'].addTo(mymap)
                obj['Chegada ao TO'].addTo(mymap)
                obj['Em Curso'].addTo(mymap)
                obj['Em Resolução'].addTo(mymap)
                obj['Conclusão'].addTo(mymap)
                obj['Vigilância'].addTo(mymap)
                obj['Encerrada'].addTo(mymap)
                obj['Falso Alarme'].addTo(mymap)
                obj['Falso Alerta'].addTo(mymap)

                layerControl2 = L.control.layers(null, obj, {position: 'topright'})

                layerControl2.addTo(mymap)
                $controls = $(layerControl2.getContainer())
                $controls.find('a').css({
                    'background-image': 'none',
                    'font-size': '33px',
                    'text-align': 'center',
                    'color': '#333333'
                }).append('<i class="fas fa-map-marker"></i>')

                var cloudLayer = L.OWM.cloudsClassic({
                    legendPosition: 'bottomright',
                    showLegend: true,
                    opacity: 0.5,
                    appId: '793b3a933c50946491eeb8aad4339ad2'
                })
                var precLayer = L.OWM.precipitationClassic({
                    legendPosition: 'bottomright',
                    showLegend: true,
                    opacity: 0.5,
                    appId: '793b3a933c50946491eeb8aad4339ad2'
                })
                // var rainLayer = L.OWM.rainClassic({
                //     legendPosition: 'bottomright',
                //     showLegend: true,
                //     opacity: 0.5,
                //     appId: '793b3a933c50946491eeb8aad4339ad2'
                // });
                var pressureLayer = L.OWM.pressure({
                    legendPosition: 'bottomleft',
                    showLegend: true,
                    opacity: 0.5,
                    appId: '793b3a933c50946491eeb8aad4339ad2'
                })
                var tempLayer = L.OWM.temperature({
                    legendPosition: 'bottomleft',
                    showLegend: true,
                    opacity: 0.5,
                    appId: '793b3a933c50946491eeb8aad4339ad2'
                })
                var windLayer = L.OWM.wind({
                    legendPosition: 'bottomleft',
                    showLegend: true,
                    opacity: 0.5,
                    appId: '793b3a933c50946491eeb8aad4339ad2'
                })

                var baseLayers = {
                    'Desativar Camadas': L.tileLayer(''),
                    'Temperatura': tempLayer,
                    'Pressão': pressureLayer,
                    'Vento': windLayer
                    // "Chuva": rainLayer
                }

                var overlayLayers = {
                    'Precipitação': precLayer,
                    'Nuvens': cloudLayer
                }

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

                L.control.layers(baseLayers, overlayLayers, {collapsed: false, position: 'topright'}).addTo(mymap)
            }
        }
    })

})
const BASE_SIZE = 22
var DATA_FIRES = {number: 0, topImportance: 0, average: 0}

function calculateImportanceValue(data) {
    const manFactor = 1
    const terrainFactor = 3
    const aerialFactor = 7

    var importance = data.man * manFactor + terrainFactor * terrainFactor + aerialFactor * aerialFactor

    DATA_FIRES.number += 1
    if (DATA_FIRES.topImportance < importance) {
        DATA_FIRES.topImportance = importance
    }

    DATA_FIRES.average = (DATA_FIRES.average * (DATA_FIRES.number - 1) + importance) / (DATA_FIRES.number)

    data.importance = importance
}

function getPonderatedImportanceFactor(importance, statusCode) {
    var importanceSize

    // check for fake alarm's or calls
    if (statusCode == 11 || statusCode == 12) {
        return 0.6
    }
    if (importance > DATA_FIRES.average) {
        var topPercentage = importance / DATA_FIRES.topImportance
        topPercentage *= 2.3
        topPercentage += 0.5

        var avgPercentage = DATA_FIRES.average / importance

        importanceSize = topPercentage - avgPercentage

        if (importanceSize > 1.75) {
            importanceSize = 1.75
        }

        if (importanceSize < 1) {
            importanceSize = 1
        }
    }

    if (importance < DATA_FIRES.average) {
        var avgPercentage = importance / DATA_FIRES.average * 0.8
        if (avgPercentage < 0.5) {
            importanceSize = 0.5
        } else {
            importanceSize = avgPercentage
        }
    }
    return importanceSize
}

function addMaker(item, mymap) {

    var x = randomGeo(item.lat, item.lng)
    var coords = [x['latitude'], x['longitude']]

    var el = document.createElement('div')
    el.className = 'marker'

    var marker = L.marker(coords)

    marker.properties = {}
    marker.properties.item = item

    isActive = window.location.pathname.split('/')[2]

    //Base iconHtml
    iconHtml = '<i class="dot status-'
    if (item.important) {
        iconHtml += '99'
    } else {
        iconHtml += item.statusCode
    }
    if (isActive && isActive === item.id) {
        iconHtml += ' active'
        mymap.setView(coords, 10)
    }

    iconHtml += '"'
    iconHtml += 'id=' + item.id + '></i>'
    var sizeFactor = getPonderatedImportanceFactor(item.importance, item.statusCode)
    marker.sizeFactor = sizeFactor
    var size = sizeFactor * BASE_SIZE

    marker.setIcon(L.divIcon({
        className: 'count-icon-emergency',
        html: iconHtml,
        iconSize: [size, size],
        forceZIndex: item.importance
    }))

    window.fogosLayers[item.statusCode].addLayer(marker)

    marker.addTo(mymap)
    marker.id = item.id

    if (isActive && isActive === item.id) {
        changeElementSizeById(item.id, 48 + sizeFactor)
    } else {
        changeElementSizeById(item.id, size)
    }

    marker.on('click', function (e) {
        var previouslyActive = $('#map').find('.active')
        if (previouslyActive.length) {
            changeElementSizeById(previouslyActive[0].id, (parseFloat(previouslyActive[0].style.height) - 48) * BASE_SIZE)
            previouslyActive.removeClass('active')
        }
        changeElementSizeById(marker.id, 48 + marker.sizeFactor)
        mymap.setView(e.latlng, 10)

        var $icon = $(e.target._icon)
        $icon.find('i').addClass('active')

        var item = e.sourceTarget.properties.item
        $('.sidebar').addClass('active').scrollTop(0)
        $('.f-local').text(item.location)
        $('.f-man').text(item.man)
        $('.f-aerial').text(item.aerial)
        $('.f-terrain').text(item.terrain)
        $('.f-nature').text(item.natureza)
        $('.f-start').text(item.date + ' ' + item.hour)
        $('.click-notification').data('id', item.id)

        window.history.pushState('obj', 'newtitle', '/fogo/' + item.id)
        status(item.id)
        plot(item.id)
        danger(item.id)
        meteo(item.id)
        extra(item.id)
        addPageview()
    })

}

function plot(id) {
    var url = 'https://api-lb.fogos.pt/fires/data?id=' + id
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            data = JSON.parse(data)
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
                            }, {
                                label: 'Aéreos',
                                data: aerial,
                                fill: false,
                                backgroundColor: '#4E88B2',
                                borderColor: '#4E88B2'
                            }]
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

function status(id) {
    $('#status').empty()
    var url = '/views/status/' + id
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            $('.f-status').html(data)
        }
    })
}

function danger(id) {
    var url = '/views/risk/' + id
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            $('.f-danger').html(data)
        }
    })

}

function meteo(id) {
    var url = '/views/meteo/' + id
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            $('.f-meteo').html(data)
        }
    })

}

function extra(id) {
    var url = '/views/extra/' + id
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            if (data && data.length !== 0) {
                $('.f-extra').html(data)
                $('.extra').addClass('active')
            } else {
                $('.extra').removeClass('active')
            }

        }
    })

}

function getColor(d) {
    var color
    switch (d) {
        case 1:
            color = '#509e2f';
            break;
        case 2:
            color = '#ffe900';
            break;
        case 3:
            color = '#e87722';
            break;
        case 4:
            color = '#cb333b';
            break;
        case 5:
            color = '#6f263d'
            break;
        default:
            color = 'rgb(255,  255,  255)';
    }
    return color;
}

function addRisk(mymap) {
    // lel
    var url = 'https://api-lb.fogos.pt/v1/risk-today'
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            data = JSON.parse(data)
            if (data.success) {
                var riskToday = L.geoJson(concelhos, {
                    style: function (feature) {
                        var d = data.data.local[feature.properties.DICO].data.rcm
                        return {weight: 1.0, color: '#666', fillOpacity: 0.6, fillColor: getColor(d)}
                    }
                })

                // for phantom
                if (getParameterByName('risk')) {
                    riskToday.addTo(mymap)
                    $('main #map .map-marker').hide()
                }

                var url = 'https://api-lb.fogos.pt/v1/risk-tomorrow'
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function (data) {
                        data = JSON.parse(data)
                        if (data.success) {
                            var riskTomorrow = L.geoJson(concelhos, {
                                style: function (feature) {
                                    var d = data.data.local[feature.properties.DICO].data.rcm
                                    return {weight: 1.0, color: '#666', fillOpacity: 0.6, fillColor: getColor(d)}
                                }
                            })

                            // for phantom
                            if (getParameterByName('risk-tomorrow')) {
                                riskTomorrow.addTo(mymap)
                                $('main #map .map-marker').hide()
                            }

                            var url = 'https://api-lb.fogos.pt/v1/risk-after'
                            $.ajax({
                                url: url,
                                method: 'GET',
                                success: function (data) {
                                    data = JSON.parse(data)
                                    if (data.success) {
                                        var riskAfter = L.geoJson(concelhos, {
                                            style: function (feature) {
                                                var d = data.data.local[feature.properties.DICO].data.rcm
                                                return {
                                                    weight: 1.0,
                                                    color: '#666',
                                                    fillOpacity: 0.6,
                                                    fillColor: getColor(d)
                                                }
                                            }
                                        })

                                        var baseMaps = {
                                            'Desativar Risco': L.tileLayer(''),
                                            'Risco Hoje': riskToday,
                                            'Risco Amanhã': riskTomorrow,
                                            'Risco Depois': riskAfter

                                        }

                                        //var riskLayerControl = L.control.groupedLayers(null, riskOverlays, riskOptions);
                                        //map.addControl(riskLayerControl);
                                        L.control.layers(baseMaps, null, {
                                            collapsed: false,
                                            position: 'topright'
                                        }).addTo(mymap)
                                    }
                                }
                            })

                        }
                    }
                })

            }
        }
    })
}

function getParameterByName(name, url) {
    if (!url) url = window.location.href
    name = name.replace(/[\[\]]/g, '\\$&')
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url)
    if (!results) return null
    if (!results[2]) return ''
    return decodeURIComponent(results[2].replace(/\+/g, ' '))
}

function addPageview() {
    if (window.ga) {
        if ('ga' in window) {
            var tracker = window.ga.getAll()[0]
            if (tracker)
                tracker.send('pageview')
        }
    }
}

function extend() {
    for (var o = {}, i = 0; i < arguments.length; i++) {
        for (var k in arguments[i]) {
            if (arguments[i].hasOwnProperty(k)) {
                o[k] = arguments[i][k].constructor === Object ? extend(o[k] || {}, arguments[i][k]) : arguments[i][k]
            }
        }
    }
    return o
}

//https://stackoverflow.com/questions/31192451/generate-random-geo-coordinates-within-specific-radius-from-seed-point
//Create random lat/long coordinates in a specified radius around a center point
function randomGeo(latitude, longitude) {
    var radius = 50
    var y0 = latitude
    var x0 = longitude
    var rd = radius / 111300 //about 111300 meters in one degree

    var u = Math.random()
    var v = Math.random()

    var w = rd * Math.sqrt(u)
    var t = 2 * Math.PI * v
    var x = w * Math.cos(t)
    var y = w * Math.sin(t)

    //Adjust the x-coordinate for the shrinking of the east-west distances
    var xp = x / Math.cos(y0)

    var newlat = y + y0
    var newlon = x + x0
    var newlon2 = xp + x0

    return {
        'latitude': newlat.toFixed(5),
        'longitude': newlon2.toFixed(5)
    }
}

function changeElementSizeById(id, size) {
    var markerHtml = document.getElementById(id)

    //Set costum size
    markerHtml.style.height = size + 'px'
    markerHtml.style.width = size + 'px'
}
