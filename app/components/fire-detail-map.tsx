'use client';

import { useEffect, useMemo, useRef, useState } from 'react';
import Script from 'next/script';

type Fire = {
  id: string;
  location?: string;
  localidade?: string;
  detailLocation?: string;
  natureza?: string;
  date?: string;
  hour?: string;
  man?: number;
  terrain?: number;
  aerial?: number;
  lat?: number;
  lng?: number;
};

type FireDetailData = {
  fire: Fire | null;
  kml: string | null;
  kmlVost: string | null;
};

declare global {
  interface Window {
    L?: any;
  }
}

type Coord = [number, number];

function parseKmlCoordinates(kml: string | null): Coord[][] {
  if (!kml) return [];

  const matches = [...kml.matchAll(/<coordinates>([\s\S]*?)<\/coordinates>/g)];
  if (!matches.length) return [];

  return matches
    .map((match) => {
      const raw = match[1] ?? '';
      return raw
        .trim()
        .split(/\s+/)
        .map((point) => {
          const [lngRaw, latRaw] = point.split(',');
          const lat = Number(latRaw);
          const lng = Number(lngRaw);
          if (!Number.isFinite(lat) || !Number.isFinite(lng)) return null;
          return [lat, lng] as Coord;
        })
        .filter((value): value is Coord => Boolean(value));
    })
    .filter((line) => line.length >= 3);
}

export function FireDetailMap({ id }: { id: string }) {
  const [scriptLoaded, setScriptLoaded] = useState(false);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [data, setData] = useState<FireDetailData | null>(null);
  const mapRef = useRef<any>(null);
  const layersRef = useRef<any[]>([]);
  const mapContainerRef = useRef<HTMLDivElement>(null);

  const polygonsIcnf = useMemo(() => parseKmlCoordinates(data?.kml ?? null), [data?.kml]);
  const polygonsVost = useMemo(() => parseKmlCoordinates(data?.kmlVost ?? null), [data?.kmlVost]);

  useEffect(() => {
    let cancelled = false;

    async function load() {
      setLoading(true);
      setError(null);
      try {
        const response = await fetch(`/api/fires/${id}`, { cache: 'no-store' });
        const payload = await response.json();

        if (!response.ok || !payload?.data?.fire) {
          throw new Error(payload?.message ?? 'Não foi possível carregar o detalhe.');
        }

        if (!cancelled) {
          setData(payload.data as FireDetailData);
        }
      } catch (err) {
        if (!cancelled) {
          setError(err instanceof Error ? err.message : 'Erro inesperado no detalhe.');
        }
      } finally {
        if (!cancelled) setLoading(false);
      }
    }

    load();
    return () => {
      cancelled = true;
    };
  }, [id]);

  useEffect(() => {
    if (!scriptLoaded || !window.L || !mapContainerRef.current || mapRef.current) return;

    mapRef.current = window.L.map(mapContainerRef.current).setView([40.5, -7.9], 8);
    window.L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; OpenStreetMap contributors'
    }).addTo(mapRef.current);

    return () => {
      layersRef.current.forEach((layer) => {
        mapRef.current?.removeLayer(layer);
      });
      layersRef.current = [];
      mapRef.current?.remove();
      mapRef.current = null;
    };
  }, [scriptLoaded]);

  useEffect(() => {
    if (!mapRef.current || !data?.fire?.lat || !data?.fire?.lng || !window.L) return;

    layersRef.current.forEach((layer) => {
      mapRef.current.removeLayer(layer);
    });
    layersRef.current = [];

    const marker = window.L.marker([data.fire.lat, data.fire.lng]).addTo(mapRef.current);
    const circle = window.L.circle([data.fire.lat, data.fire.lng], {
      color: '#d4242a',
      fillColor: '#ff6f1e',
      fillOpacity: 0.25,
      radius: 500
    }).addTo(mapRef.current);

    layersRef.current.push(marker, circle);

    polygonsIcnf.forEach((polygon) => {
      const layer = window.L.polygon(polygon, {
        color: '#2563eb',
        weight: 2,
        fillOpacity: 0.15
      }).addTo(mapRef.current);
      layersRef.current.push(layer);
    });

    polygonsVost.forEach((polygon) => {
      const layer = window.L.polygon(polygon, {
        color: '#16a34a',
        weight: 2,
        fillOpacity: 0.15,
        dashArray: '4,6'
      }).addTo(mapRef.current);
      layersRef.current.push(layer);
    });

    const bounds = window.L.featureGroup(layersRef.current).getBounds();
    if (bounds.isValid()) {
      mapRef.current.fitBounds(bounds, { padding: [20, 20] });
    } else {
      mapRef.current.setView([data.fire.lat, data.fire.lng], 11);
    }
  }, [data, polygonsIcnf, polygonsVost]);

  return (
    <section className="card fire-detail-wrap">
      <h2>Mapa de detalhe</h2>
      {loading ? <p>A carregar detalhe da ocorrência...</p> : null}
      {error ? <p>{error}</p> : null}

      <div ref={mapContainerRef} className="fire-detail-map" />

      {data?.fire ? (
        <div className="detail-grid">
          <p>
            <strong>Local:</strong> {data.fire.location ?? '—'}
            {data.fire.localidade ? ` - ${data.fire.localidade}` : ''}
            {data.fire.detailLocation ? ` - ${data.fire.detailLocation}` : ''}
          </p>
          <p>
            <strong>Início:</strong> {data.fire.hour ?? '—'} {data.fire.date ?? ''}
          </p>
          <p>
            <strong>Natureza:</strong> {data.fire.natureza ?? '—'}
          </p>
          <p>
            <strong>Meios:</strong> {data.fire.man ?? 0} operacionais, {data.fire.terrain ?? 0} terrestres, {data.fire.aerial ?? 0} aéreos
          </p>
          <p>
            <strong>Área:</strong> {polygonsIcnf.length ? 'Área ICNF disponível' : 'Sem área ICNF'} /{' '}
            {polygonsVost.length ? 'Área VOST disponível' : 'Sem área VOST'}
          </p>
        </div>
      ) : null}

      <Script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" strategy="afterInteractive" onLoad={() => setScriptLoaded(true)} />
    </section>
  );
}
