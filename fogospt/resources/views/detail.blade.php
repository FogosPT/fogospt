@extends('app')

@section('content')
    <main role="main" class="mb-auto margin-top-10">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-12">
                    <div class="card">
                                <div style="height: 400px" id="mymap"></div>
                            <p>
                                @if(isset($fire['kml']))Área Ardida segundo dados do ICNF. @endif
                                @if(isset($fire['kmlVost']))Áreas a <span style="color:#3388ff;font-weight:bold">azul</span>: área de interesse por <a href="https://vost.pt">VOST.pt</a>. @endif
                                <span class="js-kml-firms-legend d-none">Áreas a <span style="color:#e60000;font-weight:bold">vermelho</span>: área de interesse automática com base nos pontos VIIRS/MODIS/IPMA FRP.</span>
                            </p>

                        <div class="card-body">

                            <div class="row">
                                <div class="col-sm-12 col-md-6">
                                    <h4 class="card-title">@lang('elements.cards.general.place')</h4>
                                    <p class="f-local mb-0">
                                        @isset($fire['location'])
                                            {{ $fire['location'] }} - {{ $fire['localidade'] }}
                                        @endisset
                                    </p>
                                    @isset($fire['detailLocation'])
                                    <p class="mt-0">{{$fire['detailLocation']}}
                                    </p>
                                    @endisset
                                    <h4 class="card-title">@lang('elements.cards.general.start_at')</h4>
                                    <p class="f-start">
                                        @isset($fire['date'])
                                            {{ $fire['hour'] }} {{ $fire['date'] }}
                                        @endisset
                                    </p>

                                    <h4 class="card-title">@lang('elements.cards.general.nature')</h4>
                                    <p class="f-nature">
                                        @isset($fire['natureza'])
                                            {{ $fire['natureza'] }}
                                        @endisset
                                    </p>

                                    @isset($fire['id'])
                                        <div class="js-fire-subscribe mb-3" data-fire-id="{{ $fire['id'] }}">
                                            <button type="button" class="btn btn-outline-primary btn-sm js-fire-subscribe-btn">
                                                <i class="fa fa-bell"></i>
                                                <span class="js-fire-subscribe-label">Notificar-me sobre esta ocorrência</span>
                                            </button>
                                            <small class="d-block text-muted mt-1 js-fire-subscribe-hint d-none">
                                                Ative primeiro as notificações em <a href="/notificacoes">/notificacoes</a>.
                                            </small>
                                        </div>
                                    @endisset

                                    @isset($fire['icnf']['fontealerta'])
                                        <h4 class="card-title">@lang('elements.cards.general.alertFrom')</h4>
                                        <p class="f-nature">
                                            {{ $fire['icnf']['fontealerta'] }}
                                        </p>
                                    @endisset

                                    @if(isset($fire['icnf']['burnArea']) || isset($fire['kmlVost']))
                                        <h4 class="card-title">@lang('elements.cards.detail.burn.title')</h4>
                                        <p class="f-nature">
                                            @isset($fire['icnf']['burnArea']){{ $fire['icnf']['burnArea']['total'] }} HA @endisset @isset($fire['kml']) <a target="_blank" href="https://api.fogos.pt/v2/incidents/{{$fire['id']}}/kml">Download ICNF</a> @endisset @isset($fire['kmlVost']) <a target="_blank" href="https://api.fogos.pt/v2/incidents/{{$fire['id']}}/kmlVost">Download VOST.pt</a> @endisset
                                        </p>
                                    @endif

                                    @isset($fire['id'])
                                        <p class="f-nature small text-muted js-kml-firms-link d-none" data-fire-id="{{ $fire['id'] }}">
                                            <a target="_blank" href="https://source.fogos.pt/v2/incidents/{{ $fire['id'] }}/kmlFirms">Download área de interesse automática (VIIRS/MODIS/IPMA FRP)</a>
                                        </p>
                                    @endisset

                                    @isset($fire['icnf']['tipocausa'])
                                        <h4 class="card-title">@lang('elements.cards.detail.cause.title')</h4>
                                        <p class="f-nature">
                                            @isset($fire['icnf']['causafamilia']) {{ $fire['icnf']['causafamilia'] }} - @endisset {{ $fire['icnf']['tipocausa'] }}
                                        </p>
                                    @endisset


                                    <h4 class="card-title">@lang('elements.cards.general.fireRisk')</h4>
                                    <div class="f-danger">
                                        @include('elements.risk')
                                    </div>

                                    @if( !empty($fire['extra']) || !empty($fire['pco']) || !empty($fire['cos']) )
                                        <div class="f-extra">
                                            @include('elements.extra')
                                        </div>
                                    @endif



                                </div>

                                <div class="col-sm-12 col-md-6">
                                    <h4 class="card-title">@lang('elements.cards.resources.units')</h4>
                                    <div class="assets d-flex flex-md-wrap align-items-center justify-content-center">
                                        <img class="assets-icon" src="/img/fireman.svg">
                                        <span class="assets-nr f-man">
                                            @isset($fire['man'])
                                                {{ $fire['man'] }}
                                            @endisset
                                        </span>
                                        <img class="assets-icon" src="/img/firetruck.svg">
                                        <span class="assets-nr f-terrain">
                                            @isset($fire['terrain'])
                                                {{ $fire['terrain'] }}
                                            @endisset
                                        </span>
                                        <img class="assets-icon" src="/img/plane.svg">
                                        <span class="assets-nr f-aerial">
                                            @isset($fire['aerial'])
                                                {{ $fire['aerial'] }}
                                            @endisset
                                        </span>
                                    </div>
                                    <div style="position:relative;min-height:220px">
                                        <canvas class="px-2 py-0" id="myChart" width="400" height="150" data-id="{{$fire['id']}}"></canvas>
                                    </div>

                                    <h4 class="card-title">@lang('elements.cards.status.status')</h4>
                                    <div class="f-status">
                                        <div id="status">
                                            @isset($fire['statusHistory'])
                                                @foreach( $fire['statusHistory'] as $status)
                                                    <div>
                                                        <span class="dot status-{{ $status['statusCode'] }} timelineDot"></span>
                                                        <div>
                                                            <p class="status-hour">{{ $status['label'] }}</p>
                                                            <p class="status-label">{{ $status['status'] }}</p>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endisset
                                        </div>
                                    </div>

                                    <h4 class="card-title">@lang('elements.cards.meteo.title')</h4>
                                    <div class="f-meteo">
                                        @include('elements.meteo')
                                    </div>

                                    <h4 class="card-title">@lang('elements.cards.shares.title')</h4>
                                    <div class="row justify-content-center">
                                        <div class="col-8">
                                            <div class="f-shares">
                                                @include('elements.shares')
                                            </div>
                                        </div>
                                    </div>


                                </div>
                            </div>

                            @include('elements.cards.photos')

                            <div class="ipma-charts mt-4"
                                 data-lat="{{ $fire['lat'] ?? '' }}"
                                 data-lng="{{ $fire['lng'] ?? '' }}">
                                <h4 class="card-title">@lang('elements.cards.ipmaCharts.title')</h4>
                                <p class="ipma-charts__attribution small text-muted">
                                    @lang('elements.cards.ipmaCharts.source')
                                    <a href="https://www.ipma.pt" target="_blank" rel="noopener">IPMA</a>.
                                </p>
                                <div class="ipma-charts__loader text-center my-3 d-none">
                                    <i class="fas fa-spinner fa-spin"></i>
                                </div>
                                <div class="ipma-charts__grid row d-none">
                                    <div class="col-md-6"><canvas id="ipmaTempHum"   width="400" height="220"></canvas></div>
                                    <div class="col-md-6"><canvas id="ipmaWind"      width="400" height="220"></canvas></div>
                                    <div class="col-md-6"><canvas id="ipmaPressure"  width="400" height="220"></canvas></div>
                                    <div class="col-md-6"><canvas id="ipmaPrecip"    width="400" height="220"></canvas></div>
                                    <div class="col-md-6"><canvas id="ipmaFwiIsiBui" width="400" height="220"></canvas></div>
                                    <div class="col-md-6"><canvas id="ipmaDcDmcFfmc" width="400" height="220"></canvas></div>
                                    <div class="col-md-6"><canvas id="ipmaFrm"       width="400" height="220"></canvas></div>
                                    <div class="col-md-6"><canvas id="ipmaRcm"       width="400" height="220"></canvas></div>
                                </div>
                                <div class="ipma-charts__error alert alert-warning small d-none">
                                    @lang('elements.cards.ipmaCharts.error')
                                </div>
                                <p class="ipma-charts__learn-more small text-muted mt-2">
                                    <a href="{{ route('information', ['locale' => app()->getLocale()]) }}#ipma-charts">
                                        @lang('elements.cards.ipmaCharts.learnMore')
                                    </a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection


@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mapbox-gl/2.4.1/mapbox-gl.js" integrity="sha512-sbT60T8AS2DW+q6hiN+PM/389c9ZYGd1gZ7TvF7mhKsypzZLQ6E81lC6eB/ZQ30LnBdxB0uodYfuOsaFoZixEA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mapbox-gl/2.4.1/mapbox-gl.min.js" integrity="sha512-NTZ4yfDV+hnycF2x28e43icmAkkDWZ6b+RXZQJkyUGKA78WQb0gIuv7RKF8W+5XAKunxzzJq4i8FD2L04h9O1g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.8.0/leaflet.js" integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://unpkg.com/mapbox-gl-leaflet@0.0.16/leaflet-mapbox-gl.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script src="/js/vendor/store2.min.js"></script>
    <script src="/js/notifications.js"></script>
    <script>
        $(document).ready(function () {
            var $wrap = $('.js-fire-subscribe');
            if ($wrap.length === 0 || !window.Fogos || !window.Fogos.notifications) return;

            var fireId = String($wrap.data('fire-id'));
            var topic = window.Fogos.notifications.topicForIncident(fireId);
            var api = window.Fogos.notifications;

            function render() {
                var subbed = api.isSubscribed(topic);
                $wrap.find('.js-fire-subscribe-btn')
                    .toggleClass('btn-outline-primary', !subbed)
                    .toggleClass('btn-primary', subbed);
                $wrap.find('.js-fire-subscribe-label').text(
                    subbed ? 'A receber notificações desta ocorrência' : 'Notificar-me sobre esta ocorrência'
                );
            }

            render();

            $wrap.on('click', '.js-fire-subscribe-btn', function () {
                var $btn = $(this).prop('disabled', true);
                var done = function () { $btn.prop('disabled', false); render(); };

                var go = function () {
                    if (api.isSubscribed(topic)) {
                        api.unsubscribe(topic, function (err) {
                            if (err) toastr.error('Ocorreu um erro');
                            else toastr.success('Subscrição removida');
                            done();
                        });
                    } else {
                        api.subscribe(topic, function (err) {
                            if (err) toastr.error('Ocorreu um erro');
                            else toastr.success('Subscrito com sucesso');
                            done();
                        });
                    }
                };

                if (api.hasAuth()) {
                    go();
                } else {
                    api.requestAuth().then(go).catch(function () {
                        $wrap.find('.js-fire-subscribe-hint').removeClass('d-none');
                        $btn.prop('disabled', false);
                    });
                }
            });
        });
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js" integrity="sha256-4iQZ6BVL4qNKlQ27TExEhBN1HFPvAvAMbFavKKosSWQ=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/vendor/L.KLM.js') }}"></script>
    <link rel="stylesheet" href="https://unpkg.com/photoswipe@5/dist/photoswipe.css">
    <script src="/js/photos.js?v={{ filemtime(public_path('js/photos.js')) }}"></script>
    <style>
        .fogos-photo-divicon { background: transparent; border: 0; }
        .fogos-photo-divicon .photo-marker {
            width: 40px; height: 40px; cursor: pointer;
            transform-origin: 50% 50%;
            transition: transform 0.15s ease;
        }
        .fogos-photo-divicon .photo-marker:hover { filter: drop-shadow(0 0 3px rgba(0,0,0,0.4)); }
        .fogos-fire-divicon { background: transparent; border: 0; }
        .fogos-fire-divicon .fire-marker {
            position: relative; width: 40px; height: 40px;
            display: flex; align-items: center; justify-content: center;
        }
        .fogos-fire-divicon .fire-marker__dot {
            position: absolute; inset: 0; border-radius: 50%;
            background: rgba(240, 0, 51, 0.5);
            border: 2px solid red;
        }
        .fogos-fire-divicon .fire-marker img {
            position: relative; width: 24px; height: 24px;
        }
    </style>
    <script type="module">
        import PhotoSwipeLightbox from 'https://unpkg.com/photoswipe@5/dist/photoswipe-lightbox.esm.js';
        window.PhotoSwipeLightbox = PhotoSwipeLightbox;
        window.dispatchEvent(new Event('photoswipe-ready'));
    </script>
    <script src="{{ asset('js/detail.js') }}"></script>
    <script src="/js/ipma-charts.js?v={{ filemtime(public_path('js/ipma-charts.js')) }}"></script>

    <script>
        $(document).ready( function () {
            // Make basemap
            const map = new L.Map('mymap', { center: new L.LatLng({{$fire['lat']}}, {{$fire['lng']}}), zoom: 11 });
            const normalLayer = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {});
            const satLayer = L.layerGroup([
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
            const osm = normalLayer;

            @if(isset($fire['kml']) || isset($fire['kmlVost']))
                var fireIcon = L.divIcon({
                    className: 'fogos-fire-divicon',
                    html: '<div class="fire-marker">' +
                        '<span class="fire-marker__dot"></span>' +
                        '<img src="/img/ico_fire.svg" alt="">' +
                        '</div>',
                    iconSize: [40, 40],
                    iconAnchor: [20, 20]
                });
                L.marker([{{$fire['lat']}}, {{$fire['lng']}}], { icon: fireIcon, zIndexOffset: 1000 }).addTo(map);
            @else
                var circle = L.circle([{{$fire['lat']}}, {{$fire['lng']}}], {
                    color: 'red',
                    fillColor: '#f03',
                    fillOpacity: 0.5,
                    radius: 500
                }).addTo(map);
            @endif

            map.addLayer(osm);

            L.control.layers({
                'Normal': normalLayer,
                'Satélite': satLayer
            }, {}, { collapsed: true, position: 'topright' }).addTo(map);

            @if(isset($fire['kml']))
                // Load kml file
                var kmltext = '{!! $kml !!}';
                // Create new kml overlay
                const parser = new DOMParser();
                const kml = parser.parseFromString(kmltext, 'text/xml');
                const track = new L.KML(kml);
                map.addLayer(track);

                // Adjust map to show the kml
                const bounds = track.getBounds();
                map.fitBounds(bounds);
            @endif


            @if(isset($fire['kmlVost']))
                // Load kml file
                var kmltextVost = '{!! $kmlVost !!}';
                // Create new kml overlay
                const parserVost = new DOMParser();
                const kmlVost = parserVost.parseFromString(kmltextVost, 'text/xml');
                const trackVost = new L.KML(kmlVost);
                map.addLayer(trackVost);

            // Adjust map to show the kml
            const boundsVost = trackVost.getBounds();
            map.fitBounds(boundsVost);
            @endif

            @isset($fire['id'])
            $.ajax({
                url: 'https://source.fogos.pt/v2/incidents/{{ $fire['id'] }}/kmlFirms',
                method: 'GET',
                dataType: 'text'
            }).done(function (kmlFirmsText) {
                if (!kmlFirmsText || !kmlFirmsText.trim()) return;
                try {
                    var firmsDoc = new DOMParser().parseFromString(kmlFirmsText, 'text/xml');
                    if (firmsDoc.getElementsByTagName('parsererror').length) return;
                    var firmsTrack = new L.KML(firmsDoc);
                    map.addLayer(firmsTrack);
                    $('.js-kml-firms-link, .js-kml-firms-legend').removeClass('d-none');
                } catch (e) {}
            });
            @endisset

            var photoMarkers = [];
            function clearPhotoMarkers() {
                photoMarkers.forEach(function (m) { map.removeLayer(m); });
                photoMarkers = [];
            }
            function buildPhotoIcon(headingDeg) {
                var hasHeading = (typeof headingDeg === 'number' && isFinite(headingDeg));
                var inner;
                if (hasHeading) {
                    inner = '<svg viewBox="0 0 40 40" width="40" height="40" style="overflow:visible">' +
                        '<path d="M20 20 L8 2 A18 18 0 0 1 32 2 Z" fill="rgba(255,140,0,0.45)" stroke="rgba(255,120,0,0.95)" stroke-width="1.2" stroke-linejoin="round"/>' +
                        '<circle cx="20" cy="20" r="5" fill="#fff" stroke="#ff7800" stroke-width="2"/>' +
                        '</svg>';
                } else {
                    inner = '<svg viewBox="0 0 40 40" width="40" height="40">' +
                        '<circle cx="20" cy="20" r="9" fill="#fff" stroke="#ff7800" stroke-width="2.5"/>' +
                        '<circle cx="20" cy="20" r="3.5" fill="#ff7800"/>' +
                        '</svg>';
                }
                var style = hasHeading ? 'transform: rotate(' + headingDeg + 'deg);' : '';
                return L.divIcon({
                    className: 'fogos-photo-divicon',
                    html: '<div class="photo-marker" style="' + style + '">' + inner + '</div>',
                    iconSize: [40, 40],
                    iconAnchor: [20, 20]
                });
            }
            function renderPhotoMarkers(items) {
                clearPhotoMarkers();
                (items || []).forEach(function (item, idx) {
                    var g = item && item.gps;
                    if (!g || g.lat == null || g.lng == null) return;
                    var marker = L.marker([g.lat, g.lng], {
                        icon: buildPhotoIcon(g.heading_deg),
                        zIndexOffset: 500,
                        keyboard: false
                    });
                    marker.on('click', function () {
                        if (typeof window.openFogosPhoto === 'function') {
                            window.openFogosPhoto(idx);
                        }
                    });
                    marker.addTo(map);
                    photoMarkers.push(marker);
                });
            }
            window.addEventListener('fogos-photos-loaded', function (e) {
                renderPhotoMarkers(e && e.detail && e.detail.items);
            });
            if (Array.isArray(window.fogosPhotoItems) && window.fogosPhotoItems.length) {
                renderPhotoMarkers(window.fogosPhotoItems);
            }
        } );
    </script>

@endpush
