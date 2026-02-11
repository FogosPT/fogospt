export type Fire = {
  id: string;
  date?: string;
  hour?: string;
  district?: string;
  concelho?: string;
  freguesia?: string;
  localidade?: string;
  location?: string;
  detailLocation?: string;
  natureza?: string;
  status?: string;
  statusCode?: number;
  man?: number;
  terrain?: number;
  aerial?: number;
  lat?: number;
  lng?: number;
  important?: boolean;
};

const DEFAULT_REMOTE_URL = 'https://api-dev.fogos.pt/new/fires';
const ACTIVE_ALL_REMOTE_URL = 'https://api-dev.fogos.pt/v2/incidents/active?all=1';

function parseNumber(value: unknown): number | undefined {
  if (typeof value === 'number') return value;
  if (typeof value === 'string' && value.trim() !== '') {
    const parsed = Number(value);
    return Number.isFinite(parsed) ? parsed : undefined;
  }
  return undefined;
}

function normalizeFire(raw: Record<string, unknown>): Fire {
  return {
    id: String(raw.id ?? raw.incident_id ?? ''),
    date: typeof raw.date === 'string' ? raw.date : undefined,
    hour: typeof raw.hour === 'string' ? raw.hour : undefined,
    district: typeof raw.district === 'string' ? raw.district : undefined,
    concelho: typeof raw.concelho === 'string' ? raw.concelho : undefined,
    freguesia: typeof raw.freguesia === 'string' ? raw.freguesia : undefined,
    localidade: typeof raw.localidade === 'string' ? raw.localidade : undefined,
    location: typeof raw.location === 'string' ? raw.location : undefined,
    detailLocation: typeof raw.detailLocation === 'string' ? raw.detailLocation : undefined,
    natureza: typeof raw.natureza === 'string' ? raw.natureza : undefined,
    status: typeof raw.status === 'string' ? raw.status : undefined,
    statusCode: parseNumber(raw.statusCode ?? raw.status_code),
    man: parseNumber(raw.man),
    terrain: parseNumber(raw.terrain),
    aerial: parseNumber(raw.aerial),
    lat: parseNumber(raw.lat ?? raw.latitude),
    lng: parseNumber(raw.lng ?? raw.longitude),
    important: Boolean(raw.important)
  };
}

async function fetchFiresByUrl(url: string): Promise<Fire[]> {
  try {
    const response = await fetch(url, {
      headers: {
        Accept: 'application/json'
      },
      next: { revalidate: 60 }
    });

    if (!response.ok) {
      return [];
    }

    const payload: unknown = await response.json();
    if (!payload || typeof payload !== 'object') return [];

    const source = payload as { data?: unknown; incidents?: unknown };
    const data = Array.isArray(source.data) ? source.data : Array.isArray(source.incidents) ? source.incidents : null;
    if (!Array.isArray(data)) return [];

    return data
      .filter((item): item is Record<string, unknown> => !!item && typeof item === 'object')
      .map(normalizeFire)
      .filter((item) => item.id);
  } catch {
    return [];
  }
}

export async function fetchFires(): Promise<Fire[]> {
  return fetchFiresByUrl(DEFAULT_REMOTE_URL);
}

export async function fetchAllActiveFires(): Promise<Fire[]> {
  return fetchFiresByUrl(ACTIVE_ALL_REMOTE_URL);
}
