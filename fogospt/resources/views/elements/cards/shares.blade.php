@if( !empty($shares))
    <div class="row shares active">
@else
    <div class="row shares">
@endif

    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">@lang('elements.cards.shares.title')</h4>
                <div class="f-shares">
                    @include('elements.shares')
                </div>
            </div>
        </div>
    </div>
</div>