import { PageShell } from '../components/page-shell';
import { buildMetadata } from '../lib/seo';

export const metadata = buildMetadata({
  title: 'Mapa Madeira',
  description: 'Monitorização dedicada para a Região Autónoma da Madeira.',
  path: '/madeira'
});

export default function MadeiraPage() {
  return (
    <PageShell title="Madeira" description="Área dedicada às ocorrências e avisos da Madeira.">
      <p>Esta rota preserva a funcionalidade histórica do projeto para a região da Madeira.</p>
    </PageShell>
  );
}
