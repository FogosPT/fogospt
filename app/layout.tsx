import type { Metadata } from 'next';
import { Navbar } from './components/navbar';
import { buildMetadata } from './lib/seo';
import './globals.css';

export const metadata: Metadata = {
  ...buildMetadata({
    title: 'Fogos.pt',
    description: 'Monitorização de incêndios, avisos e meteorologia em Portugal.',
    path: '/'
  }),
  metadataBase: new URL('https://fogos.pt')
};

export default function RootLayout({ children }: { children: React.ReactNode }) {
  return (
    <html lang="pt-PT">
      <body>
        <Navbar />
        {children}
      </body>
    </html>
  );
}
