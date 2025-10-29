<?php
// public/api/session/status.php - returns whether we have tokens
require __DIR__ . '/../../oauth/config.php';
ensureHttps();
header('Content-Type: application/json');
$has = !empty($_COOKIE['spotify_access_token']) || !empty($_COOKIE['spotify_refresh_token']);
echo json_encode(['authenticated' => (bool)$has]);
