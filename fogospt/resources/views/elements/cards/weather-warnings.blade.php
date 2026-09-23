@php
    $hasWw = isset($fire['weatherWarnings']) && is_array($fire['weatherWarnings']) && count($fire['weatherWarnings']) > 0;

    // Accent the card with the most severe active level (red > orange > yellow),
    // matching the checklist item in the integration guide.
    $wwHighestBg = null;
    if ($hasWw) {
        $rank = ['yellow' => 1, 'orange' => 2, 'red' => 3];
        $palette = ['yellow' => '#FFB202', 'orange' => '#FF6E02', 'red' => '#B81E1F'];
        $best = 0;
        $bestLevel = 'yellow';
        foreach ($fire['weatherWarnings'] as $w) {
            $lvl = $w['awarenessLevelID'] ?? 'yellow';
            $r = $rank[$lvl] ?? 0;
            if ($r > $best) { $best = $r; $bestLevel = $lvl; }
        }
        $wwHighestBg = $palette[$bestLevel];
    }
@endphp
<div class="row weather-warnings @if($hasWw) active @endif" @if(!$hasWw) style="display:none;" @endif>
    <div class="col-12">
        <div class="card" @if($wwHighestBg) style="border-left: 4px solid {{ $wwHighestBg }};" @endif>
            <div class="card-body">
                <h4 class="card-title">@lang('elements.cards.weatherWarnings.title')</h4>
                <div class="f-weather-warnings">
                    @include('elements.weather-warnings')
                </div>
            </div>
        </div>
    </div>
</div>
