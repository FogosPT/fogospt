{{-- Site-wide notice modal. Filled (as text) and opened by main.js /
     main-other.js from source.fogos.pt/v1/warnings/site. --}}
<div id="warning-site" class="modal" tabindex="-1" role="dialog" aria-labelledby="warning-site-title">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="warning-site-title">@lang('includes.siteWarning.title')</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="@lang('includes.siteWarning.close')"></button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">@lang('includes.siteWarning.close')</button>
            </div>
        </div>
    </div>
</div>
