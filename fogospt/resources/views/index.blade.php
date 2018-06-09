@extends('app')

@section('content')
    <main role="main" class="mb-auto">
        @include('includes.sidebar')
        <div id="map"></div>
    </main>
@endsection


@push('scripts')
    <script src="/js/concelhos.js"></script>
    <script src="/js/main.js"></script>
@endpush