import { NextResponse } from 'next/server';
import { fetchAllActiveFires } from '@/app/lib/fires';

export async function GET() {
  const data = await fetchAllActiveFires();
  return NextResponse.json({ success: true, data }, { status: 200 });
}
