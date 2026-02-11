import { PageShell } from '../components/page-shell';
import { buildMetadata } from '../lib/seo';

export const metadata = buildMetadata({
  title: 'Sobre',
  description: 'Informação sobre o projeto Fogos.pt.',
  path: '/sobre'
});

export default function SobrePage() {
  return (
    <PageShell title="Sobre" description="Plataforma comunitária para acompanhamento de incêndios em Portugal.">
      <p>Migração para Next.js concluída para melhorar performance, SEO e manutenção.</p>
    </PageShell>
  );
}
