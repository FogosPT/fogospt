<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    @include('includes.head')
    @include('includes.meta')
</head>
<body>

@include('includes.navbar')

<main role="main" class="mb-auto">
    <div>MANIFEST</div>
</main>

@include('includes.scripts')
<script>
    $("#menuTopo").find('a.active').removeClass('active');
    $("#menuTopo").find('a[href*="/manifesto"]').addClass('active');
</script>
</body>
</html>
