import { getPortugalForecast } from '../lib/weather';

export async function WeatherPanel() {
  try {
    const forecast = await getPortugalForecast();

    return (
      <section className="card">
        <h2>Estado do tempo (API Open-Meteo)</h2>
        <div className="grid">
          {forecast.map((item) => (
            <article key={item.city} className="card weather-card">
              <h3>{item.city}</h3>
              <p>{item.label}</p>
              <p>
                <strong>{item.temperatureMin}ºC</strong> / <strong>{item.temperatureMax}ºC</strong>
              </p>
              <p>Prob. precipitação: {item.precipitationProbability}%</p>
              <p>Vento máx: {item.windSpeed} km/h</p>
            </article>
          ))}
        </div>
      </section>
    );
  } catch {
    return (
      <section className="card">
        <h2>Estado do tempo</h2>
        <p>Não foi possível carregar a previsão neste momento.</p>
      </section>
    );
  }
}
