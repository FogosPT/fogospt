import { PageShell } from '../components/page-shell';
import { buildMetadata } from '../lib/seo';

export const metadata = buildMetadata({
  title: 'Outros incêndios',
  description: 'Ocorrências fora da vista principal.',
  path: '/outros'
});

export default function OutrosPage() {
  return (
    <PageShell title="Outros incêndios" description="Ocorrências complementares e conteúdos de contexto.">
      <p>Página preparada para listagens secundárias.</p>
    </PageShell>
  );
}
