@extends('app')

@section('content')
    <main role="main" class="mb-auto margin-top-10">
        <div class="container">

            <div class="row align-items-stretch">
                <div class="col-12">
                    <h1>@lang('includes.menu.stats')</h1>
                </div>
            </div>

            <div class="card flex-column flex-md-row align-items-stretch">
                <div class="col-12 col-md-6 px-0">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Agora</h4>
                            <div class="d-flex justify-content-between">
                                <div class="assets d-flex align-items-center justify-content-center">
                                    <img class="assets-icon" src="/img/logo_flame.svg">
                                    <span class="assets-nr f-man">
                                        @isset($data['now']['total'])
                                            {{ $data['now']['total'] }}
                                        @endisset
                                    </span>
                                    <img class="assets-icon" src="/img/fireman.svg">
                                    <span class="assets-nr f-man">
                                        @isset($data['now']['man'])
                                            {{ $data['now']['man'] }}
                                        @endisset
                                    </span>
                                    <img class="assets-icon" src="/img/firetruck.svg">
                                    <span class="assets-nr f-terrain">
                                        @isset($data['now']['cars'])
                                            {{ $data['now']['cars'] }}
                                        @endisset
                                    </span>
                                    <img class="assets-icon" src="/img/plane.svg">
                                    <span class="assets-nr f-aerial">
                                    @isset($data['now']['aerial'])
                                            {{ $data['now']['aerial'] }}
                                        @endisset
                                    </span>

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
            </div>


            <div class="card flex-column flex-md-row align-items-stretch phantom-hide">
                <div class="col-6 col-md-12 px-0">
                    <div class="card">
                        <div class="card-body">
                            <h4 class="card-title">Últimos dias</h4>
                            <canvas style="padding: 0 5px" id="myChartStatsWeek" class="col-12 col-md-12 px-0"></canvas>
                            </div>
                        </div>
                    </div>
                </div>

            </div>


        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <script src="js/stats.js"></script>
@endpush