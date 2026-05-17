import { NextResponse } from 'next/server';

const FIREBASE_IID_BATCH_REMOVE = 'https://iid.googleapis.com/iid/v1:batchRemove';

export async function POST(request: Request) {
  const tokenKey = process.env.FIREBASE_TOKEN;
  if (!tokenKey) {
    return NextResponse.json({ success: false, error: 'FIREBASE_TOKEN não configurado' }, { status: 500 });
  }

  const payload = (await request.json().catch(() => null)) as { token?: string; topic?: string } | null;
  const token = payload?.token?.trim();
  const topic = payload?.topic?.trim();

  if (!token || !topic) {
    return NextResponse.json({ success: false, error: 'token/topic inválidos' }, { status: 400 });
  }

  try {
    const response = await fetch(FIREBASE_IID_BATCH_REMOVE, {
      method: 'POST',
      headers: {
        Authorization: `key=${tokenKey}`,
        'Content-Type': 'application/json'
      },
      body: JSON.stringify({
        to: `/topics/web-${topic}`,
        registration_tokens: [token]
      })
    });

    return NextResponse.json({ success: response.ok }, { status: response.ok ? 200 : 502 });
  } catch {
    return NextResponse.json({ success: false }, { status: 500 });
  }
}
