<?php
// public/api/session/logout.php - clears cookies
require __DIR__ . '/../../oauth/config.php';
ensureHttps();
$expire = time()-3600;
foreach (['spotify_access_token','spotify_refresh_token','spotify_oauth_state'] as $c) {
  setcookie($c, '', $expire, '/', '', true, true);
}
header('Content-Type: application/json');
echo json_encode(['ok'=>true]);
