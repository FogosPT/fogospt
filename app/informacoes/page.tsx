import { PageShell } from '../components/page-shell';
import { buildMetadata } from '../lib/seo';

export const metadata = buildMetadata({
  title: 'Informações',
  description: 'Contactos úteis e recomendações de segurança.',
  path: '/informacoes'
});

export default function InformacoesPage() {
  return (
    <PageShell title="Informações" description="Conteúdos de prevenção e orientação.">
      <p>Brevemente: secção de perguntas frequentes e procedimentos.</p>
    </PageShell>
  );
}
