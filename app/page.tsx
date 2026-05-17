import Link from 'next/link';
import { FireMap } from './components/fire-map';
import { WeatherPanel } from './components/weather-panel';
import { TwitterPanel } from './components/twitter-panel';
import { buildMetadata } from './lib/seo';

export const metadata = buildMetadata({
  title: 'Mapa de incêndios',
  description: 'Mapa principal com ocorrências ativas, detalhe rápido, meteo e monitorização social.',
  path: '/'
});

export default function HomePage() {
  return (
    <>
      <main className="home-main">
        <FireMap />
      </main>

      <section className="main">
        <div className="grid">
          <article className="card">
            <h2>Rotas disponíveis</h2>
            <p>
              <code>/</code>, <code>/madeira</code>, <code>/lista</code>, <code>/tabela</code>, <code>/avisos</code> e detalhes
              dinâmicos de fogo.
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
