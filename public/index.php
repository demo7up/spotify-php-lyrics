<?php
// public/index.php
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Spotify Lyrics Viewer (PHP)</title>
  <link rel="stylesheet" href="/assets/styles.css">
</head>
<body>
  <div class="wrap">
    <header>
      <h1>Spotify Lyrics Viewer</h1>
      <button id="connect" class="btn">Connect to Spotify</button>
      <button id="refresh" class="btn btn-outline" title="Refresh token">Refresh Token</button>
    </header>

    <section id="now-playing" class="card">
      <h2>Now Playing</h2>
      <div id="track">
        <div class="cover"><img id="cover" src="" alt="" /></div>
        <div class="meta">
          <div id="title">—</div>
          <div id="artist">—</div>
          <div id="album">—</div>
        </div>
      </div>
    </section>

    <section id="lyrics" class="card">
      <h2>Lyrics</h2>
      <pre id="lyrics-text">Connect to Spotify to load lyrics…</pre>
    </section>
  </div>

  <script src="/assets/app.js"></script>
</body>
</html>
