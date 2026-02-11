# Fogos.pt (Next.js)

Este repositório foi migrado para **Next.js (App Router)**, com foco em performance e SEO.

## Incluído nesta fase

- Estrutura base Next.js 14 + React 18
- Página inicial com mapa Leaflet e ocorrências ativas (equivalente funcional ao `main.js` legado)
- Rotas públicas principais migradas para `app/`
- Páginas `/lista` (cards) e `/tabela` (tabular) ligadas ao feed de ocorrências
- Painel `/estatisticas` implementado com dados equivalentes ao legado (`v1/now/data`, semana, 8h, distritos, área ardida e motivos)
- Página `/notificacoes` integrada com Firebase Messaging (subscrição por tópicos/distritos)
- **SEO por página** com `metadata`, Open Graph, Twitter Card e canonical
- `robots.ts` e `sitemap.ts`
- Integração de **estado do tempo** via API pública Open-Meteo
- Bloco de **procura no Twitter/X** com link de pesquisa e embed da conta `@fogospt`

## Requisitos

- Node.js 18+
- npm 9+

## Execução local

```bash
npm install
npm run dev
```

Abrir: `http://localhost:3000`

## Scripts

- `npm run dev` — ambiente de desenvolvimento
- `npm run lint` — linting do Next.js
- `npm run build` — build de produção
- `npm run start` — servidor da build de produção

## Código legado

O código Laravel/PHP original permanece na pasta `fogospt/` para referência e migração gradual de regras de negócio.
