<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Local</h4>
                <p class="f-local">
                    @isset($fire['location'])
                        {{ $fire['location'] }}
                    @endisset
                </p>
                <h4 class="card-title">Início</h4>
                <p class="f-start">
                    @isset($fire['date'])
                        {{ $fire['hour'] }} {{ $fire['date'] }}
                    @endisset
                </p>
                <h4 class="card-title">Natureza</h4>
                <p class="f-nature">
                    @isset($fire['natureza'])
                        {{ $fire['natureza'] }}
                    @endisset
                </p>
                <h4 class="card-title">Risco de Incêndio</h4>
                <div class="f-danger">
                    @isset($fire['risk'])
                        @include('elements.risk')
                    @endisset
                </div>
            </div>
        </div>
    </div>
</div>
