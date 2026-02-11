import type { Metadata } from 'next';
import { PageShell } from '../../../components/page-shell';
import { buildMetadata } from '../../../lib/seo';

export function generateMetadata({ params }: { params: { id: string } }): Metadata {
  return buildMetadata({
    title: `Ocorrência Madeira #${params.id}`,
    description: `Detalhe da ocorrência ${params.id} na Madeira.`,
    path: `/madeira/fogo/${params.id}`
  });
}

export default function FogoMadeiraDetailPage({ params }: { params: { id: string } }) {
  return (
    <PageShell title={`Detalhe Madeira #${params.id}`} description="Ocorrência da Madeira com dados dinâmicos.">
      <p>Equivalente à rota histórica /madeira/fogo/&#123;id&#125;.</p>
    </PageShell>
  );
}
