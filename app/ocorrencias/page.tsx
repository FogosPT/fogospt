import Link from 'next/link';
import { FireMap } from '@/app/components/fire-map';
import { WeatherPanel } from '@/app/components/weather-panel';
import { TwitterPanel } from '@/app/components/twitter-panel';
import { buildMetadata } from '@/app/lib/seo';

export const metadata = buildMetadata({
  title: 'Ocorrências',
  description: 'Mapa de ocorrências ativas com todas as ocorrências do endpoint all=1.',
  path: '/ocorrencias'
});

export default function OcorrenciasPage() {
  return (
    <>
      <main className="home-main">
        <FireMap apiPath="/api/fires/all" />
      </main>

      <section className="main">
        <div className="grid">
          <article className="card">
            <h2>Ocorrências (all=1)</h2>
            <p>
              Esta página usa o endpoint <code>/v2/incidents/active?all=1</code> para carregar o conjunto completo de ocorrências
              ativas.
            </p>
          </article>
          <article className="card">
            <h2>Exemplo detalhe</h2>
            <p>
              Abrir detalhe dinâmico em <Link href="/fogo/123">/fogo/123</Link>.
            </p>
          </article>
        </div>

        <WeatherPanel />
        <TwitterPanel />
      </section>
    </>
  );
}
