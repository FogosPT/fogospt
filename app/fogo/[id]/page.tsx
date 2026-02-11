import type { Metadata } from 'next';
import { PageShell } from '../../components/page-shell';
import { FireDetailMap } from '../../components/fire-detail-map';
import { buildMetadata } from '../../lib/seo';

export function generateMetadata({ params }: { params: { id: string } }): Metadata {
  return buildMetadata({
    title: `Ocorrência #${params.id}`,
    description: `Detalhe da ocorrência ${params.id} com mapa e informação operacional.`,
    path: `/fogo/${params.id}`
  });
}

export default function FogoDetailPage({ params }: { params: { id: string } }) {
  return (
    <PageShell title={`Detalhe da ocorrência #${params.id}`} description="Resumo operacional e cartografia da ocorrência.">
      <FireDetailMap id={params.id} />
    </PageShell>
  );
}
