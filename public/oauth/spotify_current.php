<?php
require __DIR__ . '/config.php';
ensureHttps();

$access = $_COOKIE['spotify_access_token'] ?? null;
if (!$access) { http_response_code(401); exit('Unauthorized'); }

$url = 'https://api.spotify.com/v1/me/player/currently-playing?additional_types=track';
$ch = curl_init($url);
curl_setopt_array($ch, [
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_HTTPHEADER     => [ 'Authorization: Bearer ' . $access ]
]);
$resp = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($code);
header('Content-Type: application/json');
echo $resp;
