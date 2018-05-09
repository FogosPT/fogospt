<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">@lang('elements.cards.status.status')</h4>
                <div class="f-status">
                    @isset($fire['statusHistory'])
                        @include('elements.status')
                    @endisset
                </div>
            </div>
        </div>
    </div>
</div>
