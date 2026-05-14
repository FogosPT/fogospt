@extends('app')

@section('content')
    <main role="main" class="mb-auto margin-top-10">
        <div class="container">
            <div class="container my-5">

                <div class="row justify-content-center">
                    <div class="col-lg-10">

                        <div class="mb-4">
                            <h1 class="mb-3">{{ __('api-docs.title') }}</h1>
                            <p class="lead">
                                {{ __('api-docs.intro_lead') }}
                            </p>
                            <p>
                                {{ __('api-docs.intro_p2_before') }}
                                <strong>{{ __('api-docs.intro_p2_strong') }}</strong>{{ __('api-docs.intro_p2_after') }}
                            </p>
                            <p>
                                {{ __('api-docs.intro_p3_before') }}
                                <strong>{{ __('api-docs.intro_p3_strong') }}</strong>{{ __('api-docs.intro_p3_after') }}
                            </p>
                            <p>
                                {{ __('api-docs.intro_p4') }}
                            </p>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h2>{{ __('api-docs.why_title') }}</h2>
                            <p>
                                {{ __('api-docs.why_p1_before') }}
                                <strong>{{ __('api-docs.why_p1_strong') }}</strong>{{ __('api-docs.why_p1_after') }}
                            </p>
                            <p>
                                {{ __('api-docs.why_p2') }}
                            </p>
                            <p>
                                {{ __('api-docs.why_p3') }}
                            </p>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h2>{{ __('api-docs.change_title') }}</h2>

                            <div class="alert alert-warning">
                                @if(__('api-docs.change_alert_before') !== '')
                                    {{ __('api-docs.change_alert_before') }}
                                @endif
                                <strong>{{ __('api-docs.change_alert_strong') }}</strong>{{ __('api-docs.change_alert_after') }}
                            </div>

                            <p>{{ __('api-docs.change_intro') }}</p>

                            <ul>
                                <li>
                                    {{ __('api-docs.req_auth_intro') }}
                                    <pre class="bg-light p-2">{{ __('api-docs.req_auth_pre') }}</pre>
                                </li>
                                <li>{!! __('api-docs.req_token') !!}</li>
                                <li>{!! __('api-docs.req_user_agent') !!}</li>
                                <li>{!! __('api-docs.req_ip') !!}</li>
                            </ul>

                            <div class="alert alert-danger mt-3">
                                {{ __('api-docs.change_danger') }}
                            </div>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h2>{{ __('api-docs.access_title') }}</h2>

                            <p>
                                {{ __('api-docs.access_p1') }}
                            </p>

                            <p>
                                {{ __('api-docs.access_p2') }}
                            </p>

                            <div class="alert alert-info">
                                👉 <strong><a href="{{ __('api-docs.form_url') }}">{{ __('api-docs.form_label') }}</a></strong>
                            </div>

                            <p>{{ __('api-docs.review_intro') }}</p>

                            <ul>
                                @foreach(__('api-docs.review_criteria') as $criterion)
                                    <li>{{ $criterion }}</li>
                                @endforeach
                            </ul>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h2>{{ __('api-docs.principles_title') }}</h2>

                            <ul>
                                @foreach(__('api-docs.principles') as $principle)
                                    <li>{!! $principle !!}</li>
                                @endforeach
                            </ul>
                        </div>

                        <hr>

                        <div class="mb-4">
                            <h2>{{ __('api-docs.community_title') }}</h2>

                            <p>
                                {{ __('api-docs.community_p1_before') }}
                                <strong>{{ __('api-docs.community_p1_strong') }}</strong>{{ __('api-docs.community_p1_after') }}
                            </p>

                            <p>
                                {{ __('api-docs.community_p2_before') }}
                                <strong>{{ __('api-docs.community_p2_strong') }}</strong>{{ __('api-docs.community_p2_after') }}
                            </p>

                            <p>
                                {{ __('api-docs.community_p3_before') }}
                                <strong>{{ __('api-docs.community_p3_strong') }}</strong>{{ __('api-docs.community_p3_after') }}
                            </p>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </main>
@endsection
