@php
    $currentPath  = request()->path(); // e.g. "pt/fogo/123" or "pt"
    $pathWithoutLocale = trim(preg_replace('#^[a-z]{2}(/|$)#', '', $currentPath), '/');
    $suffix = $pathWithoutLocale === '' ? '' : '/' . $pathWithoutLocale;
    $ptUrl = url('pt' . $suffix);
    $enUrl = url('en' . $suffix);
    $esUrl = url('es' . $suffix);
    $canonicalUrl = url(app()->getLocale() . $suffix);
@endphp
<link rel="canonical" href="{{ $canonicalUrl }}">
<link rel="alternate" hreflang="x-default" href="{{ $ptUrl }}">
<link rel="alternate" hreflang="pt" href="{{ $ptUrl }}">
<link rel="alternate" hreflang="en" href="{{ $enUrl }}">
<link rel="alternate" hreflang="es" href="{{ $esUrl }}">

<meta name="Description" content="{{$metadata['description']}}">
<meta name="Keywords"
      content="@lang('includes.meta.content')">
<meta property="og:title" content="Fogos.pt {{$metadata['title']}}">
<meta property="og:site_name" content="Fogos.pt">
<meta property="og:url" content="{{$metadata['url']}}">
<meta property="og:description" content="{{$metadata['description']}}">
<meta property="og:type" content="website">
<meta property="og:image" content="https://fogos.pt/img/og-image.png">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:image:type" content="image/png">
<meta property="fb:app_id" content="966421526811840">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@tomahock">
<meta name="twitter:title" content="Fogos.pt - {{$metadata['title']}}">
<meta name="twitter:description" content="{{$metadata['description']}}">
<meta name="twitter:image" content="https://fogos.pt/img/og-image.png">

<title>Fogos.pt {{$metadata['pageTitle']}}</title>