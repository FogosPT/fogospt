import { WeatherPanel } from '../components/weather-panel';
import { PageShell } from '../components/page-shell';
import { buildMetadata } from '../lib/seo';

export const metadata = buildMetadata({
  title: 'Avisos',
  description: 'Avisos meteorológicos e operacionais para Portugal.',
  path: '/avisos'
});

export default function AvisosPage() {
  return (
    <PageShell title="Avisos" description="Avisos oficiais e visão meteo resumida.">
      <WeatherPanel />
    </PageShell>
  );
}
