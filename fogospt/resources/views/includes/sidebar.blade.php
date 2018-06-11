@isset($fire)
    <div class="sidebar active">
@endisset

@empty($fire)
    <div class="sidebar">
@endempty

@include('elements.cards.general')

@include('elements.cards.resources')
@include('elements.cards.status')
@include('elements.cards.meteo')

</div>