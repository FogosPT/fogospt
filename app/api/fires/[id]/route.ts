import { NextResponse } from 'next/server';
import { fetchFireDetail } from '@/app/lib/fires-detail';

export async function GET(_request: Request, { params }: { params: { id: string } }) {
  const data = await fetchFireDetail(params.id);

  if (!data.fire) {
    return NextResponse.json({ success: false, message: 'Ocorrência não encontrada', data }, { status: 404 });
  }

  return NextResponse.json({ success: true, data }, { status: 200 });
}
