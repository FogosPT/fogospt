@extends('app')

@section('content')
    <div class="container">
        <main role="main" class="mb-auto margin-top-10">
            <div class="modal-body">
                <h3>@lang('pages.information.statesOfOccurrences.title')</h3>
                <ul>
                    <li>@lang('pages.information.statesOfOccurrences.items.firstAlertdispatch')</li>
                    <li>@lang('pages.information.statesOfOccurrences.items.arrivalToOccurrence')</li>
                    <li>@lang('pages.information.statesOfOccurrences.items.ongoing')</li>
                    <li>@lang('pages.information.statesOfOccurrences.items.inResolution')</li>
                    <li>@lang('pages.information.statesOfOccurrences.items.inConclusion')</li>
                    <li>@lang('pages.information.statesOfOccurrences.items.surveillance')</li>
                    <li>@lang('pages.information.statesOfOccurrences.items.closed')</li>
                </ul>
                <h3>@lang('pages.information.typeOfUnits.title')</h3>
                <ul>
                    <li>@lang('pages.information.typeOfUnits.items.humans')
                    </li>
                    <li>@lang('pages.information.typeOfUnits.items.terrestrial')</li>
                    <li>@lang('pages.information.typeOfUnits.items.air')</li>
                </ul>
                <p>@lang('pages.information.numberDescription')</p>
                <p>@lang('pages.information.hoursDescription')</p>
                <p>@lang('pages.information.source')</p>
            </div>
        </main>
    </div>
@endsection