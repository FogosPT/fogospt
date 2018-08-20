@extends('app')

@push('scripts')
    <script src="js/vendor/store2.min.js"></script>
    <script src="js/notifications.js"></script>
@endpush

@section('content')
    <div class="container">
        <main role="main" class="mb-auto margin-top-10 notifications ">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('includes.menu.notifications')</h1>
                </div>
            </div>

            <div class="row no-auth">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-6">
                                <p>Para receber notificações de novos incêndios, ocorrências importantes ou outros
                                    avisos, clique no
                                    botão ao lado.</p>
                            </div>
                            <div class="col-6">
                                <button type="button"
                                        class="btn btn-outline-success btn-lg btn-block js-notifications-auth">Quero
                                    receber notificações
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <section class="auth bg-white">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Gerais</h2>
                        @foreach(config('custom.notifications') as $item)
                            <div class="row justify-content-start">
                                <div class="col-sm"><strong>@lang($item["name"])</strong></div>
                                <div class="col-sm">
                                    <label class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                               data-value="{{$item["value"]}}"
                                               data-type="site">
                                        <span class="custom-control-indicator"></span>
                                    </label>
                                </div>
                                <div class="col-sm d-none">
                                    <label class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                               data-value="{{$item["value"]}}"
                                               data-type="sms">
                                        <span class="custom-control-indicator"></span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section class="auth bg-white">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Distritos</h2>
                        <div class="row mb-auto margin-top-10">
                            <div class="col-sm"></div>
                            <div class="col-sm"><strong>Site</strong></div>
                            <div class="col-sm d-none"><strong>SMS</strong></div>
                        </div>
                        @foreach(config('custom.districts') as $item)
                            <div class="row justify-content-start">
                                <div class="col-sm"><strong>@lang($item["name"])</strong></div>
                                <div class="col-sm">
                                    <label class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                               data-value="{{$item["value"]}}"
                                               data-type="site">
                                        <span class="custom-control-indicator"></span>
                                    </label>
                                </div>
                                <div class="col-sm d-none">
                                    <label class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input"
                                               data-value="{{$item["value"]}}"
                                               data-type="sms">
                                        <span class="custom-control-indicator"></span>
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <div class="alert alert-warning alert-dismissible collapse" role="alert">
                <strong>Holy guacamole!</strong> You should check in on some of those fields below.
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </main>
    </div>
@endsection