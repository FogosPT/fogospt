@extends('app')

@section('content')
    <main role="main" class="mb-auto margin-top-10">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('includes.menu.partnerships')</h1>
                </div>
            </div>


            <div class="card-deck mb-3 text-center">
                <div class="card mb-4 shadow-sm">
                    <div class="card-header">
                        <h4 class="my-0 font-weight-normal">PTServidor</h4>
                    </div>
                    <div class="card-body">
                        <div class="card-body">
                            <a href="https://ptservidor.pt/">
                                <img src="{{asset('img/partners/ptservidor.png')}}" alt="PTServodor">
                            </a>
                        </div>
                        <a type="button" href="https://ptservidor.pt/"
                           class="btn btn-lg btn-block btn-primary">Visitar</a>
                    </div>
                </div>

                <div class="card mb-4 shadow-sm">
                    <div class="card-header">
                        <h4 class="my-0 font-weight-normal">Officelan</h4>
                    </div>
                    <div class="card-body">
                        <div class="card-body">
                            <a href="https://officelan.pt/">
                                <img src="{{asset('img/partners/officelan.png')}}" alt="Officelan">
                            </a>
                        </div>
                        <a type="button" href="https://officelan.pt/"
                           class="btn btn-lg btn-block btn-primary">Visitar</a>
                    </div>
                </div>

                <div class="card-deck mb-3 text-center">
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header">
                            <h4 class="my-0 font-weight-normal">VOST Portugal</h4>
                        </div>
                        <div class="card-body">
                            <div class="card-body">
                                <a href="https://vost.pt/">
                                    <img src="{{asset('img/partners/vostpt.png')}}" alt="VOST Portugal">
                                </a>
                            </div>
                            <a type="button" href="https://vost.pt/"
                               class="btn btn-lg btn-block btn-primary">Visitar</a>
                        </div>
                    </div>

                    <div class="card mb-4 shadow-sm">
                        <div class="card-header">
                            <h4 class="my-0 font-weight-normal">MAPBOX</h4>
                        </div>
                        <div class="card-body">
                            <div class="card-body">
                                <a href="https://www.mapbox.com">
                                    <img src="{{asset('img/partners/mapbox.png')}}" alt="Mapbox">
                                </a>
                            </div>
                            <a type="button" href="https://www.mapbox.com" class="btn btn-lg btn-block btn-primary">Visitar</a>
                        </div>
                    </div>
                </div>
                <div class="row  bg-white">

                </div>
            </div>
    </main>
@endsection
