export type SeriesPoint = {
  label: string;
  total?: number;
  man?: number;
  terrain?: number;
  aerial?: number;
};

export type DistrictTotals = Record<string, number>;

export type StatsPageData = {
  nowData: SeriesPoint[];
  weekStats: SeriesPoint[];
  hours8Today: SeriesPoint[];
  hours8Yesterday: SeriesPoint[];
  todayDistricts: DistrictTotals;
  yesterdayDistricts: DistrictTotals;
  lastNightDistricts: DistrictTotals;
  burnAreaLastDays: Record<string, number>;
  motives: Record<string, number>;
};

const API_LB = 'https://api-lb.fogos.pt';
const API_MAIN = 'https://api.fogos.pt';

async function fetchJson(url: string): Promise<unknown> {
  try {
    const response = await fetch(url, { next: { revalidate: 300 } });
    if (!response.ok) return null;
    return response.json();
  } catch {
    return null;
  }
}

function toNumber(value: unknown): number {
  if (typeof value === 'number') return value;
  if (typeof value === 'string') {
    const parsed = Number(value);
    return Number.isFinite(parsed) ? parsed : 0;
  }
  return 0;
}

function parseSeries(data: unknown): SeriesPoint[] {
  if (!Array.isArray(data)) return [];

  return data.reduce<SeriesPoint[]>((acc, item) => {
    if (!item || typeof item !== 'object') return acc;

    const source = item as Record<string, unknown>;
    const label = String(source.label ?? '');
    if (!label) return acc;

    acc.push({
      label,
      total: toNumber(source.total),
      man: toNumber(source.man),
      terrain: toNumber(source.terrain),
      aerial: toNumber(source.aerial)
    });

    return acc;
  }, []);
}

function parseObjectNumberMap(data: unknown): Record<string, number> {
  if (!data || typeof data !== 'object' || Array.isArray(data)) return {};
  const source = data as Record<string, unknown>;

  return Object.fromEntries(Object.entries(source).map(([key, value]) => [key, toNumber(value)]));
}

function parse8hSeries(data: unknown): SeriesPoint[] {
  if (!data || typeof data !== 'object' || Array.isArray(data)) return [];
  const source = data as Record<string, unknown>;

  return Object.entries(source).map(([label, value]) => {
    const point = value && typeof value === 'object' ? (value as Record<string, unknown>) : {};
    return {
      label,
      total: toNumber(point.total)
    };
  });
}

function parseDistrictBlock(data: unknown): DistrictTotals {
  if (!data || typeof data !== 'object' || Array.isArray(data)) return {};
  const source = data as Record<string, unknown>;
  return parseObjectNumberMap(source.distritos);
}

function unwrapData(payload: unknown): unknown {
  if (!payload || typeof payload !== 'object') return null;
  const source = payload as Record<string, unknown>;
  if (source.success === true) return source.data;
  return null;
}

export async function fetchStatsPageData(): Promise<StatsPageData> {
  const [
    nowPayload,
    weekPayload,
    h8TodayPayload,
    h8YesterdayPayload,
    todayPayload,
    yesterdayPayload,
    lastNightPayload,
    burnAreaPayload,
    motivesPayload
  ] = await Promise.all([
    fetchJson(`${API_LB}/v1/now/data`),
    fetchJson(`${API_LB}/v1/stats/week`),
    fetchJson(`${API_LB}/v1/stats/8hours`),
    fetchJson(`${API_LB}/v1/stats/8hours/yesterday`),
    fetchJson(`${API_LB}/v1/stats/today`),
    fetchJson(`${API_LB}/v1/stats/yesterday`),
    fetchJson(`${API_LB}/v1/stats/last-night`),
    fetchJson(`${API_LB}/v1/stats/burn-area`),
    fetchJson(`${API_MAIN}/v1/stats/motive`)
  ]);

  return {
    nowData: parseSeries(unwrapData(nowPayload)),
    weekStats: parseSeries(unwrapData(weekPayload)),
    hours8Today: parse8hSeries(unwrapData(h8TodayPayload)),
    hours8Yesterday: parse8hSeries(unwrapData(h8YesterdayPayload)),
    todayDistricts: parseDistrictBlock(unwrapData(todayPayload)),
    yesterdayDistricts: parseDistrictBlock(unwrapData(yesterdayPayload)),
    lastNightDistricts: parseDistrictBlock(unwrapData(lastNightPayload)),
    burnAreaLastDays: parseObjectNumberMap(unwrapData(burnAreaPayload)),
    motives: parseObjectNumberMap(unwrapData(motivesPayload))
  };
}
