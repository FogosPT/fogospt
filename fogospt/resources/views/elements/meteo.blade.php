@isset($fire['weather'])
    <div id="meteo">
        <div class="temp_atual">
            @lang('elements.cards.meteo.temp_atual'):&nbsp;{{ $fire['weather']['temperatura'] }}ºC
        </div>
        <div class="temp_min">
            @lang('elements.cards.meteo.humidity'):&nbsp;{{ $fire['weather']['humidade'] }}%
        </div>
        <div class="temp_min">
            @lang('elements.cards.meteo.wind.speed'):&nbsp;{{ $fire['weather']['intensidadeVentoKM'] }} km/h
        </div>
        @if($fire['weather']['direccVento'])
            <div class="temp_min">
                @lang('elements.cards.meteo.wind.deg'):&nbsp;{{ $fire['weather']['direccVento'] }}
            </div>
        @endif
        <div class="temp_min">
            @lang('elements.cards.meteo.pressure'):&nbsp;{{ $fire['weather']['pressao'] }} hPa
        </div>
        <div class="temp_min">
            Precipitação acumulada:&nbsp;{{ $fire['weather']['precAcumulada'] }} mm
        </div>
        <div class="temp_min">
            Radiação:&nbsp;{{ $fire['weather']['radiacao'] }} W/m²
        </div>
        <div class="mt-2" style="font-size: 0.85em; color: #888;">
            <div>
                📍 Estação: {{ $fire['weather']['stationLocation'] }}
            </div>
            <div>
                🕐 Dados de: {{ \Carbon\Carbon::parse($fire['weather']['date'])->format('d/m/Y H:i') }}
            </div>
            <div>
                Fonte: <a href="https://www.ipma.pt" target="_blank" rel="noopener">IPMA</a>
            </div>
        </div>
    </div>
@endisset
