@extends('app')

@push('styles')
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endpush

@push('scripts')
    <script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js" />
@endpush

@section('content')
    <div class="container">
        <main role="main" class="mb-auto margin-top-10 notifications" >

            <div class="mb-auto margin-top-10">
                <h3>@lang('includes.menu.notifications')</h3>
            </div>
            <div class="row mb-auto margin-top-10">
                <div class="col-sm"></div>
                <div class="col-sm"><strong>Site</strong></div>
                <div class="col-sm"><strong>SMS</strong></div>
            </div>

            @foreach(config('custom.districts') as $item)
                <div class="row justify-content-start">
                    <div class="col-sm"> <strong>@lang($item["name"])</strong></div>
                    <div class="col-sm">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" data-value="{{$item["value"]}}" data-type="site">
                            <span class="custom-control-indicator"></span>
                        </label>
                    </div>
                    <div class="col-sm">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" data-value="{{$item["value"]}}" data-type="sms">
                            <span class="custom-control-indicator"></span>
                        </label>
                    </div>
                </div>
            @endforeach



            <div class="alert alert-warning alert-dismissible collapse" role="alert">
                <strong>Holy guacamole!</strong> You should check in on some of those fields below.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </main>
    </div>
@endsection