@extends('app')

@push('styles')
    <link rel="stylesheet" href="/css/vendor/leaflet-openweathermap.css?v={{ filemtime(public_path('css/vendor/leaflet-openweathermap.css')) }}">
@endpush

@section('content')
    <main role="main" class="mb-auto">
        @include('includes.sidebar')
        <div id="map">
        </div>
    </main>

    @include('includes.site-warning')
@endsection

@push('scripts')

    <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet/1.9.4/leaflet.js" integrity="sha512-BwHfrr4c9kmRkLw6iXFdzcdWV/PGkVgiIyIWLLlTSXzWQzxuSg4DiQUCpauz/EWjgk5TYQqX/kvn9pG1NpYfqg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.5.1/chart.umd.min.js" integrity="sha512-WoViKhKD4qI2WruSZqv9+kvM4WfFhUMQCLN4QlDTt5aU56fLQy2gYoxWIqlEnXqJy/+Ac5q/hk1oWfqnMDhwMA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="/js/resources-chart.js?v={{ filemtime(public_path('js/resources-chart.js')) }}"></script>
    <script>window.FOGOS_CONCELHOS_URL = "/js/concelhos.js?v={{ filemtime(public_path('js/concelhos.js')) }}";</script>
    <script src="/js/portugal.js?v={{ filemtime(public_path('js/portugal.js')) }}"></script>
    <script src="/js/vendor/leaflet-openweathermap.js?v={{ filemtime(public_path('js/vendor/leaflet-openweathermap.js')) }}"></script>
    <script src="/js/vendor/store2.min.js?v={{ filemtime(public_path('js/vendor/store2.min.js')) }}"></script>
    <script src="/js/share.js?v={{ filemtime(public_path('js/share.js')) }}"></script>
    <script src="/js/fogos-core.js?v={{ filemtime(public_path('js/fogos-core.js')) }}"></script>
    <script src="/js/main-other.js?v={{ filemtime(public_path('js/main-other.js')) }}"></script>

@endpush
