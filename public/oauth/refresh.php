<?php
require __DIR__ . '/config.php';
ensureHttps();

$refresh = $_COOKIE['spotify_refresh_token'] ?? null;
if (!$refresh) { http_response_code(401); exit('No refresh token'); }

$basicAuth = base64_encode(SPOTIFY_CLIENT_ID . ':' . SPOTIFY_CLIENT_SECRET);

$ch = curl_init('https://accounts.spotify.com/api/token');
curl_setopt_array($ch, [
  CURLOPT_POST           => true,
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER     => [
    'Authorization: Basic ' . $basicAuth,
    'Content-Type: application/x-www-form-urlencoded'
  ],
  CURLOPT_POSTFIELDS     => http_build_query([
    'grant_type'    => 'refresh_token',
    'refresh_token' => $refresh
  ])
]);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($code !== 200) {
  http_response_code(401);
  exit('Refresh failed');
}
$data = json_decode($resp, true);

if (isset($data['access_token'])) {
  setcookie('spotify_access_token', $data['access_token'], [
    'expires'=> time() + $data['expires_in'] - 30,
    'path'=>'/', 'secure'=>true, 'httponly'=>true, 'samesite'=>'Lax'
  ]);
}

header('Content-Type: application/json');
echo json_encode(['ok' => true]);
