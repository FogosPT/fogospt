@extends('app')

@section('content')
    <div class="container">
        <main role="main" class="mb-auto margin-top-1 bg-white">
            <div class="modal-body">
                <p>@lang('pages.about.entries_from')</p>
                <p>@lang('pages.about.update_interval')</p>
                <p>@lang('pages.about.near_location')</p>
                <p>@lang('pages.about.suggestion_bugs')</p>
                <p>@lang('pages.about.made_by')</p>
            </div>
        </main>
    </div>
@endsection