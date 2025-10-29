<?php
// Generic pass-through to Spotify Web API (use with caution; restrict allowed paths as needed)
require __DIR__ . '/config.php';
ensureHttps();

$access = $_COOKIE['spotify_access_token'] ?? null;
if (!$access) { http_response_code(401); exit('Unauthorized'); }

$path = $_GET['path'] ?? '';
if (!preg_match('#^v1/[-A-Za-z0-9_/.?=&]+$#', $path)) {
  http_response_code(400);
  exit('Invalid path');
}

$url = 'https://api.spotify.com/' . $path;
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
