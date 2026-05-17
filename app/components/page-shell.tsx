import type { ReactNode } from 'react';

export function PageShell({
  title,
  description,
  children
}: {
  title: string;
  description?: string;
  children: ReactNode;
}) {
  return (
    <main className="main">
      <section className="card">
        <h1>{title}</h1>
        {description ? <p className="lead">{description}</p> : null}
        {children}
      </section>
    </main>
  );
}
