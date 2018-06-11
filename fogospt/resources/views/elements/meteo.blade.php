<div id="meteo">
    <div class="temp_atual">
        @lang('elements.cards.meteo.temp_atual'):&nbsp;{{$fire['meteo']['main']['temp']}} 
</div>
<div class="temp_min">
        @lang('elements.cards.meteo.temp_min'):&nbsp;{{$fire['meteo']['main']['temp_min']}} 
</div>
<div class="temp_min">
        @lang('elements.cards.meteo.temp_max'):&nbsp;{{$fire['meteo']['main']['temp_max']}} 
</div>
<div class="temp_min">
        @lang('elements.cards.meteo.estado_atual'):&nbsp;{{$fire['meteo']['weather'][0]['description']}}&nbsp; <img src="https://openweathermap.org/img/w/{{$fire['meteo']['weather'][0]['icon']}}.png" />
</div>

</div>