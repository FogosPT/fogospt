export type CityForecast = {
  city: string;
  temperatureMax: number;
  temperatureMin: number;
  precipitationProbability: number;
  weatherCode: number;
  windSpeed: number;
};

const cities = [
  { city: 'Lisboa', latitude: 38.7223, longitude: -9.1393 },
  { city: 'Porto', latitude: 41.1579, longitude: -8.6291 },
  { city: 'Funchal', latitude: 32.6669, longitude: -16.9241 }
] as const;

function getWeatherLabel(code: number): string {
  if (code === 0) return 'Céu limpo';
  if ([1, 2, 3].includes(code)) return 'Parcialmente nublado';
  if ([45, 48].includes(code)) return 'Nevoeiro';
  if ([51, 53, 55, 56, 57].includes(code)) return 'Chuvisco';
  if ([61, 63, 65, 66, 67].includes(code)) return 'Chuva';
  if ([71, 73, 75, 77].includes(code)) return 'Neve';
  if ([80, 81, 82].includes(code)) return 'Aguaceiros';
  if ([95, 96, 99].includes(code)) return 'Trovoada';
  return 'Condição variável';
}

export async function getPortugalForecast(): Promise<(CityForecast & { label: string })[]> {
  const today = new Date().toISOString().slice(0, 10);

  const data = await Promise.all(
    cities.map(async ({ city, latitude, longitude }) => {
      const response = await fetch(
        `https://api.open-meteo.com/v1/forecast?latitude=${latitude}&longitude=${longitude}&daily=weathercode,temperature_2m_max,temperature_2m_min,precipitation_probability_max,windspeed_10m_max&timezone=Europe%2FLisbon&start_date=${today}&end_date=${today}`,
        {
          next: { revalidate: 1800 }
        }
      );

      if (!response.ok) {
        throw new Error(`Falha ao carregar meteo para ${city}`);
      }

      const json = await response.json();
      return {
        city,
        temperatureMax: json.daily.temperature_2m_max[0],
        temperatureMin: json.daily.temperature_2m_min[0],
        precipitationProbability: json.daily.precipitation_probability_max[0],
        weatherCode: json.daily.weathercode[0],
        windSpeed: json.daily.windspeed_10m_max[0],
        label: getWeatherLabel(json.daily.weathercode[0])
      };
    })
  );

  return data;
}
