<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">@lang('elements.cards.resources.units')</h4>
                <table class="table table-sm">
                    <tbody class="active">
                    <tr>
                        <td class="float-left">
                            <i class="fa fa-user" aria-hidden="true"></i> <span class="f-man">
                                 @isset($fire['man'])
                                    {{ $fire['man'] }}
                                @endisset
                            </span>
                        </td>
                        <td class="float-left">
                            <i class="fa fa-plane" aria-hidden="true"></i> <span class="f-aerial">
                                 @isset($fire['aerial'])
                                    {{ $fire['aerial'] }}
                                @endisset
                            </span>
                        </td>
                        <td class="float-left">
                            <i class="fa fa-truck" aria-hidden="true"></i> <span class="f-terrain">
                                 @isset($fire['terrain'])
                                    {{ $fire['terrain'] }}
                                @endisset
                            </span>
                        </td>
                    </tr>

                    </tbody>
                </table>
                <canvas style="padding: 0 5px" id="myChart" width="400" height="150"></canvas>
            </div>
        </div>
    </div>
</div>
