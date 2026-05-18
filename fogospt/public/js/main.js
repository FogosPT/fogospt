$.ajaxSetup({ headers: { "FPTSC": "xw2gfca9l7" } });
var locale = window.location.pathname.split('/')[1] || 'pt';

$(document).ready(function () {
    const messaging = firebase.messaging()

    messaging.onMessage(function (payload) {
        toastr.warning(payload.notification.body)
    })


    $('.click-notification').on('click', function (e) {
        $that = $(e.currentTarget);

        const url = '/' + locale + '/notifications/subscribe';

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
                    toastr.success(window.trans.notifications.success);
                    $('.notification-container').find('i').removeClass('far').addClass('fas')
                    store.set('fire-' + window.location.pathname.split('/')[3], true);
                } else {
                    toastr.error(window.trans.notifications.error);
                    store.set('fire-' + window.location.pathname.split('/')[3], false);
                }
            },
        });
    });

    if (getParameterByName('jn')) {
        $('#header').hide();
        $('#map').css({top:0});
    }


    var mymap = L.map('map').setView([40.5050025, -7.9053189], 7)

    if (getParameterByName('icao')) {
        addPlane(getParameterByName('icao'), mymap);
    }

    /*var normalLayer = L.tileLayer('https://api.mapbox.com/styles/v1/{id}/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoiZm9nb3NwdCIsImEiOiJjbDV0YnQza24wZmY1M2pwM3g4eHowZnRoIn0.MxbsPA-TJa-4ouvsnd99mg', {
        attribution: '© <a href="https://www.mapbox.com/about/maps/">Mapbox</a> © <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> <strong><a href="https://www.mapbox.com/map-feedback/" target="_blank">Improve this map</a></strong>',
        tileSize: 512,
        maxZoom: 18,
        zoomOffset: -1,
        id: 'mapbox/streets-v11',
        accessToken: 'pk.eyJ1IjoiZm9nb3NwdCIsImEiOiJjbDV0YnQza24wZmY1M2pwM3g4eHowZnRoIn0.MxbsPA-TJa-4ouvsnd99mg'
    }).addTo(mymap);*/




    var normalLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
    }).addTo(mymap);

    // Hybrid satellite = imagery + transportation (roads) + places & boundaries overlays.
    // Each tile request is a separate Esri request; the LayerGroup just glues them so the
    // panel can toggle them together as a single "base" choice.
    var satelliteLayer = L.layerGroup([
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19,
            attribution: 'Imagery &copy; <a href="https://www.esri.com" target="_blank">Esri</a>, Maxar, Earthstar Geographics, and the GIS User Community'
        }),
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Transportation/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19
        }),
        L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', {
            maxZoom: 19
        })
    ]);

    /*var satLayer = L.tileLayer('https://api.mapbox.com/styles/v1/fogospt/cjksgciqsctfg2rp9x9uyh37g/tiles/256/{z}/{x}/{y}@2x?access_token=pk.eyJ1IjoiZm9nb3NwdCIsImEiOiJjbDV0YnQza24wZmY1M2pwM3g4eHowZnRoIn0.MxbsPA-TJa-4ouvsnd99mg', {
        attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>',
        maxZoom: 18,
        id: 'mapbox.streets',
        accessToken: 'pk.eyJ1IjoidG9tYWhvY2siLCJhIjoiY2pmYmgydHJnMzMwaTJ3azduYzI2eGZteiJ9.4Z0iB0Pgbb3M_8t9VG10kQ'
    })*/

    mymap.attributionControl.addAttribution('Map data &copy; <a href="http://openstreetmap.org" target="_blank">OpenStreetMap</a> contributors, <a target="_blank" href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © - Patrocinado por <a target="_blank" href="https://www.officelan.pt">Officelan</a> <a target="_blank" href="https://www.ptservidor.pt">PTServidor</a> <a target="_blank" href="https://www.mapbox.com">Mapbox</a>')

    // Unified map control panel — single home for all layer/filter toggles.
    var panel = new L.Control.FogosPanel();
    panel.addTo(mymap);
    window.fogosPanel = panel;

    var tp = (window.trans && window.trans.panel) || {};
    panel.registerSection('base', tp.base || 'Base', 'radio');
    panel.addItem('base', 'normal',    window.trans.map.normal,    normalLayer,    true);
    panel.addItem('base', 'satellite', window.trans.map.satellite, satelliteLayer, false);

    panel.registerSection('status', tp.fires || 'Estado dos fogos', 'checkbox');
    panel.registerSection('risk', tp.risk || 'Perigo de Incêndio Rural', 'radio');
    panel.registerSection('satellite', tp.satellite || 'Hotspots satélite', 'checkbox');
    panel.registerSection('ipma', tp.ipma || 'Previsão IPMA', 'radio');

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

    var res = window.location.pathname.match(/\/fogo\/(\d+)/)
    if (res && res.length === 2) {
        var fireId = res[1]
        $('.sidebar').addClass('active').scrollTop(0)
        plot(fireId)
        status(fireId)
        danger(fireId)
        meteo(fireId)
        extra(fireId)
        shares(fireId)
        if (typeof photos === 'function') photos(fireId)
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
    window.fogosLayers[81] = L.layerGroup()

    window.fogosLayers[81].addTo(mymap)

    var obj = getNewFires(mymap);


    var baseLayers = {
    }

    // IPMA AROME forecast overlays. Each product has three regional variants
    // (continent / madeira / azores) — wrapped in a LayerGroup so the panel
    // toggles them as one item. Default time = current model run; WMS GetMap
    // returns the current forecast without needing TIME= explicitly.
    //
    // Each tile carries _legendUrl/_legendLabel so the bottom-left legend
    // control can render the IPMA-provided color scale while the layer is on.
    // Tracks every AROME WMS tile so we can inject reference_time once
    // /v1/ipma-reference-time resolves. Without that param the IPMA WMS
    // returns 404 for every GetMap, so layers are effectively dead until
    // we set it.
    window.fogosIpmaAromeTiles = [];

    function makeIpmaLayer(layerNames, attribution, label, extraOpts) {
        var legendUrl = 'https://mf2.ipma.pt/services?version=1.3.0&service=WMS&request=GetLegendGraphic&sld_version=1.1.0&layer=' +
            encodeURIComponent(layerNames[0]) + '&format=image/png&STYLE=default';
        var group = L.layerGroup(layerNames.map(function (name) {
            var opts = {
                layers: name,
                format: 'image/png',
                transparent: true,
                version: '1.3.0',
                opacity: 0.6,
                attribution: attribution
            };
            if (window.fogosIpmaRefTime) opts.reference_time = window.fogosIpmaRefTime;
            if (extraOpts) {
                for (var k in extraOpts) opts[k] = extraOpts[k];
            }
            var tile = L.tileLayer.wms('https://mf2.ipma.pt/services/', opts);
            tile._legendUrl = legendUrl;
            tile._legendLabel = label;
            window.fogosIpmaAromeTiles.push(tile);
            return tile;
        }));
        group._isIpma = true;
        return group;
    }

    // Resolve the latest AROME run that the WMS will actually render, then
    // backfill reference_time on every AROME tile. Tiles registered before
    // this resolves automatically refresh when setParams triggers a redraw.
    fetch('/v1/ipma-reference-time', { credentials: 'omit' })
        .then(function (r) { return r.ok ? r.json() : null; })
        .then(function (data) {
            if (!data || !data.reference_time) return;
            window.fogosIpmaRefTime = data.reference_time;
            window.fogosIpmaAromeTiles.forEach(function (tile) {
                tile.setParams({ reference_time: data.reference_time });
            });
        })
        .catch(function () { /* layers stay inert if the probe fails */ });
    var IPMA_ATTR = 'Previsão &copy; <a href="https://www.ipma.pt" target="_blank">IPMA</a> (modelo AROME)';

    // Lazy wrapper for the animated wind layer. Fetches the u/v JSON from
    // our cached backend route only on first activation, then builds the
    // L.velocityLayer and hands its lifecycle to Leaflet via add/remove.
    var IpmaWindAnimated = L.Layer.extend({
        _isIpma: true,
        onAdd: function (map) {
            this._map = map;
            var self = this;
            if (this._data) {
                this._attachVelocity();
                return this;
            }
            fetch('/v1/ipma-wind', { credentials: 'omit' })
                .then(function (r) { return r.ok ? r.json() : null; })
                .then(function (data) {
                    if (!data || !Array.isArray(data) || data.length !== 2) return;
                    self._data = data;
                    if (self._map) self._attachVelocity();
                })
                .catch(function () { /* silent */ });
            return this;
        },
        onRemove: function (map) {
            if (this._velocity && map.hasLayer(this._velocity)) {
                map.removeLayer(this._velocity);
            }
            this._velocity = null;
            this._map = null;
            return this;
        },
        _attachVelocity: function () {
            if (!L.velocityLayer || !this._map) return;
            this._velocity = L.velocityLayer({
                displayValues: true,
                displayOptions: {
                    velocityType: window.trans.map.wind,
                    position: 'bottomright',
                    emptyString: '-',
                    angleConvention: 'bearingCW',
                    speedUnit: 'm/s'
                },
                data: this._data,
                maxVelocity: 25,
                velocityScale: 0.005,
                particleAge: 60,
                lineWidth: 1.5,
                particleMultiplier: 1 / 300,
                attribution: IPMA_ATTR
            });
            this._velocity.addTo(this._map);
        }
    });

    panel.addItem('ipma', 'temperature',   window.trans.map.temperature,
        makeIpmaLayer(['arome.2m.temperature.continent', 'arome.2m.temperature.madeira', 'arome.2m.temperature.azores'], IPMA_ATTR, window.trans.map.temperature), false)
    panel.addItem('ipma', 'wind',          window.trans.map.wind,
        makeIpmaLayer(['arome.10m.windintensity.continent', 'arome.10m.windintensity.madeira', 'arome.10m.windintensity.azores'], IPMA_ATTR, window.trans.map.wind), false)
    panel.addItem('ipma', 'windDirection', window.trans.map.windDirection,
        makeIpmaLayer(['arome.10m.windbarbs.continent', 'arome.10m.windbarbs.madeira', 'arome.10m.windbarbs.azores'], IPMA_ATTR, window.trans.map.windDirection,
            { opacity: 1.0, className: 'ipma-windbarbs-tile' }), false)
    panel.addItem('ipma', 'windAnimated', window.trans.map.windAnimated,
        new IpmaWindAnimated(), false)
    panel.addItem('ipma', 'precipitation', window.trans.map.precipitation,
        makeIpmaLayer(['arome.0m.precipitation.continent', 'arome.0m.precipitation.madeira', 'arome.0m.precipitation.azores'], IPMA_ATTR, window.trans.map.precipitation), false)
    panel.addItem('ipma', 'humidity',      window.trans.map.humidity,
        makeIpmaLayer(['arome.2m.relative_humidity.continent', 'arome.2m.relative_humidity.madeira', 'arome.2m.relative_humidity.azores'], IPMA_ATTR, window.trans.map.humidity), false)

    // When an IPMA forecast layer is active, mirror the mf2.ipma.pt viewer:
    // hide normal/satellite and stack a CARTO Positron light_nolabels base
    // under the WMS overlay, plus a Voyager labels-only tile on top in a
    // dedicated pane so place names stay readable above the colours. Restore
    // the user's chosen base (normal/satellite) when IPMA is turned off.
    if (!mymap.getPane('ipmaLabels')) {
        mymap.createPane('ipmaLabels');
        mymap.getPane('ipmaLabels').style.zIndex = 450; // above overlayPane (400)
        mymap.getPane('ipmaLabels').style.pointerEvents = 'none';
    }
    var ipmaBaseLayer = L.tileLayer('https://cartodb-basemaps-{s}.global.ssl.fastly.net/light_nolabels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        subdomains: 'abcd',
        attribution: '&copy; <a href="https://carto.com/" target="_blank">CARTO</a>, &copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a>'
    });
    var ipmaLabelsLayer = L.tileLayer('https://cartodb-basemaps-{s}.global.ssl.fastly.net/rastertiles/voyager_only_labels/{z}/{x}/{y}{r}.png', {
        maxZoom: 19,
        subdomains: 'abcd',
        pane: 'ipmaLabels'
    });

    function isIpmaActive() {
        var st = (window.fogosPanel && window.fogosPanel._state) || {};
        return !!(st['ipma:temperature'] || st['ipma:wind'] || st['ipma:windDirection'] ||
                  st['ipma:windAnimated'] || st['ipma:precipitation'] || st['ipma:humidity']);
    }

    function applyIpmaBaseMode() {
        if (isIpmaActive()) {
            if (mymap.hasLayer(normalLayer)) mymap.removeLayer(normalLayer);
            if (mymap.hasLayer(satelliteLayer)) mymap.removeLayer(satelliteLayer);
            if (!mymap.hasLayer(ipmaBaseLayer)) ipmaBaseLayer.addTo(mymap);
            ipmaBaseLayer.bringToBack();
            if (!mymap.hasLayer(ipmaLabelsLayer)) ipmaLabelsLayer.addTo(mymap);
        } else {
            if (mymap.hasLayer(ipmaBaseLayer)) mymap.removeLayer(ipmaBaseLayer);
            if (mymap.hasLayer(ipmaLabelsLayer)) mymap.removeLayer(ipmaLabelsLayer);
            var st = (window.fogosPanel && window.fogosPanel._state) || {};
            var wantSat = !!st['base:satellite'];
            var target = wantSat ? satelliteLayer : normalLayer;
            var other  = wantSat ? normalLayer    : satelliteLayer;
            if (mymap.hasLayer(other)) mymap.removeLayer(other);
            if (!mymap.hasLayer(target)) target.addTo(mymap);
        }
    }

    mymap.on('layeradd layerremove', function (e) {
        if (!e.layer) return;
        // React when an IPMA layer toggles or when the user reselects a base
        // while IPMA is on (the panel would re-add the base behind our backs).
        if (e.layer._legendUrl || e.layer._isIpma || e.layer === normalLayer || e.layer === satelliteLayer) {
            applyIpmaBaseMode();
        }
    });

    applyIpmaBaseMode();

    // Floating legend for active IPMA layers. Listens to layeradd/layerremove
    // on the map and renders the GetLegendGraphic image for each unique active
    // overlay. Hidden when nothing's on.
    var FogosLegend = L.Control.extend({
        options: { position: 'bottomleft' },
        onAdd: function (map) {
            var c = this._container = L.DomUtil.create('div', 'fogos-legend');
            L.DomEvent.disableClickPropagation(c);
            L.DomEvent.disableScrollPropagation(c);
            this._map = map;
            map.on('layeradd layerremove', this._refresh, this);
            this._refresh();
            return c;
        },
        _refresh: function () {
            var seen = {};
            var items = [];
            this._map.eachLayer(function (layer) {
                if (layer._legendUrl && !seen[layer._legendUrl]) {
                    seen[layer._legendUrl] = true;
                    items.push({ url: layer._legendUrl, label: layer._legendLabel || '' });
                }
            });
            if (!items.length) {
                this._container.style.display = 'none';
                this._container.innerHTML = '';
                return;
            }
            this._container.style.display = '';
            this._container.innerHTML = items.map(function (it) {
                var label = it.label
                    ? '<div class="fogos-legend__label">' + it.label + '</div>'
                    : '';
                return '<div class="fogos-legend__item">' + label +
                    '<img src="' + it.url + '" alt="" loading="lazy">' +
                    '</div>';
            }).join('');
        }
    });
    new FogosLegend().addTo(mymap);

    // Discrete legend for the rural fire danger (RCM) choropleth layers.
    // Visible only while one of the risk layers (today/tomorrow/after) is on.
    var FogosRiskLegend = L.Control.extend({
        options: { position: 'bottomleft' },
        onAdd: function (map) {
            var c = this._container = L.DomUtil.create('div', 'fogos-risk-legend');
            L.DomEvent.disableClickPropagation(c);
            L.DomEvent.disableScrollPropagation(c);
            this._map = map;
            map.on('layeradd layerremove', this._refresh, this);
            this._refresh();
            return c;
        },
        _refresh: function () {
            var active = false;
            this._map.eachLayer(function (layer) {
                if (layer._isRisk) active = true;
            });
            if (!active) {
                this._container.style.display = 'none';
                this._container.innerHTML = '';
                return;
            }
            var cls = (window.trans && window.trans.risk && window.trans.risk.classes) || {};
            var rows = [
                { d: 1, label: cls.reduced  || 'Reduzido' },
                { d: 2, label: cls.moderate || 'Moderado' },
                { d: 3, label: cls.high     || 'Elevado' },
                { d: 4, label: cls.veryHigh || 'Muito Elevado' },
                { d: 5, label: cls.maximum  || 'Máximo' }
            ];
            this._container.style.display = '';
            this._container.innerHTML =
                '<div class="fogos-risk-legend__title">' +
                ((window.trans && window.trans.panel && window.trans.panel.risk) || 'Perigo de Incêndio Rural') +
                '</div>' +
                rows.map(function (r) {
                    return '<div class="fogos-risk-legend__row">' +
                        '<span class="fogos-risk-legend__swatch" style="background:' + getColor(r.d) + '"></span>' +
                        '<span class="fogos-risk-legend__label">' + r.label + '</span>' +
                        '</div>';
                }).join('');
        }
    });
    new FogosRiskLegend().addTo(mymap);


    /*$.ajax({
        url: '/lightnings',
        dataType: "json",
        method: 'GET',
        success: function (data) {
            window.lightningLayer = []
            window.lightningLayer[0] = L.layerGroup()

            var objLightning = {}
            objLightning['Descargas Elétricas'] = window.lightningLayer[0]


            layerControl4 = L.control.layers(null, objLightning, {
                position: 'topleft',
                collapsed: false,
            })

            layerControl4.addTo(mymap)

            for (i in data.data) {
                var date = new Date(data.data[i].timestamp)
                var hours = Math.floor((new Date() - date) / 3600000);
                if (hours <= 24 && insidePT([data.data[i].payload.longitude, data.data[i].payload.latitude])) {
                    addLightning(data.data[i], mymap);
                }
            }

        }
    })*/

    $.ajax({
        url: '/v1/modis',
        dataType: "json",
        method: 'GET',
        success: function (data) {
            window.modisLayer = []
            window.modisLayer[0] = L.layerGroup()

            window.fogosPanel.addItem('satellite', 'modis', 'MODIS', window.modisLayer[0], false)

            for (i in data) {
                if (data[i].latitude && data[i].longitude) {
                    if (insidePT([data[i].longitude, data[i].latitude])) {
                        addModisPoint(data[i], mymap);
                    }

                }
            }

            $.ajax({
                url: '/v1/viirs',
                dataType: "json",
                method: 'GET',
                success: function (data) {
                    window.modisLayer[1] = L.layerGroup()

                    window.fogosPanel.addItem('satellite', 'viirs', 'VIIRS', window.modisLayer[1], false)

                    for (i in data) {
                        if (data[i].latitude && data[i].longitude) {
                            if (insidePT([data[i].longitude, data[i].latitude])) {
                                addVIIRSPoint(data[i], mymap);
                            }
                        }
                    }
                }
            })

            // IPMA Fire Radiative Power (LSA-SAF satellite product, 15-min refresh)
            window.fogosPanel.addItem('satellite', 'frp', 'IPMA FRP',
                L.tileLayer.wms('https://mf2.ipma.pt/services/', {
                    layers: 'lsasaf.frp.continent',
                    format: 'image/png',
                    transparent: true,
                    version: '1.3.0',
                    opacity: 0.7,
                    attribution: 'Fire Radiative Power &copy; LSA-SAF / <a href="https://www.ipma.pt" target="_blank">IPMA</a>'
                }), false)
        }
    })

    $.ajax({
        type: "GET",
        url: 'https://source.fogos.pt/v1/warnings/site',
        success: function (data) {
            if (data.success && data.data[0] && data.data[0].active) {
                $('#warning-site').find('.modal-body').html('<p>' + data.data[0].text + '</p>');
                $('#warning-site').modal('show');
            }
        },
    });


    $('.js-refresh').on('click',function(){
        getNewFires(mymap, true);
    });

    setInterval(function (){
        toastr.success(window.trans.map.updating, {timeOut: 1000});
        getNewFires(mymap, true);
    },60000)

})


function insidePT(point) {
    var x = point[0], y = point[1];

    var pt = portugal.geometry.coordinates[0][0];
    var inside = false;

    for (var i = 0, j = pt.length - 1; i < pt.length; j = i++) {
        var xi = pt[i][0], yi = pt[i][1];
        var xj = pt[j][0], yj = pt[j][1];

        var intersect = ((yi > y) != (yj > y))
            && (x < (xj - xi) * (y - yi) / (yj - yi) + xi);
        if (intersect) inside = !inside;
    }
    return inside;
};


function addLightning(data, mymap) {
    var marker = L.marker([data.payload.latitude, data.payload.longitude])

    marker.properties = {}

    iconHtml = '<i class="fas fa-bolt" style="color: #F96E5B"></i>'

    marker.sizeFactor = 1

    marker.setIcon(L.divIcon({
        className: 'count-icon-emergency',
        html: iconHtml,
        iconSize: [80, 80]
    }))

    window.lightningLayer[0].addLayer(marker)
}


function addModisPoint(data, mymap) {
    var marker = L.marker([data.latitude, data.longitude])

    marker.properties = {}

    iconHtml = '<i class="fab fa-hotjar" style="color: #F96E5B"></i>'

    marker.sizeFactor = 3

    marker.setIcon(L.divIcon({
        className: 'count-icon-emergency',
        html: iconHtml,
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    }))

    var confidence = '';
    if (data.confidence === 'nominal') {
        confidence = 'normal';
    } else if (data.confidence === 'low') {
        confidence = 'baixa';
    } else if (data.confidence === 'high') {
        confidence = 'alta';
    } else {
        confidence = data.confidence;
    }

    var date = data.acq_date + ' ' + data.acq_time.substr(0, 2) + ':' + data.acq_time.substr(2);

    date = moment.utc(date).local().format('DD-MM-YYYY HH:MM');
    var content = '<p>Data: ' + date + '</p><p>Confiança: ' + confidence + '%</p>';
    marker.bindPopup(content);

    window.modisLayer[0].addLayer(marker)
}

function addVIIRSPoint(data, mymap) {
    var marker = L.marker([data.latitude, data.longitude])

    marker.properties = {}

    iconHtml = '<i class="fab fa-hotjar" style="color: #F96E5B"></i>'

    marker.sizeFactor = 3

    marker.setIcon(L.divIcon({
        className: 'count-icon-emergency',
        html: iconHtml,
        iconSize: [20, 20],
        iconAnchor: [10, 10]
    }))


    var confidence = '';
    if (data.confidence === 'nominal') {
        confidence = 'normal';
    } else if (data.confidence === 'low') {
        confidence = 'baixa';
    } else if (data.confidence === 'high') {
        confidence = 'alta';
    } else {
        confidence = data.confidence;
    }

    var date = data.acq_date + ' ' + data.acq_time.substr(0, 2) + ':' + data.acq_time.substr(2);

    date = moment.utc(data).local().format('DD-MM-YYYY HH:MM');
    var content = '<p>Date: ' + date + '</p><p>Confiança: ' + confidence + '</p>';
    marker.bindPopup(content);

    window.modisLayer[1].addLayer(marker)
}


const BASE_SIZE = 22
var DATA_FIRES = {
    number: 0,
    topImportance: 0,
    average: 0
}

function calculateImportanceValue(data) {
    const manFactor = 1
    const terrainFactor = 3
    const aerialFactor = 7

    var importance = data.man * manFactor + data.terrain * terrainFactor + data.aerial * aerialFactor;

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

function fillSidebar(item) {
    var momentDate = moment.unix(item.updated.sec).format('HH:mm DD-MM-YYYY')
    var location = '<a href="https://www.google.com/maps/search/' + item.lat + ',' + item.lng + '" target="_blank"><i class="far fa-map"></i></a> ' + item.lat + ',' + item.lng
    var locationText = item.location
    if (item.localidade) {
        locationText += ' - ' + item.localidade
    }
    locationText += ' <a href="/' + locale + '/fogo/' + item.id + '/detalhe">' + window.trans.fire.moreDetails + '</a>'

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

    var notificationsAuth = store.get('notificationsAuth')
    if (notificationsAuth) {
        $('.notification-container').css({ 'display': 'inline-block' })
        $('.click-notification').css({ 'display': 'inline-block' })
        var notifyFire = store.get('fire-' + item.id)
        if (notifyFire) {
            $('.notification-container').find('i').removeClass('far').addClass('fas')
        } else {
            $('.notification-container').find('i').removeClass('fas').addClass('far')
        }
    }
}

function addMaker(item, mymap) {
    var x = randomGeo(item.lat, item.lng)
    var coords = [x['latitude'], x['longitude']]

    var el = document.createElement('div')
    el.className = 'marker'

    var marker = L.marker(coords)

    marker.properties = {}
    marker.properties.item = item

    isActive = window.location.pathname.split('/')[3]

    //Base iconHtml
    iconHtml = '<i class="dot status-'
    if(item.important && (item.statusCode == 8 || item.statusCode == 7 || item.statusCode == 9)){
        iconHtml += '99-r'
    } else if (item.important) {
        iconHtml += '99'
    } else {
        iconHtml += item.statusCode
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
    if (['3111', '3109', '4335'].indexOf(String(item.naturezaCode)) !== -1) {
        iconHtml += ' style="opacity:0.6"'
    }
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

    marker.addTo(mymap);
    marker.id = item.id

    if (isActive && isActive === item.id) {
        changeElementSizeById(item.id, 48 + sizeFactor)
        fillSidebar(item)
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

        fillSidebar(item)
        window.history.pushState('obj', 'newtitle', '/' + locale + '/fogo/' + item.id)
        status(item.id)
        plot(item.id)
        danger(item.id)
        meteo(item.id)
        extra(item.id)
        shares(item.id)
        if (typeof photos === 'function') photos(item.id)
        addPageview()
    });

    if(item.kml){
        var kmltext = item.kml;
        // Create new kml overlay
        const parser = new DOMParser();
        const kml = parser.parseFromString(kmltext, 'text/xml');
        const track = new L.KML(kml);
        window.fogosLayers[81].addLayer(track)

    }

    if(item.kmlVost){
        var kmltext = item.kmlVost;
        // Create new kml overlay
        const parser = new DOMParser();
        const kml = parser.parseFromString(kmltext, 'text/xml');
        const track = new L.KML(kml);
        window.fogosLayers[81].addLayer(track)
    }

}

let detailsChart

function plot(id) {
    var url = 'https://source.fogos.pt/fires/data?id=' + id
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

                if(detailsChart)
                    detailsChart.destroy()

                detailsChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [{
                            label: window.trans.chart.humans,
                            data: man,
                            fill: false,
                            backgroundColor: '#EFC800',
                            borderColor: '#EFC800'
                        },
                            {
                                label: window.trans.chart.terrestrial,
                                data: terrain,
                                fill: false,
                                backgroundColor: '#6D720B',
                                borderColor: '#6D720B'
                            }, {
                                label: window.trans.chart.aerial,
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

function status(id) {
    var url = '/' + locale + '/views/status/' + id
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            $('.f-status').html(data)
        }
    })
}

function danger(id) {
    var url = '/' + locale + '/views/risk/' + id
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            $('.f-danger').html(data)
        }
    })

}

function meteo(id) {
    var url = '/' + locale + '/views/meteo/' + id
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            $('.f-meteo').html(data)
        }
    })

}

function extra(id) {
    var url = '/' + locale + '/views/extra/' + id
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


function shares(id) {
    var url = '/' + locale + '/views/shares/' + id
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            $('.f-shares').html(data)
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
                riskToday._isRisk = true;

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
                            riskTomorrow._isRisk = true;

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
                                        riskAfter._isRisk = true;

                                        if (window.fogosPanel) {
                                            window.fogosPanel.addItem('risk', 'today',    window.trans.risk.today,    riskToday,    !!getParameterByName('risk'))
                                            window.fogosPanel.addItem('risk', 'tomorrow', window.trans.risk.tomorrow, riskTomorrow, !!getParameterByName('risk-tomorrow'))
                                            window.fogosPanel.addItem('risk', 'after',    window.trans.risk.after,    riskAfter,    false)
                                        }
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

// Maps status code -> { id, labelKey }. Order defines panel display order.
var FIRE_STATUS_DEFS = [
    { code: 3,  id: 'dispatch',           labelKey: 'dispatch' },
    { code: 4,  id: 'firstAlertDispatch', labelKey: 'firstAlertDispatch' },
    { code: 6,  id: 'arrival',            labelKey: 'arrival' },
    { code: 5,  id: 'ongoing',            labelKey: 'ongoing' },
    { code: 7,  id: 'inResolution',       labelKey: 'inResolution' },
    { code: 8,  id: 'conclusion',         labelKey: 'conclusion' },
    { code: 9,  id: 'surveillance',       labelKey: 'surveillance' },
    { code: 10, id: 'closed',             labelKey: 'closed' },
    { code: 11, id: 'falseAlarm',         labelKey: 'falseAlarm' },
    { code: 12, id: 'falseAlert',         labelKey: 'falseAlert' },
]

function getNewFires(mymap, refresh = false)
{
    if(refresh){
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
        window.fogosLayers[81].remove()

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
        window.fogosLayers[81] = L.layerGroup()

        window.fogosLayers[81].addTo(mymap)
    }

    var url = 'https://source.fogos.pt/new/fires'
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

                var t = window.trans.status
                FIRE_STATUS_DEFS.forEach(function (def) {
                    var layer = window.fogosLayers[def.code]
                    var label = t[def.labelKey]
                    if (refresh && window.fogosPanel._findItem('status', def.id)) {
                        // Swap the new layer reference in place; on/off state preserved.
                        window.fogosPanel.updateLayer('status', def.id, layer)
                    } else {
                        window.fogosPanel.addItem('status', def.id, label, layer, true)
                    }
                })
            }
        }
    })

}

function addPlane(icao, mymap){
    var url = 'https://source.fogos.pt/v2/planes/' + icao
    $.ajax({
        url: url,
        method: 'GET',
        success: function (data) {
            if (data.success) {

                for (i in data.data) {
                    addPlanePoint(data.data[i], mymap)
                }
            }
        }
    })
}


function addPlanePoint(data, mymap) {
    var marker = L.marker([data.lat, data.lon])

    marker.properties = {}
    iconHtml = '<i class="fas fa-plane" style="color: #000000;font-size: 20px;"></i>'

    marker.sizeFactor = 3

    marker.setIcon(L.divIcon({
        className: 'count-icon-emergency',
        html: iconHtml,
        iconSize: [50, 50]
    }))


    date = moment.utc(data.created).local().format('DD-MM-YYYY HH:MM');
    var content = '<p>Date: ' + date + '</p>';
    marker.bindPopup(content);

    marker.addTo(mymap);
}
