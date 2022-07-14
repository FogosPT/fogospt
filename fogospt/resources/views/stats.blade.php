@extends('app')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.4.0/dist/leaflet.css"
   integrity="sha512-puBpdR0798OZvTTbP4A8Ix/l+A4dHDD0DGqYW6RQ+9jxkRFclaxxQb/SJAWZfWAkuyeQUytO7+7N4QKrDh+drA=="
   crossorigin="">

@endpush

@section('content')
    <main role="main" class="mb-auto margin-top-10">
        <div class="container">

            <div class="row align-items-stretch">
                <div class="col-12">
                    <h1>@lang('includes.menu.stats')</h1>
                </div>
            </div>

            <section class="card flex-column flex-md-row align-items-stretch stats">
                <div class="col-12 col-md-6 px-0">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">@lang('pages.stats.now-text')</h4>
                            <div class="d-flex justify-content-between">
                                <div class="assets d-flex align-items-center justify-content-center">
                                    <div class="d-flex flex-column">
                                        <div class="d-flex flex-row align-items-center">
                                            <img class="assets-icon" src="/img/logo_flame.svg">
                                            <span class="assets-nr f-man">
                                                @isset($data['now']['total'])
                                                {{ $data['now']['total'] }}
                                                @endisset
                                            </span>
                                        </div>
                                        <div class="d-flex flex-row align-items-center">
                                            <img class="assets-icon" src="/img/fireman.svg">
                                            <span class="assets-nr f-man">
                                                @isset($data['now']['man'])
                                                {{ $data['now']['man'] }}
                                                @endisset
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex flex-column p-4">
                                        <div class="d-flex flex-row align-items-center">
                                            <img class="assets-icon" src="/img/firetruck.svg">
                                            <span class="assets-nr f-terrain">
                                                @isset($data['now']['cars'])
                                                {{ $data['now']['cars'] }}
                                                @endisset
                                            </span>
                                        </div>
                                        <div class="d-flex flex-row align-items-center">
                                            <img class="assets-icon" src="/img/plane.svg">
                                            <span class="assets-nr f-aerial">
                                                @isset($data['now']['aerial'])
                                                {{ $data['now']['aerial'] }}
                                                @endisset
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-between">
                                <p>
                                    <small>@lang('pages.stats.now.footer')</small>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <canvas style="padding: 0 5px" id="myChart" class="col-12 col-md-6 px-0"></canvas>
            </section>

            <section class="row phantom-hide">
                <section class="col-12 col-md-6">
                    <section class="card flex-column flex-md-row align-items-stretch stats">
                        <div class="col-12 px-0">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">@lang('pages.stats.today')</h4>
                                    <canvas style="padding: 0 5px" id="myChartStats8hours"
                                            class="col-12 px-0"></canvas>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="card flex-column flex-md-row align-items-stretch stats">
                        <div class="col-12 px-0">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">@lang('pages.stats.yesterday')</h4>
                                    <canvas style="padding: 0 5px" id="myChartStatsYesterday"
                                            class="col-12 px-0"></canvas>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="card flex-column flex-md-row align-items-stretch stats">
                        <div class="col-12 col-md-12 px-0">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">@lang('pages.stats.last-days')</h4>
                                    <canvas style="padding: 0 5px" id="myChartStatsWeek"
                                            class="col-12 col-md-12 px-0"></canvas>
                                </div>
                            </div>
                        </div>
                    </section>


                    <section class="card flex-column flex-md-row align-items-stretch stats">
                        <div class="col-12 px-0">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">@lang('pages.stats.today')</h4>

                                     <div id="mapid" style="height:700px; "></div>


                                </div>
                            </div>
                        </div>
                    </section>
                </section>

                <section class="col-12 col-md-6">
                    <section class="card flex-column flex-md-row align-items-stretch stats">
                        <div class="col-12 px-0">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">@lang('pages.stats.today')</h4>
                                    <canvas style="padding: 0 5px" id="myChartStatsToday"
                                            class="col-12 px-0"></canvas>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="card flex-column flex-md-row align-items-stretch stats">
                        <div class="col-12 px-0">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">@lang('pages.stats.yesterday')</h4>
                                    <canvas style="padding: 0 5px" id="myChartStats8hoursYesterday"
                                            class="col-12 px-0"></canvas>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="card flex-column flex-md-row align-items-stretch stats">
                        <div class="col-12 px-0">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">@lang('pages.stats.last-night')</h4>
                                    <canvas style="padding: 0 5px" id="myChartStatsLastNight"
                                            class="col-12 px-0"></canvas>
                                    <div class="d-flex justify-content-between">
                                        <p>
                                            <small>@lang('pages.stats.last-night-footer')</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>


                    <section class="card flex-column flex-md-row align-items-stretch stats">
                        <div class="col-12 px-0">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">@lang('pages.stats.burn-area-last-days')</h4>
                                    <canvas style="padding: 0 5px" id="myChartBurnAreaLastDays"
                                            class="col-12 px-0"></canvas>
                                    <div class="d-flex justify-content-between">
                                        <p>
                                            <small>@lang('pages.stats.burn-area-last-days-footer')</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>



                    <section class="card flex-column flex-md-row align-items-stretch stats">
                        <div class="col-12 px-0">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title">@lang('pages.stats.motives.title')</h4>
                                    <canvas style="padding: 0 5px" id="myChartMotives"
                                            class="col-12 px-0"></canvas>
                                    <div class="d-flex justify-content-between">
                                        <p>
                                            <small>@lang('pages.stats.motives.footer')</small>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>



                </section>

            </section>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.8.0/chart.min.js" integrity="sha512-sW/w8s4RWTdFFSduOTGtk4isV1+190E/GghVffMA9XczdJ2MDzSzLEubKAs5h0wzgSJOQTRYyaz73L3d6RtJSg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script> window.mapboxKey = '{{ env('MAPBOX_TOKEN') }}' </script>

    <script src="js/stats.js"></script>

    <script src="https://unpkg.com/leaflet@1.4.0/dist/leaflet.js"
   integrity="sha512-QVftwZFqvtRNi0ZyCtsznlKSWOStnDORoefr1enyq5mVL4tmKB3S/EnC3rRJcxCPavG10IcrVGSmPh6Qw5lwrg=="
   crossorigin=""></script>

   <script src="/js/distritos.js"></script>


@endpush
