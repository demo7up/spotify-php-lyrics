<?php
require __DIR__ . '/config.php';
ensureHttps();

$access = $_COOKIE['spotify_access_token'] ?? null;
header('Content-Type: application/json');
if (!$access) {
  http_response_code(401);
  echo json_encode(['authorized' => false]);
  exit;
}
echo json_encode(['authorized' => true]);
