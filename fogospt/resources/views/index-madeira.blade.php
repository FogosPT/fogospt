@extends('app')

@push('styles')
    <link rel="stylesheet" href="/css/vendor/leaflet-openweathermap.css?v={{ filemtime(public_path('css/vendor/leaflet-openweathermap.css')) }}">
@endpush

@section('content')
    <main role="main" class="mb-auto madeira-page">
        <h1 class="visually-hidden">{{ __('pages.seo.madeira.h1') }}</h1>
        <p class="visually-hidden">{{ __('pages.seo.madeira.intro') }}</p>
        @include('includes.sidebar')
        {{-- SRPC, IP-RAM data (Fogos.pt API v2), filled in by main2.js. --}}
        <aside class="madeira-panel" aria-labelledby="madeira-occ-title">
            <section id="madeira-event" class="card madeira-event" aria-live="polite" hidden></section>
            <section class="card">
                <div class="card-body">
                    <h2 class="card-title" id="madeira-occ-title">{{ __('js.madeira.title') }}</h2>
                    <p class="madeira-meta" id="madeira-occ-meta" aria-live="polite">{{ __('js.madeira.loading') }}</p>
                    <div id="madeira-occ-list"></div>
                    <p class="madeira-note"><i class="fas fa-info-circle" aria-hidden="true"></i> {{ __('js.madeira.approxNote') }}</p>
                    <p class="madeira-note">{{ __('js.madeira.source') }} <span id="madeira-occ-updated"></span></p>
                </div>
            </section>
        </aside>
        <div id="map">
            <a href="https://www.mapbox.com/about/maps" class="mapbox-wordmark" target="_blank" rel="noopener">Mapbox</a>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.8.0/leaflet.js" integrity="sha512-BB3hKbKWOc9Ez/TAwyWxNXeoV9c1v6FIeYiBieIWkpLjauysF18NzgR1MBNBXf8/KABdlkX68nAhlwcDFLGPCQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="/js/concelhos.js?v={{ filemtime(public_path('js/concelhos.js')) }}"></script>
    <script src="/js/vendor/leaflet-openweathermap.js?v={{ filemtime(public_path('js/vendor/leaflet-openweathermap.js')) }}"></script>
    <script src="/js/vendor/store2.min.js?v={{ filemtime(public_path('js/vendor/store2.min.js')) }}"></script>
    <script src="/js/satellite.js?v={{ filemtime(public_path('js/satellite.js')) }}"></script>
    <script src="/js/main2.js?v={{ filemtime(public_path('js/main2.js')) }}"></script>
@endpush