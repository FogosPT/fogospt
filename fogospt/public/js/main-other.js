$.ajaxSetup({ headers: { "FPTSC": "xw2gfca9l7" } });
var layerControl2 = null;

$(document).ready(function () {
    const messaging = firebase.messaging()

    messaging.onMessage(function (payload) {
        toastr.warning(payload.notification.body)
    })


    $('.click-notification').on('click', function (e) {
        $that = $(e.currentTarget);

        const url = '/notifications/subscribe';

        const topic = $that.data('value');
        const data = {
            'token': store.get('token'),
            'topic': $that.data('id')
        };

        $.ajax({
            type: "POST",
            url: url,
            data: data,
            success: function (data) {
                if (data.success) {
                    toastr.success('Registado com sucesso');
                    $('.notification-container').find('i').removeClass('far').addClass('fas')
                    store.set('fire-' + window.location.pathname.split('/')[2], true);
                } else {
                    toastr.error('Ocorreu um erro');
                    store.set('fire-' + window.location.pathname.split('/')[2], false);
                }
            },
        });
    });

    var mymap = L.map('map').setView([40.5050025, -7.9053189], 7)

    if (getParameterByName('icao')) {
        addPlane(getParameterByName('icao'), mymap);
    }


    var base = fogosBaseLayers();
    var normalLayer = base.normal.addTo(mymap);
    var satLayer = base.satellite;

    var xx = {
        'Normal': normalLayer,
        'Satélite': satLayer
    }

    L.control.layers(xx, {}, {
        collapsed: false,
        position: 'topleft'
    }).addTo(mymap)


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

    var obj = getNewFires(mymap);


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
    }


    var overlayLayers = {
        'Temperatura': tempLayer,
        'Pressão': pressureLayer,
        'Vento': windLayer,
        'Precipitação': precLayer,
        'Nuvens': cloudLayer
    }

    L.control.layers(baseLayers, overlayLayers, {
        collapsed: false,
        position: 'topright'
    }).addTo(mymap)


    // Lightnings — defer the /lightnings fetch until the user actually
    // enables the layer in the control. Most visitors never do, and the
    // upstream IPMA scrape is expensive even when cached.
    window.lightningLayer = [L.layerGroup()]
    var lightningLoaded = false

    window.lightningLayer[0].on('add', function () {
        if (lightningLoaded) return;
        lightningLoaded = true;
        $.ajax({
            url: '/lightnings',
            dataType: 'json',
            method: 'GET',
            success: function (data) {
                for (var i in data.data) {
                    var date = new Date(data.data[i].timestamp);
                    var hours = Math.floor((new Date() - date) / 3600000);
                    if (hours <= 24 && insidePT([data.data[i].payload.longitude, data.data[i].payload.latitude])) {
                        addLightning(data.data[i], mymap);
                    }
                }
            },
            error: function () { lightningLoaded = false; }
        })
    });

    L.control.layers(null, { 'Descargas Elétricas': window.lightningLayer[0] }, {
        position: 'topleft',
        collapsed: false,
    }).addTo(mymap)

    // NASA FIRMS hotspots — heavy upstream fetch, so we only hit /v1/modis and
    // /v1/viirs when the user enables the layer in the control. Each layer
    // fetches once per session and reuses the markers afterwards.
    window.modisLayer = [L.layerGroup(), L.layerGroup()]
    var hotspotLoaded = [false, false]

    function loadHotspots(index, url, addPoint) {
        if (hotspotLoaded[index]) return;
        hotspotLoaded[index] = true;
        $.ajax({
            url: url,
            dataType: 'json',
            method: 'GET',
            success: function (data) {
                for (var i in data) {
                    if (data[i].latitude && data[i].longitude
                        && insidePT([data[i].longitude, data[i].latitude])) {
                        addPoint(data[i], mymap);
                    }
                }
            },
            error: function () { hotspotLoaded[index] = false; }
        })
    }

    window.modisLayer[0].on('add', function () {
        loadHotspots(0, '/v1/modis', addModisPoint);
    });
    window.modisLayer[1].on('add', function () {
        loadHotspots(1, '/v1/viirs', addVIIRSPoint);
    });

    L.control.layers(null, { 'MODIS': window.modisLayer[0] }, {
        position: 'topleft',
        collapsed: false,
    }).addTo(mymap)

    L.control.layers(null, { 'VIIRS': window.modisLayer[1] }, {
        position: 'topleft',
        collapsed: false,
    }).addTo(mymap)

    $.ajax({
        type: "GET",
        url: 'https://source.fogos.pt/v1/warnings/site',
        success: function (data) {
            if (data.success && data.data[0] && data.data[0].active) {
                $('#warning-site').find('.modal-body').empty().append($('<p>').text(data.data[0].text));
                $('#warning-site').modal('show');
            }
        },
    });


    $('.js-refresh').on('click',function(){
        getNewFires(mymap, true);
    });

    // Skip refreshes while the tab is hidden; catch up as soon as it's
    // visible again (saves upstream calls from background tabs).
    var lastFiresRefresh = Date.now();
    function refreshFires() {
        if (document.hidden) return;
        lastFiresRefresh = Date.now();
        toastr.success(window.trans.map.updating, {timeOut: 1000});
        getNewFires(mymap, true);
    }
    setInterval(refreshFires, 60000);
    document.addEventListener('visibilitychange', function () {
        if (!document.hidden && Date.now() - lastFiresRefresh >= 60000) refreshFires();
    });

})



function addLightning(data, mymap) {
    var marker = L.marker([data.payload.latitude, data.payload.longitude])

    marker.properties = {}

    iconHtml = '<i class="fas fa-bolt" style="color: #F96E5B"></i>'

    marker.sizeFactor = 1

    marker.setIcon(L.divIcon({
        className: 'count-icon-emergency',
        html: iconHtml,
        iconSize: [20, 20]
    }))

    window.lightningLayer[0].addLayer(marker)
}



const BASE_SIZE = 22
var DATA_FIRES = {
    number: 0,
    topImportance: 0,
    average: 0
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
        iconHtml += parseInt(item.statusCode, 10)
    }
    if (isActive && isActive === item.id) {
        iconHtml += ' active'
        mymap.setView(coords, 10)
    }

    notificationsAuth = store.get('notificationsAuth');
    if (notificationsAuth) {
        $('.notification-container').css({
            'display': 'inline-block'
        });
        $('.click-notification').css({
            'display': 'inline-block'
        });
        let notifyFire = store.get('fire-' + item.id);
        if (notifyFire) {
            $('.notification-container').find('i').removeClass('far').addClass('fas')
        }

    }

    iconHtml += '"'
    iconHtml += ' id="' + escapeFireText(item.id) + '"></i>'
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

    marker.addTo(mymap);
    marker.id = item.id

    marker.bindTooltip(buildFireTooltip(item), {
        direction: 'top',
        offset: [0, -8],
        opacity: 0.95,
        className: 'fire-tooltip',
        sticky: false
    });

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

        var momentDate = dayjs.unix(item.updated.sec).format('HH:mm DD-MM-YYYY');

        // Upstream (ANEPC) text — escape everything that lands in .html().
        var location = '<a href="https://www.google.com/maps/search/' + encodeURIComponent(item.lat + ',' + item.lng) + '" target="_blank" rel="noopener"><i class="far fa-map"></i></a> ' + escapeFireText(item.lat + ',' + item.lng);

        var locationText = escapeFireText(item.location);
        if(item.detailLocation){
            locationText += ' - ' + escapeFireText(item.detailLocation);
        }

        locationText += ' <a href="/' + locale + '/fogo/' + encodeURIComponent(item.id) + '/detalhe"><i class="fas fa-link"></i></a>';
        $('.sidebar').addClass('active').scrollTop(0)
        $('.f-local').html(locationText)
        $('.f-man').text(item.man)
        $('.f-aerial').text(item.aerial)
        $('.f-terrain').text(item.terrain)
        $('.f-location').html(location)
        $('.f-nature').text(item.natureza)
        $('.f-update').text(momentDate)
        $('.f-start').text(item.date + ' ' + item.hour)
        $('.click-notification').data('id', item.id)

        if (notificationsAuth) {
            $('.notification-container').css({
                'display': 'inline-block'
            });
            let notifyFire = store.get('fire-' + item.id);
            if (notifyFire) {
                $('.notification-container').find('i').removeClass('far').addClass('fas')
            } else {
                $('.notification-container').find('i').removeClass('fas').addClass('far')
            }
        }


        window.history.pushState('obj', 'newtitle', '/fogo/' + item.id)
        status(item.id)
        plot(item.id)
        danger(item.id)
        meteo(item.id)
        extra(item.id)
        twitter(item.id)
        shares(item.id)
        addPageview()
    })

}

function plot(id) {
    var url = 'https://source.fogos.pt/fires/data?id=' + id
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            if (data.success && data.data.length) {
                fogosResourcesChart(document.getElementById('myChart'), data.data)
            } else {
                fogosResourcesChartEmpty('#info')
            }
        }
    })

}


function twitter(id) {
    var url = '/' + locale + '/views/twitter/' + id
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            $('.f-twitter').html(data)
        }
    })
}


function addRisk(mymap) {
    // lel
    var url = 'https://source.fogos.pt/v1/risk-today'
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            if (data.success) {
                var riskToday = L.geoJson(concelhos, {
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

                // for phantom
                if (getParameterByName('risk')) {
                    riskToday.addTo(mymap)
                    $('main #map .map-marker').hide()
                }

                var url = 'https://source.fogos.pt/v1/risk-tomorrow'
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function (data) {
                        if (data.success) {
                            var riskTomorrow = L.geoJson(concelhos, {
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

                            // for phantom
                            if (getParameterByName('risk-tomorrow')) {
                                riskTomorrow.addTo(mymap)
                                $('main #map .map-marker').hide()
                            }

                            var url = 'https://source.fogos.pt/v1/risk-after'
                            $.ajax({
                                url: url,
                                method: 'GET',
                                success: function (data) {
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
                                            'Risco Hoje': riskToday,
                                            'Risco Amanhã': riskTomorrow,
                                            'Risco Depois': riskAfter
                                        }

                                        //var riskLayerControl = L.control.groupedLayers(null, riskOverlays, riskOptions);
                                        //map.addControl(riskLayerControl);
                                        L.control.layers([],baseMaps, {
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


function getNewFires(mymap, refresh = false)
{
    if(refresh){
        layerControl2.remove();
        window.fogosLayers[3].remove()
        window.fogosLayers[4].remove()
        window.fogosLayers[5].remove()
        window.fogosLayers[6].remove()
        window.fogosLayers[7].remove()
        window.fogosLayers[8].remove()
        window.fogosLayers[9].remove()
        window.fogosLayers[10].remove()
        window.fogosLayers[11].remove()
        window.fogosLayers[12].remove()
        window.fogosLayers[80].remove()

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
    }

    var url = 'https://source.fogos.pt/v2/incidents/active?otherfire=1&all=1'
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
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

                layerControl2 = L.control.layers(null, obj, {
                    position: 'topright'
                })

                layerControl2.addTo(mymap)
                $controls = $(layerControl2.getContainer())
                $controls.find('a').css({
                    'background-image': 'none',
                    'font-size': '33px',
                    'text-align': 'center',
                    'color': '#333333'
                }).append('<i class="fas fa-map-marker"></i>')
            }
        }
    })

}


