import Link from 'next/link';

const links = [
  ['/', 'Mapa'],
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
  return (
    <header className="nav">
      <nav className="nav-links" aria-label="Navegação principal">
        {links.map(([href, label]) => (
          <Link href={href} key={href}>
            {label}
          </Link>
        ))}
      </nav>
    </header>
  );
}
