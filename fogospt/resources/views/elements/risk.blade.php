<div class="fire-status row align-items-stretch">
    <div class="col-12 col-md text-center status-max {{$fire['risk'] === 'Máximo' ? 'active' : ''}}" data-status="Maximo">Máximo</div>
    <div class="col-12 col-md text-center status-vhigh {{$fire['risk'] === 'Muito Elevado' ? 'active' : ''}}" data-status="MuitoElevado">Muito Elevado</div>
    <div class="col-12 col-md text-center status-high {{$fire['risk'] === 'Elevado' ? 'active' : ''}}" data-status="Elevado">Elevado</div>
    <div class="col-12 col-md text-center status-mod {{$fire['risk'] === 'Moderado' ? 'active' : ''}}" data-status="Moderado">Moderado</div>
    <div class="col-12 col-md text-center status-min {{$fire['risk'] === 'Reduzido' ? 'active' : ''}}" data-status="Reduzido">Reduzido</div>
</div>