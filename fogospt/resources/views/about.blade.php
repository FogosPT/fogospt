@extends('app')

@section('content')
    <main role="main" class="mb-auto margin-top-10">
        <div class="container">
            <div class="col-12">
                <h1>@lang('includes.menu.about')</h1>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <p>@lang('pages.about.entries_from')</p>
                    <p>@lang('pages.about.update_interval')</p>
                    <p>@lang('pages.about.near_location')</p>
                    <p>{!! __('pages.about.suggestion_bugs') !!}</p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h4>@lang('pages.about.about_title')</h4>
                    <p>@lang('pages.about.about_text')</p>
                    <p>@lang('pages.about.about_text_2')</p>
                    <p>@lang('pages.about.about_text_3')</p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h4>@lang('pages.about.data_title')</h4>
                    <p>@lang('pages.about.data_intro')</p>
                    <p><strong>@lang('pages.about.data_authorities_title')</strong></p>
                    <ul>
                        <li>@lang('pages.about.data_authorities_anepc')</li>
                        <li>@lang('pages.about.data_authorities_icnf')</li>
                        <li>@lang('pages.about.data_authorities_agif')</li>
                    </ul>
                    <p><strong>@lang('pages.about.data_satellites_title')</strong></p>
                    <ul>
                        <li>@lang('pages.about.data_satellites_text')</li>
                    </ul>
                    <p><strong>@lang('pages.about.data_other_title')</strong></p>
                    <ul>
                        <li>@lang('pages.about.data_other_text')</li>
                    </ul>
                    <p>@lang('pages.about.data_footer')</p>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h4>@lang('pages.about.partners_title')</h4>
                    <p>@lang('pages.about.partners_intro')</p>
                    <ul>
                        <li>@lang('pages.about.partners_anepc')</li>
                        <li>@lang('pages.about.partners_pt_servidor')</li>
                        <li>@lang('pages.about.partners_cloudflare')</li>
                        <li>@lang('pages.about.partners_mapbox')</li>
                        <li>@lang('pages.about.partners_agif')</li>
                    </ul>
                </div>
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <h4>@lang('pages.about.commitment_title')</h4>
                    <p>@lang('pages.about.commitment_text')</p>
                    <p>{!! __('pages.about.made_by') !!}</p>
                </div>
            </div>
        </div>
    </main>
@endsection
