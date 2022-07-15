@isset($fire['extra'])
    <p>{{ $fire['extra'] }}</p>
@endisset


@isset($fire['cos'])
    <h4 class="card-title">Comandante Operações de Socorro</h4>
    <p>{{ $fire['cos'] }}</p>
@endisset

@isset($fire['pco'])
    <h4 class="card-title">Posto de Comando Operacional</h4>
    <p>{{ $fire['pco'] }}</p>
@endisset


@if(isset($fire['heliFight']) || isset($fire['heliCoord']) || isset($fire['planeFight']))
    <h4 class="card-title">Meios aéreos</h4>
    @if(isset($fire['heliFight']))
        <p>Helicóptero de combate: {{ $fire['heliFight'] }}</p>
    @endif
    @if(isset($fire['planeFight']))
        <p>Aviões de combate: {{ $fire['planeFight'] }}</p>
    @endif
    @if(isset($fire['heliCoord']))
        <p>Meios de coordenação: {{ $fire['heliCoord'] }}</p>
    @endif
@endif
