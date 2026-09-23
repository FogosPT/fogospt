@php
    $ww = (isset($fire) && !empty($fire['weatherWarnings']) && is_array($fire['weatherWarnings']))
        ? $fire['weatherWarnings']
        : [];

    $wwLevelBg = [
        'yellow' => '#FFB202',
        'orange' => '#FF6E02',
        'red'    => '#B81E1F',
    ];

    $wwFormatTime = function ($iso) {
        if (empty($iso) || !is_string($iso)) return null;
        try {
            return \Carbon\Carbon::parse($iso, 'Europe/Lisbon')->format('d/m H:i');
        } catch (\Throwable $e) {
            return $iso;
        }
    };
@endphp

@if(!empty($ww))
    <ul class="weather-warnings-list">
        @foreach($ww as $w)
            @php
                $level = isset($w['awarenessLevelID']) && isset($wwLevelBg[$w['awarenessLevelID']])
                    ? $w['awarenessLevelID']
                    : 'yellow';
                $bg    = $wwLevelBg[$level];
                $type  = $w['awarenessTypeName'] ?? '';
                $start = $wwFormatTime($w['startTime'] ?? null);
                $end   = $wwFormatTime($w['endTime']   ?? null);
                $text  = $w['text'] ?? '';
            @endphp
            <li class="weather-warnings-item weather-warnings-item--{{ $level }}">
                <div class="weather-warnings-item__head">
                    <span class="weather-warnings-badge" style="background: {{ $bg }};">{{ $type }}</span>
                    @if($start && $end)
                        <span class="weather-warnings-when">{{ $start }} → {{ $end }}</span>
                    @endif
                </div>
                @if(!empty($text))
                    <p class="weather-warnings-text mb-0">{{ $text }}</p>
                @endif
            </li>
        @endforeach
    </ul>
@endif
