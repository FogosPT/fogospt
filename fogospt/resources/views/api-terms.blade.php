@extends('app')

@section('content')
    <main role="main" class="mb-auto margin-top-10">
        <div class="container my-5">
            <div class="row justify-content-center">
                <div class="col-lg-10">

                    <h1 class="mb-4">{{ __('api-terms.title') }}</h1>

                    <div class="alert alert-warning">
                        {{ __('api-terms.intro_alert') }}
                    </div>

                    @foreach(__('api-terms.sections') as $section)
                        <div class="mb-4">
                            <h2>{{ $section['title'] }}</h2>
                            @foreach($section['blocks'] as $block)
                                @switch($block['type'])
                                    @case('p')
                                        <p>{!! $block['html'] !!}</p>
                                        @break
                                    @case('alert_warning')
                                        <div class="alert alert-warning">{!! $block['html'] !!}</div>
                                        @break
                                    @case('alert_danger')
                                        <div class="alert alert-danger">{!! $block['html'] !!}</div>
                                        @break
                                    @case('alert_light')
                                        <div class="alert alert-light border">{!! $block['html'] !!}</div>
                                        @break
                                    @case('ul')
                                        <ul>
                                            @foreach($block['items'] as $item)
                                                <li>{!! $item['html'] !!}
                                                    @if(!empty($item['subitems']))
                                                        <ul>
                                                            @foreach($item['subitems'] as $sub)
                                                                <li>{!! $sub !!}</li>
                                                            @endforeach
                                                        </ul>
                                                    @endif
                                                </li>
                                            @endforeach
                                        </ul>
                                        @break
                                @endswitch
                            @endforeach
                        </div>
                        @if(!$loop->last)
                            <hr>
                        @endif
                    @endforeach

                </div>
            </div>
        </div>
    </main>
@endsection
