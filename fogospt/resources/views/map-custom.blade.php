@extends('app')

@push('styles')
    <link rel="stylesheet" href="/css/vendor/leaflet-openweathermap.css?v={{ filemtime(public_path('css/vendor/leaflet-openweathermap.css')) }}">
    <link rel="stylesheet" href="https://unpkg.com/photoswipe@5/dist/photoswipe.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet-velocity@2/dist/leaflet-velocity.css">
    <style>
        .fogos-map-summary {
            background: rgba(255, 255, 255, .96);
            border: 1px solid #d0d0d0;
            border-radius: 6px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .18);
            padding: 8px 10px;
            font: 12px/1.35 sans-serif;
            color: #222;
            max-width: 280px;
        }
        .fogos-map-summary__title {
            font-weight: 600;
            font-size: .78rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #b81e1f;
            margin-bottom: 3px;
        }
        .fogos-map-summary__list {
            text-transform: capitalize;
            margin-bottom: 6px;
            max-height: 96px;
            overflow-y: auto;
        }
        .fogos-map-summary__edit {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            font-size: .82rem;
            padding: 4px 10px;
            background: #b81e1f;
            color: #fff;
            border-radius: 4px;
            text-decoration: none;
        }
        .fogos-map-summary__edit:hover {
            background: #9a1919;
            color: #fff;
        }
        .fogos-map-summary__more {
            color: #666;
            font-size: .78rem;
            font-style: italic;
            text-transform: none;
        }
    </style>
@endpush

@section('content')
    <main role="main" class="mb-auto">
        <h1 class="visually-hidden">{{ __('pages.seo.mapCustom.title') }}</h1>
        <p class="visually-hidden">{{ __('pages.seo.mapCustom.description') }}</p>
        @include('includes.sidebar')
        <div id="map">
        </div>
    </main>

    @include('includes.site-warning')
@endsection

@push('scripts')
    <script>window.fogosCustomMap = true;</script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js" integrity="sha512-BwHfrr4c9kmRkLw6iXFdzcdWV/PGkVgiIyIWLLlTSXzWQzxuSg4DiQUCpauz/EWjgk5TYQqX/kvn9pG1NpYfqg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.1/chart.umd.min.js" integrity="sha512-WoViKhKD4qI2WruSZqv9+kvM4WfFhUMQCLN4QlDTt5aU56fLQy2gYoxWIqlEnXqJy/+Ac5q/hk1oWfqnMDhwMA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="/js/resources-chart.js?v={{ filemtime(public_path('js/resources-chart.js')) }}"></script>
    <script src="/js/concelhos.js?v={{ filemtime(public_path('js/concelhos.js')) }}"></script>
    <script src="/js/portugal.js?v={{ filemtime(public_path('js/portugal.js')) }}"></script>
    <script src="/js/vendor/store2.min.js?v={{ filemtime(public_path('js/vendor/store2.min.js')) }}"></script>
    <script src="/js/share.js?v={{ filemtime(public_path('js/share.js')) }}"></script>
    <script src="/js/vendor/L.KLM.js?v={{ filemtime(public_path('js/vendor/L.KLM.js')) }}"></script>
    <script src="https://unpkg.com/leaflet-velocity@2/dist/leaflet-velocity.min.js"></script>
    <script src="/js/map-panel.js?v={{ filemtime(public_path('js/map-panel.js')) }}"></script>
    <script src="/js/satellite.js?v={{ filemtime(public_path('js/satellite.js')) }}"></script>
    <script src="/js/map-filters.js?v={{ filemtime(public_path('js/map-filters.js')) }}"></script>
    <script src="/js/photos.js?v={{ filemtime(public_path('js/photos.js')) }}"></script>
    <script src="/js/planes.js?v={{ filemtime(public_path('js/planes.js')) }}"></script>
    <script type="module">
        import PhotoSwipeLightbox from 'https://unpkg.com/photoswipe@5/dist/photoswipe-lightbox.esm.js';
        window.PhotoSwipeLightbox = PhotoSwipeLightbox;
        window.dispatchEvent(new Event('photoswipe-ready'));
    </script>
    <script src="/js/fogos-core.js?v={{ filemtime(public_path('js/fogos-core.js')) }}"></script>
    <script src="/js/main.js?v={{ filemtime(public_path('js/main.js')) }}"></script>
@endpush
