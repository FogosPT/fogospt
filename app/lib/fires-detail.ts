import type { Fire } from './fires';
import { fetchAllActiveFires, fetchFires } from './fires';

const API_DEV = 'https://api-dev.fogos.pt';

type FireDetailPayload = {
  fire: Fire | null;
  kml: string | null;
  kmlVost: string | null;
};

async function fetchText(url: string): Promise<string | null> {
  try {
    const response = await fetch(url, { next: { revalidate: 120 } });
    if (!response.ok) return null;
    return response.text();
  } catch {
    return null;
  }
}

async function findFireById(id: string): Promise<Fire | null> {
  const [activeAll, defaultFeed] = await Promise.all([fetchAllActiveFires(), fetchFires()]);

  const byId = activeAll.find((item) => String(item.id) === String(id)) ?? defaultFeed.find((item) => String(item.id) === String(id));
  return byId ?? null;
}

export async function fetchFireDetail(id: string): Promise<FireDetailPayload> {
  const [fire, kml, kmlVost] = await Promise.all([
    findFireById(id),
    fetchText(`${API_DEV}/v2/incidents/${id}/kml`),
    fetchText(`${API_DEV}/v2/incidents/${id}/kmlVost`)
  ]);

  return {
    fire,
    kml: kml && kml.includes('<kml') ? kml : null,
    kmlVost: kmlVost && kmlVost.includes('<kml') ? kmlVost : null
  };
}
