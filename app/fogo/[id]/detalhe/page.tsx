import type { Metadata } from 'next';
import { PageShell } from '../../../components/page-shell';
import { FireDetailMap } from '../../../components/fire-detail-map';
import { buildMetadata } from '../../../lib/seo';

export function generateMetadata({ params }: { params: { id: string } }): Metadata {
  return buildMetadata({
    title: `Detalhe da ocorrência #${params.id}`,
    description: `Vista detalhada da ocorrência ${params.id}, com mapa, marcador e área quando disponível.`,
    path: `/fogo/${params.id}/detalhe`
  });
}

export default function FogoFullDetailPage({ params }: { params: { id: string } }) {
  return (
    <PageShell title={`Ocorrência #${params.id} (detalhe)`} description="Mapa com marcador de foco e área ardida quando existir.">
      <FireDetailMap id={params.id} />
    </PageShell>
  );
}
