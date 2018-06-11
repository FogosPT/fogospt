<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">@lang('elements.cards.resources.units')</h4>
                <div class="assets d-flex align-items-center justify-content-center">
                    <img class="assets-icon" src="/img/fireman.svg">
                    <span class="assets-nr f-man">
                        @isset($fire['man'])
                            {{ $fire['man'] }}
                        @endisset
                    </span>
                    <img class="assets-icon" src="/img/firetruck.svg">
                    <span class="assets-nr f-terrain">
                            @isset($fire['terrain'])
                            {{ $fire['terrain'] }}
                        @endisset
                    </span>
                    <img class="assets-icon" src="/img/plane.svg">
                    <span class="assets-nr f-aerial">
                            @isset($fire['aerial'])
                            {{ $fire['aerial'] }}
                        @endisset
                    </span>
                </div>
                <canvas style="padding: 0 5px" id="myChart" width="400" height="150"></canvas>
            </div>
        </div>
    </div>
</div>
