'use client';

import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import Script from 'next/script';

type FireItem = {
  id: string;
  lat: number;
  lng: number;
  location?: string;
  localidade?: string;
  natureza?: string;
  statusCode: number;
  date?: string;
  hour?: string;
  man?: number;
  terrain?: number;
  aerial?: number;
  important?: boolean;
};

declare global {
  interface Window {
    L?: any;
  }
}

const BASE_SIZE = 20;

function getImportance(item: FireItem): number {
  const man = item.man ?? 0;
  const terrain = item.terrain ?? 0;
  const aerial = item.aerial ?? 0;
  return man + terrain * 3 + aerial * 7;
}

function getMarkerColor(item: FireItem): string {
  if (item.statusCode === 11 || item.statusCode === 12) return '#64748b';
  if (item.important) return '#dc2626';
  if (item.statusCode >= 8) return '#ea580c';
  if (item.statusCode >= 6) return '#f59e0b';
  return '#16a34a';
}

export function FireMap() {
  const mapContainerRef = useRef<HTMLDivElement>(null);
  const mapRef = useRef<any>(null);
  const markersRef = useRef<any[]>([]);
  const [scriptLoaded, setScriptLoaded] = useState(false);
  const [fires, setFires] = useState<FireItem[]>([]);
  const [selectedFireId, setSelectedFireId] = useState<string | null>(null);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  const selectedFire = useMemo(
    () => fires.find((fire) => String(fire.id) === String(selectedFireId)) ?? null,
    [fires, selectedFireId]
  );

  const clearMarkers = useCallback(() => {
    if (!mapRef.current) return;
    markersRef.current.forEach((marker) => {
      mapRef.current.removeLayer(marker);
    });
    markersRef.current = [];
  }, []);

  const drawMarkers = useCallback(
    (items: FireItem[]) => {
      if (!window.L || !mapRef.current) return;
      clearMarkers();

      items.forEach((item) => {
        if (!item.lat || !item.lng) return;
        const size = Math.max(BASE_SIZE, Math.min(56, BASE_SIZE + getImportance(item) * 0.03));
        const color = getMarkerColor(item);

        const marker = window.L.marker([item.lat, item.lng], {
          icon: window.L.divIcon({
            className: 'fire-icon-wrap',
            html: `<span class="fire-icon" style="width:${size}px;height:${size}px;background:${color}"></span>`,
            iconSize: [size, size]
          })
        });

        marker.addTo(mapRef.current);
        marker.on('click', () => {
          setSelectedFireId(String(item.id));
          mapRef.current.setView([item.lat, item.lng], 10);
          window.history.replaceState({}, '', `/fogo/${item.id}`);
        });

        markersRef.current.push(marker);
      });
    },
    [clearMarkers]
  );

  const loadFires = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const response = await fetch('/api/fires', { cache: 'no-store' });
      if (!response.ok) throw new Error('Falha a carregar ocorrências.');

      const payload = await response.json();
      const data: FireItem[] = Array.isArray(payload?.data) ? payload.data : [];
      setFires(data);

      if (data.length && !selectedFireId) {
        setSelectedFireId(String(data[0].id));
      }
      drawMarkers(data);
    } catch {
      setError('Não foi possível carregar ocorrências neste momento.');
    } finally {
      setLoading(false);
    }
  }, [drawMarkers, selectedFireId]);

  useEffect(() => {
    if (!scriptLoaded || !window.L || !mapContainerRef.current || mapRef.current) return;

    mapRef.current = window.L.map(mapContainerRef.current).setView([40.5050025, -7.9053189], 7);
    window.L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution:
        '&copy; <a href="https://www.openstreetmap.org/copyright" target="_blank">OpenStreetMap</a> contributors'
    }).addTo(mapRef.current);

    loadFires();

    const refreshInterval = setInterval(() => {
      loadFires();
    }, 60000);

    return () => {
      clearInterval(refreshInterval);
      clearMarkers();
      mapRef.current?.remove();
      mapRef.current = null;
    };
  }, [scriptLoaded, loadFires, clearMarkers]);

  useEffect(() => {
    if (!selectedFire || !mapRef.current) return;
    mapRef.current.setView([selectedFire.lat, selectedFire.lng], 10);
  }, [selectedFire]);

  return (
    <section className="map-layout card">
      <div className="map-toolbar">
        <strong>Ocorrências ativas: {fires.length}</strong>
        <button onClick={loadFires} disabled={loading} className="refresh-btn" type="button">
          {loading ? 'A atualizar...' : 'Atualizar agora'}
        </button>
      </div>

      <div className="map-grid">
        <div ref={mapContainerRef} id="map" className="map-canvas" />

        <aside className="fire-sidebar">
          <h2>Detalhe da ocorrência</h2>
          {error ? <p>{error}</p> : null}
          {selectedFire ? (
            <>
              <p>
                <strong>ID:</strong> {selectedFire.id}
              </p>
              <p>
                <strong>Local:</strong> {selectedFire.location}
                {selectedFire.localidade ? ` - ${selectedFire.localidade}` : ''}
              </p>
              <p>
                <strong>Natureza:</strong> {selectedFire.natureza ?? '—'}
              </p>
              <p>
                <strong>Início:</strong> {selectedFire.date} {selectedFire.hour}
              </p>
              <p>
                <strong>Meios:</strong> {selectedFire.man ?? 0} operacionais, {selectedFire.terrain ?? 0} terrestres,{' '}
                {selectedFire.aerial ?? 0} aéreos
              </p>
              <p>
                <a href={`/fogo/${selectedFire.id}/detalhe`}>Ver página de detalhe</a>
              </p>
            </>
          ) : (
            <p>Seleciona um marcador no mapa para ver os dados.</p>
          )}
        </aside>
      </div>

      <Script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" strategy="afterInteractive" onLoad={() => setScriptLoaded(true)} />
    </section>
  );
}
