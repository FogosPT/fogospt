<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    @include('includes.head')
    @include('includes.meta')
    {{-- Defined in <head> so page scripts (e.g. detail.blade.php) can extend
         window.trans without it being clobbered afterwards. --}}
    <script>window.trans = @json(__('js'));</script>
</head>
<body>

@include('includes.navbar')

@yield('content')

@include('includes.scripts')

<script>
  $(document).on('click', '.js-open-consent', function (e) {
    e.preventDefault();
    if (window.FogosConsent) FogosConsent.open();
  });
</script>
</body>
</html>