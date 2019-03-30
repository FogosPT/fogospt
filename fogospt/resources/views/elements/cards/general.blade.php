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

                <div class="notification-container">
                    <i class="far fa-bell click-notification" data-id="@isset($fire['id']){{$fire['id']}}@endisset"></i>

                </div>

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

                @isset($fire['updated'])
                    <p><small>Última atualização <span class="f-update">{{ date('H:i d-m-Y', $fire['updated']['sec'])}}</span></small></p>
                @endisset
            </div>
        </div>
    </div>
</div>
