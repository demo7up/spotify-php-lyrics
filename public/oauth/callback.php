<?php
require __DIR__ . '/config.php';
ensureHttps();

if (!isset($_GET['code'], $_GET['state'])) {
  http_response_code(400);
  exit('Missing code/state');
}
if (empty($_COOKIE['spotify_oauth_state']) || hash_equals($_COOKIE['spotify_oauth_state'], $_GET['state']) === false) {
  http_response_code(400);
  exit('Invalid state');
}
setcookie('spotify_oauth_state', '', time()-3600, '/');

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
    'grant_type'   => 'authorization_code',
    'code'         => $_GET['code'],
    'redirect_uri' => SPOTIFY_REDIRECT_URI
  ])
]);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($code !== 200) {
  http_response_code(500);
  exit('Failed to exchange code');
}

$data = json_decode($resp, true);

// Store tokens in secure cookies
setcookie('spotify_access_token',  $data['access_token'],  [
  'expires'=> time() + $data['expires_in'] - 30,
  'path'=>'/', 'secure'=>true, 'httponly'=>true, 'samesite'=>'Lax'
]);
if (isset($data['refresh_token'])) {
  setcookie('spotify_refresh_token', $data['refresh_token'], [
    'expires'=> time() + 60*60*24*30,
    'path'=>'/', 'secure'=>true, 'httponly'=>true, 'samesite'=>'Lax'
  ]);
}

header('Location: /');
exit;
