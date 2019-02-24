@extends('app')

@section('content')
    <main role="main" class="mb-auto margin-top-10">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>@lang('includes.menu.partnerships')</h1>
                </div>
            </div>


            <div class="card-deck mb-3 text-center">

              <div class="card mb-4 shadow-sm">
                <div class="card-header">
                  <h4 class="my-0 font-weight-normal">BRPX</h4>
                </div>
                <div class="card-body">
                  <div class="card-body">
                      <a href="https://brpx.com">
                          <img src="{{asset('img/partners/brpx.png')}}" alt="Bright Pixel">
                      </a>
                  </div>
                  <a type="button"  href="https://brpx.com" class="btn btn-lg btn-block btn-primary">Visitar</a>
                </div>
              </div>
              <div class="card mb-4 shadow-sm">
                <div class="card-header">
                  <h4 class="my-0 font-weight-normal">MAPBOX</h4>
                </div>
                <div class="card-body">
                  <div class="card-body">
                      <a href="https://www.mapbox.com">
                          <img src="{{asset('img/partners/mapbox.png')}}" alt="Mapbox">
                      </a>
                  </div>
                  <a type="button" href="https://www.mapbox.com" class="btn btn-lg btn-block btn-primary">Visitar</a>
                </div>
              </div>
            </div>
            <div class="row  bg-white">

              <!--  <div class="col-sm">
                    <div class="card" style="width: 18rem;">
                        <div class="card-body">
                            <a href="https://brpx.com">
                                <img src="{{asset('img/partners/brpx.png')}}" alt="Bright Pixel">
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-sm">
                    <div class="card" style="width: 18rem;">
                        <div class="card-body">
                            <a href="https://www.mapbox.com">
                                <img src="{{asset('img/partners/mapbox-logo-color.png')}}" alt="Mapbox">
                            </a>
                        </div>
                    </div>
                </div>-->
            </div>
        </div>
    </main>
@endsection
