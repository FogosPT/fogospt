import type { NextRequest } from 'next/server';
import { NextResponse } from 'next/server';
import { fetchFireDetail } from '@/app/lib/fires-detail';

export async function GET(_request: NextRequest, context: { params: Promise<{ id: string }> }) {
  const { id } = await context.params;
  const data = await fetchFireDetail(id);

  if (!data.fire) {
    return NextResponse.json({ success: false, message: 'Ocorrência não encontrada', data }, { status: 404 });
  }

  return NextResponse.json({ success: true, data }, { status: 200 });
}
