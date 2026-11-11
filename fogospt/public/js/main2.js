$.ajaxSetup({ headers: { "FPTSC": "xw2gfca9l7" } });
var locale = window.location.pathname.split('/')[1] || 'pt';
$(document).ready(function () {
    // FCM can be unsupported (Safari private mode, headless browsers): it must
    // not take the map down with it.
    try {
        const messaging = firebase.messaging()
        messaging.onMessage(function (payload) {
            toastr.warning(payload.notification.body)
        })
    } catch (e) { /* notifications unavailable */ }

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

    var normalLayer = L.tileLayer('https://api.mapbox.com/styles/v1/fogospt/cjgppvcdp00aa2spjclz9sjst/tiles/256/{z}/{x}/{y}@2x?access_token=pk.eyJ1IjoiZm9nb3NwdCIsImEiOiJjbDV0YnQza24wZmY1M2pwM3g4eHowZnRoIn0.MxbsPA-TJa-4ouvsnd99mg', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com">Mapbox</a>',
        maxZoom: 18,
        id: 'mapbox.streets',
        accessToken: 'pk.eyJ1IjoidG9tYWhvY2siLCJhIjoiY2pmYmgydHJnMzMwaTJ3azduYzI2eGZteiJ9.4Z0iB0Pgbb3M_8t9VG10kQ'
    }).addTo(mymap)

    var satLayer = L.tileLayer('https://api.mapbox.com/styles/v1/fogospt/cjksgciqsctfg2rp9x9uyh37g/tiles/256/{z}/{x}/{y}@2x?access_token=pk.eyJ1IjoiZm9nb3NwdCIsImEiOiJjbDV0YnQza24wZmY1M2pwM3g4eHowZnRoIn0.MxbsPA-TJa-4ouvsnd99mg', {
        attribution: 'Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com">Mapbox</a>',
        maxZoom: 18,
        id: 'mapbox.streets',
        accessToken: 'pk.eyJ1IjoidG9tYWhvY2siLCJhIjoiY2pmYmgydHJnMzMwaTJ3azduYzI2eGZteiJ9.4Z0iB0Pgbb3M_8t9VG10kQ'
    })

    mymap.attributionControl.addAttribution('Map data &copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors, <a href="https://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="https://www.mapbox.com">Mapbox</a>')

    var tm = (window.trans && window.trans.madeira) || {}
    var tmap = (window.trans && window.trans.map) || {}
    mymap.attributionControl.addAttribution(escapeFireText(tm.source || 'Fonte: SRPC, IP-RAM (via Fogos.pt)'))

    var xx = {}
    xx[tmap.normal || 'Normal'] = normalLayer
    xx[tmap.satellite || 'Satélite'] = satLayer

    var occurrencesLayer = L.layerGroup().addTo(mymap)
    var overlays = {}
    overlays[tm.layer || 'Ocorrências (24 h)'] = occurrencesLayer

    L.control.layers(xx, overlays, {collapsed: false, position: 'topleft'}).addTo(mymap)

    addRisk(mymap)

    if (window.FogosSat) FogosSat.installMain(mymap, null, { defaultOn: false });

    addWeatherLayers(mymap)

    MadeiraOccurrences.init(mymap, occurrencesLayer)
})

function addWeatherLayers(mymap) {
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
        'Desativar Camadas': L.tileLayer(''),
        'Temperatura': tempLayer,
        'Pressão': pressureLayer,
        'Vento': windLayer
    }

    var overlayLayers = {
        'Precipitação': precLayer,
        'Nuvens': cloudLayer
    }

    L.control.layers(baseLayers, overlayLayers, {collapsed: false, position: 'topright'}).addTo(mymap)
}

/**
 * SRPC, IP-RAM occurrences (Fogos.pt API v2) on the Madeira map.
 *
 * - /v2/madeira/occurrences (last 24 h): one marker per parish, at the parish
 *   centroid (approximate — the source has no exact location), sized by the
 *   number of occurrences. Occurrences without coordinates are only listed.
 * - /v2/madeira/events/current: the adverse-weather event panel, hidden when
 *   there is no event (data === null).
 *
 * There is no per-occurrence status and no aerial means in the source: do
 * not show or infer them. Every upstream string goes through esc().
 */
var MadeiraOccurrences = (function () {
    var API = 'https://source.fogos.pt/v2/madeira'
    var REFRESH_MS = 3 * 60 * 1000
    var TZ = 'Atlantic/Madeira' // same offset as Europe/Lisbon
    var NO_PARISH = '__none__'

    var t = (window.trans && window.trans.madeira) || {}
    var esc = escapeFireText

    var intlLocale = { pt: 'pt-PT', en: 'en-GB', es: 'es-ES' }[locale] || 'pt-PT'
    var fmtDateTime, fmtTime
    try {
        fmtDateTime = new Intl.DateTimeFormat(intlLocale, { timeZone: TZ, day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' })
        fmtTime = new Intl.DateTimeFormat(intlLocale, { timeZone: TZ, hour: '2-digit', minute: '2-digit' })
    } catch (e) {
        fmtDateTime = fmtTime = new Intl.DateTimeFormat(intlLocale, { day: '2-digit', month: '2-digit', hour: '2-digit', minute: '2-digit' })
    }

    var map, layer
    var markers = {}       // group key -> L.marker
    var openGroups = {}    // group keys whose <details> are open in the list
    var lastLoad = 0
    var loadedOnce = false

    function tr(key, fallback, vars) {
        var s = t[key] || fallback
        for (var k in (vars || {})) s = s.split('{' + k + '}').join(vars[k])
        return s
    }

    function int(v) { return parseInt(v, 10) || 0 }

    function when(iso, timeOnly) {
        if (!iso) return ''
        var d = new Date(iso)
        if (isNaN(d.getTime())) return ''
        return (timeOnly ? fmtTime : fmtDateTime).format(d)
    }

    function coord(v) {
        var n = typeof v === 'number' ? v : parseFloat(v)
        return isFinite(n) ? n : null
    }

    function safeLink(url) {
        return typeof url === 'string' && /^https?:\/\//i.test(url) ? url : null
    }

    function getJson(path, done) {
        $.ajax({ url: API + path, method: 'GET', dataType: 'json', timeout: 20000 })
            .done(function (data) {
                if (typeof data === 'string') {
                    try { data = JSON.parse(data) } catch (e) { data = null }
                }
                done(data && data.success ? data : null)
            })
            .fail(function () { done(null) })
    }

    // ---- grouping --------------------------------------------------------

    function groupByParish(list) {
        var groups = {}
        var order = []
        list.forEach(function (o) {
            var key = o.parishCode ? 'p:' + o.parishCode
                : (o.parish ? 'n:' + o.parish + '|' + (o.municipality || '') : NO_PARISH)
            var g = groups[key]
            if (!g) {
                g = groups[key] = {
                    key: key, parish: o.parish || null, municipality: o.municipality || null,
                    lat: null, lng: null, items: [], count: 0, firefighters: 0, vehicles: 0
                }
                order.push(key)
            }
            var lat = coord(o.lat), lng = coord(o.lng)
            if (g.lat === null && lat !== null && lng !== null) { g.lat = lat; g.lng = lng }
            g.items.push(o)
            g.count += Math.max(1, int(o.copies))
            g.firefighters += int(o.firefighters)
            g.vehicles += int(o.vehicles)
        })
        return order.map(function (k) { return groups[k] }).sort(function (a, b) {
            if (a.key === NO_PARISH) return 1
            if (b.key === NO_PARISH) return -1
            return b.count - a.count || String(a.parish).localeCompare(String(b.parish))
        })
    }

    function groupTitle(g) {
        if (g.key === NO_PARISH) return esc(tr('noParish', 'Sem freguesia'))
        return esc(g.parish) + (g.municipality && g.municipality !== g.parish ? ' <small>(' + esc(g.municipality) + ')</small>' : '')
    }

    function occurrenceItem(o) {
        var copies = int(o.copies)
        var entities = (o.entities || []).filter(Boolean)
        return '<li class="madeira-occ">'
            + '<span class="madeira-occ__time">' + esc(when(o.start)) + '</span> '
            + '<span class="madeira-occ__nature">' + esc(o.nature || o.natureCode || '—') + '</span>'
            + (copies > 1 ? ' <span class="madeira-occ__copies" title="' + esc(tr('copiesTitle', 'Registos idênticos')) + '">×' + copies + '</span>' : '')
            + '<div class="madeira-occ__meta">'
            +   '<span title="' + esc(tr('firefighters', 'Operacionais')) + '"><i class="fas fa-user" aria-hidden="true"></i> ' + int(o.firefighters) + '</span>'
            +   ' <span title="' + esc(tr('vehicles', 'Veículos')) + '"><i class="fas fa-truck" aria-hidden="true"></i> ' + int(o.vehicles) + '</span>'
            +   (entities.length ? ' <span title="' + esc(tr('entities', 'Entidades')) + '"><i class="fas fa-shield-alt" aria-hidden="true"></i> ' + esc(entities.join(', ')) + '</span>' : '')
            + '</div>'
            + '</li>'
    }

    function countLabel(n) {
        return n === 1 ? tr('occurrenceOne', '1 ocorrência') : tr('occurrenceMany', '{n} ocorrências', { n: n })
    }

    // ---- map -------------------------------------------------------------

    function popupHtml(g) {
        return '<div class="madeira-popup">'
            + '<div class="madeira-popup__title">' + groupTitle(g) + '</div>'
            + '<div class="madeira-popup__count">' + esc(countLabel(g.count))
            +   ' · <i class="fas fa-user" aria-hidden="true"></i> ' + g.firefighters
            +   ' · <i class="fas fa-truck" aria-hidden="true"></i> ' + g.vehicles + '</div>'
            + '<p class="madeira-popup__note"><i class="fas fa-info-circle" aria-hidden="true"></i> ' + esc(tr('approxNote', 'Localização aproximada: centro da freguesia. A fonte não indica o local exato.')) + '</p>'
            + '<ul class="madeira-occ-list">' + g.items.map(occurrenceItem).join('') + '</ul>'
            + '</div>'
    }

    function iconSize(count) {
        return Math.round(Math.min(58, 24 + 7 * Math.sqrt(count - 1)))
    }

    function renderMap(groups) {
        var openKey = null
        for (var k in markers) {
            if (markers[k].isPopupOpen()) openKey = k
        }
        layer.clearLayers()
        markers = {}

        groups.forEach(function (g) {
            if (g.lat === null || g.key === NO_PARISH) return
            var size = iconSize(g.count)
            var marker = L.marker([g.lat, g.lng], {
                icon: L.divIcon({
                    className: 'madeira-occ-icon',
                    html: '<span class="madeira-occ-icon__count" style="font-size:' + (size > 40 ? 15 : 12) + 'px">' + g.count + '</span>',
                    iconSize: [size, size]
                }),
                title: (g.parish || '') + ' — ' + countLabel(g.count),
                riseOnHover: true,
                zIndexOffset: g.count
            })
            marker.bindPopup(popupHtml(g), { maxWidth: 320, maxHeight: 300, className: 'madeira-popup-wrap' })
            marker.addTo(layer)
            markers[g.key] = marker
        })

        if (openKey && markers[openKey]) markers[openKey].openPopup()
    }

    // ---- list panel ------------------------------------------------------

    function renderList(list, groups) {
        var total = 0, mapped = 0, unmapped = 0, parishes = 0
        groups.forEach(function (g) {
            total += g.count
            if (g.key !== NO_PARISH) parishes++
            if (g.lat !== null && g.key !== NO_PARISH) mapped += g.count
            else unmapped += g.count
        })

        var meta = tr('summary', '{n} ocorrências em {p} freguesias nas últimas 24 horas', { n: total, p: parishes })
        if (unmapped) meta += ' · ' + tr('unmapped', '{n} sem localização no mapa', { n: unmapped })
        $('#madeira-occ-meta').text(meta)

        if (!list.length) {
            $('#madeira-occ-list').html('<p class="madeira-empty">' + esc(tr('none', 'Sem ocorrências registadas nas últimas 24 horas.')) + '</p>')
            return
        }

        var html = '<ul class="madeira-parishes">'
        groups.forEach(function (g) {
            var onMap = g.lat !== null && g.key !== NO_PARISH
            html += '<li><details data-key="' + esc(g.key) + '"' + (openGroups[g.key] ? ' open' : '') + '>'
                + '<summary><span class="madeira-parishes__name">' + groupTitle(g) + '</span>'
                + '<span class="madeira-parishes__count" title="' + esc(countLabel(g.count)) + '">' + g.count + '</span>'
                + (onMap
                    ? '<button type="button" class="btn btn-link btn-sm madeira-parishes__locate" data-key="' + esc(g.key) + '" title="' + esc(tr('showOnMap', 'Ver no mapa')) + '"><i class="fas fa-map-marker-alt" aria-hidden="true"></i><span class="visually-hidden">' + esc(tr('showOnMap', 'Ver no mapa')) + '</span></button>'
                    : '<span class="madeira-parishes__nomap" title="' + esc(tr('notOnMap', 'Sem localização no mapa')) + '"><i class="fas fa-map-marker-alt" aria-hidden="true"></i></span>')
                + '</summary>'
                + '<ul class="madeira-occ-list">' + g.items.map(occurrenceItem).join('') + '</ul>'
                + '</details></li>'
        })
        html += '</ul>'
        $('#madeira-occ-list').html(html)
    }

    function locate(key) {
        var m = markers[key]
        if (!m) return
        if (!map.hasLayer(layer)) layer.addTo(map)
        map.setView(m.getLatLng(), Math.max(map.getZoom(), 13))
        m.openPopup()
        if (window.innerWidth < 992) document.getElementById('map').scrollIntoView({ behavior: 'smooth' })
    }

    // ---- event panel -----------------------------------------------------

    function kpi(value, label) {
        return '<div class="madeira-kpi"><span class="madeira-kpi__value">' + int(value) + '</span>'
            + '<span class="madeira-kpi__label">' + esc(label) + '</span></div>'
    }

    function renderEvent(ev) {
        var $box = $('#madeira-event')
        if (!ev) {
            $box.attr('hidden', true).empty()
            return
        }
        var k = ev.kpi || {}
        var active = ev.active === true || ev.status === 'ATIVO'
        var period = when(ev.start) + ' – ' + (ev.untilNow ? tr('untilNow', 'até agora') : when(ev.end))

        var html = '<div class="card-body">'
            + '<span class="madeira-badge' + (active ? ' is-active' : '') + '">' + esc(active ? tr('eventActive', 'Em curso') : tr('eventClosed', 'Encerrado')) + '</span>'
            + '<h2 class="card-title madeira-event__title">' + esc(ev.title) + '</h2>'
            + (ev.zone ? '<p class="madeira-event__zone">' + esc(ev.zone) + '</p>' : '')
            + '<p class="madeira-meta">' + esc(tr('period', 'Período')) + ': ' + esc(period)
            + (ev.updatedAt ? '<br>' + esc(tr('updated', 'Última atualização')) + ': ' + esc(when(ev.updatedAt)) : '') + '</p>'
            + '<div class="madeira-kpis">'
            +   kpi(k.occurrences, tr('kpiOccurrences', 'Ocorrências'))
            +   kpi(k.activeOccurrences, tr('kpiActive', 'Ativas'))
            +   kpi(k.operationals, tr('kpiOperationals', 'Operacionais'))
            +   kpi(k.groundAssets, tr('kpiGroundAssets', 'Meios terrestres'))
            +   kpi(k.municipalities, tr('kpiMunicipalities', 'Concelhos'))
            + '</div>'

        if (k.peak && k.peak.time) {
            html += '<p class="madeira-meta">' + esc(tr('peak', 'Pico: {n} ocorrências/hora às {time}', { n: int(k.peak.occurrences), time: when(k.peak.time) })) + '</p>'
        }
        if (ev.summary) html += '<p class="madeira-event__summary">' + esc(ev.summary) + '</p>'

        var munis = (ev.municipalities || []).slice().sort(function (a, b) { return int(b.occurrences) - int(a.occurrences) })
        if (munis.length) {
            html += '<h3 class="madeira-h3">' + esc(tr('byMunicipality', 'Por concelho')) + '</h3>'
                + '<div class="table-responsive"><table class="table table-sm madeira-table"><thead><tr>'
                + '<th scope="col">' + esc(tr('municipality', 'Concelho')) + '</th>'
                + '<th scope="col" class="text-end">' + esc(tr('kpiOccurrences', 'Ocorrências')) + '</th>'
                + '<th scope="col" class="text-end">' + esc(tr('kpiOperationals', 'Operacionais')) + '</th>'
                + '<th scope="col" class="text-end">' + esc(tr('kpiGroundAssets', 'Meios terrestres')) + '</th>'
                + '</tr></thead><tbody>'
            munis.forEach(function (m) {
                html += '<tr><td>' + esc(m.name) + '</td><td class="text-end">' + int(m.occurrences) + '</td>'
                    + '<td class="text-end">' + int(m.operationals) + '</td><td class="text-end">' + int(m.groundAssets) + '</td></tr>'
            })
            html += '</tbody></table></div>'
        }

        var roads = ev.roadRestrictions || []
        if (roads.length) {
            var closed = roads.filter(function (r) { return r.closed === true })
            var conditioned = roads.filter(function (r) { return r.closed !== true })
            html += '<h3 class="madeira-h3">' + esc(tr('roads', 'Estradas')) + '</h3>'
            ;[[closed, tr('roadsClosed', 'Cortadas'), 'is-closed'], [conditioned, tr('roadsConditioned', 'Condicionadas'), 'is-conditioned']].forEach(function (grp) {
                if (!grp[0].length) return
                html += '<p class="madeira-roads__label ' + grp[2] + '">' + esc(grp[1]) + ' (' + grp[0].length + ')</p><ul class="madeira-roads">'
                grp[0].forEach(function (r) {
                    html += '<li><strong>' + esc(r.road) + '</strong>' + (r.text ? ' — ' + esc(r.text) : '')
                        + (r.time ? ' <small class="text-muted">(' + esc(when(r.time)) + ')</small>' : '') + '</li>'
                })
                html += '</ul>'
            })
        }

        var comms = ev.communiques || []
        if (comms.length) {
            html += '<h3 class="madeira-h3">' + esc(tr('communiques', 'Comunicados')) + '</h3><ul class="madeira-communiques">'
            comms.forEach(function (c) {
                var link = c.official ? safeLink(c.link) : null
                var title = esc(c.title || '—')
                html += '<li>'
                    + (link ? '<a href="' + esc(link) + '" target="_blank" rel="noopener">' + title + ' <i class="fas fa-external-link-alt" aria-hidden="true"></i></a>' : '<strong>' + title + '</strong>')
                    + (c.official ? ' <span class="madeira-badge is-official">' + esc(tr('official', 'Oficial')) + '</span>' : '')
                    + (c.time ? '<br><small class="text-muted">' + esc(when(c.time)) + '</small>' : '')
                    + (c.text ? '<p class="madeira-communiques__text">' + esc(c.text) + '</p>' : '')
                    + '</li>'
            })
            html += '</ul>'
        }

        var recs = (ev.recommendations || []).filter(Boolean)
        if (recs.length) {
            html += '<h3 class="madeira-h3">' + esc(tr('recommendations', 'Recomendações')) + '</h3><ul class="madeira-recommendations">'
            recs.forEach(function (r) { html += '<li>' + esc(r) + '</li>' })
            html += '</ul>'
        }

        if (ev.notes) html += '<p class="madeira-note">' + esc(ev.notes) + '</p>'
        if (ev.source) html += '<p class="madeira-note">' + esc(tr('eventSource', 'Fontes')) + ': ' + esc(ev.source) + '</p>'
        html += '</div>'

        $box.html(html).removeAttr('hidden')
    }

    // ---- loading ---------------------------------------------------------

    function load() {
        lastLoad = Date.now()
        getJson('/occurrences?limit=2000', function (res) {
            if (!res) {
                if (!loadedOnce) $('#madeira-occ-meta').text(tr('error', 'Não foi possível carregar as ocorrências. A tentar novamente…'))
                return
            }
            loadedOnce = true
            var list = Array.isArray(res.data) ? res.data : []
            var groups = groupByParish(list)
            renderMap(groups)
            renderList(list, groups)
            $('#madeira-occ-updated').text(tr('updated', 'Última atualização') + ': ' + when(new Date().toISOString(), true))
        })
        getJson('/events/current', function (res) {
            // Keep the last event on a failed refresh; hide it only when the
            // API says there is none.
            if (res) renderEvent(res.data || null)
        })
    }

    function init(m, l) {
        map = m
        layer = l

        $('#madeira-occ-list')
            .on('click', '.madeira-parishes__locate', function (e) {
                e.preventDefault()
                e.stopPropagation()
                locate(this.getAttribute('data-key'))
            })
        // 'toggle' does not bubble: listen in the capture phase.
        var listEl = document.getElementById('madeira-occ-list')
        if (listEl) {
            listEl.addEventListener('toggle', function (e) {
                var d = e.target
                if (!d || d.tagName !== 'DETAILS') return
                var key = d.getAttribute('data-key')
                if (d.open) openGroups[key] = true
                else delete openGroups[key]
            }, true)
        }

        load()
        setInterval(function () { if (!document.hidden) load() }, REFRESH_MS)
        document.addEventListener('visibilitychange', function () {
            if (!document.hidden && Date.now() - lastLoad > REFRESH_MS) load()
        })
    }

    return { init: init, groupByParish: groupByParish }
})()

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

function getParameterByName(name, url) {
    if (!url) url = window.location.href
    name = name.replace(/[\[\]]/g, '\\$&')
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url)
    if (!results) return null
    if (!results[2]) return ''
    return decodeURIComponent(results[2].replace(/\+/g, ' '))
}
