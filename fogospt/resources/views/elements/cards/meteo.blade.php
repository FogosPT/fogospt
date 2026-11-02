<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">@lang('elements.cards.meteo.title')</h4>
                <div class="f-meteo">
                    @include('elements.meteo')
                </div>
                <h4 class="card-title">@lang('elements.cards.general.fireRisk')</h4>
                <div class="f-danger">
                    @include('elements.risk')
                </div>
            </div>
        </div>
    </div>
</div>
