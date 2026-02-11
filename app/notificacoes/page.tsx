import { NotificationsCenter } from '../components/notifications-center';
import { PageShell } from '../components/page-shell';
import { buildMetadata } from '../lib/seo';

export const metadata = buildMetadata({
  title: 'Notificações',
  description: 'Gestão de subscrição de notificações push por tema e distrito via Firebase.',
  path: '/notificacoes'
});

export default function NotificacoesPage() {
  return (
    <PageShell title="Notificações" description="Ative notificações push e escolha os tópicos que pretende receber.">
      <NotificationsCenter />
    </PageShell>
  );
}
