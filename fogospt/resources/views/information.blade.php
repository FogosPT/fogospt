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
    
<h3>Estados das Ocorrências</h3>
<ul>
<li>DESPACHO 1º ALERTA – Meios em trânsito para o teatro de operações.</li>
<li>CHEGADA AO TO – chega ao teatro de operações.</li>
<li>EM CURSO - Incêndio em evolução sem limitação de área</li>
<li>EM RESOLUÇÃO – Incêndio sem perigo de propagação para além do perímetro já atingido</li>
<li>EM CONCLUSÃO – Incêndio extinto, com pequenos focos de combustão dentro do perímetro do incêndio</li>
<li>VIGILÂNCIA – Meios no local para actuar em caso de necessidade</li>
<li>ENCERRADA – Entrada, nas respectivas entidades, de todos os meios envolvidos</li>
</ul>
<h3>Meios</h3>
<ul>
<li>HUMANOS - Bombeiros, Força Especial de Bombeiros, PSP, Forças Armadas, INEM, Equipas Sapadores Florestais, GNR, GIPS Grupo Intervenção de Proteção e Socorro</li>
<li>TERRESTRES - Veículos rodoviários</li>
<li>AEREOS - Helicópteros / Aviões</li>
</ul>
<p>Os números disponibilizados são os totais de meios accionados. O número pode diferir do que se encontra no terreno, uma vez que os meios accionados podem ainda estar em trânsito.</p>
<p>As horas indicadas tanto no gráfico de meios como na linha do tempo dos estados do incêndios, são as horas que o nosso sistema detetou uma mudança de dados por parte da ANPC podendo não corresponder ao momento exato em que essa alteração ocorreu.</p>
<p>Risco de incêndio recolhido do IPMA.</p>

    </div>
</main>

@include('includes.scripts')
<script>
    $("#menuTopo").find('a.active').removeClass('active');
    $("#menuTopo").find('a[href*="/informacoes"]').addClass('active');
</script>
</body>
</html>
