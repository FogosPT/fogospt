import type { MetadataRoute } from 'next';

const routes = ['/', '/ocorrencias', '/madeira', '/lista', '/tabela', '/avisos', '/madeira/avisos', '/informacoes', '/parceiros', '/estatisticas', '/notificacoes', '/sobre', '/outros'];

export default function sitemap(): MetadataRoute.Sitemap {
  return routes.map((route) => ({
    url: `https://fogos.pt${route}`,
    changeFrequency: 'hourly',
    priority: route === '/' ? 1 : 0.7
  }));
}
