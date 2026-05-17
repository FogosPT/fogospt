import type { DistrictTotals, SeriesPoint } from '../lib/stats';

function EmptyState() {
  return <p>Não há dados disponíveis neste momento.</p>;
}

function maxValue(values: number[]): number {
  const max = Math.max(0, ...values);
  return max === 0 ? 1 : max;
}

export function SeriesBars({ title, data, metric }: { title: string; data: SeriesPoint[]; metric: 'total' | 'man' | 'terrain' | 'aerial' }) {
  if (!data.length) {
    return (
      <section className="card stats-card">
        <h2>{title}</h2>
        <EmptyState />
      </section>
    );
  }

  const values = data.map((item) => item[metric] ?? 0);
  const peak = maxValue(values);

  return (
    <section className="card stats-card">
      <h2>{title}</h2>
      <div className="stats-bars">
        {data.map((item) => {
          const value = item[metric] ?? 0;
          const width = `${Math.max(3, (value / peak) * 100)}%`;
          return (
            <div className="stats-row" key={`${title}-${item.label}`}>
              <span className="stats-label">{item.label}</span>
              <div className="stats-track">
                <span className="stats-fill" style={{ width }} />
              </div>
              <strong>{value}</strong>
            </div>
          );
        })}
      </div>
    </section>
  );
}

export function DistrictDonutFallback({ title, districts }: { title: string; districts: DistrictTotals }) {
  const entries = Object.entries(districts).sort((a, b) => b[1] - a[1]);

  return (
    <section className="card stats-card">
      <h2>{title}</h2>
      {!entries.length ? (
        <EmptyState />
      ) : (
        <ul className="district-list">
          {entries.map(([district, value]) => (
            <li key={`${title}-${district}`}>
              <span>{district}</span>
              <strong>{value}</strong>
            </li>
          ))}
        </ul>
      )}
    </section>
  );
}

export function KeyValueBars({ title, values }: { title: string; values: Record<string, number> }) {
  const entries = Object.entries(values).sort((a, b) => b[1] - a[1]);
  if (!entries.length) {
    return (
      <section className="card stats-card">
        <h2>{title}</h2>
        <EmptyState />
      </section>
    );
  }

  const peak = maxValue(entries.map(([, value]) => value));

  return (
    <section className="card stats-card">
      <h2>{title}</h2>
      <div className="stats-bars">
        {entries.map(([label, value]) => (
          <div className="stats-row" key={`${title}-${label}`}>
            <span className="stats-label">{label}</span>
            <div className="stats-track">
              <span className="stats-fill" style={{ width: `${Math.max(3, (value / peak) * 100)}%` }} />
            </div>
            <strong>{value}</strong>
          </div>
        ))}
      </div>
    </section>
  );
}
