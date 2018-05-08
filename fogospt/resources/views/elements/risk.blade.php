<div class="fire-status row align-items-stretch">
    <div class="col-12 col-md text-center status-max {{$fire['risk'] === 'Máximo' ? 'active' : ''}}" data-status="Maximo">@lang('elements.cards.riskLevels.maximum')</div>
    <div class="col-12 col-md text-center status-vhigh {{$fire['risk'] === 'Muito Elevado' ? 'active' : ''}}" data-status="MuitoElevado">@lang('elements.cards.riskLevels.veryHigh')</div>
    <div class="col-12 col-md text-center status-high {{$fire['risk'] === 'Elevado' ? 'active' : ''}}" data-status="Elevado">@lang('elements.cards.riskLevels.high')</div>
    <div class="col-12 col-md text-center status-mod {{$fire['risk'] === 'Moderado' ? 'active' : ''}}" data-status="Moderado">@lang('elements.cards.riskLevels.moderate')</div>
    <div class="col-12 col-md text-center status-min {{$fire['risk'] === 'Reduzido' ? 'active' : ''}}" data-status="Reduzido">@lang('elements.cards.riskLevels.reduced')</div>
</div>