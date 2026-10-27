// Madeira map. Shared helpers come from fogos-core.js.
window.FOGOS_CARD_PREFIX = '/madeira';

$.ajaxSetup({ headers: { "FPTSC": "xw2gfca9l7" } });

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
                    $('.notification-container').find('i').removeClass('far').addClass('fas')
                    store.set('fire-' + window.location.pathname.split('/')[2], true);
                } else {
                    toastr.error('Ocorreu um erro');
                    store.set('fire-' + window.location.pathname.split('/')[2], false);
                }
            },
        });
    });

    var mymap = L.map('map').setView([32.753498, -16.982694], 10)

    var base = fogosBaseLayers();
    var normalLayer = base.normal.addTo(mymap);
    var satLayer = base.satellite;

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

    if (window.FogosSat) FogosSat.installMain(mymap, null, { defaultOn: false });

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

    var url = 'https://source.fogos.pt/v1/madeira/fires'
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            if (typeof data === 'string') data = JSON.parse(data)
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

                L.control.layers(baseLayers, overlayLayers, {collapsed: false, position: 'topright'}).addTo(mymap)
            }
        }
    })

})
const BASE_SIZE = 22
var DATA_FIRES = {number: 0, topImportance: 0, average: 0}


function addMaker(item, mymap) {
    console.log(item);
    var x = randomGeo(parseFloat(item.lat), parseFloat(item.lng))
    var coords = [x['latitude'], x['longitude']]

    var el = document.createElement('div')
    el.className = 'marker'

    var marker = L.marker(coords)

    marker.properties = {}
    marker.properties.item = item

    isActive = window.location.pathname.split('/')[3]

    console.log(isActive)

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
    if(notificationsAuth){
        $('.click-notification').css({'display':'inline-block'});
        let notifyFire = store.get('fire-' + item.id);
        if(notifyFire){
            $('.notification-container').find('i').removeClass('far').addClass('fas')
        }

    }

    iconHtml += '"'
    if (['3111', '3109', '4335'].indexOf(String(item.naturezaCode)) !== -1) {
        iconHtml += ' style="opacity:0.6"'
    }
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

    marker.addTo(mymap)
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
        console.log(item)
        $('.sidebar').addClass('active').scrollTop(0)
        $('.f-local').text(item.location)
        $('.f-man').text(item.man)
        $('.f-aerial').text(item.aerial)
        $('.f-terrain').text(item.terrain)
        $('.f-nature').text(item.natureza)
        $('.f-start').text(item.date + ' ' + item.hour)
        $('.click-notification').data('id', item.id)

        if(notificationsAuth){
            $('.notification-container').css({'display':'inline-block'});
            let notifyFire = store.get('fire-' + item.id);
            if(notifyFire){
                $('.notification-container').find('i').removeClass('far').addClass('fas')
            } else {
                $('.notification-container').find('i').removeClass('fas').addClass('far')
            }
        }


        window.history.pushState('obj', 'newtitle', '/madeira/fogo/' + item.id)
        status(item.id)
        plot(item.id)
        danger(item.id)
        meteo(item.id)
        extra(item.id)
        addPageview()
    })

}

function plot(id) {
    var url = 'https://source.fogos.pt/v1/madeira/fires/data?id=' + id
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            if (typeof data === 'string') data = JSON.parse(data)
            if (data.success && data.data.length) {
                fogosResourcesChart(document.getElementById('myChart'), data.data)
            } else {
                fogosResourcesChartEmpty('#info')
            }
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
            if (typeof data === 'string') data = JSON.parse(data)
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
                    document.getElementById('map').classList.add('risk-layer-ready')
                }

                var url = 'https://source.fogos.pt/v1/risk-tomorrow'
                $.ajax({
                    url: url,
                    method: 'GET',
                    success: function (data) {
                        if (typeof data === 'string') data = JSON.parse(data)
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
                                document.getElementById('map').classList.add('risk-layer-ready')
                            }

                            var url = 'https://source.fogos.pt/v1/risk-after'
                            $.ajax({
                                url: url,
                                method: 'GET',
                                success: function (data) {
                                    if (typeof data === 'string') data = JSON.parse(data)
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


