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
