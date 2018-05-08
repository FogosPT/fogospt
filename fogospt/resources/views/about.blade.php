<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    @include('includes.head')
    @include('includes.meta')
</head>
<body>

@include('includes.navbar')

<main role="main" class="mb-auto">
<div class="modal-body">
<p>Registos retirados da <a href="http://www.prociv.pt/">Página da Protecção Civil Portuguesa</a></p>
<p>Actualizações de 10 em 10 minutos</p>
<p>Localização aproximada.</p>
<p>Sugestões / Bugs - <a href="mailto:mail@fogos.pt">mail@fogos.pt</a></p>
<p>Made with ♥ by <a href="https://twitter.com/tomahock">Tomahock</a></p>
</div>
</main>

@include('includes.scripts')

</body>
</html>
