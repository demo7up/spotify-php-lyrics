// Patch assets/app.js to use /lyrics/find.php and karaoke render
import { parseLRC } from '/assets/lrc.js';

async function checkAuth() {
  const r = await fetch('/api/session/status.php', { credentials: 'include' });
  const j = r.ok ? await r.json() : { authenticated:false };
  document.getElementById('connect').style.display = j.authenticated ? 'none' : 'inline-block';
  return j.authenticated;
}

async function refreshToken() {
  const r = await fetch('/oauth/refresh.php', { credentials: 'include' });
  return r.ok;
}

async function getCurrentlyPlaying() {
  const r = await fetch('/oauth/spotify_current.php', { credentials: 'include' });
  if (!r.ok) return null;
  return await r.json();
}

async function findLyrics(artist, title) {
  const q = new URLSearchParams({ artist, title });
  const r = await fetch('/lyrics/find.php?' + q.toString(), { credentials: 'include' });
  if (!r.ok) return { synced:false, text:'' };
  return await r.json();
}

function renderTrack(info) {
  const cover = document.getElementById('cover');
  const title = document.getElementById('title');
  const artist = document.getElementById('artist');
  const album = document.getElementById('album');
  if (!info || !info.item) {
    title.textContent = 'Nothing playing';
    artist.textContent = '—';
    album.textContent  = '—';
    cover.src = '';
    return;
  }
  title.textContent  = info.item.name || '—';
  artist.textContent = (info.item.artists || []).map(a => a.name).join(', ');
  album.textContent  = info.item.album ? info.item.album.name : '—';
  const img = info.item.album?.images?.[1]?.url || info.item.album?.images?.[0]?.url || '';
  if (img) cover.src = img;
}

function renderLyrics(payload, progressMs){
  const el = document.getElementById('lyrics-text');
  if (!payload) { el.textContent = 'Lyrics unavailable.'; return; }
  if (!payload.synced) { el.textContent = payload.text || 'Lyrics unavailable.'; return; }
  const lines = payload.lines || [];
  if (!lines.length) { el.textContent = 'Lyrics unavailable.'; return; }
  // choose current line by progressMs
  let i = 0;
  for (let k=0;k<lines.length;k++){ if (lines[k].t <= progressMs) i = k; else break; }
  // build a small window around current
  const start = Math.max(0, i-3);
  const end   = Math.min(lines.length, i+4);
  const view  = lines.slice(start, end).map((ln,idx)=> (start+idx===i? '> ' : '  ') + ln.text).join('
');
  el.textContent = view;
}

async function tick() {
  const ok = await checkAuth();
  if (!ok) return;
  const info = await getCurrentlyPlaying();
  renderTrack(info);
  if (info && info.item) {
    const artist = (info.item.artists || []).map(a => a.name).join(', ');
    const title  = info.item.name || '';
    const lyr    = await findLyrics(artist, title);
    const progress = (info.progress_ms || 0);
    renderLyrics(lyr, progress);
  }
}

document.getElementById('connect').addEventListener('click', () => {
  window.location.href = '/oauth/authorize.php';
});

document.getElementById('refresh').addEventListener('click', async () => {
  await refreshToken();
  await tick();
});

// run immediately and then every 5s
tick();
setInterval(tick, 5000);
