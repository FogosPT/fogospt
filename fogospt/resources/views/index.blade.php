@extends('app')

@push('head')
    <link rel="stylesheet" href="/css/vendor/leaflet-openweathermap.css">
@endpush

@section('content')
    <main role="main" class="mb-auto">
        @include('includes.sidebar')
        <div id="map">
            <a href="http://mapbox.com/about/maps" class='mapbox-wordmark' target="_blank">Mapbox</a>
        </div>
    </main>
@endsection


@push('scripts')
    <script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js"
            integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw=="
            crossorigin=""></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>
    <script src="/js/concelhos.js"></script>
    <script src="/js/vendor/leaflet-openweathermap.js"></script>
    <script src="/js/vendor/store2.min.js"></script>
    <script src="/js/main.js"></script>

@endpush
