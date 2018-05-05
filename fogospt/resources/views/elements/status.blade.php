<div id="status">
    @foreach( $fire['status'] as $status)
        <div>
            <span class="dot"></span>
            <div>
                <p class="status-hour">{{ $status['label'] }}</p>
                <p class="status-label">{{ $status['status'] }}</p>
            </div>
        </div>
    @endforeach
</div>