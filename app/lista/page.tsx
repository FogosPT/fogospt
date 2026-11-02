import Link from 'next/link';
import { PageShell } from '../components/page-shell';
import { buildMetadata } from '../lib/seo';
import { fetchFires } from '../lib/fires';

export const metadata = buildMetadata({
  title: 'Lista de incêndios',
  description: 'Listagem de ocorrências com foco em consulta rápida.',
  path: '/lista'
});

function getStatusClass(statusCode?: number): string {
  if (!statusCode) return 'status-pill status-unknown';
  if (statusCode === 11 || statusCode === 12) return 'status-pill status-muted';
  if (statusCode >= 8) return 'status-pill status-critical';
  if (statusCode >= 6) return 'status-pill status-warning';
  return 'status-pill status-ok';
}

export default async function ListaPage() {
  const fires = await fetchFires();

  return (
    <PageShell title="Lista de incêndios" description="Cartões de ocorrências ativas com acesso rápido ao detalhe.">
      {fires.length === 0 ? (
        <p>Sem dados disponíveis neste momento.</p>
      ) : (
        <div className="fire-list-grid">
          {fires.map((fire) => (
            <article className="card fire-card" key={fire.id}>
              <h2>
                <Link href={`/fogo/${fire.id}/detalhe`}>Ocorrência #{fire.id}</Link>
              </h2>
              <p>
                <strong>Local:</strong> {fire.location ?? '—'}
                {fire.localidade ? ` - ${fire.localidade}` : ''}
                {fire.detailLocation ? ` - ${fire.detailLocation}` : ''}
              </p>
              <p>
                <strong>Início:</strong> {fire.hour ?? '--:--'} {fire.date ?? '--/--/----'}
              </p>
              <p>
                <strong>Natureza:</strong> {fire.natureza ?? '—'}
              </p>
              <p>
                <span className={getStatusClass(fire.statusCode)}>{fire.status ?? 'Sem estado'}</span>
              </p>
              <p>
                <strong>Meios:</strong> {fire.man ?? 0} 👨‍🚒 · {fire.terrain ?? 0} 🚒 · {fire.aerial ?? 0} 🚁
              </p>
            </article>
          ))}
        </div>
      )}
    </PageShell>
  );
}
