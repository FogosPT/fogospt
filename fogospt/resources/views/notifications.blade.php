@extends('app')
@section('content')
    <div class="container">
        <main role="main" class="mb-auto margin-top-10 notifications" >

            <div class="row mb-auto margin-top-10">
                <div class="col-sm"></div>
                <div class="col-sm">Site</div>
                <div class="col-sm">SMS</div>
            </div>

            @foreach(config('custom.districts') as $item)
                <div class="row justify-content-start">
                    <div class="col-sm"> @lang($item["name"])</div>
                    <div class="col-sm">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input">
                            <span class="custom-control-indicator"></span>
                        </label>
                    </div>
                    <div class="col-sm">
                        <label class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input">
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