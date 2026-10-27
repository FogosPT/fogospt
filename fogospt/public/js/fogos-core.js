// Shared helpers for the three map entry points (main.js = mainland/custom
// map, main2.js = Madeira, main-other.js = "outros"). Loaded right before
// them; everything here used to be copy-pasted (and slowly diverging) in
// each file. Keep these as plain globals: the entry files and inline
// handlers call them by name.

var locale = window.location.pathname.split('/')[1] || 'pt';

// Card fragments live under /{locale}/views/* (mainland) or
// /{locale}/madeira/views/* — main2.js sets FOGOS_CARD_PREFIX = '/madeira'.
window.FOGOS_CARD_PREFIX = window.FOGOS_CARD_PREFIX || '';

function fogosCardUrl(card, id) {
    return '/' + locale + window.FOGOS_CARD_PREFIX + '/views/' + card + '/' + encodeURIComponent(id);
}

// Base layers shared by every map: OSM streets + Esri hybrid satellite
// (imagery + roads + places), the same pair the mainland map uses.
function fogosBaseLayers() {
    var esri = 'https://server.arcgisonline.com/ArcGIS/rest/services/';
    return {
        normal: L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 19,
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }),
        satellite: L.layerGroup([
            L.tileLayer(esri + 'World_Imagery/MapServer/tile/{z}/{y}/{x}', {
                maxZoom: 19,
                attribution: 'Imagery &copy; <a href="https://www.esri.com" target="_blank" rel="noopener">Esri</a>, Maxar, Earthstar Geographics, and the GIS User Community'
            }),
            L.tileLayer(esri + 'Reference/World_Transportation/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 }),
            L.tileLayer(esri + 'Reference/World_Boundaries_and_Places/MapServer/tile/{z}/{y}/{x}', { maxZoom: 19 })
        ])
    };
}

// Card HTML is rendered (and escaped) server-side by Blade.
function status(id) {
    $('#status').empty();
    $.get(fogosCardUrl('status', id), function (data) {
        $('.f-status').html(data);
    });
}

function danger(id) {
    $.get(fogosCardUrl('risk', id), function (data) {
        $('.f-danger').html(data);
    });
}

function meteo(id) {
    $.get(fogosCardUrl('meteo', id), function (data) {
        $('.f-meteo').html(data);
    });
}

function extra(id) {
    $.get(fogosCardUrl('extra', id), function (data) {
        if (data && data.length !== 0) {
            $('.f-extra').html(data);
            $('.extra').addClass('active');
        } else {
            $('.extra').removeClass('active');
        }
    });
}

// Hover tooltip for a fire marker. Upstream text is escaped.
function buildFireTooltip(item) {
    var t = (window.trans && window.trans.fire) || {};
    var loc = escapeFireText(item.location || '');
    if (item.detailLocation) loc += ' — ' + escapeFireText(item.detailLocation);
    else if (item.localidade) loc += ' — ' + escapeFireText(item.localidade);
    var start = escapeFireText((item.date || '') + (item.hour ? ' ' + item.hour : ''));
    var men     = parseInt(item.man,     10) || 0;
    var terrain = parseInt(item.terrain, 10) || 0;
    var aerial  = parseInt(item.aerial,  10) || 0;
    return ''
        + '<div class="fire-tooltip-title">' + (loc || '&nbsp;') + '</div>'
        + (start ? '<div class="fire-tooltip-row"><i class="far fa-clock"></i> '
            + escapeFireText(t.tooltipStart || 'Início') + ': ' + start + '</div>' : '')
        + '<div class="fire-tooltip-row">'
        +   '<span title="' + escapeFireText(t.tooltipMen     || 'Operacionais') + '"><i class="fas fa-user"></i> ' + men     + '</span>'
        +   ' &nbsp;<span title="' + escapeFireText(t.tooltipTerrain || 'Terrestres') + '"><i class="fas fa-truck"></i> ' + terrain + '</span>'
        +   ' &nbsp;<span title="' + escapeFireText(t.tooltipAerial  || 'Aéreos') + '"><i class="fas fa-helicopter"></i> ' + aerial  + '</span>'
        + '</div>';
}

function buildFireTooltip(item) {
    var t = (window.trans && window.trans.fire) || {};
    var loc = escapeFireText(item.location || '');
    if (item.detailLocation) loc += ' — ' + escapeFireText(item.detailLocation);
    else if (item.localidade) loc += ' — ' + escapeFireText(item.localidade);
    var start = escapeFireText((item.date || '') + (item.hour ? ' ' + item.hour : ''));
    var men     = parseInt(item.man,     10) || 0;
    var terrain = parseInt(item.terrain, 10) || 0;
    var aerial  = parseInt(item.aerial,  10) || 0;
    return ''
        + '<div class="fire-tooltip-title">' + (loc || '&nbsp;') + '</div>'
        + (start ? '<div class="fire-tooltip-row"><i class="far fa-clock"></i> '
            + escapeFireText(t.tooltipStart || 'Início') + ': ' + start + '</div>' : '')
        + '<div class="fire-tooltip-row">'
        +   '<span title="' + escapeFireText(t.tooltipMen     || 'Operacionais') + '"><i class="fas fa-user"></i> ' + men     + '</span>'
        +   ' &nbsp;<span title="' + escapeFireText(t.tooltipTerrain || 'Terrestres') + '"><i class="fas fa-truck"></i> ' + terrain + '</span>'
        +   ' &nbsp;<span title="' + escapeFireText(t.tooltipAerial  || 'Aéreos') + '"><i class="fas fa-helicopter"></i> ' + aerial  + '</span>'
        + '</div>';
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

function changeElementSizeById(id, size) {
    var markerHtml = document.getElementById(id)

    //Set costum size
    markerHtml.style.height = size + 'px'
    markerHtml.style.width = size + 'px'
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

function escapeFireText(s) {
    return String(s == null ? '' : s)
        .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
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

function calculateImportanceValue(data) {
    const manFactor = 1
    const terrainFactor = 3
    const aerialFactor = 7

    var hasUnknownMeios = data.man == -1 || data.terrain == -1 || data.aerial == -1
    var importance = hasUnknownMeios
        ? 10 * manFactor
        : data.man * manFactor + data.terrain * terrainFactor + data.aerial * aerialFactor;

    DATA_FIRES.number += 1
    if (DATA_FIRES.topImportance < importance) {
        DATA_FIRES.topImportance = importance
    }

    DATA_FIRES.average = (DATA_FIRES.average * (DATA_FIRES.number - 1) + importance) / (DATA_FIRES.number)

    data.importance = importance
}

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
        confidence = escapeFireText(data.confidence);
    }

    var date = data.acq_date + ' ' + data.acq_time.substr(0, 2) + ':' + data.acq_time.substr(2);

    date = dayjs.utc(date).local().format('DD-MM-YYYY HH:mm');
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
        confidence = escapeFireText(data.confidence);
    }

    var date = data.acq_date + ' ' + data.acq_time.substr(0, 2) + ':' + data.acq_time.substr(2);

    date = dayjs.utc(date).local().format('DD-MM-YYYY HH:mm');
    var content = '<p>Date: ' + date + '</p><p>Confiança: ' + confidence + '</p>';
    marker.bindPopup(content);

    window.modisLayer[1].addLayer(marker)
}

function addPlane(icao, mymap){
    var url = 'https://source.fogos.pt/v2/planes/' + encodeURIComponent(icao)
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


    date = dayjs.utc(data.created).local().format('DD-MM-YYYY HH:mm');
    var content = '<p>Date: ' + date + '</p>';
    marker.bindPopup(content);

    marker.addTo(mymap);
}

