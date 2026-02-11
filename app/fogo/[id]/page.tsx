import type { Metadata } from 'next';
import { PageShell } from '../../components/page-shell';
import { buildMetadata } from '../../lib/seo';

export function generateMetadata({ params }: { params: { id: string } }): Metadata {
  return buildMetadata({
    title: `Ocorrência #${params.id}`,
    description: `Detalhe da ocorrência ${params.id} no Fogos.pt.`,
    path: `/fogo/${params.id}`
  });
}

export default function FogoDetailPage({ params }: { params: { id: string } }) {
  return (
    <PageShell title={`Detalhe da ocorrência #${params.id}`} description="Resumo operacional e atualizações relevantes.">
      <p>Conteúdo dinâmico da ocorrência. Esta rota substitui o antigo endpoint /fogo/&#123;id&#125;.</p>
    </PageShell>
  );
}
