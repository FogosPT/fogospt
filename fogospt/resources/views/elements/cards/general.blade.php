<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <div class="notification-container">
                    <i class="far fa-bell click-notification" data-id="@isset($fire['id']){{$fire['id']}}@endisset"></i>
                </div>

                <div class="field">
                    <h6 class="field-label">@lang('elements.cards.general.place')</h6>
                    <p class="f-local field-value">
                        @isset($fire['location'])
                            {{ $fire['location'] }} - {{ $fire['localidade'] }}@isset($fire['detailLocation']) - {{$fire['detailLocation']}} @endisset <a href="/fogo/{{$fire['id']}}/detalhe">Mais detalhes</a>
                        @endisset
                    </p>
                </div>

                <hr class="field-divider">

                <div class="field">
                    <h6 class="field-label">@lang('elements.cards.general.start_at')</h6>
                    <p class="f-start field-value">
                        @isset($fire['date'])
                            {{ $fire['hour'] }} {{ $fire['date'] }}
                        @endisset
                    </p>
                </div>

                <hr class="field-divider">

                <div class="field">
                    <h6 class="field-label">@lang('elements.cards.general.nature')</h6>
                    <p class="f-nature field-value">
                        @isset($fire['natureza'])
                            {{ $fire['natureza'] }}
                        @endisset
                    </p>
                </div>

                <hr class="field-divider">

                <div class="field">
                    <h6 class="field-label">@lang('elements.cards.general.location')</h6>
                    <p class="f-location field-value">
                        @isset($fire['lat'])
                        <a href="https://www.google.com/maps/search/{{ $fire['lat'] }},{{$fire['lng']}}" target="_blank"><i class="far fa-map"></i></a>
                            {{ $fire['lat'] }}, {{ $fire['lng'] }}
                        @endisset
                    </p>
                </div>

                @isset($fire['updated'])
                    <hr class="field-divider">
                    <p class="mb-0"><small>@lang('elements.cards.general.updated') <span class="f-update">{{ \Carbon\Carbon::parse( $fire['updated']['sec'] )->format('H:i d-m-Y')}}</span></small></p>
                @endisset
            </div>
        </div>
    </div>
</div>
