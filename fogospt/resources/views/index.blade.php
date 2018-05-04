<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="//fonts.googleapis.com/css?family=Roboto:300,400,500,700">

    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.11/css/all.css"
          integrity="sha384-p2jx59pefphTFIpeqCcISO9MdVfIm4pNnsL08A6v5vaQc4owkQqxMV8kg4Yvhaw/" crossorigin="anonymous">


    {{--<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">--}}

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.5.2/animate.min.css">
    <link rel="stylesheet" href="/css/app.css">

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.3.1/dist/leaflet.css"
          integrity="sha512-Rksm5RenBEKSKFjgI3a41vrjkw4EVPlJ3+OiI65vTjIdo9brlAacEuKOiQ5OFh7cOI1bkDwLqdLw3Zg0cRJAAQ=="
          crossorigin=""/>
    <script src="https://unpkg.com/leaflet@1.3.1/dist/leaflet.js"
            integrity="sha512-/Nsx9X4HebavoBvEBuyp3I7od5tA0UzAxs+j83KgC8PU0kgB4XiK4Lfe4y4cgBtaRJQEIFCW+oC506aPT2L1zw=="
            crossorigin=""></script>

    {{--<script src='https://api.tiles.mapbox.com/mapbox-gl-js/v0.44.1/mapbox-gl.js'></script>--}}
    {{--<link href='https://api.tiles.mapbox.com/mapbox-gl-js/v0.44.1/mapbox-gl.css' rel='stylesheet'/>--}}

    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.4.0/Chart.min.js"></script>

    <script src="/js/concelhos.js"></script>
    <script src="/js/main.js"></script>

    <title>Beta Fogos.pt</title>
</head>
<body>
<header id="header" class="fixed-top" role="banner">
    <nav class="navbar navbar-dark">
        <div class="container flex-column flex-sm-row justify-content-center justify-content-sm-between">
            <div>
                <a class="navbar-brand" href="#"><img src="/img/logo.svg" width="240px"></a>
            </div>
        </div>
    </nav>
</header>
<main role="main" class="mb-auto">
    <div class="sidebar">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Local</h4>
                        <p class="f-local"></p>
                        <h4 class="card-title">Início</h4>
                        <p class="f-start"></p>
                        <h4 class="card-title">Natureza</h4>
                        <p class="f-nature"></p>
                        <h4 class="card-title">Risco de Incêndio</h4>
                        <div class="f-danger"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Meios</h4>
                        <table class="table table-sm">
                            <tbody class="active">
                            <tr>
                                <td class="float-left">
                                    <i class="fa fa-user" aria-hidden="true"></i> <span class="f-man"></span>
                                </td>
                                <td class="float-left">
                                    <i class="fa fa-plane" aria-hidden="true"></i> <span class="f-aerial"></span>
                                </td>
                                <td class="float-left">
                                    <i class="fa fa-truck" aria-hidden="true"></i> <span class="f-terrain"></span>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                        <canvas style="padding: 0 5px" id="myChart" width="400" height="150"></canvas>
                    </div>
                </div>
            </div>
        </div>


        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title">Estado</h4>
                        <div id="status"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div id="map"></div>
</main>

{{--<div class="sidebar fadeInLeft">--}}
{{--<div class="wrap">--}}
{{--<div class="card">--}}
{{--<div class="card-body">--}}
{{--<h4 class="card-title">Meios</h4>--}}
{{--<div class="row align-items-center align-items-sm-top  align-items-md-top align-items-lg-center">--}}
{{--<div class="col-4 icon">--}}
{{--<i class="fa fa-user" aria-hidden="true"></i>90--}}
{{--</div>--}}
{{--<div class="col-4 icon">--}}
{{--<i class="fa fa-truck" aria-hidden="true"></i>20--}}
{{--</div>--}}
{{--<div class="col-4 icon">--}}
{{--<i class="fa fa-plane" aria-hidden="true"></i>11--}}
{{--</div>--}}
{{--</div>--}}
{{--<div class="row align-items-center align-items-sm-top  align-items-md-top align-items-lg-center">--}}
{{--<canvas id="myChart"></canvas>--}}
{{--</div>--}}
{{--<div class="d-flex justify-content-between">--}}
{{--<a href="#" class="data-source">Fonte: ANPC</a>--}}
{{--<a href="#" class="share-icon"><i class="fa fa-share"></i></a>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}

{{--<div class="card">--}}
{{--<div class="card-body">--}}
{{--<h4 class="card-title">Estado</h4>--}}
{{--<div class="row align-items-center align-items-sm-top  align-items-md-top align-items-lg-center">--}}
{{--<ul class="timeline">--}}
{{--<li>--}}
{{--<div class="direction-l">--}}
{{--<div class="flag-wrapper"><span class="flag">Inicio</span><span--}}
{{--class="time-wrapper"><span class="time">17:02</span></span></div>--}}
{{--</div>--}}
{{--</li>--}}
{{--<li>--}}
{{--<div class="direction-r">--}}
{{--<div class="flag-wrapper"><span class="flag">Despacho de 1º Alerta</span><span--}}
{{--class="time-wrapper"><span class="time">17:14</span></span></div>--}}
{{--</div>--}}
{{--</li>--}}
{{--<li>--}}
{{--<div class="direction-l">--}}
{{--<div class="flag-wrapper"><span class="flag">Chegada ao TO</span><span--}}
{{--class="time-wrapper"><span class="time">17:24</span></span></div>--}}
{{--</div>--}}
{{--</li>--}}
{{--<li>--}}
{{--<div class="direction-r">--}}
{{--<div class="flag-wrapper"><span class="flag">Conclusão</span><span class="time-wrapper"><span--}}
{{--class="time">17:32</span></span></div>--}}
{{--</div>--}}
{{--</li>--}}
{{--</ul>--}}
{{--</div>--}}

{{--<div class="fire-status align-items-stretch">--}}
{{--<div class="col-12 col-md show"><strong>Risco de Incêndio</strong></div>--}}
{{--<div class="col-12 col-md text-center status-max" data-status="Maximo">Máximo</div>--}}
{{--<div class="col-12 col-md text-center status-vhigh" data-status="MuitoElevado">Muito Elevado</div>--}}
{{--<div class="col-12 col-md text-center status-high" data-status="Elevado">Elevado</div>--}}
{{--<div class="col-12 col-md text-center status-mod active" data-status="Moderado">Moderado</div>--}}
{{--<div class="col-12 col-md text-center status-min" data-status="Reduzido">Reduzido</div>--}}
{{--</div>--}}

{{--<div class="d-flex justify-content-between">--}}
{{--<a href="#" class="data-source">Fonte: ANPC</a>--}}
{{--<a href="#" class="share-icon"><i class="fa fa-share"></i></a>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}

{{--<div class="card">--}}
{{--<div class="card-body">--}}
{{--<h4 class="card-title">Demografia</h4>--}}
{{--<table class="table table-sm">--}}
{{--<tbody>--}}
{{--<tr>--}}
{{--<td>Área Total--}}
{{--<small class="text-lowercase">(km<sup>2</sup>)</small>--}}
{{--</td>--}}
{{--<td> 100</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td>Densidade População--}}
{{--<small class="text-lowercase">(hab/km<sup>2</sup>)</small>--}}
{{--</td>--}}
{{--<td>5047</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td>População Residente</td>--}}
{{--<td>504964</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td>População idosa--}}
{{--<small class="text-lowercase">(+65)</small>--}}
{{--</td>--}}
{{--<td>28.39%</td>--}}
{{--</tr>--}}
{{--<tr>--}}
{{--<td>Hospitais e centros de saúde</td>--}}
{{--<td>51</td>--}}
{{--</tr>--}}
{{--</tbody>--}}
{{--</table>--}}
{{--<div class="d-flex justify-content-between">--}}
{{--<a href="#" class="data-source">Fonte: INE</a>--}}
{{--<a href="#" class="share-icon"><i class="fa fa-share"></i></a>--}}
{{--</div>--}}
{{--</div>--}}
{{--</div>--}}

{{--</div>--}}
{{--<script>--}}
{{--$(function () {--}}
{{--console.log('ready')--}}
{{--$('body').on('click', function () {--}}
{{--console.log('clicked')--}}
{{--$('.sidebar').fadeToggle();--}}
{{--});--}}
{{--});--}}
{{--</script>--}}

<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=UA-29689840-9"></script>
<script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }

    gtag('js', new Date());

    gtag('config', 'UA-29689840-9');
</script>


</body>
</html>

