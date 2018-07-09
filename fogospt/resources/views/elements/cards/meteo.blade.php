<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">@lang('elements.cards.meteo.title')</h4>
                <div class="f-meteo">
                    @isset($fire['meteo'])
                        @include('elements.meteo')
                    @endisset
                </div>
                <h4 class="card-title">@lang('elements.cards.general.fireRisk')</h4>
                <div class="f-danger">
                    @isset($fire['risk'])
                        @include('elements.risk')
                    @endisset
                </div>
            </div>
        </div>
    </div>
</div>
