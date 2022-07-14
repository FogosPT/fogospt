<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">@lang('elements.cards.general.place')</h4>
                <p class="f-local">
                    @isset($fire['location'])
                        {{ $fire['location'] }} - {{ $fire['localidade'] }}@isset($fire['detailLocation']) - {{$fire['detailLocation']}} @endisset <a href="/fogo/{{$fire['id']}}/detalhe">Mais detalhes</a>
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

                <h4 class="card-title">@lang('elements.cards.general.location')</h4>
                <p class="f-location">
                    @isset($fire['lat'])
                    <a href="https://www.google.com/maps/search/{{ $fire['lat'] }},{{$fire['lng']}}" target="_blank"><i class="far fa-map"></i></a>
                        {{ $fire['lat'] }}, {{ $fire['lng'] }}
                    @endisset
                </p>

                @isset($fire['updated'])
                    <p><small>@lang('elements.cards.general.updated') <span class="f-update">{{ \Carbon\Carbon::parse( $fire['updated']['sec'] )->format('H:i d-m-Y')}}</span></small></p>
                @endisset
            </div>
        </div>
    </div>
</div>
