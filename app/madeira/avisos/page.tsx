import { PageShell } from '../../components/page-shell';
import { buildMetadata } from '../../lib/seo';

export const metadata = buildMetadata({
  title: 'Avisos Madeira',
  description: 'Avisos específicos para a Região Autónoma da Madeira.',
  path: '/madeira/avisos'
});

export default function AvisosMadeiraPage() {
  return (
    <PageShell title="Avisos Madeira" description="Canal de avisos para a Madeira.">
      <p>Integração de feed dedicado em fase seguinte.</p>
    </PageShell>
  );
}
