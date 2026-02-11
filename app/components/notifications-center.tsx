'use client';

import Script from 'next/script';
import { useEffect, useMemo, useState } from 'react';

type Topic = { value: string; label: string; group: 'Geral' | 'Distritos' };

declare global {
  interface Window {
    firebase?: any;
  }
}

const topics: Topic[] = [
  { value: 'important', label: 'Ocorrências importantes', group: 'Geral' },
  { value: 'alerts', label: 'Avisos gerais', group: 'Geral' },
  { value: 'Aveiro', label: 'Aveiro', group: 'Distritos' },
  { value: 'Beja', label: 'Beja', group: 'Distritos' },
  { value: 'Braga', label: 'Braga', group: 'Distritos' },
  { value: 'Braganca', label: 'Bragança', group: 'Distritos' },
  { value: 'CasteloBranco', label: 'Castelo Branco', group: 'Distritos' },
  { value: 'Coimbra', label: 'Coimbra', group: 'Distritos' },
  { value: 'Evora', label: 'Évora', group: 'Distritos' },
  { value: 'Faro', label: 'Faro', group: 'Distritos' },
  { value: 'Guarda', label: 'Guarda', group: 'Distritos' },
  { value: 'Leiria', label: 'Leiria', group: 'Distritos' },
  { value: 'Lisboa', label: 'Lisboa', group: 'Distritos' },
  { value: 'Portalegre', label: 'Portalegre', group: 'Distritos' },
  { value: 'Porto', label: 'Porto', group: 'Distritos' },
  { value: 'Santarem', label: 'Santarém', group: 'Distritos' },
  { value: 'Setubal', label: 'Setúbal', group: 'Distritos' },
  { value: 'VianadoCastelo', label: 'Viana do Castelo', group: 'Distritos' },
  { value: 'VilaReal', label: 'Vila Real', group: 'Distritos' },
  { value: 'Viseu', label: 'Viseu', group: 'Distritos' },
  { value: 'Madeira', label: 'Madeira', group: 'Distritos' }
];

const firebaseConfig = {
  apiKey: 'AIzaSyCxxu_jTrBrGE8Em1kaqn3wTbCBa8_Ra7M',
  authDomain: 'admob-app-id-6663345165.firebaseapp.com',
  databaseURL: 'https://admob-app-id-6663345165.firebaseio.com',
  projectId: 'admob-app-id-6663345165',
  storageBucket: 'admob-app-id-6663345165.appspot.com',
  messagingSenderId: '726949968874',
  appId: '1:726949968874:web:9ee6c0784c6992a96f4f26'
};

function storageKey(topic: string) {
  return `topic:${topic}`;
}

export function NotificationsCenter() {
  const [scriptsReady, setScriptsReady] = useState(false);
  const [supported, setSupported] = useState(false);
  const [authorized, setAuthorized] = useState(false);
  const [token, setToken] = useState<string>('');
  const [busyTopic, setBusyTopic] = useState<string>('');
  const [message, setMessage] = useState<string>('');
  const [selected, setSelected] = useState<Record<string, boolean>>({});

  useEffect(() => {
    const ok = typeof window !== 'undefined' && 'serviceWorker' in navigator && 'PushManager' in window;
    setSupported(ok);

    if (!ok) return;

    const savedToken = window.localStorage.getItem('fcm-token') ?? '';
    const auth = window.localStorage.getItem('notificationsAuth') === 'true';

    const state: Record<string, boolean> = {};
    topics.forEach((topic) => {
      state[topic.value] = window.localStorage.getItem(storageKey(topic.value)) === 'true';
    });

    setSelected(state);
    setAuthorized(auth);
    setToken(savedToken);
  }, []);

  useEffect(() => {
    if (!scriptsReady || !supported || !window.firebase) return;

    if (!window.firebase.apps?.length) {
      window.firebase.initializeApp(firebaseConfig);
    }

    const messaging = window.firebase.messaging();
    messaging.onMessage((payload: any) => {
      const text = payload?.notification?.body ?? 'Nova notificação recebida.';
      setMessage(text);
    });
  }, [scriptsReady, supported]);

  const grouped = useMemo(
    () => ({
      Geral: topics.filter((topic) => topic.group === 'Geral'),
      Distritos: topics.filter((topic) => topic.group === 'Distritos')
    }),
    []
  );

  async function enableNotifications() {
    if (!window.firebase) return;

    try {
      const permission = await Notification.requestPermission();
      if (permission !== 'granted') {
        setMessage('Permissão recusada para notificações.');
        return;
      }

      await navigator.serviceWorker.register('/firebase-messaging-sw.js');
      const messaging = window.firebase.messaging();
      const currentToken = await messaging.getToken();

      if (!currentToken) {
        setMessage('Não foi possível obter token de notificações.');
        return;
      }

      window.localStorage.setItem('notificationsAuth', 'true');
      window.localStorage.setItem('fcm-token', currentToken);
      setAuthorized(true);
      setToken(currentToken);
      setMessage('Notificações ativadas com sucesso.');
    } catch {
      setMessage('Falha ao ativar notificações Firebase.');
    }
  }

  async function toggleTopic(topic: string, checked: boolean) {
    if (!token) {
      setMessage('Ative primeiro as notificações para gerir tópicos.');
      return;
    }

    setBusyTopic(topic);

    try {
      const response = await fetch(`/api/notifications/${checked ? 'subscribe' : 'unsubscribe'}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ token, topic })
      });

      const payload = (await response.json().catch(() => null)) as { success?: boolean } | null;
      if (response.ok && payload?.success) {
        const next = { ...selected, [topic]: checked };
        setSelected(next);
        window.localStorage.setItem(storageKey(topic), String(checked));
        setMessage(checked ? `Subscrição ativa para ${topic}.` : `Subscrição removida para ${topic}.`);
      } else {
        setMessage('Não foi possível atualizar a subscrição.');
      }
    } catch {
      setMessage('Erro de ligação ao serviço de notificações.');
    } finally {
      setBusyTopic('');
    }
  }

  function resetAll() {
    window.localStorage.clear();
    setAuthorized(false);
    setToken('');
    const resetState: Record<string, boolean> = {};
    topics.forEach((topic) => {
      resetState[topic.value] = false;
    });
    setSelected(resetState);
    setMessage('Preferências locais removidas.');
  }

  if (!supported) {
    return (
      <section className="card">
        <h2>Notificações</h2>
        <p>Este browser não suporta notificações push.</p>
      </section>
    );
  }

  return (
    <section className="card">
      <Script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app-compat.js" strategy="afterInteractive" onLoad={() => setScriptsReady(true)} />
      <Script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-messaging-compat.js" strategy="afterInteractive" />

      <h2>Notificações Firebase</h2>
      {!authorized ? (
        <button className="refresh-btn" type="button" onClick={enableNotifications}>
          Ativar notificações
        </button>
      ) : (
        <p>Notificações ativas neste browser.</p>
      )}

      {message ? <p className="lead">{message}</p> : null}

      <div className="notifications-grid">
        {(['Geral', 'Distritos'] as const).map((group) => (
          <section key={group} className="card">
            <h3>{group}</h3>
            <div className="topics-list">
              {grouped[group].map((topic) => (
                <label key={topic.value} className="topic-item">
                  <input
                    type="checkbox"
                    checked={Boolean(selected[topic.value])}
                    disabled={!authorized || busyTopic === topic.value}
                    onChange={(event) => toggleTopic(topic.value, event.target.checked)}
                  />
                  <span>{topic.label}</span>
                </label>
              ))}
            </div>
          </section>
        ))}
      </div>

      <button className="btn-reset" type="button" onClick={resetAll}>
        Limpar preferências locais
      </button>
    </section>
  );
}
