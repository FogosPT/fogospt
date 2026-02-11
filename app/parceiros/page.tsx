import { PageShell } from '../components/page-shell';
import { buildMetadata } from '../lib/seo';

export const metadata = buildMetadata({
  title: 'Parceiros',
  description: 'Entidades parceiras e apoios ao Fogos.pt.',
  path: '/parceiros'
});

export default function ParceirosPage() {
  return (
    <PageShell title="Parceiros" description="Instituições e comunidades que colaboram com o projeto.">
      <p>Página preparada para catálogo de parceiros com links oficiais.</p>
    </PageShell>
  );
}
