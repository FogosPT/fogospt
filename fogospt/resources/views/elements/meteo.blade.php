@if(!empty($fire) && isset($fire['weather']))
    <div id="meteo">
        <div class="temp_atual">
            @lang('elements.cards.meteo.temp_atual'):&nbsp;{{ $fire['weather']['temperatura'] ?? '—' }}ºC
        </div>
        <div class="temp_min">
            @lang('elements.cards.meteo.humidity'):&nbsp;{{ $fire['weather']['humidade'] ?? '—' }}%
        </div>
        <div class="temp_min">
            @lang('elements.cards.meteo.wind.speed'):&nbsp;{{ $fire['weather']['intensidadeVentoKM'] ?? '—' }} km/h
        </div>
        @if(!empty($fire['weather']['direccVento']))
            <div class="temp_min">
                @lang('elements.cards.meteo.wind.deg'):&nbsp;{{ $fire['weather']['direccVento'] }}
            </div>
        @endif
        <div class="temp_min">
            @lang('elements.cards.meteo.pressure'):&nbsp;{{ $fire['weather']['pressao'] ?? '—' }} hPa
        </div>
        @if(isset($fire['weather']['precAcumulada']))
            <div class="temp_min">
                @lang('elements.cards.meteo.precipitation'):&nbsp;{{ $fire['weather']['precAcumulada'] }} mm
            </div>
        @endif
        @if(isset($fire['weather']['radiacao']))
            <div class="temp_min">
                @lang('elements.cards.meteo.radiation'):&nbsp;{{ $fire['weather']['radiacao'] }} W/m²
            </div>
        @endif
        <div class="mt-2" style="font-size: 0.85em; color: #888;">
            @if(!empty($fire['weather']['stationLocation']))
                <div>📍 @lang('elements.cards.meteo.station'): {{ $fire['weather']['stationLocation'] }}</div>
            @endif
            @if(!empty($fire['weather']['date']))
                <div>🕐 @lang('elements.cards.meteo.data_from'): {{ \Carbon\Carbon::parse($fire['weather']['date'])->format('d/m/Y H:i') }}</div>
            @endif
            <div>@lang('elements.cards.meteo.source'): <a href="https://www.ipma.pt" target="_blank" rel="noopener">IPMA</a></div>
        </div>
    </div>
@endif
