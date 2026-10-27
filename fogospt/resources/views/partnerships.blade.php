@extends('app')

@php
    $partners = [
        ['name' => 'PTServidor',    'url' => 'https://ptservidor.pt/',  'img' => 'img/partners/ptservidor.png'],
        ['name' => 'Officelan',     'url' => 'https://officelan.pt/',   'img' => 'img/partners/officelan.png'],
        ['name' => 'VOST Portugal', 'url' => 'https://vost.pt/',        'img' => 'img/partners/vostpt.png'],
        ['name' => 'Mapbox',        'url' => 'https://www.mapbox.com/', 'img' => 'img/partners/mapbox.png'],
    ];
@endphp

@section('content')
    <main role="main" class="mb-auto margin-top-10">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('includes.menu.partnerships')</h1>
                </div>
            </div>

            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 mb-4 text-center">
                @foreach($partners as $partner)
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <div class="card-header">
                                <h4 class="my-0 fw-normal">{{ $partner['name'] }}</h4>
                            </div>
                            <div class="card-body d-flex flex-column">
                                <a href="{{ $partner['url'] }}" target="_blank" rel="noopener" class="flex-grow-1 d-flex align-items-center justify-content-center mb-3">
                                    <img src="{{ asset($partner['img']) }}" alt="{{ $partner['name'] }}" class="img-fluid" loading="lazy">
                                </a>
                                <a href="{{ $partner['url'] }}" target="_blank" rel="noopener" class="btn btn-lg btn-primary w-100">Visitar</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </main>
@endsection
