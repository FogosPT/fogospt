import type { Metadata } from 'next';
import { PageShell } from '../../../components/page-shell';
import { buildMetadata } from '../../../lib/seo';

export function generateMetadata({ params }: { params: { id: string } }): Metadata {
  return buildMetadata({
    title: `Detalhe da ocorrência #${params.id}`,
    description: `Vista detalhada da ocorrência ${params.id}.`,
    path: `/fogo/${params.id}/detalhe`
  });
}

export default function FogoFullDetailPage({ params }: { params: { id: string } }) {
  return (
    <PageShell title={`Ocorrência #${params.id} (detalhe)`} description="Página equivalente ao histórico /fogo/{id}/detalhe.">
      <p>Inclui secções reservadas para risco, estado, meteo, extra e partilhas sociais.</p>
    </PageShell>
  );
}
