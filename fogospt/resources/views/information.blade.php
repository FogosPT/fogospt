@extends('app')


@section('content')
    <div class="container">
        <main role="main" class="mb-auto margin-top-10 bg-white">
            <div class="modal-body">
                <h3>@lang('pages.information.statesOfOccurrences.title')</h3>
                <ul class="list-none">
                    <li><i class="dot dotInfo status-4"></i> @lang('pages.information.statesOfOccurrences.items.firstAlertdispatch')</li>
                    <li><i class="dot dotInfo status-6"></i>@lang('pages.information.statesOfOccurrences.items.arrivalToOccurrence')</li>
                    <li><i class="dot dotInfo status-5"></i>@lang('pages.information.statesOfOccurrences.items.ongoing')</li>
                    <li><i class="dot dotInfo status-7"></i>@lang('pages.information.statesOfOccurrences.items.inResolution')</li>
                    <li><i class="dot dotInfo status-8"></i>@lang('pages.information.statesOfOccurrences.items.inConclusion')</li>
                    <li><i class="dot dotInfo status-9"></i>@lang('pages.information.statesOfOccurrences.items.surveillance')</li>
                    <li><i class="dot dotInfo status-10"></i>@lang('pages.information.statesOfOccurrences.items.closed')</li>
                    <li><i class="dot dotInfo status-11"></i>@lang('pages.information.statesOfOccurrences.items.false')</li>
                    <li><i class="dot dotInfo status-12"></i>@lang('pages.information.statesOfOccurrences.items.fake')</li>
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