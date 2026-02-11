'use client';

import Link from 'next/link';
import { useState } from 'react';

const links = [
  ['/', 'Mapa'],
  ['/ocorrencias', 'Ocorrências'],
  ['/madeira', 'Madeira'],
  ['/lista', 'Lista'],
  ['/tabela', 'Tabela'],
  ['/avisos', 'Avisos'],
  ['/madeira/avisos', 'Avisos Madeira'],
  ['/informacoes', 'Informações'],
  ['/estatisticas', 'Estatísticas'],
  ['/notificacoes', 'Notificações'],
  ['/parceiros', 'Parceiros'],
  ['/sobre', 'Sobre'],
  ['/outros', 'Outros']
] as const;

export function Navbar() {
  const [menuOpen, setMenuOpen] = useState(false);

  return (
    <header className="nav">
      <div className="nav-bar">
        <Link href="/" className="nav-brand" onClick={() => setMenuOpen(false)}>
          <span className="nav-title">Fogos.pt</span>
        </Link>

        <button
          type="button"
          className="hamburger-btn"
          aria-label="Abrir menu"
          aria-expanded={menuOpen}
          onClick={() => setMenuOpen((value) => !value)}
        >
          <span />
          <span />
          <span />
        </button>
      </div>

      <div className={`nav-overlay ${menuOpen ? 'is-open' : ''}`} onClick={() => setMenuOpen(false)} />

      <nav className={`side-menu ${menuOpen ? 'is-open' : ''}`} aria-label="Navegação principal">
        <div className="side-menu-head">
          <strong>Menu</strong>
          <button type="button" className="menu-close" onClick={() => setMenuOpen(false)} aria-label="Fechar menu">
            ×
          </button>
        </div>

        <div className="side-menu-links">
          {links.map(([href, label]) => (
            <Link href={href} key={href} onClick={() => setMenuOpen(false)}>
              {label}
            </Link>
          ))}
        </div>
      </nav>
    </header>
  );
}
