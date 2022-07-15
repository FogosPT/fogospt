@extends('app')

@section('content')
    <main role="main" class="mb-auto margin-top-10">
        <div class="container">
            <div class="row">
                <div class="col-sm-12 col-md-12">
                    <div class="card">
                        @isset($fire['kml'])
                                <div style="height: 400px" id="mymap"></div>
                            <p>Área Ardida segundo dados do ICNF ou Área de interesse por <a href="https://vost.pt">VOST.pt</a></p>
                        @else
                            <a  href="{{route('fireDetail', $fire['id'])}}" >

                                <img class="card-img-top img-fluid" src="https://api.mapbox.com/styles/v1/mapbox/satellite-streets-v11/static/url-https%3A%2F%2Ffogos.pt%2Fimg%2Fprimeiro_despacho.png({{$fire['lng']}},{{$fire['lat']}})/{{$fire['lng']}},{{$fire['lat']}},15,0,00/700x393?access_token={{env('MAPBOX_TOKEN')}}">
                            </a>
                        @endisset
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

                                    @isset($fire['icnf']['fontealerta'])
                                        <h4 class="card-title">@lang('elements.cards.general.alertFrom')</h4>
                                        <p class="f-nature">
                                            {{ $fire['icnf']['fontealerta'] }}
                                        </p>
                                    @endisset

                                    @isset($fire['icnf']['burnArea'])
                                        <h4 class="card-title">@lang('elements.cards.detail.burn.title')</h4>
                                        <p class="f-nature">
                                                {{ $fire['icnf']['burnArea']['total'] }} HA @isset($fire['kml']) <a target="_blank" href="https://{{env('API_URL')}}/v2/incidents/{{$fire['id']}}/kml">Download</a> @endisset
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
                                        @isset($fire['risk'])
                                            @include('elements.risk')
                                        @endisset
                                    </div>

                                    @if( !empty($fire['extra']) || !empty($fire['pco']) || !empty($fire['cos']) )
                                        <div class="f-extra">
                                            @include('elements.extra')
                                        </div>
                                    @endif

                                    <h4 class="card-title">@lang('elements.cards.twitter.title')</h4>
                                    <p class="f-twitter">
                                        @include('elements.twitter')
                                    </p>


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
                                    <canvas class="px-2 py-0" id="myChart" width="400" height="150" data-id="{{$fire['id']}}"></canvas>

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
                                        @isset($fire['meteo'])
                                            @include('elements.meteo')
                                        @endisset
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

    <script src="https://unpkg.com/mapbox-gl-leaflet/leaflet-mapbox-gl.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <script src="/js/vendor/store2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.24.0/moment.min.js" integrity="sha256-4iQZ6BVL4qNKlQ27TExEhBN1HFPvAvAMbFavKKosSWQ=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/vendor/L.KLM.js') }}"></script>
    <script src="{{ asset('js/detail.js') }}"></script>

    <script>
        @isset($fire['kml'])
        $(document).ready( function () {
            // Make basemap
            const map = new L.Map('mymap', { center: new L.LatLng(58.4, 43.0), zoom: 11 });
            const osm = new L.TileLayer('https://api.mapbox.com/styles/v1/fogospt/cjksgciqsctfg2rp9x9uyh37g/tiles/256/{z}/{x}/{y}@2x?access_token=pk.eyJ1IjoiZm9nb3NwdCIsImEiOiJjamZ3Y2E5OTMyMjFnMnFxbjAxbmt3bmdtIn0.xg1X-A17WRBaDghhzsmjIA');

            map.addLayer(osm);

            // Load kml file
                var kmltext = '{!! $kml !!}';
                // Create new kml overlay
                const parser = new DOMParser();
                const kml = parser.parseFromString(kmltext, 'text/xml');
                const track = new L.KML(kml);
                map.addLayer(track);

                // Adjust map to show the kml
                const bounds = track.getBounds();
                console.log(bounds);
                map.fitBounds(bounds);
        } );
        @endisset
    </script>

@endpush
