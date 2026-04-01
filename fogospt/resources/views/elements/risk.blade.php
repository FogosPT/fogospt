@if(!empty($fire))
<ul class="fire-status list-unstyled d-flex text-center">
    <li class="status-min {{ ($fire['risk'] ?? '') === 'Reduzido' ? 'active' : '' }}" data-status="@lang('elements.riskLevels.Reduzido')"></li>
    <li class="status-mod {{ ($fire['risk'] ?? '') === 'Moderado' ? 'active' : '' }}" data-status="@lang('elements.riskLevels.Moderado')"></li>
    <li class="status-high {{ ($fire['risk'] ?? '') === 'Elevado' ? 'active' : '' }}" data-status="@lang('elements.riskLevels.Elevado')"></li>
    <li class="status-vhigh {{ ($fire['risk'] ?? '') === 'Muito Elevado' ? 'active' : '' }}" data-status="@lang('elements.riskLevels.Muito Elevado')"></li>
    <li class="status-max {{ ($fire['risk'] ?? '') === 'Máximo' ? 'active' : '' }}" data-status="@lang('elements.riskLevels.Máximo')"></li>
</ul>
@endif
