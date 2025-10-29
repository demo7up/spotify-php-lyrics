async function checkAuth() {
  const r = await fetch('/oauth/token.php', { credentials: 'include' });
  if (r.status !== 200) {
    document.getElementById('connect').style.display = 'inline-block';
    return false;
  }
  document.getElementById('connect').style.display = 'none';
  return true;
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

async function loadLyrics(artist, title) {
  const q = new URLSearchParams({ artist, title });
  const r = await fetch('/lyrics/fetch.php?' + q.toString(), { credentials: 'include' });
  if (!r.ok) return 'Lyrics unavailable.';
  const data = await r.json();
  return data.lyrics || 'Lyrics unavailable.';
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

async function tick() {
  const ok = await checkAuth();
  if (!ok) return;
  const info = await getCurrentlyPlaying();
  renderTrack(info);
  if (info && info.item) {
    const artist = (info.item.artists || []).map(a => a.name).join(', ');
    const title  = info.item.name || '';
    const text   = await loadLyrics(artist, title);
    document.getElementById('lyrics-text').textContent = text;
  }
}

document.getElementById('connect').addEventListener('click', () => {
  window.location.href = '/oauth/authorize.php';
});

document.getElementById('refresh').addEventListener('click', async () => {
  await refreshToken();
  await tick();
});

// run immediately and then every 10s
tick();
setInterval(tick, 10000);
