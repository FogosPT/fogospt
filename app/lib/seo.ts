import type { Metadata } from 'next';

const baseUrl = 'https://fogos.pt';
const siteName = 'Fogos.pt';
const defaultDescription = 'Acompanhe incêndios, avisos e condições meteorológicas em Portugal.';

export function buildMetadata({
  title,
  description = defaultDescription,
  path = '/'
}: {
  title: string;
  description?: string;
  path?: string;
}): Metadata {
  const fullTitle = `${title} | ${siteName}`;
  const url = new URL(path, baseUrl).toString();

  return {
    title: fullTitle,
    description,
    alternates: {
      canonical: url
    },
    openGraph: {
      title: fullTitle,
      description,
      url,
      siteName,
      locale: 'pt_PT',
      type: 'website'
    },
    twitter: {
      card: 'summary_large_image',
      title: fullTitle,
      description,
      creator: '@fogospt'
    }
  };
}
