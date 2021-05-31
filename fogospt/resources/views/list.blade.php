@extends('app')

@section('content')
    <main role="main" class="mb-auto margin-top-10">
        <div class="container">
            <div class="row">
                @if ( $data && count($data) !== 0)
                    @foreach($data as $fire)
                        <div class="col-sm-12 col-md-4">
                            <div class="card">
                              <a  href="{{route('fireDetail', $fire['id'])}}" >
                                <img class="card-img-top" src="https://api.mapbox.com/styles/v1/mapbox/satellite-streets-v11/static/{{$fire['lng']}},{{$fire['lat']}},15,0,00/450x300?access_token={{env('MAPBOX_TOKEN')}}">
                              </a>
                                <div class="card-body">

                                    <h4 class="card-title">@lang('elements.cards.general.place')</h4>
                                    <p class="f-local">
                                        @isset($fire['location'])
                                            {{ $fire['location'] }} - {{ $fire['localidade'] }}@isset($fire['detailLocation']) - {{$fire['detailLocation']}} @endisset
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

                                    <h4 class="card-title">@lang('elements.cards.status.status')</h4>
                                    <div class="f-status">
                                        @isset($fire['status'])
                                        <div class="list-status-container">
                                            <div>
                                                <div><span class="dot status-{{ $fire['statusCode'] }}"></span></div>
                                                <div><span class="status-label">{{ $fire['status'] }}</span></div>
                                            </div>
                                        </div>
                                        @endisset
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-sm-12 col-md-12">
                        <div class="card">
                            <h4 class="card-title">@lang('pages.list.no-data')</h4>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </main>
@endsection

@push('scripts')
@endpush
