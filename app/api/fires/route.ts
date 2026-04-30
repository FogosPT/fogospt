import { NextResponse } from 'next/server';
import { fetchFires } from '@/app/lib/fires';

export async function GET() {
  const data = await fetchFires();
  return NextResponse.json({ success: true, data }, { status: 200 });
}
