<div id="status">
    @foreach( $fire['statusHistory'] as $status)
        <div>
            <span class="dot status-{{ $status['statusCode'] }} timelineDot"></span>
            <div>
                <p class="status-hour">{{ $status['label'] }}</p>
                <p class="status-label">{{ $status['status'] }}</p>
            </div>
        </div>
    @endforeach
</div>