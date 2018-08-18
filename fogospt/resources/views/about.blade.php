@extends('app')

@section('content')
    <main role="main" class="mb-auto margin-top-10">
        <div class="container">
            <div class="col-12">
                <h1>@lang('includes.menu.about')</h1>
            </div>
            <div class="card">
                <div class="card-body">
                    <p>@lang('pages.about.entries_from')</p>
                    <p>@lang('pages.about.update_interval')</p>
                    <p>@lang('pages.about.near_location')</p>
                    <p>@lang('pages.about.suggestion_bugs')</p>
                    <p>@lang('pages.about.made_by')</p>
                </div>
            </div>
        </div>
    </main>
@endsection