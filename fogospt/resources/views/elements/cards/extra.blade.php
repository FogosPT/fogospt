@if( !empty($fire['extra']) || !empty($fire['pco']) || !empty($fire['cos']))
    <div class="row extra active">
@else
    <div class="row extra">
@endif
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">@lang('elements.cards.extra.title')</h4>
                <div class="f-extra">
                    @include('elements.extra')
                </div>
            </div>
        </div>
    </div>
</div>
