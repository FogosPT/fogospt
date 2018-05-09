<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">@lang('elements.cards.general.place')</h4>
                <p class="f-local">
                    @isset($fire['location'])
                        {{ $fire['location'] }}
                    @endisset
                </p>
                <h4 class="card-title">@lang('elements.cards.general.start_at')</h4>
                <p class="f-start">
                    @isset($fire['date'])
                        {{ $fire['hour'] }} {{ $fire['date'] }}
                    @endisset
                </p>
                <h4 class="card-title">@lang('elements.cards.general.nature')</h4>
                <p class="f-nature">
                    @isset($fire['natureza'])
                        {{ $fire['natureza'] }}
                    @endisset
                </p>
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
