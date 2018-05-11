@extends('app')

@section('content')
    <main role="main" class="mb-auto">
        <div class="modal-body">
            <p>@lang('pages.about.entries_from')</p>
            <p>@lang('pages.about.update_interval')</p>
            <p>@lang('pages.about.near_location')</p>
            <p>@lang('pages.about.suggestion_bugs')</p>
            <p>@lang('pages.about.made_by')</p>
        </div>
    </main>
@endsection