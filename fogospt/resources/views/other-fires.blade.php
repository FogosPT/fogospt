@extends('app')

@push('styles')
    <link rel="stylesheet" href="/css/vendor/leaflet-openweathermap.css?v={{ filemtime(public_path('css/vendor/leaflet-openweathermap.css')) }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/mapbox-gl/2.4.1/mapbox-gl.min.css">
@endpush

@section('content')
    <main role="main" class="mb-auto">
        @include('includes.sidebar')
        <div id="map">
            <a href="https://www.mapbox.com/about/maps" class="mapbox-wordmark" target="_blank" rel="noopener">Mapbox</a>
        </div>
    </main>

    @include('includes.site-warning')
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mapbox-gl/2.4.1/mapbox-gl.min.js" integrity="sha512-NTZ4yfDV+hnycF2x28e43icmAkkDWZ6b+RXZQJkyUGKA78WQb0gIuv7RKF8W+5XAKunxzzJq4i8FD2L04h9O1g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.8.0/leaflet.js" integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://unpkg.com/mapbox-gl-leaflet@0.0.16/leaflet-mapbox-gl.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js"></script>
    <script src="/js/concelhos.js?v={{ filemtime(public_path('js/concelhos.js')) }}"></script>
    <script src="/js/portugal.js?v={{ filemtime(public_path('js/portugal.js')) }}"></script>
    <script src="/js/vendor/leaflet-openweathermap.js?v={{ filemtime(public_path('js/vendor/leaflet-openweathermap.js')) }}"></script>
    <script src="/js/vendor/store2.min.js?v={{ filemtime(public_path('js/vendor/store2.min.js')) }}"></script>
    <script src="/js/share.js?v={{ filemtime(public_path('js/share.js')) }}"></script>
    <script src="/js/main-other.js?v={{ filemtime(public_path('js/main-other.js')) }}"></script>

@endpush
