@php
    $currentPath  = request()->path(); // e.g. "pt/fogo/123"
    $pathWithoutLocale = preg_replace('#^[a-z]{2}(/|$)#', '', $currentPath); // e.g. "fogo/123"
    $canonicalUrl = url('pt/' . $pathWithoutLocale);
@endphp
<link rel="canonical" href="{{ $canonicalUrl }}">
<link rel="alternate" hreflang="x-default" href="{{ $canonicalUrl }}">
<link rel="alternate" hreflang="pt" href="{{ url('pt/' . $pathWithoutLocale) }}">
<link rel="alternate" hreflang="en" href="{{ url('en/' . $pathWithoutLocale) }}">
<link rel="alternate" hreflang="es" href="{{ url('es/' . $pathWithoutLocale) }}">

<meta name="Description" content="{{$metadata['description']}}">
<meta name="Keywords"
      content="@lang('includes.meta.content')">
<meta property="og:title" content="Fogos.pt {{$metadata['title']}}">
<meta property="og:site_name" content="Fogos.pt">
<meta property="og:url" content="{{$metadata['url']}}">
<meta property="og:description" content="{{$metadata['description']}}">
<meta property="og:type" content="website">
<meta property="og:image" content="https://fogos.pt/img/logo.png">
<meta property="fb:app_id" content="966421526811840">
<meta name="twitter:card" content="summary">
<meta name="twitter:site" content="@tomahock">
<meta name="twitter:title" content="Fogos.pt - {{$metadata['title']}}">
<meta name="twitter:description" content="{{$metadata['description']}}">
<meta name="twitter:image" content="https://fogos.pt/img/logo.png">

<title>Fogos.pt {{$metadata['pageTitle']}}</title>