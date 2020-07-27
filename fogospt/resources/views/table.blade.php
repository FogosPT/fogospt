@extends('app')

@push('styles')
    <link rel="stylesheet" href="//cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
@endpush
@section('content')
    <main role="main" class="mb-auto margin-top-10">
        <div class="container container-fluid">
            <div class="row">
                <table id="fires" class="table table-striped table-bordered table-responsive">
                    <thead>
                    <tr>
                        <th></th>
                        <th>@lang('elements.cards.general.start_at')</th>
                        <th>@lang('elements.cards.general.district')</th>
                        <th>@lang('elements.cards.general.concelho')</th>
                        <th>@lang('elements.cards.general.freguesia')</th>
                        <th>@lang('elements.cards.general.localidade')</th>
                        <th>@lang('elements.cards.status.status')</th>
                        <th>👨‍🚒</th>
                        <th>🚒</th>
                        <th>🚁</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($data as $fire)
                        <tr>
                            <td><a href="{{route('fire', $fire['id'])}}">{{$fire['id']}}</a></td>
                            <td>{{ $fire['hour'] }} {{ $fire['date'] }}</td>
                            <td>{{ $fire['district'] }}</td>
                            <td>{{ $fire['concelho'] }}</td>
                            <td>{{ $fire['freguesia'] }}</td>
                            <td>{{ $fire['localidade'] }}</td>
                            <td>
                                <div><span class="dot status-{{ $fire['statusCode'] }}" style="display:inline-block;position:relative"></span> <span class="status-label">{{ $fire['status'] }}</span></div>
                            </td>
                            <td>{{$fire['man']}}</td>
                            <td>{{$fire['terrain']}}</td>
                            <td>{{$fire['aerial']}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="row">
                <div class="col-12">@lang('pages.table.reload')</div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script src="//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
    <script>
        $(document).ready( function () {
            $('#fires').DataTable({
                paging: false,
                stateSave: true,
                order: [[ 0, 'desc' ]]
            });

            setTimeout(function() {
                location.reload();
            }, 180000);
        } );
    </script>
@endpush
