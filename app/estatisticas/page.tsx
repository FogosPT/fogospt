import { PageShell } from '../components/page-shell';
import { buildMetadata } from '../lib/seo';
import { fetchStatsPageData } from '../lib/stats';
import { DistrictDonutFallback, KeyValueBars, SeriesBars } from '../components/stats-panels';

export const metadata = buildMetadata({
  title: 'Estatísticas',
  description: 'Indicadores operacionais, incêndios por período e distribuição distrital.',
  path: '/estatisticas'
});

export default async function EstatisticasPage() {
  const stats = await fetchStatsPageData();

  return (
    <PageShell
      title="Estatísticas"
      description="Implementação baseada no stats.js legado: série temporal, últimas 8h, distritos, área ardida e motivos."
    >
      <div className="stats-grid">
        <SeriesBars title="Evolução atual (incêndios ativos)" data={stats.nowData} metric="total" />
        <SeriesBars title="Evolução atual (operacionais)" data={stats.nowData} metric="man" />

        <SeriesBars title="Últimos dias (incêndios)" data={stats.weekStats} metric="total" />
        <SeriesBars title="Últimos dias (operacionais)" data={stats.weekStats} metric="man" />

        <SeriesBars title="Últimas 8 horas (hoje)" data={stats.hours8Today} metric="total" />
        <SeriesBars title="Últimas 8 horas (ontem)" data={stats.hours8Yesterday} metric="total" />

        <DistrictDonutFallback title="Distribuição por distrito (hoje)" districts={stats.todayDistricts} />
        <DistrictDonutFallback title="Distribuição por distrito (ontem)" districts={stats.yesterdayDistricts} />
        <DistrictDonutFallback title="Distribuição por distrito (última noite)" districts={stats.lastNightDistricts} />

        <KeyValueBars title="Área ardida (últimos dias)" values={stats.burnAreaLastDays} />
        <KeyValueBars title="Motivos" values={stats.motives} />
      </div>
    </PageShell>
  );
}
