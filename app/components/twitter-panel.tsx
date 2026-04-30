'use client';

import Script from 'next/script';

export function TwitterPanel() {
  const query = encodeURIComponent('fogos portugal OR incêndio portugal');

  return (
    <section className="card">
      <h2>Procura no Twitter/X</h2>
      <p>
        Pesquisa rápida por ocorrências: 
        <a href={`https://twitter.com/search?q=${query}&src=typed_query`} target="_blank" rel="noreferrer">
          abrir resultados de pesquisa
        </a>
      </p>
      <a
        className="twitter-timeline"
        data-height="500"
        href="https://twitter.com/fogospt?ref_src=twsrc%5Etfw"
      >
        Tweets by fogospt
      </a>
      <Script async src="https://platform.twitter.com/widgets.js" charSet="utf-8" />
    </section>
  );
}
