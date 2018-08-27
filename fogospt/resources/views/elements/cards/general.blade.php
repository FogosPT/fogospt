<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">@lang('elements.cards.general.place')</h4>
                <p class="f-local">
                    @isset($fire['location'])
                        {{ $fire['location'] }} - {{ $fire['localidade'] }}
                    @endisset
                </p>

                @isset($fire['id'])
                    <i class="fas fa-bell click-notification" data-id="{{$fire['id']}}"></i>
                @endisset

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
            </div>
        </div>
    </div>
</div>
