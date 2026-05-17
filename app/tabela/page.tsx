import Link from 'next/link';
import { PageShell } from '../components/page-shell';
import { buildMetadata } from '../lib/seo';
import { fetchFires } from '../lib/fires';

export const metadata = buildMetadata({
  title: 'Tabela de ocorrências',
  description: 'Visualização tabular das ocorrências.',
  path: '/tabela'
});

function sortFiresByIdDesc<T extends { id: string }>(fires: T[]): T[] {
  return [...fires].sort((a, b) => Number(b.id) - Number(a.id));
}

export default async function TabelaPage() {
  const fires = sortFiresByIdDesc(await fetchFires());

  return (
    <PageShell title="Tabela de ocorrências" description="Visão tabular das ocorrências com as principais colunas operacionais.">
      {fires.length === 0 ? (
        <p>Sem dados disponíveis neste momento.</p>
      ) : (
        <div className="table-wrap">
          <table className="fires-table">
            <thead>
              <tr>
                <th>ID</th>
                <th>Início</th>
                <th>Distrito</th>
                <th>Concelho</th>
                <th>Freguesia</th>
                <th>Localidade</th>
                <th>Estado</th>
                <th>👨‍🚒</th>
                <th>🚒</th>
                <th>🚁</th>
              </tr>
            </thead>
            <tbody>
              {fires.map((fire) => (
                <tr key={fire.id}>
                  <td>
                    <Link href={`/fogo/${fire.id}/detalhe`}>{fire.id}</Link>
                  </td>
                  <td>
                    {fire.date ?? '--/--/----'} {fire.hour ?? '--:--'}
                  </td>
                  <td>{fire.district ?? '-'}</td>
                  <td>{fire.concelho ?? '-'}</td>
                  <td>{fire.freguesia ?? '-'}</td>
                  <td>{fire.localidade ?? fire.location ?? '-'}</td>
                  <td>{fire.status ?? '-'}</td>
                  <td>{fire.man ?? 0}</td>
                  <td>{fire.terrain ?? 0}</td>
                  <td>{fire.aerial ?? 0}</td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </PageShell>
  );
}
